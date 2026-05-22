<?php
/**
 * Bulk TDP validation (all scholars for a campus at once).
 */

require_once __DIR__ . '/validation_compare.php';
require_once __DIR__ . '/masterlist_rows.php';
require_once __DIR__ . '/validation_filters.php';

if (!function_exists('schogms_tdp_row_validation')) {
    /**
     * @param array<string, mixed> $row
     * @param array<string, array<string, mixed>> $docIndex
     * @return array{
     *   course_match: bool,
     *   year_level_match: bool,
     *   has_cor: bool,
     *   has_cog: bool,
     *   passed: bool,
     *   cor_path: string,
     *   cog_path: string
     * }
     */
    function schogms_tdp_row_validation(array $row, array $docIndex): array
    {
        $docs = schogms_coordinator_resolve_doc($docIndex, $row);
        $courseMatch = schogms_courses_match(
            (string) ($row['course_program_enrolled'] ?? ''),
            (string) ($row['reg_course'] ?? '')
        );
        $yearLevelMatch = schogms_year_levels_match(
            (string) ($row['year_level'] ?? ''),
            (string) ($row['reg_year_level'] ?? '')
        );

        return [
            'course_match' => $courseMatch,
            'year_level_match' => $yearLevelMatch,
            'has_cor' => $docs['has_cor'],
            'has_cog' => $docs['has_cog'],
            'passed' => $courseMatch && $yearLevelMatch,
            'cor_path' => $docs['cor_path'],
            'cog_path' => $docs['cog_path'],
        ];
    }
}

if (!function_exists('schogms_tdp_fetch_validation_rows')) {
    /** @return array<int, array<string, mixed>> */
    function schogms_tdp_fetch_validation_rows(mysqli $conn, string $sheetName, array $get): array
    {
        return schogms_validation_fetch_rows($conn, 'tdp', $sheetName, $get, true);
    }
}

if (!function_exists('schogms_tdp_bulk_validate_campus')) {
    /**
     * Validate every TDP row for the campus and update validation_status.
     *
     * @return array{total: int, passed: int, failed: int, no_registrar: int, updated: int}
     */
    function schogms_tdp_bulk_validate_campus(mysqli $conn, string $sheetName, array $get = []): array
    {
        $stats = [
            'total' => 0,
            'passed' => 0,
            'failed' => 0,
            'no_registrar' => 0,
            'updated' => 0,
        ];

        $rows = schogms_validation_fetch_rows($conn, 'tdp', $sheetName, $get, false);
        $docIndex = schogms_coordinator_document_index($conn, $sheetName);

        $updPass = $conn->prepare(
            "UPDATE ched_masterlist SET validation_status = 'Validated' WHERE id = ? AND sheet_name = ?"
        );
        $updFail = $conn->prepare(
            "UPDATE ched_masterlist SET validation_status = 'Failed' WHERE id = ? AND sheet_name = ?"
        );
        if (!$updPass || !$updFail) {
            return $stats;
        }

        foreach ($rows as $row) {
            $stats['total']++;
            $regCourse = trim((string) ($row['reg_course'] ?? ''));
            if ($regCourse === '') {
                $stats['no_registrar']++;
            }

            $check = schogms_validation_row_check($row, $docIndex, 'tdp');
            if ($check['passed']) {
                $stats['passed']++;
                $updPass->bind_param('is', $row['id'], $sheetName);
                if ($updPass->execute()) {
                    $stats['updated']++;
                }
            } else {
                $stats['failed']++;
                $id = (int) $row['id'];
                $updFail->bind_param('is', $id, $sheetName);
                $updFail->execute();
            }
        }

        $updPass->close();
        $updFail->close();

        return $stats;
    }
}
