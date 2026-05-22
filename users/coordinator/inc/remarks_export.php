<?php
/**
 * Remarks validation export — CSV columns match TDP/TES verification templates.
 */

require_once __DIR__ . '/validation_export.php';
require_once __DIR__ . '/validation_edit_guide.php';

if (!function_exists('schogms_remarks_cor_cog_label')) {
    function schogms_remarks_cor_cog_label(array $check): string
    {
        $hasCor = !empty($check['has_cor']);
        $hasCog = !empty($check['has_cog']);
        if ($hasCor && $hasCog) {
            return 'COR & COG Submitted';
        }
        if ($hasCor) {
            return 'Only COR Submitted';
        }
        if ($hasCog) {
            return 'Only COG Submitted';
        }

        return 'Not Submitted';
    }
}

if (!function_exists('schogms_remarks_suggested_text')) {
    /**
     * Build coordinator remark when masterlist remarks field is empty.
     */
    function schogms_remarks_suggested_text(array $row, array $check): string
    {
        $existing = trim((string) ($row['remarks'] ?? ''));
        if ($existing !== '') {
            return $existing;
        }

        $guide = schogms_validation_edit_guide($row, $check);
        if (!empty($guide['passed'])) {
            return 'Validated — complete';
        }

        $parts = [];
        foreach ($guide['items'] as $item) {
            $parts[] = (string) ($item['issue'] ?? '');
        }

        return $parts !== [] ? implode('; ', $parts) : 'For verification';
    }
}

if (!function_exists('schogms_remarks_csv_format')) {
    function schogms_remarks_csv_format(array $get): string
    {
        $f = strtolower(trim((string) ($get['format'] ?? 'template')));
        return $f === 'extended' ? 'extended' : 'template';
    }
}

if (!function_exists('schogms_remarks_csv_headers')) {
    /**
     * template = same columns as validated_remarks Excel (A–O TDP, A–P TES)
     * extended = adds validation status + masterlist/suggested remarks
     *
     * @return list<string>
     */
    function schogms_remarks_csv_headers(string $program, string $format = 'template'): array
    {
        $program = strtolower(trim($program));
        $format = $format === 'extended' ? 'extended' : 'template';

        if ($program === 'tes') {
            if ($format === 'template') {
                return [
                    'SEQ',
                    'APP NO',
                    'LASTNAME',
                    'FIRSTNAME',
                    'EXT NAME',
                    'MIDDLENAME',
                    'SEX',
                    'COURSE/PROGRAM ENROLLED',
                    'YEAR LEVEL',
                    'STREET',
                    'TOWN/CITY',
                    'CONTACT',
                    'BATCH NO',
                    'COR/COG STATUS',
                    'ENROLLMENT STATUS',
                    'REMARKS',
                ];
            }

            return [
                'SEQ',
                'APP NO',
                'LASTNAME',
                'FIRSTNAME',
                'EXT NAME',
                'MIDDLENAME',
                'SEX',
                'COURSE/PROGRAM ENROLLED',
                'YEAR LEVEL',
                'STREET',
                'TOWN/CITY',
                'CONTACT',
                'BATCH NO',
                'COR/COG STATUS',
                'ENROLLMENT STATUS',
                'VALIDATION STATUS',
                'REMARKS (MASTERLIST)',
                'SUGGESTED REMARKS',
            ];
        }

        if ($format === 'template') {
            return [
                'SEQ',
                'APP NO',
                'AWARD NO',
                'LASTNAME',
                'FIRSTNAME',
                'EXTNAME',
                'MIDDLENAME',
                'SEX',
                'BIRTHDATE',
                'COURSE/PROGRAM ENROLLED',
                'YEAR LEVEL',
                'UNITS ENROLLED (REGISTRAR)',
                'COR/COG STATUS',
                'ENROLLMENT STATUS',
                'REMARKS',
            ];
        }

        return [
            'SEQ',
            'APP NO',
            'AWARD NO',
            'LASTNAME',
            'FIRSTNAME',
            'EXTNAME',
            'MIDDLENAME',
            'SEX',
            'BIRTHDATE',
            'COURSE/PROGRAM ENROLLED',
            'YEAR LEVEL',
            'UNITS ENROLLED (REGISTRAR)',
            'COR/COG STATUS',
            'ENROLLMENT STATUS',
            'VALIDATION STATUS',
            'REMARKS (MASTERLIST)',
            'SUGGESTED REMARKS',
        ];
    }
}

