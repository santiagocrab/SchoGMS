<?php
/**
 * Build edit guidance from validation check (masterlist vs registrar).
 */

if (!function_exists('schogms_validation_edit_guide')) {
    /**
     * @param array<string, mixed> $row
     * @param array<string, mixed> $check from schogms_validation_row_check()
     * @return array{passed: bool, items: list<array{field: string, issue: string, masterlist: string, registrar: string, action: string}>}
     */
    function schogms_validation_edit_guide(array $row, array $check): array
    {
        $items = [];
        $mlCourse = trim((string) ($row['course_program_enrolled'] ?? ''));
        $regCourse = trim((string) ($row['reg_course'] ?? ''));
        $mlYear = trim((string) ($row['year_level'] ?? ''));
        $regYear = trim((string) ($row['reg_year_level'] ?? ''));

        if (empty($check['course_match'])) {
            $items[] = [
                'field' => 'course',
                'issue' => 'Course does not match registrar',
                'masterlist' => $mlCourse,
                'registrar' => $regCourse,
                'action' => $regCourse !== ''
                    ? 'Change Course to match registrar (or fix registrar record if masterlist is correct).'
                    : 'No registrar course on file — verify name match or update registrar data.',
            ];
        }

        if (empty($check['year_level_match'])) {
            $items[] = [
                'field' => 'year_level',
                'issue' => 'Year level does not match registrar',
                'masterlist' => $mlYear,
                'registrar' => $regYear,
                'action' => $regYear !== ''
                    ? 'Change Year level to match registrar (e.g. use same format: 1st year, 2, etc.).'
                    : 'No registrar year level — verify name match or update registrar data.',
            ];
        }

        if (empty($check['has_cor'])) {
            $items[] = [
                'field' => 'cor',
                'issue' => 'No COR uploaded',
                'masterlist' => 'Missing',
                'registrar' => '—',
                'action' => 'Upload COR below (filename: LASTNAME, FIRSTNAME MIDDLENAME.pdf).',
            ];
        }

        if (empty($check['has_cog'])) {
            $items[] = [
                'field' => 'cog',
                'issue' => 'No COG uploaded',
                'masterlist' => 'Missing',
                'registrar' => '—',
                'action' => 'Upload COG below if required for enrollment.',
            ];
        }

        return [
            'passed' => !empty($check['passed']),
            'items' => $items,
        ];
    }
}

if (!function_exists('schogms_validation_edit_guide_attr')) {
    /** @param array<string, mixed> $row */
    function schogms_validation_edit_guide_attr(array $row, array $check): string
    {
        $guide = schogms_validation_edit_guide($row, $check);

        return htmlspecialchars(
            json_encode($guide, JSON_UNESCAPED_UNICODE),
            ENT_QUOTES,
            'UTF-8'
        );
    }
}
