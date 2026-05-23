<?php
/**
 * COR / COG bulk upload: match filenames to CHED masterlist (TDP + TES).
 */

require_once __DIR__ . '/masterlist_rows.php';
require_once __DIR__ . '/masterlist_edit.php';
require_once __DIR__ . '/../../../inc/schogms_document_uploads.php';

if (!function_exists('schogms_cor_cog_upload_root')) {
    /** Shared storage: users/coordinator/uploads/{COR|COG}/ (all roles use same table + paths). */
    function schogms_cor_cog_upload_root(string $category): string
    {
        $category = strtoupper(trim($category));
        if (!in_array($category, ['COR', 'COG'], true)) {
            $category = 'COR';
        }

        return dirname(__DIR__) . '/uploads/' . $category . '/';
    }
}

if (!function_exists('schogms_cor_cog_storage_bases')) {
    /** @return list<string> Absolute directories to prefix stored relative paths (uploads/COR/…). */
    function schogms_cor_cog_storage_bases(): array
    {
        $coordinatorDir = dirname(__DIR__);
        $usersDir = dirname(__DIR__, 2);
        $projectRoot = dirname(__DIR__, 3);

        return array_values(array_unique([
            $coordinatorDir . '/',
            $usersDir . '/registrar/',
            $projectRoot . '/',
            $usersDir . '/',
        ]));
    }
}

if (!function_exists('schogms_cor_cog_resolve_disk_path')) {
    /**
     * Resolve document_uploads.file_path to an absolute file on disk.
     */
    function schogms_cor_cog_resolve_disk_path(string $storedPath, ?string $fileName = null): ?string
    {
        $storedPath = trim(str_replace('\\', '/', $storedPath));
        if ($storedPath === '') {
            return null;
        }

        if (preg_match('#^https?://#i', $storedPath)) {
            return null;
        }

        $candidates = [];
        if ($storedPath[0] === '/') {
            $candidates[] = $storedPath;
        }

        $relative = ltrim($storedPath, '/');
        foreach (schogms_cor_cog_storage_bases() as $base) {
            $candidates[] = $base . $relative;
        }

        if (str_starts_with($relative, 'uploads/')) {
            $tail = substr($relative, strlen('uploads/'));
            foreach (schogms_cor_cog_storage_bases() as $base) {
                $candidates[] = $base . 'uploads/' . $tail;
            }
        }

        if ($fileName !== null && $fileName !== '') {
            $dir = trim(str_replace('\\', '/', dirname($relative)), '.');
            foreach (schogms_cor_cog_storage_bases() as $base) {
                $candidates[] = $base . ($dir !== '' ? $dir . '/' : '') . $fileName;
                $candidates[] = $base . 'uploads/COR/' . $fileName;
                $candidates[] = $base . 'uploads/COG/' . $fileName;
            }
        }

        foreach ($candidates as $path) {
            $path = preg_replace('#/+#', '/', $path);
            if (is_file($path) && is_readable($path)) {
                return $path;
            }
        }

        return null;
    }
}

if (!function_exists('schogms_cor_cog_view_document_url')) {
    /** URL for the shared PDF/image viewer (path relative to SchoGMS project root). */
    function schogms_cor_cog_view_document_url(string $storedPath, string $viewerRole = 'registrar'): string
    {
        $storedPath = trim(str_replace('\\', '/', $storedPath));
        if ($storedPath === '') {
            return '';
        }
        if (preg_match('#^https?://#i', $storedPath)) {
            return $storedPath;
        }

        $prefix = match ($viewerRole) {
            'coordinator' => '../../',
            'chairman' => '../../',
            'registrar' => '../../',
            default => '../../',
        };

        return $prefix . 'view_document.php?path=' . rawurlencode(base64_encode(ltrim($storedPath, '/')));
    }
}