if (!function_exists('schogms_remarks_csv_row')) {
    /**
     * @param array<string, mixed> $row
     * @param array<string, mixed> $check
     * @return list<string>
     */
    function schogms_remarks_csv_row(array $row, array $check, string $program, string $format = 'template'): array
    {
        $program = strtolower(trim($program));
        $format = $format === 'extended' ? 'extended' : 'template';
        $corCog = schogms_remarks_cor_cog_label($check);
        $enrollment = (string) ($check['enrollment'] ?? 'Not Enrolled');
        $validation = (string) ($check['validation_label'] ?? ($check['passed'] ? 'Validated' : 'Failed'));
        $masterRemarks = trim((string) ($row['remarks'] ?? ''));
        $suggested = schogms_remarks_suggested_text($row, $check);
        $remarksCol = $masterRemarks !== '' ? $masterRemarks : $suggested;

        if ($program === 'tes') {
            $base = [
                (string) ($row['seq'] ?? ''),
                (string) ($row['app_no'] ?? ''),
                (string) ($row['lastname'] ?? ''),
                (string) ($row['firstname'] ?? ''),
                (string) ($row['extname'] ?? ($row['ext'] ?? '')),
                (string) ($row['middlename'] ?? ''),
                (string) ($row['sex'] ?? ''),
                (string) ($row['course_program_enrolled'] ?? ''),
                (string) ($row['year_level'] ?? ''),
                (string) ($row['street'] ?? ''),
                (string) ($row['town_city'] ?? ''),
                (string) ($row['contact'] ?? ''),
                (string) ($row['batch_no'] ?? ''),
                $corCog,
                $enrollment,
            ];
            if ($format === 'template') {
                $base[] = $remarksCol;

                return $base;
            }

            return array_merge($base, [$validation, $masterRemarks, $suggested]);
        }

        $base = [
            (string) ($row['seq'] ?? ''),
            (string) ($row['app_no'] ?? ''),
            (string) ($row['award_no'] ?? ''),
            (string) ($row['lastname'] ?? ''),
            (string) ($row['firstname'] ?? ''),
            (string) ($row['extname'] ?? ''),
            (string) ($row['middlename'] ?? ''),
            (string) ($row['sex'] ?? ''),
            (string) ($row['birthdate'] ?? ''),
            (string) ($row['course_program_enrolled'] ?? ''),
            (string) ($row['year_level'] ?? ''),
            (string) ($row['enrolled'] ?? ''),
            $corCog,
            $enrollment,
        ];
        if ($format === 'template') {
            $base[] = $remarksCol;

            return $base;
        }

        return array_merge($base, [$validation, $masterRemarks, $suggested]);
    }
}

if (!function_exists('schogms_remarks_prepare_rows')) {
    /**
     * @param list<array<string, mixed>> $rows
     * @return list<array{row: array<string, mixed>, check: array<string, mixed>}>
     */
    function schogms_remarks_prepare_rows(array $rows, string $program): array
    {
        $out = [];
        foreach ($rows as $row) {
            $check = $row['_check'] ?? schogms_validation_row_check($row, [], $program);
            $out[] = ['row' => $row, 'check' => $check];
        }

        return $out;
    }
}

if (!function_exists('schogms_remarks_stats')) {
    /**
     * @param list<array{row: array, check: array}> $prepared
     * @return array{total: int, enrolled: int, passed: int, failed: int, pending: int, no_cor: int, no_cog: int}
     */
    function schogms_remarks_stats(array $prepared): array
    {
        $stats = [
            'total' => count($prepared),
            'enrolled' => 0,
            'passed' => 0,
            'failed' => 0,
            'pending' => 0,
            'no_cor' => 0,
            'no_cog' => 0,
        ];
        foreach ($prepared as $item) {
            $c = $item['check'];
            if (($c['enrollment'] ?? '') === 'Enrolled') {
                $stats['enrolled']++;
            }
            if (!empty($c['passed'])) {
                $stats['passed']++;
            } elseif (strtolower((string) ($c['validation_label'] ?? '')) === 'pending') {
                $stats['pending']++;
            } else {
                $stats['failed']++;
            }
            if (empty($c['has_cor'])) {
                $stats['no_cor']++;
            }
            if (empty($c['has_cog'])) {
                $stats['no_cog']++;
            }
        }

        return $stats;
    }
}

if (!function_exists('schogms_remarks_stream_csv')) {
    /**
     * @param list<array{row: array<string, mixed>, check: array<string, mixed>}> $prepared
     */
    function schogms_remarks_stream_csv(array $prepared, string $program, string $campus, string $format = 'template'): void
    {
        $program = strtolower(trim($program));
        $format = $format === 'extended' ? 'extended' : 'template';
        $campus = preg_replace('/[^A-Za-z0-9_-]+/', '_', trim($campus));
        $suffix = $format === 'extended' ? '_extended' : '';
        $filename = 'remarks_' . ($program === 'tes' ? 'TES' : 'TDP') . '_' . ($campus !== '' ? $campus : 'campus') . $suffix . '_' . date('Y-m-d') . '.csv';

        header('Content-Type: text/csv; charset=UTF-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: no-store, no-cache, must-revalidate');
        header('Pragma: no-cache');

        $out = fopen('php://output', 'w');
        if ($out === false) {
            return;
        }

        fwrite($out, "\xEF\xBB\xBF");
        fputcsv($out, schogms_remarks_csv_headers($program, $format));

        foreach ($prepared as $item) {
            fputcsv($out, schogms_remarks_csv_row($item['row'], $item['check'], $program, $format));
        }

        fclose($out);
        exit;
    }
}
