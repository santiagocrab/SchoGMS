<?php
/**
 * Fast CHED masterlist rows for coordinator tables (no per-row document_uploads LIKE join).
 */

if (!function_exists('schogms_coordinator_registrar_join_sql')) {
    function schogms_coordinator_registrar_join_sql(): string
    {
        return 'LEFT JOIN registrar_master_list rm ON '
            . 'cm.lastname COLLATE utf8mb4_general_ci = rm.last_name COLLATE utf8mb4_general_ci '
            . 'AND cm.firstname COLLATE utf8mb4_general_ci = rm.first_name COLLATE utf8mb4_general_ci '
            . 'AND cm.middlename COLLATE utf8mb4_general_ci = rm.middle_name COLLATE utf8mb4_general_ci';
    }
}

if (!function_exists('schogms_coordinator_document_index_keys_for_row')) {
    /**
     * Normalized lookup keys for a document_uploads row (matches upload + CHED name keys).
     *
     * @return list<string>
     */
    function schogms_coordinator_document_index_keys_for_row(string $fileName, string $filePath): array
    {
        if (!function_exists('schogms_cor_cog_filename_to_key')) {
            require_once __DIR__ . '/cor_cog_upload_helpers.php';
        }
        if (!function_exists('schogms_student_doc_basename')) {
            require_once __DIR__ . '/masterlist_edit.php';
        }

        $keys = [];
        $fileName = trim($fileName);
        if ($fileName !== '') {
            $base = preg_replace('/\.(pdf|jpe?g|png|gif|webp)$/i', '', $fileName);
            $keys[] = schogms_cor_cog_normalize_name_key($base);
            $keys[] = schogms_cor_cog_filename_to_key($fileName);
        }
        if ($filePath !== '') {
            $keys[] = schogms_cor_cog_filename_to_key(basename($filePath));
        }

        return array_values(array_unique(array_filter($keys, static fn(string $k): bool => $k !== '')));
    }
}

if (!function_exists('schogms_coordinator_document_index_merge')) {
    /**
     * @param array<string, array{COR:bool, COG:bool, files: list<string>, cor_path: string, cog_path: string}> $index
     * @param list<string> $keys
     * @param array{category: string, file_path: string, file_name: string} $row
     */
    function schogms_coordinator_document_index_merge(array &$index, array $keys, array $row): void
    {
        if ($keys === []) {
            return;
        }
        $cat = (string) ($row['category'] ?? '');
        $fpath = (string) ($row['file_path'] ?? '');
        $fname = (string) ($row['file_name'] ?? '');

        $bucket = ['COR' => false, 'COG' => false, 'files' => [], 'cor_path' => '', 'cog_path' => ''];
        foreach ($keys as $key) {
            if (isset($index[$key])) {
                $bucket = $index[$key];
                break;
            }
        }

        if ($cat === 'COR') {
            $bucket['COR'] = true;
            if ($bucket['cor_path'] === '' && $fpath !== '') {
                $bucket['cor_path'] = $fpath;
            }
        }
        if ($cat === 'COG') {
            $bucket['COG'] = true;
            if ($bucket['cog_path'] === '' && $fpath !== '') {
                $bucket['cog_path'] = $fpath;
            }
        }
        if ($fname !== '') {
            $bucket['files'][] = $fname;
        }

        foreach ($keys as $key) {
            $index[$key] = $bucket;
        }
    }
}