if (!function_exists('schogms_cor_cog_file_href')) {
    /** Build browser URL for a stored document_uploads.file_path from coordinator or registrar pages. */
    function schogms_cor_cog_file_href(string $filePath, string $viewerRole = 'coordinator'): string
    {
        $filePath = trim($filePath);
        if ($filePath === '') {
            return '';
        }
        if (preg_match('#^https?://#i', $filePath)) {
            return $filePath;
        }

        return schogms_cor_cog_view_document_url($filePath, $viewerRole);
    }
}

if (!function_exists('schogms_cor_cog_normalize_name_key')) {
    function schogms_cor_cog_normalize_name_key(string $text): string
    {
        $text = strtoupper(trim($text));
        $text = str_replace(['Ñ', 'ñ', '?'], 'N', $text);
        $text = preg_replace('/[^A-Z0-9, ]+/', ' ', $text);
        $text = preg_replace('/\s+/', ' ', $text);

        return trim($text);
    }
}

if (!function_exists('schogms_cor_cog_filename_to_key')) {
    function schogms_cor_cog_filename_to_key(string $filename): string
    {
        $base = pathinfo($filename, PATHINFO_FILENAME);
        $base = preg_replace('/_\d{10,}.*$/', '', $base);

        return schogms_cor_cog_normalize_name_key($base);
    }
}

if (!function_exists('schogms_cor_cog_masterlist_index')) {
    /**
     * @param string $scope all|tdp|tes
     * @return array<string, array{display: string, lastname: string, firstname: string, middlename: string, program: string}>
     */
    function schogms_cor_cog_masterlist_index(mysqli $conn, string $campus, string $scope = 'all'): array
    {
        $index = [];
        $campus = trim($campus);
        $scope = strtolower(trim($scope));
        if ($campus === '') {
            return $index;
        }

        $load = static function (array $rows, string $program) use (&$index): void {
            foreach ($rows as $row) {
                $lastname = trim((string) ($row['lastname'] ?? ''));
                $firstname = trim((string) ($row['firstname'] ?? ''));
                $middlename = trim((string) ($row['middlename'] ?? ''));
                if ($lastname === '' && $firstname === '') {
                    continue;
                }
                $display = schogms_student_doc_basename($lastname, $firstname, $middlename);
                $key = schogms_cor_cog_normalize_name_key($display);
                if ($key === '') {
                    continue;
                }
                $index[$key] = [
                    'display' => $display,
                    'lastname' => $lastname,
                    'firstname' => $firstname,
                    'middlename' => $middlename,
                    'program' => $program,
                ];
            }
        };

        if ($scope === 'all' || $scope === 'tdp') {
            $tdp = schogms_coordinator_ched_tdp_rows($conn, $campus);
            $load($tdp['rows'], 'TDP');
        }
        if ($scope === 'all' || $scope === 'tes') {
            $tes = schogms_coordinator_ched_tes_rows($conn, $campus);
            $load($tes['rows'], 'TES');
        }

        return $index;
    }
}

if (!function_exists('schogms_cor_cog_match_masterlist_student')) {
    /**
     * @param array<string, array{display: string, lastname: string, firstname: string, middlename: string, program: string}> $index
     * @return array{display: string, lastname: string, firstname: string, middlename: string, program: string}|null
     */
    function schogms_cor_cog_match_masterlist_student(string $filename, array $index): ?array
    {
        $fileKey = schogms_cor_cog_filename_to_key($filename);
        if ($fileKey === '') {
            return null;
        }
        if (isset($index[$fileKey])) {
            return $index[$fileKey];
        }

        foreach ($index as $key => $student) {
            $ln = schogms_cor_cog_normalize_name_key($student['lastname']);
            $fn = schogms_cor_cog_normalize_name_key($student['firstname']);
            if ($ln !== '' && $fn !== '' && str_contains($fileKey, $ln) && str_contains($fileKey, $fn)) {
                return $student;
            }
            $pattern = $ln . ', ' . $fn;
            if ($pattern !== ', ' && str_contains($fileKey, $pattern)) {
                return $student;
            }
        }

        return null;
    }
}

