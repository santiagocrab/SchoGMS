<?php
/**
 * CHED TDP masterlist Excel import helpers (column detection + status display).
 */

if (!function_exists('schogms_ched_import_status_value')) {
    /** Value from CHED file stored in DB (column M / STATUS OF ENROLLMENT). */
    function schogms_ched_import_status_value(array $row): string
    {
        return trim((string) ($row['status_of_enrollment'] ?? ''));
    }
}

if (!function_exists('schogms_ched_import_status_badge_class')) {
    function schogms_ched_import_status_badge_class(string $status): string
    {
        $l = strtolower(trim($status));
        if ($l === '') {
            return 'secondary';
        }
        if (str_contains($l, 'not') && str_contains($l, 'enroll')) {
            return 'warning';
        }
        if (str_contains($l, 'enroll')) {
            return 'success';
        }
        if (str_contains($l, 'drop') || str_contains($l, 'leave') || str_contains($l, 'cancel')) {
            return 'danger';
        }

        return 'info';
    }
}

if (!function_exists('schogms_ched_excel_find_header_column')) {
    /**
     * @param list<array<string|int, mixed>> $headerRows first N rows of sheet
     * @param list<string> $needles
     */
    function schogms_ched_excel_find_header_column(array $headerRows, array $needles): ?string
    {
        foreach ($headerRows as $row) {
            if (!is_array($row)) {
                continue;
            }
            foreach ($row as $col => $val) {
                $v = strtolower(trim((string) $val));
                if ($v === '') {
                    continue;
                }
                foreach ($needles as $needle) {
                    $n = strtolower($needle);
                    if ($v === $n || str_contains($v, $n)) {
                        return is_string($col) ? $col : (string) $col;
                    }
                }
            }
        }

        return null;
    }
}

if (!function_exists('schogms_ched_tdp_import_layout')) {
    /**
     * Detect status column letter and 0-based data start index in toArray() output.
     *
     * @param list<array<string|int, mixed>> $allRows
     * @return array{data_start: int, status_col: string, units_col: string, remarks_col: string}
     */
    function schogms_ched_tdp_import_layout(array $allRows): array
    {
        $headerScan = array_slice($allRows, 0, 6);
        $statusCol = schogms_ched_excel_find_header_column($headerScan, [
            'status of enrollment',
            'enrollment status',
            'status of enrolment',
        ]) ?? 'M';
        $unitsCol = schogms_ched_excel_find_header_column($headerScan, [
            'total units enrolled',
            'units enrolled',
            'total unit',
        ]) ?? 'L';
        $remarksCol = schogms_ched_excel_find_header_column($headerScan, ['remarks', 'remark']) ?? 'N';

        $dataStart = 3;
        foreach ($headerScan as $idx => $row) {
            if (!is_array($row)) {
                continue;
            }
            $a = strtolower(trim((string) ($row['A'] ?? '')));
            if ($a === 'seq' || $a === 'sequence' || str_contains($a, 'seq')) {
                $dataStart = $idx + 1;
                break;
            }
        }

        return [
            'data_start' => $dataStart,
            'status_col' => $statusCol,
            'units_col' => $unitsCol,
            'remarks_col' => $remarksCol,
        ];
    }
}

if (!function_exists('schogms_ched_tdp_row_cell')) {
    function schogms_ched_tdp_row_cell(array $row, string $col): string
    {
        if (isset($row[$col])) {
            return trim((string) $row[$col]);
        }
        $upper = strtoupper($col);
        if (isset($row[$upper])) {
            return trim((string) $row[$upper]);
        }

        return '';
    }
}
