<?php
/**
 * Shared multi-select filters for TDP / TES validation and exports.
 */

require_once __DIR__ . '/validation_compare.php';
require_once __DIR__ . '/masterlist_rows.php';

if (!function_exists('schogms_filter_get_array')) {
    /** @return list<string> */
    function schogms_filter_get_array(array $get, string $key): array
    {
        $values = [];
        if (isset($get[$key]) && is_array($get[$key])) {
            $values = $get[$key];
        } elseif (isset($get[$key . '[]']) && is_array($get[$key . '[]'])) {
            $values = $get[$key . '[]'];
        } elseif (isset($get[$key]) && is_string($get[$key]) && trim($get[$key]) !== '') {
            $values = preg_split('/\s*,\s*/', trim($get[$key]));
        }
        $out = [];
        foreach ($values as $v) {
            $v = trim((string) $v);
            if ($v !== '') {
                $out[] = $v;
            }
        }

        return array_values(array_unique($out));
    }
}

if (!function_exists('schogms_validation_program_config')) {
    /** @return array{table: string, campus_col: string, has_award: bool, has_validation_col: bool} */
    function schogms_validation_program_config(string $program): array
    {
        $program = strtolower(trim($program));
        if ($program === 'tes') {
            return [
                'table' => 'ched_masterlist_tes',
                'campus_col' => 'campus',
                'has_award' => false,
                'has_validation_col' => false,
            ];
        }

        return [
            'table' => 'ched_masterlist',
            'campus_col' => 'sheet_name',
            'has_award' => true,
            'has_validation_col' => true,
        ];
    }
}

if (!function_exists('schogms_validation_append_in')) {
    /**
     * @param list<string> $values
     */
    function schogms_validation_append_in(
        string $column,
        array $values,
        array &$parts,
        string &$types,
        array &$params
    ): void {
        if (count($values) === 0) {
            return;
        }
        $placeholders = implode(',', array_fill(0, count($values), '?'));
        $parts[] = "{$column} IN ({$placeholders})";
        foreach ($values as $v) {
            $types .= 's';
            $params[] = $v;
        }
    }
}

if (!function_exists('schogms_validation_build_sql_where')) {
    /**
     * @return array{sql: string, types: string, params: list<string|int>}
     */
    function schogms_validation_build_sql_where(string $program, string $campus, array $get): array
    {
        $cfg = schogms_validation_program_config($program);
        $table = $cfg['table'];
        $campusCol = $cfg['campus_col'];

        $parts = ["cm.{$campusCol} = ?"];
        $types = 's';
        $params = [$campus];

        if ($program === 'tdp') {
            $batches = schogms_filter_get_array($get, 'batch');
            if (count($batches) === 1) {
                $parts[] = 'cm.file_group = ?';
                $types .= 's';
                $params[] = $batches[0];
            } elseif (count($batches) > 1) {
                schogms_validation_append_in('cm.file_group', $batches, $parts, $types, $params);
            }

            $batchNos = schogms_filter_get_array($get, 'batch_no');
            schogms_validation_append_in('cm.batch_no', $batchNos, $parts, $types, $params);
        } else {
            $batchNos = schogms_filter_get_array($get, 'batch_no');
            schogms_validation_append_in('cm.batch_no', $batchNos, $parts, $types, $params);
            $fileGroups = schogms_filter_get_array($get, 'batch');
            schogms_validation_append_in('cm.file_group', $fileGroups, $parts, $types, $params);
        }

        schogms_validation_append_in('cm.sex', schogms_filter_get_array($get, 'sex'), $parts, $types, $params);
        schogms_validation_append_in('cm.course_program_enrolled', schogms_filter_get_array($get, 'course'), $parts, $types, $params);

        $yearLevels = schogms_filter_get_array($get, 'year_level');
        if (count($yearLevels) > 0) {
            $ylParts = [];
            foreach ($yearLevels as $yl) {
                $ylParts[] = 'CAST(cm.year_level AS CHAR) = ?';
                $types .= 's';
                $params[] = $yl;
            }
            $parts[] = '(' . implode(' OR ', $ylParts) . ')';
        }

        if ($cfg['has_validation_col']) {
            $validations = schogms_filter_get_array($get, 'validation');
            $sqlVals = [];
            foreach ($validations as $v) {
                $lv = strtolower($v);
                if ($lv === 'validated' || $lv === 'passed') {
                    $sqlVals[] = 'Validated';
                } elseif ($lv === 'failed') {
                    $sqlVals[] = 'Failed';
                } elseif ($lv === 'pending') {
                    $sqlVals[] = '';
                }
            }
            if (count($sqlVals) > 0) {
                $hasPending = in_array('', $sqlVals, true);
                $named = array_filter($sqlVals, static fn ($x) => $x !== '');
                $or = [];
                if (count($named) > 0) {
                    $ph = implode(',', array_fill(0, count($named), '?'));
                    $or[] = "cm.validation_status IN ({$ph})";
                    foreach ($named as $n) {
                        $types .= 's';
                        $params[] = $n;
                    }
                }
                if ($hasPending) {
                    $or[] = "(cm.validation_status IS NULL OR cm.validation_status = '')";
                }
                if (count($or) > 0) {
                    $parts[] = '(' . implode(' OR ', $or) . ')';
                }
            }
        }

        $search = trim((string) ($get['search'] ?? ''));
        if ($search !== '') {
            $like = '%' . $search . '%';
            $parts[] = '(cm.lastname LIKE ? OR cm.firstname LIKE ? OR cm.app_no LIKE ? OR cm.course_program_enrolled LIKE ?)';
            $types .= 'ssss';
            $params[] = $like;
            $params[] = $like;
            $params[] = $like;
            $params[] = $like;
        }

        return ['sql' => implode(' AND ', $parts), 'types' => $types, 'params' => $params];
    }
}

