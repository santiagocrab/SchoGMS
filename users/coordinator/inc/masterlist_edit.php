<?php
/**
 * Edit masterlist row + per-student COR/COG + CSV snapshot sync.
 */

require_once __DIR__ . '/masterlist_rows.php';
require_once __DIR__ . '/../../../inc/schogms_document_uploads.php';

if (!function_exists('schogms_masterlist_program_config')) {
    /** @return array{table: string, campus_col: string, program: string} */
    function schogms_masterlist_program_config(string $program): array
    {
        $program = strtolower(trim($program));
        if ($program === 'tes') {
            return ['table' => 'ched_masterlist_tes', 'campus_col' => 'campus', 'program' => 'tes'];
        }

        return ['table' => 'ched_masterlist', 'campus_col' => 'sheet_name', 'program' => 'tdp'];
    }
}

if (!function_exists('schogms_student_doc_basename')) {
    function schogms_student_doc_basename(string $lastname, string $firstname, string $middlename = ''): string
    {
        $lastname = trim($lastname);
        $firstname = trim($firstname);
        $middlename = trim($middlename);
        $base = $lastname . ', ' . $firstname;
        if ($middlename !== '') {
            $base .= ' ' . $middlename;
        }

        return $base;
    }
}

if (!function_exists('schogms_masterlist_csv_dir')) {
    function schogms_masterlist_csv_dir(): string
    {
        $dir = dirname(__DIR__) . '/data/masterlist_exports';
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        return $dir;
    }
}

if (!function_exists('schogms_masterlist_fetch_row')) {
    /** @return array<string, mixed>|null */
    function schogms_masterlist_fetch_row(mysqli $conn, string $program, int $id, string $campus): ?array
    {
        $cfg = schogms_masterlist_program_config($program);
        $sql = "SELECT * FROM {$cfg['table']} WHERE id = ? AND LOWER(TRIM({$cfg['campus_col']})) = LOWER(TRIM(?)) LIMIT 1";
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            return null;
        }
        $stmt->bind_param('is', $id, $campus);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        return $row ?: null;
    }
}