if (!function_exists('schogms_cor_cog_process_upload_batch')) {
    /**
     * @param array<string, array{display: string, lastname: string, firstname: string, middlename: string, program: string}> $masterlistIndex
     * @return array{
     *   accepted: list<array{file: string, category: string, student: string, program: string}>,
     *   rejected: list<array{file: string, category: string, reason: string}>,
     *   errors: list<array{file: string, category: string, reason: string}>,
     *   by_student: array<string, array{program: string, cor: list<string>, cog: list<string>}>
     * }
     */
    function schogms_cor_cog_process_upload_batch(
        mysqli $conn,
        string $campus,
        string $fileGroup,
        string $category,
        array $files,
        array $masterlistIndex
    ): array {
        $category = strtoupper(trim($category));
        $result = [
            'accepted' => [],
            'rejected' => [],
            'errors' => [],
            'by_student' => [],
        ];

        if (!in_array($category, ['COR', 'COG'], true)) {
            return $result;
        }

        $fgCheck = schogms_document_uploads_normalize_file_group($conn, $fileGroup);
        if (!$fgCheck['ok']) {
            $result['errors'][] = [
                'file' => '(all)',
                'category' => $category,
                'reason' => $fgCheck['error'],
            ];

            return $result;
        }
        $fileGroup = $fgCheck['value'];

        $allowedExt = ['pdf', 'jpg', 'jpeg', 'png'];
        $maxBytes = 15 * 1024 * 1024;
        $uploadRoot = schogms_cor_cog_upload_root($category);
        $dirCheck = schogms_ensure_writable_upload_dir($uploadRoot);
        if (!$dirCheck['ok']) {
            $result['errors'][] = [
                'file' => '(all)',
                'category' => $category,
                'reason' => $dirCheck['error'],
            ];

            return $result;
        }

        $stmt = $conn->prepare(
            'INSERT INTO document_uploads (campus, file_group, category, file_name, file_path) VALUES (?, ?, ?, ?, ?)'
        );
        if (!$stmt) {
            $result['errors'][] = [
                'file' => '(all)',
                'category' => $category,
                'reason' => 'Database error.',
            ];

            return $result;
        }

        $names = $files['name'] ?? [];
        if (!is_array($names)) {
            return $result;
        }

        foreach ($names as $index => $originalName) {
            $originalName = trim((string) $originalName);
            if ($originalName === '') {
                continue;
            }

            $tmp = $files['tmp_name'][$index] ?? '';
            $err = (int) ($files['error'][$index] ?? UPLOAD_ERR_NO_FILE);
            $size = (int) ($files['size'][$index] ?? 0);

            if ($err !== UPLOAD_ERR_OK || !is_uploaded_file($tmp)) {
                $result['errors'][] = [
                    'file' => $originalName,
                    'category' => $category,
                    'reason' => 'Upload failed.',
                ];
                continue;
            }
            if ($size <= 0 || $size > $maxBytes) {
                $result['errors'][] = [
                    'file' => $originalName,
                    'category' => $category,
                    'reason' => 'Invalid or too large (max 15 MB).',
                ];
                continue;
            }

            $ext = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
            if (!in_array($ext, $allowedExt, true)) {
                $result['errors'][] = [
                    'file' => $originalName,
                    'category' => $category,
                    'reason' => 'Only PDF, JPG, PNG allowed.',
                ];
                continue;
            }

            $student = schogms_cor_cog_match_masterlist_student($originalName, $masterlistIndex);
            if ($student === null) {
                $result['rejected'][] = [
                    'file' => $originalName,
                    'category' => $category,
                    'reason' => 'Removed — does not match any scholar on the current masterlist (TDP/TES).',
                ];
                continue;
            }

            $dbFileName = schogms_student_doc_basename(
                $student['lastname'],
                $student['firstname'],
                $student['middlename']
            ) . '.' . $ext;

            $ln = $student['lastname'];
            $fn = $student['firstname'];
            $like = $ln . ', ' . $fn . '%';
            $del = $conn->prepare(
                'DELETE FROM document_uploads WHERE campus = ? AND category = ? AND file_name LIKE ?'
            );
            if ($del) {
                $del->bind_param('sss', $campus, $category, $like);
                $del->execute();
                $del->close();
            }

            $storedName = preg_replace('/[^a-zA-Z0-9,_\-\. ]+/', '_', $dbFileName);
            $destPath = $uploadRoot . $storedName;
            if (file_exists($destPath)) {
                $storedName = time() . '_' . $storedName;
                $destPath = $uploadRoot . $storedName;
            }

            if (!move_uploaded_file($tmp, $destPath)) {
                $result['errors'][] = [
                    'file' => $originalName,
                    'category' => $category,
                    'reason' => 'Could not save file on server.',
                ];
                continue;
            }

            $dbPath = 'uploads/' . $category . '/' . $storedName;
            $stmt->bind_param('sssss', $campus, $fileGroup, $category, $dbFileName, $dbPath);
            if (!$stmt->execute()) {
                @unlink($destPath);
                $dbErr = trim((string) $stmt->error);
                $reason = 'Database insert failed.';
                if ($dbErr !== '' && stripos($dbErr, 'file_group') !== false) {
                    $reason = 'File group name is too long for the database. Use a shorter batch label (max '
                        . schogms_document_uploads_file_group_max() . ' characters).';
                } elseif ($dbErr !== '') {
                    $reason = 'Database insert failed: ' . $dbErr;
                }
                $result['errors'][] = [
                    'file' => $originalName,
                    'category' => $category,
                    'reason' => $reason,
                ];
                continue;
            }

            $display = $student['display'];
            $result['accepted'][] = [
                'file' => $dbFileName,
                'category' => $category,
                'student' => $display,
                'program' => $student['program'],
            ];

            if (!isset($result['by_student'][$display])) {
                $result['by_student'][$display] = [
                    'program' => $student['program'],
                    'cor' => [],
                    'cog' => [],
                ];
            }
            $catKey = strtolower($category);
            $result['by_student'][$display][$catKey][] = $dbFileName;
        }

        $stmt->close();
        ksort($result['by_student']);

        return $result;
    }
}

