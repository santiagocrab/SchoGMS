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

if (!function_exists('schogms_coordinator_document_index')) {
    /**
     * @return array<string, array{COR:bool, COG:bool, files: string[]}>
     */
    function schogms_coordinator_document_index(mysqli $conn, string $campus): array
    {
        $index = [];
        $campus = trim($campus);
        if ($campus === '') {
            return $index;
        }
        $stmt = $conn->prepare(
            'SELECT file_name, category, file_path FROM document_uploads WHERE campus = ? AND category IN (\'COR\', \'COG\')'
        );
        if (!$stmt) {
            return $index;
        }
        $stmt->bind_param('s', $campus);
        $stmt->execute();
        $res = $stmt->get_result();
        while ($res && ($row = $res->fetch_assoc())) {
            $fn = strtolower(trim((string) ($row['file_name'] ?? '')));
            $fn = preg_replace('/\.(pdf|jpe?g|png)$/i', '', $fn);
            if ($fn === '') {
                continue;
            }
            if (!isset($index[$fn])) {
                $index[$fn] = ['COR' => false, 'COG' => false, 'files' => [], 'cor_path' => '', 'cog_path' => ''];
            }
            $cat = (string) ($row['category'] ?? '');
            $fpath = (string) ($row['file_path'] ?? '');
            if ($cat === 'COR') {
                $index[$fn]['COR'] = true;
                if ($index[$fn]['cor_path'] === '' && $fpath !== '') {
                    $index[$fn]['cor_path'] = $fpath;
                }
            }
            if ($cat === 'COG') {
                $index[$fn]['COG'] = true;
                if ($index[$fn]['cog_path'] === '' && $fpath !== '') {
                    $index[$fn]['cog_path'] = $fpath;
                }
            }
            $index[$fn]['files'][] = (string) ($row['file_name'] ?? '');
        }
        $stmt->close();
        return $index;
    }
}

if (!function_exists('schogms_coordinator_student_doc_key')) {
    function schogms_coordinator_student_doc_key(string $lastname, string $firstname, string $middlename): string
    {
        return strtolower(trim($lastname . ', ' . $firstname . ' ' . $middlename));
    }
}

if (!function_exists('schogms_coordinator_resolve_doc')) {
    /**
     * @param array<string, array{COR:bool, COG:bool, cor_path:string, cog_path:string}> $docIndex
     * @return array{cor_path: string, cog_path: string, has_cor: bool, has_cog: bool}
     */
    function schogms_coordinator_resolve_doc(array $docIndex, array $row): array
    {
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
                WHERE cm.sheet_name = ?
                ORDER BY cm.id ASC";

        try {
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('s', $campus);
            $stmt->execute();
            $res = $stmt->get_result();
            $docIndex = schogms_coordinator_document_index($conn, $campus);
            while ($res && ($row = $res->fetch_assoc())) {
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
                $hasCor = $doc['COR'] ?? false;
                $hasCog = $doc['COG'] ?? false;
                $row['uploaded_categories'] = trim(
                    ($hasCor ? 'COR' : '') . ($hasCor && $hasCog ? ', ' : '') . ($hasCog ? 'COG' : '')
                );
                $row['uploaded_files'] = $doc ? implode(', ', array_slice($doc['files'], 0, 3)) : '';
                $row['enrollment_status'] = ($hasCor && $hasCog) ? 'Enrolled' : 'Not Enrolled';
                $row['cor_path'] = $doc['cor_path'] ?? '';
                $row['cog_path'] = $doc['cog_path'] ?? '';
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

        $sql = 'SELECT * FROM ched_masterlist_tes WHERE campus = ? ORDER BY id ASC';

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

if (!function_exists('schogms_coordinator_ched_filter_options')) {
    /**
     * @return array{filenames: list<string>, file_groups: list<string>}
     */
    function schogms_coordinator_ched_filter_options(mysqli $conn, string $campus, string $program): array
    {
        $out = ['filenames' => [], 'file_groups' => []];
        $campus = trim($campus);
        if ($campus === '') {
            return $out;
        }

        $program = strtolower(trim($program));
        $sql = $program === 'tes'
            ? 'SELECT DISTINCT filename, file_group FROM ched_masterlist_tes WHERE campus = ? ORDER BY file_group, filename'
            : 'SELECT DISTINCT filename, file_group FROM ched_masterlist WHERE sheet_name = ? ORDER BY file_group, filename';

        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            return $out;
        }
        $stmt->bind_param('s', $campus);
        $stmt->execute();
        $res = $stmt->get_result();
        $seenFn = [];
        $seenFg = [];
        while ($res && ($opt = $res->fetch_assoc())) {
            $fn = trim((string) ($opt['filename'] ?? ''));
            $fg = trim((string) ($opt['file_group'] ?? ''));
            if ($fn !== '' && !isset($seenFn[$fn])) {
                $out['filenames'][] = $fn;
                $seenFn[$fn] = true;
            }
            if ($fg !== '' && !isset($seenFg[$fg])) {
                $out['file_groups'][] = $fg;
                $seenFg[$fg] = true;
            }
        }
        $stmt->close();

        return $out;
    }
}

if (!function_exists('schogms_coordinator_ched_apply_row_filters')) {
    /** @param list<array<string, mixed>> $rows */
    function schogms_coordinator_ched_apply_row_filters(array $rows, string $filename, string $fileGroup): array
    {
        if ($filename !== '') {
            $rows = array_values(array_filter(
                $rows,
                static fn(array $r): bool => (string) ($r['filename'] ?? '') === $filename
            ));
        }
        if ($fileGroup !== '') {
            $rows = array_values(array_filter(
                $rows,
                static fn(array $r): bool => (string) ($r['file_group'] ?? '') === $fileGroup
            ));
        }

        return $rows;
    }
}