if (!function_exists('schogms_validation_row_check')) {
    /**
     * @param array<string, mixed> $row
     * @param array<string, array<string, mixed>> $docIndex
     * @return array{passed: bool, has_cor: bool, has_cog: bool, cor_path: string, cog_path: string, enrollment: string, validation_label: string}
     */
    function schogms_validation_row_check(array $row, array $docIndex, string $program): array
    {
        $docs = schogms_coordinator_resolve_doc($docIndex, $row);
        $courseMatch = schogms_courses_match(
            (string) ($row['course_program_enrolled'] ?? ''),
            (string) ($row['reg_course'] ?? '')
        );
        $yearMatch = schogms_year_levels_match(
            (string) ($row['year_level'] ?? ''),
            (string) ($row['reg_year_level'] ?? '')
        );
        $passed = $courseMatch && $yearMatch;
        $hasCor = $docs['has_cor'];
        $enrollment = $hasCor ? 'Enrolled' : 'Not Enrolled';

        $dbStatus = trim((string) ($row['validation_status'] ?? ''));
        if ($program === 'tes' || $dbStatus === '') {
            $validationLabel = $passed ? 'Validated' : ($dbStatus !== '' ? $dbStatus : 'Failed');
            if ($program === 'tes' && !$passed && $dbStatus === '') {
                $regEmpty = trim((string) ($row['reg_course'] ?? '')) === '';
                $validationLabel = $regEmpty ? 'Pending' : 'Failed';
            }
        } else {
            $validationLabel = $dbStatus;
        }

        return [
            'passed' => $passed,
            'has_cor' => $hasCor,
            'has_cog' => $docs['has_cog'],
            'cor_path' => $docs['cor_path'],
            'cog_path' => $docs['cog_path'],
            'enrollment' => $enrollment,
            'validation_label' => $validationLabel,
            'course_match' => $courseMatch,
            'year_level_match' => $yearMatch,
        ];
    }
}

if (!function_exists('schogms_validation_apply_post_filters')) {
    /**
     * @param list<array<string, mixed>> $rows
     * @return list<array<string, mixed>>
     */
    function schogms_validation_apply_post_filters(array $rows, array $docIndex, array $get, string $program): array
    {
        $statuses = array_map('strtolower', schogms_filter_get_array($get, 'status'));
        $validations = array_map('strtolower', schogms_filter_get_array($get, 'validation'));
        $cfg = schogms_validation_program_config($program);
        $needPost = count($statuses) > 0 || (count($validations) > 0 && !$cfg['has_validation_col']);

        if (!$needPost) {
            foreach ($rows as &$row) {
                $row['_check'] = schogms_validation_row_check($row, $docIndex, $program);
            }
            unset($row);

            return $rows;
        }

        $out = [];
        foreach ($rows as $row) {
            $check = schogms_validation_row_check($row, $docIndex, $program);
            if (count($statuses) > 0) {
                $en = strtolower($check['enrollment']);
                $ok = false;
                foreach ($statuses as $s) {
                    if ($s === 'enrolled' && $en === 'enrolled') {
                        $ok = true;
                    }
                    if (in_array($s, ['not enrolled', 'not_enrolled', 'notenrolled'], true) && $en === 'not enrolled') {
                        $ok = true;
                    }
                }
                if (!$ok) {
                    continue;
                }
            }

            if (count($validations) > 0 && !schogms_validation_program_config($program)['has_validation_col']) {
                $label = strtolower($check['validation_label']);
                $ok = false;
                foreach ($validations as $v) {
                    if ($v === 'validated' || $v === 'passed') {
                        if ($check['passed']) {
                            $ok = true;
                        }
                    } elseif ($v === 'failed' && !$check['passed'] && $label !== 'pending') {
                        $ok = true;
                    } elseif ($v === 'pending' && $label === 'pending') {
                        $ok = true;
                    }
                }
                if (!$ok) {
                    continue;
                }
            }

            $row['_check'] = $check;
            $out[] = $row;
        }

        return $out;
    }
}