if (!function_exists('schogms_cor_cog_merge_batch_results')) {
    /**
     * @param array{accepted: array, rejected: array, errors: array, by_student: array} $a
     * @param array{accepted: array, rejected: array, errors: array, by_student: array} $b
     */
    function schogms_cor_cog_merge_batch_results(array $a, array $b): array
    {
        $merged = [
            'accepted' => array_merge($a['accepted'], $b['accepted']),
            'rejected' => array_merge($a['rejected'], $b['rejected']),
            'errors' => array_merge($a['errors'], $b['errors']),
            'by_student' => $a['by_student'],
        ];
        foreach ($b['by_student'] as $name => $data) {
            if (!isset($merged['by_student'][$name])) {
                $merged['by_student'][$name] = $data;
                continue;
            }
            $merged['by_student'][$name]['cor'] = array_merge(
                $merged['by_student'][$name]['cor'] ?? [],
                $data['cor'] ?? []
            );
            $merged['by_student'][$name]['cog'] = array_merge(
                $merged['by_student'][$name]['cog'] ?? [],
                $data['cog'] ?? []
            );
        }
        ksort($merged['by_student']);

        return $merged;
    }
}

if (!function_exists('schogms_cor_cog_build_upload_message')) {
    /**
     * @param array{accepted: array, rejected: array, errors: array, by_student: array} $batch
     */
    function schogms_cor_cog_build_upload_message(array $batch): string
    {
        $accepted = count($batch['accepted']);
        $rejected = count($batch['rejected']);
        $errors = count($batch['errors']);
        $students = count($batch['by_student']);

        $parts = [];
        if ($accepted > 0) {
            $parts[] = "{$accepted} file(s) saved for {$students} scholar(s)";
        }
        if ($rejected > 0) {
            $parts[] = "{$rejected} file(s) removed (not on masterlist)";
        }
        if ($errors > 0) {
            $parts[] = "{$errors} file(s) failed";
        }

        return $parts !== [] ? implode('. ', $parts) . '.' : 'No files processed.';
    }
}
