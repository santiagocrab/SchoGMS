<?php
/**
 * Filtered rows for validation export scripts.
 */

require_once __DIR__ . '/validation_filters.php';

if (!function_exists('schogms_validation_export_rows')) {
    /**
     * @return list<array<string, mixed>>
     */
    function schogms_validation_export_rows(mysqli $conn, string $program, string $campus, array $get): array
    {
        $program = strtolower(trim($program));
        if (!in_array($program, ['tdp', 'tes'], true)) {
            $program = 'tdp';
        }
        $campus = trim($campus);
        if ($campus === '') {
            return [];
        }

        return schogms_validation_fetch_rows($conn, $program, $campus, $get, true);
    }
}