if (!function_exists('schogms_validation_fetch_rows')) {
    /**
     * @return list<array<string, mixed>>
     */
    function schogms_validation_fetch_rows(mysqli $conn, string $program, string $campus, array $get, bool $applyPost = true): array
    {
        $cfg = schogms_validation_program_config($program);
        $where = schogms_validation_build_sql_where($program, $campus, $get);

        $sql = "SELECT cm.*,
                       rm.id_number, rm.enrolled, rm.zip_code, rm.email_address, rm.mobile_number,
                       rm.course AS reg_course, rm.year_level AS reg_year_level
                FROM {$cfg['table']} cm
                LEFT JOIN registrar_master_list rm
                  ON cm.lastname COLLATE utf8mb4_general_ci = rm.last_name COLLATE utf8mb4_general_ci
                 AND cm.firstname COLLATE utf8mb4_general_ci = rm.first_name COLLATE utf8mb4_general_ci
                 AND (cm.middlename COLLATE utf8mb4_general_ci = rm.middle_name COLLATE utf8mb4_general_ci
                      OR cm.middlename IS NULL OR rm.middle_name IS NULL
                      OR cm.middlename = '' OR rm.middle_name = '')
                WHERE {$where['sql']}
                ORDER BY cm.id ASC";

        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            return [];
        }
        $stmt->bind_param($where['types'], ...$where['params']);
        $stmt->execute();
        $res = $stmt->get_result();
        $rows = [];
        while ($res && ($row = $res->fetch_assoc())) {
            $rows[] = $row;
        }
        $stmt->close();

        if (!$applyPost) {
            return $rows;
        }

        $docIndex = schogms_coordinator_document_index($conn, $campus);

        return schogms_validation_apply_post_filters($rows, $docIndex, $get, $program);
    }
}

if (!function_exists('schogms_validation_filter_options')) {
    /** @return array<string, list<string>> */
    function schogms_validation_filter_options(mysqli $conn, string $program, string $campus): array
    {
        $cfg = schogms_validation_program_config($program);
        $col = $cfg['campus_col'];
        $table = $cfg['table'];
        $opts = [
            'sex' => [],
            'course' => [],
            'year_level' => [],
            'batch' => [],
            'batch_no' => [],
        ];

        $stmts = [
            'sex' => "SELECT DISTINCT sex AS v FROM {$table} WHERE {$col} = ? AND sex IS NOT NULL AND sex != '' ORDER BY sex",
            'course' => "SELECT DISTINCT course_program_enrolled AS v FROM {$table} WHERE {$col} = ? AND course_program_enrolled != '' ORDER BY course_program_enrolled",
            'year_level' => "SELECT DISTINCT CAST(year_level AS CHAR) AS v FROM {$table} WHERE {$col} = ? ORDER BY v",
            'batch_no' => "SELECT DISTINCT batch_no AS v FROM {$table} WHERE {$col} = ? AND batch_no IS NOT NULL AND batch_no != '' ORDER BY batch_no DESC",
        ];
        if ($program === 'tdp') {
            $stmts['batch'] = "SELECT DISTINCT file_group AS v FROM {$table} WHERE {$col} = ? AND file_group IS NOT NULL AND file_group != '' ORDER BY file_group DESC";
        } else {
            $stmts['batch'] = "SELECT DISTINCT file_group AS v FROM {$table} WHERE {$col} = ? AND file_group != '' ORDER BY file_group DESC";
        }

        foreach ($stmts as $key => $sql) {
            $st = $conn->prepare($sql);
            if (!$st) {
                continue;
            }
            $st->bind_param('s', $campus);
            $st->execute();
            $r = $st->get_result();
            while ($r && ($row = $r->fetch_assoc())) {
                $opts[$key][] = (string) $row['v'];
            }
            $st->close();
        }

        return $opts;
    }
}

if (!function_exists('schogms_validation_export_query')) {
    /** Build query string for export links (preserves multi-select filters). */
    function schogms_validation_export_query(string $program, string $campus, array $get): string
    {
        $parts = [
            'sheet_name=' . rawurlencode($campus),
            'program=' . rawurlencode($program),
        ];
        if (isset($get['bulk'])) {
            $parts[] = 'bulk=' . rawurlencode((string) $get['bulk']);
        }
        foreach (['batch', 'batch_no', 'sex', 'course', 'year_level', 'status', 'validation'] as $key) {
            foreach (schogms_filter_get_array($get, $key) as $v) {
                $parts[] = rawurlencode($key) . '%5B%5D=' . rawurlencode($v);
            }
        }
        $search = trim((string) ($get['search'] ?? ''));
        if ($search !== '') {
            $parts[] = 'search=' . rawurlencode($search);
        }

        return implode('&', $parts);
    }
}