if (!function_exists('schogms_replace_student_document')) {
    /**
     * @param array<string, mixed> $student
     * @param array{name:string,type:string,tmp_name:string,error:int,size:int} $file
     * @return array{success: bool, message: string}
     */
    function schogms_replace_student_document(
        mysqli $conn,
        string $campus,
        string $category,
        array $student,
        array $file,
        string $fileGroup = ''
    ): array {
        $category = strtoupper($category);
        if (!in_array($category, ['COR', 'COG'], true)) {
            return ['success' => false, 'message' => 'Invalid category'];
        }

        if ((int) ($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
            return ['success' => false, 'message' => 'File upload failed'];
        }

        $allowed = ['pdf', 'jpg', 'jpeg', 'png'];
        $ext = strtolower(pathinfo((string) $file['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, $allowed, true)) {
            return ['success' => false, 'message' => 'Only PDF, JPG, PNG allowed'];
        }

        if ((int) ($file['size'] ?? 0) > 15 * 1024 * 1024) {
            return ['success' => false, 'message' => 'File too large (max 15 MB)'];
        }

        $basename = schogms_student_doc_basename(
            (string) ($student['lastname'] ?? ''),
            (string) ($student['firstname'] ?? ''),
            (string) ($student['middlename'] ?? $student['ext'] ?? '')
        );
        $dbFileName = $basename . '.' . $ext;

        if ($fileGroup === '') {
            $fileGroup = $category . ' ' . ucfirst(strtolower($campus));
        }
        $fgCheck = schogms_document_uploads_normalize_file_group($conn, $fileGroup);
        if (!$fgCheck['ok']) {
            return ['success' => false, 'message' => $fgCheck['error']];
        }
        $fileGroup = $fgCheck['value'];

        $ln = (string) ($student['lastname'] ?? '');
        $fn = (string) ($student['firstname'] ?? '');
        $like = $ln . ', ' . $fn . '%';
        $del = $conn->prepare(
            "DELETE FROM document_uploads WHERE campus = ? AND category = ? AND file_name LIKE ?"
        );
        if ($del) {
            $del->bind_param('sss', $campus, $category, $like);
            $del->execute();
            $del->close();
        }

        $uploadRoot = dirname(__DIR__) . '/uploads/' . $category . '/';
        if (!is_dir($uploadRoot)) {
            mkdir($uploadRoot, 0755, true);
        }
        $storedName = preg_replace('/[^a-zA-Z0-9,_\-\. ]+/', '_', $dbFileName);
        $destPath = $uploadRoot . $storedName;
        if (file_exists($destPath)) {
            $storedName = time() . '_' . $storedName;
            $destPath = $uploadRoot . $storedName;
        }

        if (!move_uploaded_file((string) $file['tmp_name'], $destPath)) {
            return ['success' => false, 'message' => 'Could not save file'];
        }

        $dbPath = 'uploads/' . $category . '/' . $storedName;
        $stmt = $conn->prepare(
            'INSERT INTO document_uploads (campus, file_group, category, file_name, file_path) VALUES (?, ?, ?, ?, ?)'
        );
        if (!$stmt) {
            @unlink($destPath);

            return ['success' => false, 'message' => 'Database error'];
        }
        $stmt->bind_param('sssss', $campus, $fileGroup, $category, $dbFileName, $dbPath);
        try {
            $ok = $stmt->execute();
        } catch (mysqli_sql_exception $e) {
            @unlink($destPath);
            $stmt->close();

            return ['success' => false, 'message' => $e->getMessage()];
        }
        $dbErr = $stmt->error;
        $stmt->close();

        if (!$ok) {
            @unlink($destPath);

            return ['success' => false, 'message' => $dbErr !== '' ? $dbErr : 'Could not save document record'];
        }

        return ['success' => true, 'message' => $category . ' uploaded'];
    }
}

if (!function_exists('schogms_update_masterlist_student')) {
    /**
     * @param array<string, string> $fields
     * @return array{success: bool, message: string}
     */
    function schogms_update_masterlist_student(
        mysqli $conn,
        string $program,
        int $id,
        string $campus,
        array $fields
    ): array {
        $cfg = schogms_masterlist_program_config($program);
        $existing = schogms_masterlist_fetch_row($conn, $program, $id, $campus);
        if ($existing === null) {
            return ['success' => false, 'message' => 'Student not found'];
        }

        if ($program === 'tes') {
            $stmt = $conn->prepare(
                'UPDATE ched_masterlist_tes SET
                    seq=?, app_no=?, lastname=?, firstname=?, ext=?, middlename=?, sex=?,
                    course_program_enrolled=?, year_level=?, street=?, town_city=?, contact=?, batch_no=?
                 WHERE id=? AND LOWER(TRIM(campus)) = LOWER(TRIM(?))'
            );
            if (!$stmt) {
                return ['success' => false, 'message' => 'Update failed: ' . $conn->error];
            }
            $stmt->bind_param(
                str_repeat('s', 13) . 'is',
                $fields['seq'],
                $fields['app_no'],
                $fields['lastname'],
                $fields['firstname'],
                $fields['ext'],
                $fields['middlename'],
                $fields['sex'],
                $fields['course_program_enrolled'],
                $fields['year_level'],
                $fields['street'],
                $fields['town_city'],
                $fields['contact'],
                $fields['batch_no'],
                $id,
                $campus
            );
        } else {
            $stmt = $conn->prepare(
                'UPDATE ched_masterlist SET
                    seq=?, app_no=?, award_no=?, lastname=?, firstname=?, extname=?, middlename=?, sex=?,
                    birthdate=?, course_program_enrolled=?, year_level=?, total_units_enrolled=?,
                    status_of_enrollment=?, remarks=?
                 WHERE id=? AND LOWER(TRIM(sheet_name)) = LOWER(TRIM(?))'
            );
            if (!$stmt) {
                return ['success' => false, 'message' => 'Update failed: ' . $conn->error];
            }
            $stmt->bind_param(
                'ssssssssssssssis',
                $fields['seq'],
                $fields['app_no'],
                $fields['award_no'],
                $fields['lastname'],
                $fields['firstname'],
                $fields['extname'],
                $fields['middlename'],
                $fields['sex'],
                $fields['birthdate'],
                $fields['course_program_enrolled'],
                $fields['year_level'],
                $fields['total_units_enrolled'],
                $fields['status_of_enrollment'],
                $fields['remarks'],
                $id,
                $campus
            );
        }

        try {
            if (!$stmt->execute()) {
                $err = $stmt->error;
                $stmt->close();

                return ['success' => false, 'message' => 'Could not update student: ' . $err];
            }
        } catch (mysqli_sql_exception $e) {
            $stmt->close();

            return ['success' => false, 'message' => 'Could not update student: ' . $e->getMessage()];
        }
        $stmt->close();

        return ['success' => true, 'message' => 'Student updated'];
    }
}

if (!function_exists('schogms_regenerate_masterlist_csv')) {
    /**
     * Writes campus masterlist CSV (+ per file_group) for coordinator exports.
     *
     * @return array{success: bool, files: list<string>, message: string}
     */
    function schogms_regenerate_masterlist_csv(mysqli $conn, string $program, string $campus): array
    {
        $program = strtolower(trim($program));
        $campus = trim($campus);
        if ($campus === '') {
            return ['success' => false, 'files' => [], 'message' => 'No campus'];
        }

        $cfg = schogms_masterlist_program_config($program);
        $sql = "SELECT * FROM {$cfg['table']} WHERE {$cfg['campus_col']} = ? ORDER BY id ASC";
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            return ['success' => false, 'files' => [], 'message' => 'Query failed'];
        }
        $stmt->bind_param('s', $campus);
        $stmt->execute();
        $res = $stmt->get_result();
        $rows = [];
        while ($res && ($r = $res->fetch_assoc())) {
            $rows[] = $r;
        }
        $stmt->close();

        $dir = schogms_masterlist_csv_dir();
        $safeCampus = preg_replace('/[^a-zA-Z0-9_-]+/', '_', $campus);
        $written = [];

        if ($program === 'tes') {
            $headers = ['SEQ', 'APP NO', 'LASTNAME', 'FIRSTNAME', 'EXT', 'MIDDLENAME', 'SEX',
                'COURSE', 'YEAR LEVEL', 'STREET', 'TOWN/CITY', 'CONTACT', 'BATCH NO'];
            $mainPath = "{$dir}/TES_{$safeCampus}_masterlist.csv";
            $fp = fopen($mainPath, 'w');
            if ($fp) {
                fputcsv($fp, $headers);
                foreach ($rows as $row) {
                    fputcsv($fp, [
                        $row['seq'] ?? '',
                        $row['app_no'] ?? '',
                        $row['lastname'] ?? '',
                        $row['firstname'] ?? '',
                        $row['ext'] ?? '',
                        $row['middlename'] ?? '',
                        $row['sex'] ?? '',
                        $row['course_program_enrolled'] ?? '',
                        $row['year_level'] ?? '',
                        $row['street'] ?? '',
                        $row['town_city'] ?? '',
                        $row['contact'] ?? '',
                        $row['batch_no'] ?? '',
                    ]);
                }
                fclose($fp);
                $written[] = $mainPath;
            }
        } else {
            $headers = ['SEQ', 'APP NO', 'AWARD NO', 'LASTNAME', 'FIRSTNAME', 'EXTNAME', 'MIDDLENAME',
                'SEX', 'BIRTHDATE', 'COURSE', 'YEAR LEVEL', 'UNITS', 'STATUS', 'REMARKS'];
            $mainPath = "{$dir}/TDP_{$safeCampus}_masterlist.csv";
            $fp = fopen($mainPath, 'w');
            if ($fp) {
                fputcsv($fp, $headers);
                foreach ($rows as $row) {
                    fputcsv($fp, [
                        $row['seq'] ?? '',
                        $row['app_no'] ?? '',
                        $row['award_no'] ?? '',
                        $row['lastname'] ?? '',
                        $row['firstname'] ?? '',
                        $row['extname'] ?? '',
                        $row['middlename'] ?? '',
                        $row['sex'] ?? '',
                        $row['birthdate'] ?? '',
                        $row['course_program_enrolled'] ?? '',
                        $row['year_level'] ?? '',
                        $row['total_units_enrolled'] ?? '',
                        $row['status_of_enrollment'] ?? '',
                        $row['remarks'] ?? '',
                    ]);
                }
                fclose($fp);
                $written[] = $mainPath;
            }
        }

        $byGroup = [];
        foreach ($rows as $row) {
            $fg = trim((string) ($row['file_group'] ?? 'default'));
            if ($fg === '') {
                $fg = 'default';
            }
            $byGroup[$fg][] = $row;
        }
        foreach ($byGroup as $group => $groupRows) {
            $safeGroup = preg_replace('/[^a-zA-Z0-9_-]+/', '_', $group);
            $path = "{$dir}/" . strtoupper($program) . "_{$safeCampus}_{$safeGroup}.csv";
            $fp = fopen($path, 'w');
            if (!$fp) {
                continue;
            }
            if ($program === 'tes') {
                fputcsv($fp, ['SEQ', 'APP NO', 'LASTNAME', 'FIRSTNAME', 'EXT', 'MIDDLENAME', 'SEX',
                    'COURSE', 'YEAR LEVEL', 'STREET', 'TOWN/CITY', 'CONTACT', 'BATCH NO']);
                foreach ($groupRows as $row) {
                    fputcsv($fp, [
                        $row['seq'] ?? '', $row['app_no'] ?? '', $row['lastname'] ?? '',
                        $row['firstname'] ?? '', $row['ext'] ?? '', $row['middlename'] ?? '',
                        $row['sex'] ?? '', $row['course_program_enrolled'] ?? '',
                        $row['year_level'] ?? '', $row['street'] ?? '', $row['town_city'] ?? '',
                        $row['contact'] ?? '', $row['batch_no'] ?? '',
                    ]);
                }
            } else {
                fputcsv($fp, ['SEQ', 'APP NO', 'AWARD NO', 'LASTNAME', 'FIRSTNAME', 'EXTNAME', 'MIDDLENAME',
                    'SEX', 'BIRTHDATE', 'COURSE', 'YEAR LEVEL', 'UNITS', 'STATUS', 'REMARKS']);
                foreach ($groupRows as $row) {
                    fputcsv($fp, [
                        $row['seq'] ?? '', $row['app_no'] ?? '', $row['award_no'] ?? '',
                        $row['lastname'] ?? '', $row['firstname'] ?? '', $row['extname'] ?? '',
                        $row['middlename'] ?? '', $row['sex'] ?? '', $row['birthdate'] ?? '',
                        $row['course_program_enrolled'] ?? '', $row['year_level'] ?? '',
                        $row['total_units_enrolled'] ?? '', $row['status_of_enrollment'] ?? '',
                        $row['remarks'] ?? '',
                    ]);
                }
            }
            fclose($fp);
            $written[] = $path;

            $csvName = basename($path);
            $upd = $conn->prepare(
                "UPDATE {$cfg['table']} SET filename = ? WHERE {$cfg['campus_col']} = ? AND file_group = ?"
            );
            if ($upd) {
                $upd->bind_param('sss', $csvName, $campus, $group);
                $upd->execute();
                $upd->close();
            }
        }

        return [
            'success' => count($written) > 0,
            'files' => $written,
            'message' => count($written) . ' CSV file(s) synced',
        ];
    }
}