if (!function_exists('schogms_coordinator_document_index')) {
    /**
     * @return array<string, array{COR:bool, COG:bool, files: list<string>, cor_path: string, cog_path: string}>
     */
    function schogms_coordinator_document_index(mysqli $conn, string $campus, bool $allCampuses = false): array
    {
        $index = [];
        $campus = trim($campus);
        if (!$allCampuses && $campus === '') {
            return $index;
        }
        if (!function_exists('schogms_cor_cog_normalize_name_key')) {
            require_once __DIR__ . '/cor_cog_upload_helpers.php';
        }

        if ($allCampuses) {
            $stmt = $conn->prepare(
                "SELECT file_name, category, file_path FROM document_uploads
                 WHERE category IN ('COR', 'COG')"
            );
        } else {
            $stmt = $conn->prepare(
                'SELECT file_name, category, file_path FROM document_uploads
                 WHERE LOWER(TRIM(campus)) = LOWER(TRIM(?)) AND category IN (\'COR\', \'COG\')'
            );
        }
        if (!$stmt) {
            return $index;
        }
        if (!$allCampuses) {
            $stmt->bind_param('s', $campus);
        }
        $stmt->execute();
        $res = $stmt->get_result();
        while ($res && ($row = $res->fetch_assoc())) {
            $keys = schogms_coordinator_document_index_keys_for_row(
                (string) ($row['file_name'] ?? ''),
                (string) ($row['file_path'] ?? '')
            );
            schogms_coordinator_document_index_merge($index, $keys, $row);
        }
        $stmt->close();

        return $index;
    }
}

if (!function_exists('schogms_coordinator_student_doc_key')) {
    function schogms_coordinator_student_doc_key(string $lastname, string $firstname, string $middlename): string
    {
        if (!function_exists('schogms_student_doc_basename')) {
            require_once __DIR__ . '/masterlist_edit.php';
        }
        if (!function_exists('schogms_cor_cog_normalize_name_key')) {
            require_once __DIR__ . '/cor_cog_upload_helpers.php';
        }

        return schogms_cor_cog_normalize_name_key(
            schogms_student_doc_basename($lastname, $firstname, $middlename)
        );
    }
}

if (!function_exists('schogms_coordinator_resolve_doc')) {
    /**
     * @param array<string, array{COR:bool, COG:bool, cor_path:string, cog_path:string}> $docIndex
     * @return array{cor_path: string, cog_path: string, has_cor: bool, has_cog: bool}
     */
    function schogms_coordinator_resolve_doc(array $docIndex, array $row): array
    {
        if (!function_exists('schogms_cor_cog_normalize_name_key')) {
            require_once __DIR__ . '/cor_cog_upload_helpers.php';
        }

        $lastname = (string) ($row['lastname'] ?? $row['last_name'] ?? '');
        $firstname = (string) ($row['firstname'] ?? $row['first_name'] ?? '');
        $middlename = (string) ($row['middlename'] ?? $row['middle_name'] ?? '');

        $key = schogms_coordinator_student_doc_key($lastname, $firstname, $middlename);
        $doc = $docIndex[$key] ?? null;

        if ($doc === null && $key !== '') {
            $ln = schogms_cor_cog_normalize_name_key($lastname);
            $fn = schogms_cor_cog_normalize_name_key($firstname);
            foreach ($docIndex as $dk => $dv) {
                if ($ln !== '' && $fn !== '' && str_contains($dk, $ln) && str_contains($dk, $fn)) {
                    $doc = $dv;
                    break;
                }
                $pattern = $ln . ', ' . $fn;
                if ($pattern !== ', ' && str_contains($dk, $pattern)) {
                    $doc = $dv;
                    break;
                }
            }
        }

        $corPath = (string) ($doc['cor_path'] ?? '');
        $cogPath = (string) ($doc['cog_path'] ?? '');

        return [
            'cor_path' => $corPath,
            'cog_path' => $cogPath,
            'has_cor' => $corPath !== '',
            'has_cog' => $cogPath !== '',
        ];
    }
}

if (!function_exists('schogms_coordinator_ched_tdp_rows')) {
    /**
     * @return array{rows: array<int, array<string, mixed>>, error: string}
     */
    function schogms_coordinator_ched_tdp_rows(mysqli $conn, string $campus): array
    {
        $rows = [];
        $campus = trim($campus);
        if ($campus === '') {
            return ['rows' => [], 'error' => 'No campus assigned to your account.'];
        }

        $join = schogms_coordinator_registrar_join_sql();
        $sql = "SELECT cm.*, rm.id_number, rm.enrolled, rm.zip_code, rm.email_address, rm.mobile_number
                FROM ched_masterlist cm {$join}
                WHERE LOWER(TRIM(cm.sheet_name)) = LOWER(TRIM(?))
                ORDER BY cm.id ASC";

        try {
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('s', $campus);
            $stmt->execute();
            $res = $stmt->get_result();
            $docIndex = schogms_coordinator_document_index($conn, $campus);
            while ($res && ($row = $res->fetch_assoc())) {
                $resolved = schogms_coordinator_resolve_doc($docIndex, $row);
                $hasCor = $resolved['has_cor'];
                $hasCog = $resolved['has_cog'];
                $key = schogms_coordinator_student_doc_key(
                    (string) ($row['lastname'] ?? ''),
                    (string) ($row['firstname'] ?? ''),
                    (string) ($row['middlename'] ?? '')
                );
                $doc = $docIndex[$key] ?? null;
                $row['uploaded_categories'] = trim(
                    ($hasCor ? 'COR' : '') . ($hasCor && $hasCog ? ', ' : '') . ($hasCog ? 'COG' : '')
                );
                $row['uploaded_files'] = $doc ? implode(', ', array_slice($doc['files'], 0, 3)) : '';
                $row['enrollment_status'] = ($hasCor && $hasCog) ? 'Enrolled' : 'Not Enrolled';
                $row['cor_path'] = $resolved['cor_path'];
                $row['cog_path'] = $resolved['cog_path'];
                $rows[] = $row;
            }
            $stmt->close();
        } catch (Throwable $e) {
            schogms_log_error('Coordinator TDP rows: ' . $e->getMessage());
            return ['rows' => [], 'error' => 'Could not load masterlist data.'];
        }

        return ['rows' => $rows, 'error' => ''];
    }
}

if (!function_exists('schogms_coordinator_ched_tes_rows')) {
    /**
     * @return array{rows: array<int, array<string, mixed>>, error: string}
     */
    function schogms_coordinator_ched_tes_rows(mysqli $conn, string $campus): array
    {
        $rows = [];
        $campus = trim($campus);
        if ($campus === '') {
            return ['rows' => [], 'error' => 'No campus assigned to your account.'];
        }

        $sql = 'SELECT * FROM ched_masterlist_tes WHERE LOWER(TRIM(campus)) = LOWER(TRIM(?)) ORDER BY id ASC';

        try {
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('s', $campus);
            $stmt->execute();
            $res = $stmt->get_result();
            while ($res && ($row = $res->fetch_assoc())) {
                $rows[] = $row;
            }
            $stmt->close();
            $docIndex = schogms_coordinator_document_index($conn, $campus);
            foreach ($rows as &$row) {
                $key = schogms_coordinator_student_doc_key(
                    (string) ($row['lastname'] ?? ''),
                    (string) ($row['firstname'] ?? ''),
                    (string) ($row['middlename'] ?? '')
                );
                $doc = $docIndex[$key] ?? null;
                if ($doc === null && $key !== '') {
                    $ln = strtolower(trim((string) ($row['lastname'] ?? '')));
                    $fn = strtolower(trim((string) ($row['firstname'] ?? '')));
                    foreach ($docIndex as $dk => $dv) {
                        if ($ln !== '' && $fn !== '' && str_contains($dk, $ln) && str_contains($dk, $fn)) {
                            $doc = $dv;
                            break;
                        }
                    }
                }
                $resolved = schogms_coordinator_resolve_doc($docIndex, $row);
                $row['cor_path'] = $resolved['cor_path'];
                $row['cog_path'] = $resolved['cog_path'];
                $row['enrollment_status'] = ($resolved['has_cor'] && $resolved['has_cog']) ? 'Enrolled' : 'Not Enrolled';
            }
            unset($row);
        } catch (Throwable $e) {
            schogms_log_error('Coordinator TES rows: ' . $e->getMessage());
            return ['rows' => [], 'error' => 'Could not load TES masterlist data.'];
        }

        return ['rows' => $rows, 'error' => ''];
    }
}

if (!function_exists('schogms_coordinator_enrollment_filter_merge')) {
    /**
     * @param array{filenames: list<string>, file_groups: list<string>} $out
     * @param array<string, true> $seenFn
     * @param array<string, true> $seenFg
     */
    function schogms_coordinator_enrollment_filter_merge(
        array &$out,
        string $filename,
        string $fileGroup,
        array &$seenFn,
        array &$seenFg
    ): void {
        $fn = trim($filename);
        $fg = trim($fileGroup);
        if ($fn !== '' && !isset($seenFn[$fn])) {
            $out['filenames'][] = $fn;
            $seenFn[$fn] = true;
        }
        if ($fg !== '' && !isset($seenFg[$fg])) {
            $out['file_groups'][] = $fg;
            $seenFg[$fg] = true;
        }
    }
}

if (!function_exists('schogms_coordinator_enrollment_batch_scholar_keys')) {
    /**
     * Scholar lookup keys for registrar masterlist + COR/COG document batches (by file group / filename).
     *
     * @return array<string, true>
     */
    function schogms_coordinator_enrollment_batch_scholar_keys(
        mysqli $conn,
        string $campus,
        string $fileGroup,
        string $filename
    ): array {
        $campus = trim($campus);
        $fileGroup = trim($fileGroup);
        $filename = trim($filename);
        if ($campus === '' || ($fileGroup === '' && $filename === '')) {
            return [];
        }

        $keys = [];

        $sql = 'SELECT last_name, first_name, middle_name FROM registrar_master_list
                WHERE LOWER(TRIM(campus)) = LOWER(TRIM(?))';
        $types = 's';
        $bind = [$campus];
        if ($fileGroup !== '') {
            $sql .= ' AND file_group = ?';
            $types .= 's';
            $bind[] = $fileGroup;
        }
        if ($filename !== '') {
            $sql .= ' AND filename = ?';
            $types .= 's';
            $bind[] = $filename;
        }
        $stmt = $conn->prepare($sql);
        if ($stmt) {
            $stmt->bind_param($types, ...$bind);
            $stmt->execute();
            $res = $stmt->get_result();
            while ($res && ($row = $res->fetch_assoc())) {
                $keys[schogms_coordinator_student_doc_key(
                    (string) ($row['last_name'] ?? ''),
                    (string) ($row['first_name'] ?? ''),
                    (string) ($row['middle_name'] ?? '')
                )] = true;
            }
            $stmt->close();
        }

        if ($fileGroup !== '') {
            $docStmt = $conn->prepare(
                "SELECT file_name, file_path FROM document_uploads
                 WHERE LOWER(TRIM(campus)) = LOWER(TRIM(?)) AND file_group = ? AND category IN ('COR', 'COG')"
            );
            if ($docStmt) {
                $docStmt->bind_param('ss', $campus, $fileGroup);
                $docStmt->execute();
                $docRes = $docStmt->get_result();
                while ($docRes && ($doc = $docRes->fetch_assoc())) {
                    foreach (schogms_coordinator_document_index_keys_for_row(
                        (string) ($doc['file_name'] ?? ''),
                        (string) ($doc['file_path'] ?? '')
                    ) as $dk) {
                        $keys[$dk] = true;
                    }
                }
                $docStmt->close();
            }
        }

        return $keys;
    }
}

if (!function_exists('schogms_coordinator_enrollment_row_matches_filters')) {
    /**
     * @param array<string, true> $batchKeys
     */
    function schogms_coordinator_enrollment_row_matches_filters(
        array $row,
        string $filename,
        string $fileGroup,
        array $batchKeys
    ): bool {
        $filename = trim($filename);
        $fileGroup = trim($fileGroup);

        if ($filename !== '' && (string) ($row['filename'] ?? '') === $filename) {
            if ($fileGroup === '' || (string) ($row['file_group'] ?? '') === $fileGroup) {
                return true;
            }
        }
        if ($fileGroup !== '' && $filename === '' && (string) ($row['file_group'] ?? '') === $fileGroup) {
            return true;
        }

        if ($batchKeys === []) {
            return false;
        }

        $lastname = (string) ($row['lastname'] ?? $row['last_name'] ?? '');
        $firstname = (string) ($row['firstname'] ?? $row['first_name'] ?? '');
        $middlename = (string) ($row['middlename'] ?? $row['middle_name'] ?? '');
        $key = schogms_coordinator_student_doc_key($lastname, $firstname, $middlename);
        if ($key !== '' && isset($batchKeys[$key])) {
            return true;
        }

        if (!function_exists('schogms_cor_cog_normalize_name_key')) {
            require_once __DIR__ . '/cor_cog_upload_helpers.php';
        }
        $ln = schogms_cor_cog_normalize_name_key($lastname);
        $fn = schogms_cor_cog_normalize_name_key($firstname);
        foreach (array_keys($batchKeys) as $dk) {
            if ($ln !== '' && $fn !== '' && str_contains($dk, $ln) && str_contains($dk, $fn)) {
                return true;
            }
        }

        return false;
    }
}

if (!function_exists('schogms_coordinator_ched_filter_options')) {
    /**
     * CHED + registrar masterlist + COR/COG document batches for enrollment status filters.
     *
     * @return array{filenames: list<string>, file_groups: list<string>}
     */
    function schogms_coordinator_ched_filter_options(mysqli $conn, string $campus, string $program): array
    {
        $out = ['filenames' => [], 'file_groups' => []];
        $campus = trim($campus);
        if ($campus === '') {
            return $out;
        }

        $seenFn = [];
        $seenFg = [];

        $program = strtolower(trim($program));
        $sql = $program === 'tes'
            ? 'SELECT DISTINCT filename, file_group FROM ched_masterlist_tes WHERE LOWER(TRIM(campus)) = LOWER(TRIM(?)) ORDER BY file_group, filename'
            : 'SELECT DISTINCT filename, file_group FROM ched_masterlist WHERE LOWER(TRIM(sheet_name)) = LOWER(TRIM(?)) ORDER BY file_group, filename';

        $stmt = $conn->prepare($sql);
        if ($stmt) {
            $stmt->bind_param('s', $campus);
            $stmt->execute();
            $res = $stmt->get_result();
            while ($res && ($opt = $res->fetch_assoc())) {
                schogms_coordinator_enrollment_filter_merge(
                    $out,
                    (string) ($opt['filename'] ?? ''),
                    (string) ($opt['file_group'] ?? ''),
                    $seenFn,
                    $seenFg
                );
            }
            $stmt->close();
        }

        $regStmt = $conn->prepare(
            'SELECT DISTINCT filename, file_group FROM registrar_master_list
             WHERE LOWER(TRIM(campus)) = LOWER(TRIM(?))
             ORDER BY file_group, filename'
        );
        if ($regStmt) {
            $regStmt->bind_param('s', $campus);
            $regStmt->execute();
            $regRes = $regStmt->get_result();
            while ($regRes && ($opt = $regRes->fetch_assoc())) {
                schogms_coordinator_enrollment_filter_merge(
                    $out,
                    (string) ($opt['filename'] ?? ''),
                    (string) ($opt['file_group'] ?? ''),
                    $seenFn,
                    $seenFg
                );
            }
            $regStmt->close();
        }

        $docStmt = $conn->prepare(
            "SELECT DISTINCT file_group FROM document_uploads
             WHERE LOWER(TRIM(campus)) = LOWER(TRIM(?)) AND category IN ('COR', 'COG')
               AND file_group IS NOT NULL AND TRIM(file_group) <> ''
             ORDER BY file_group"
        );
        if ($docStmt) {
            $docStmt->bind_param('s', $campus);
            $docStmt->execute();
            $docRes = $docStmt->get_result();
            while ($docRes && ($opt = $docRes->fetch_assoc())) {
                schogms_coordinator_enrollment_filter_merge(
                    $out,
                    '',
                    (string) ($opt['file_group'] ?? ''),
                    $seenFn,
                    $seenFg
                );
            }
            $docStmt->close();
        }

        sort($out['filenames'], SORT_NATURAL | SORT_FLAG_CASE);
        sort($out['file_groups'], SORT_NATURAL | SORT_FLAG_CASE);

        return $out;
    }
}

if (!function_exists('schogms_coordinator_ched_apply_row_filters')) {
    /**
     * @param list<array<string, mixed>> $rows
     * @return list<array<string, mixed>>
     */
    function schogms_coordinator_ched_apply_row_filters(
        mysqli $conn,
        string $campus,
        array $rows,
        string $filename,
        string $fileGroup
    ): array {
        $filename = trim($filename);
        $fileGroup = trim($fileGroup);
        if ($filename === '' && $fileGroup === '') {
            return $rows;
        }

        $batchKeys = schogms_coordinator_enrollment_batch_scholar_keys($conn, $campus, $fileGroup, $filename);

        return array_values(array_filter(
            $rows,
            static fn(array $r): bool => schogms_coordinator_enrollment_row_matches_filters(
                $r,
                $filename,
                $fileGroup,
                $batchKeys
            )
        ));
    }
}
