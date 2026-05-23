<?php
/**
 * Shared billing Excel import (Verified Scholars / chairman billing upload).
 */
declare(strict_types=1);

if (!function_exists('schogms_billing_import_sql')) {
    function schogms_billing_import_sql(): string
    {
        return '
            INSERT INTO billing_table (
                last_name, first_name, scholarship_type, units_enrolled, course, campus,
                year_and_date_submitted_ched, amount, first_semester, second_semester, status,
                payment_scholarship_type, payment_amount, payment_year_and_date, payment_or_number,
                payment_amount_per_or, refund_first_sem, refund_second_sem, refund_year_and_date_released
            ) VALUES (
                ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?
            )';
    }

    /** @return array{0:string,1:list<mixed>} */
    function schogms_billing_row_bind_values(array $row): array
    {
        $last_name = (string) ($row['A'] ?? 'N/A');
        $first_name = (string) ($row['B'] ?? 'N/A');
        $scholarship_type = (string) ($row['C'] ?? 'N/A');
        $units_enrolled = isset($row['D']) ? (int) $row['D'] : 0;
        $course = (string) ($row['E'] ?? 'N/A');
        $campus = (string) ($row['F'] ?? 'N/A');
        $year_and_date_submitted_ched = !empty($row['G']) ? date('Y-m-d', strtotime((string) $row['G'])) : null;
        $amount = isset($row['H']) ? (float) str_replace(',', '', (string) $row['H']) : 0.0;
        $first_semester = (string) ($row['I'] ?? 'N/A');
        $second_semester = (string) ($row['J'] ?? 'N/A');
        $status = (string) ($row['K'] ?? 'N/A');
        $payment_scholarship_type = (string) ($row['L'] ?? 'N/A');
        $payment_amount = isset($row['M']) ? (float) str_replace(',', '', (string) $row['M']) : 0.0;
        $payment_year_and_date = !empty($row['N']) ? date('Y-m-d', strtotime((string) $row['N'])) : null;
        $payment_or_number = (string) ($row['O'] ?? 'N/A');
        $payment_amount_per_or = isset($row['P']) ? (float) str_replace(',', '', (string) $row['P']) : 0.0;
        $refund_first_sem = isset($row['Q']) ? (float) str_replace(',', '', (string) $row['Q']) : 0.0;
        $refund_second_sem = isset($row['R']) ? (float) str_replace(',', '', (string) $row['R']) : 0.0;
        $refund_year_and_date_released = !empty($row['S']) ? date('Y-m-d', strtotime((string) $row['S'])) : null;

        return [
            'sssisssdssssdssddds',
            [
                $last_name,
                $first_name,
                $scholarship_type,
                $units_enrolled,
                $course,
                $campus,
                $year_and_date_submitted_ched,
                $amount,
                $first_semester,
                $second_semester,
                $status,
                $payment_scholarship_type,
                $payment_amount,
                $payment_year_and_date,
                $payment_or_number,
                $payment_amount_per_or,
                $refund_first_sem,
                $refund_second_sem,
                $refund_year_and_date_released,
            ],
        ];
    }

    /**
     * Detect Annex 7 submit-form files (6-column layout) uploaded to billing import by mistake.
     */
    function schogms_billing_looks_like_annex7(array $rows): bool
    {
        foreach (array_slice($rows, 0, 4, true) as $header) {
            $g = trim((string) ($header['G'] ?? ''));
            $h = trim((string) ($header['H'] ?? ''));
            $a = strtolower(trim((string) ($header['A'] ?? '')));
            if ($g !== '' || $h !== '') {
                continue;
            }
            if ($a === '' || !str_contains($a, 'last')) {
                continue;
            }
            $f = strtolower(trim((string) ($header['F'] ?? '')));
            if ($f !== '' && str_contains($f, 'campus')) {
                return true;
            }
        }

        return false;
    }
}
