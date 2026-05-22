<?php
/**
 * Coordinator dashboard counts and chart series (campus-scoped).
 */

if (!function_exists('schogms_coordinator_dashboard_campus')) {
    function schogms_coordinator_dashboard_campus(): string
    {
        return trim((string) ($GLOBALS['sheet_name'] ?? ''));
    }
}

if (!function_exists('schogms_coordinator_dashboard_stats')) {
    /**
     * @return array{
     *   campus: string,
     *   tdp_records: int,
     *   tes_records: int,
     *   tdp_courses: int,
     *   tdp_file_groups: int,
     *   file_groups: list<string>
     * }
     */
    function schogms_coordinator_dashboard_stats(mysqli $conn, string $campus, string $fileGroupFilter = ''): array
    {
        $campus = trim($campus);
        $out = [
            'campus' => $campus,
            'tdp_records' => 0,
            'tes_records' => 0,
            'tdp_courses' => 0,
            'tdp_file_groups' => 0,
            'file_groups' => [],
        ];

        if ($campus === '') {
            return $out;
        }

        $fgFilter = trim($fileGroupFilter);
        $tdpWhere = 'UPPER(TRIM(sheet_name)) = UPPER(TRIM(?))';
        $tdpTypes = 's';
        $tdpParams = [$campus];

        if ($fgFilter !== '') {
            $tdpWhere .= ' AND file_group = ?';
            $tdpTypes .= 's';
            $tdpParams[] = $fgFilter;
        }

        $queries = [
            'tdp_records' => "SELECT COUNT(*) AS c FROM ched_masterlist WHERE {$tdpWhere}",
            'tdp_courses' => "SELECT COUNT(DISTINCT course_program_enrolled) AS c FROM ched_masterlist WHERE {$tdpWhere}",
            'tdp_file_groups' => "SELECT COUNT(DISTINCT file_group) AS c FROM ched_masterlist WHERE {$tdpWhere}",
            'tes_records' => 'SELECT COUNT(*) AS c FROM ched_masterlist_tes WHERE UPPER(TRIM(campus)) = UPPER(TRIM(?))',
        ];

        foreach ($queries as $key => $sql) {
            $stmt = $conn->prepare($sql);
            if (!$stmt) {
                continue;
            }
            if ($key === 'tes_records') {
                $stmt->bind_param('s', $campus);
            } else {
                $stmt->bind_param($tdpTypes, ...$tdpParams);
            }
            $stmt->execute();
            $row = $stmt->get_result()->fetch_assoc();
            $stmt->close();
            $out[$key] = (int) ($row['c'] ?? 0);
        }

        $fgStmt = $conn->prepare(
            "SELECT DISTINCT file_group FROM ched_masterlist
             WHERE UPPER(TRIM(sheet_name)) = UPPER(TRIM(?)) AND file_group IS NOT NULL AND file_group != ''
             ORDER BY file_group DESC"
        );
        if ($fgStmt) {
            $fgStmt->bind_param('s', $campus);
            $fgStmt->execute();
            $res = $fgStmt->get_result();
            while ($res && ($r = $res->fetch_assoc())) {
                $out['file_groups'][] = (string) ($r['file_group'] ?? '');
            }
            $fgStmt->close();
        }

        return $out;
    }
}

if (!function_exists('schogms_coordinator_dashboard_chart_data')) {
    /**
     * @return array{
     *   tdp_courses: list<array{course_program_enrolled: string, count: int}>,
     *   tes_courses: list<array{course_program_enrolled: string, count: int}>,
     *   tdp_file_groups: list<array{file_group: string, count: int}>
     * }
     */
    function schogms_coordinator_dashboard_chart_data(mysqli $conn, string $campus, string $fileGroupFilter = ''): array
    {
        $campus = trim($campus);
        $empty = [
            'tdp_courses' => [],
            'tes_courses' => [],
            'tdp_file_groups' => [],
        ];
        if ($campus === '') {
            return $empty;
        }

        $fgFilter = trim($fileGroupFilter);
        $tdpExtra = $fgFilter !== '' ? ' AND file_group = ?' : '';
        $tdpTypes = 's' . ($fgFilter !== '' ? 's' : '');
        $tdpParams = $fgFilter !== '' ? [$campus, $fgFilter] : [$campus];

        $load = static function (mysqli $conn, string $sql, string $types, array $params): array {
            $rows = [];
            $stmt = $conn->prepare($sql);
            if (!$stmt) {
                return $rows;
            }
            $stmt->bind_param($types, ...$params);
            $stmt->execute();
            $res = $stmt->get_result();
            while ($res && ($r = $res->fetch_assoc())) {
                $rows[] = $r;
            }
            $stmt->close();

            return $rows;
        };

        $tdpCourses = $load(
            $conn,
            "SELECT course_program_enrolled, COUNT(*) AS count FROM ched_masterlist
             WHERE UPPER(TRIM(sheet_name)) = UPPER(TRIM(?)){$tdpExtra}
             GROUP BY course_program_enrolled ORDER BY count DESC LIMIT 12",
            $tdpTypes,
            $tdpParams
        );

        $tesCourses = $load(
            $conn,
            'SELECT course_program_enrolled, COUNT(*) AS count FROM ched_masterlist_tes
             WHERE UPPER(TRIM(campus)) = UPPER(TRIM(?))
             GROUP BY course_program_enrolled ORDER BY count DESC LIMIT 12',
            's',
            [$campus]
        );

        $tdpGroups = $load(
            $conn,
            "SELECT file_group, COUNT(*) AS count FROM ched_masterlist
             WHERE UPPER(TRIM(sheet_name)) = UPPER(TRIM(?)){$tdpExtra}
             GROUP BY file_group ORDER BY count DESC LIMIT 12",
            $tdpTypes,
            $tdpParams
        );

        return [
            'tdp_courses' => $tdpCourses,
            'tes_courses' => $tesCourses,
            'tdp_file_groups' => $tdpGroups,
        ];
    }
}
