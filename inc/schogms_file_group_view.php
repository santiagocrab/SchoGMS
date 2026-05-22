<?php
/**
 * View scholars in a CHED file group (TDP / TES).
 */

require_once __DIR__ . '/../users/registrar/inc/registrar_data.php';

if (!function_exists('schogms_file_group_view_params')) {
    /** @return array{program: string, file_group: string, filename: string, campus: string, error: string} */
    function schogms_file_group_view_params(): array
    {
        return [
            'program' => strtolower(trim((string) ($_GET['program'] ?? 'tdp'))),
            'file_group' => trim((string) ($_GET['file_group'] ?? '')),
            'filename' => trim((string) ($_GET['filename'] ?? '')),
            'campus' => trim((string) ($_GET['campus'] ?? '')),
            'error' => '',
        ];
    }
}

if (!function_exists('schogms_file_group_view_fetch')) {
    /**
     * @return array{
     *   meta: array{label: string, table: string, campus_column: string},
     *   summary: array<string, mixed>,
     *   rows: list<array<string, mixed>>,
     *   error: string
     * }
     */
    function schogms_file_group_view_fetch(string $program, string $fileGroup, string $campus, ?string $filename, mysqli $db): array
    {
        $empty = [
            'meta' => ['label' => '', 'table' => '', 'campus_column' => ''],
            'summary' => [],
            'rows' => [],
            'error' => '',
        ];

        $meta = schogms_registrar_program_list_table($program);
        if ($meta === null) {
            $empty['error'] = 'Invalid program.';

            return $empty;
        }
        if ($fileGroup === '') {
            $empty['error'] = 'File group is required.';

            return $empty;
        }
        if ($campus === '') {
            $empty['error'] = 'Campus is required. Open this page from Program list (View) so the campus is included.';

            return $empty;
        }

        $table = $meta['table'];
        $campusCol = $meta['campus_column'];
        $fgEsc = $db->real_escape_string($fileGroup);
        $campusEsc = $db->real_escape_string($campus);
        $where = "{$campusCol} = '{$campusEsc}' AND file_group = '{$fgEsc}'";
        $filename = $filename !== null ? trim((string) $filename) : '';
        if ($filename !== '') {
            $where .= " AND filename = '" . $db->real_escape_string($filename) . "'";
        }

        $summary = [
            'file_group' => $fileGroup,
            'filename' => $filename,
            'scholar_count' => 0,
            'program_count' => 0,
            'programs_summary' => '',
            'campus' => $campus,
        ];

        $sumSql = "SELECT COUNT(*) AS n,
                COUNT(DISTINCT NULLIF(TRIM(course_program_enrolled), '')) AS pc,
                GROUP_CONCAT(DISTINCT NULLIF(TRIM(course_program_enrolled), '') ORDER BY course_program_enrolled SEPARATOR ', ') AS ps
            FROM {$table} WHERE {$where}";
        if ($sumRes = $db->query($sumSql)) {
            $s = $sumRes->fetch_assoc();
            $summary['scholar_count'] = (int) ($s['n'] ?? 0);
            $summary['program_count'] = (int) ($s['pc'] ?? 0);
            $summary['programs_summary'] = (string) ($s['ps'] ?? '');
        }

        $rows = [];
        $listSql = "SELECT * FROM {$table} WHERE {$where}
            ORDER BY lastname ASC, firstname ASC LIMIT 10000";
        if ($listRes = $db->query($listSql)) {
            while ($row = $listRes->fetch_assoc()) {
                $rows[] = $row;
            }
        } else {
            $empty['error'] = 'Could not load scholars: ' . $db->error;

            return $empty;
        }

        return [
            'meta' => $meta,
            'summary' => $summary,
            'rows' => $rows,
            'error' => '',
        ];
    }
}

if (!function_exists('schogms_file_group_summary_text')) {
    function schogms_file_group_summary_text(array $summary): string
    {
        $n = (int) ($summary['scholar_count'] ?? 0);
        $pc = (int) ($summary['program_count'] ?? 0);
        $files = trim((string) ($summary['filename'] ?? ''));
        $parts = [number_format($n) . ' scholar' . ($n === 1 ? '' : 's')];
        if ($pc > 0) {
            $parts[] = $pc . ' program' . ($pc === 1 ? '' : 's');
        }
        if ($files !== '') {
            $parts[] = 'file: ' . $files;
        }

        return implode(' · ', $parts);
    }
}

if (!function_exists('schogms_file_group_batch_summary_text')) {
    /** @param array<string, mixed> $row file group summary row from program_list_fetch */
    function schogms_file_group_batch_summary_text(array $row): string
    {
        $campus = trim((string) ($row['campus'] ?? ''));
        $scholars = (int) ($row['total_entries'] ?? 0);
        $programs = (int) ($row['program_count'] ?? 0);
        $files = (int) ($row['file_count'] ?? 0);
        $parts = [];
        if ($campus !== '') {
            $parts[] = 'Campus: ' . $campus;
        }
        $parts[] = number_format($scholars) . ' scholar' . ($scholars === 1 ? '' : 's');
        $parts[] = $programs . ' program' . ($programs === 1 ? '' : 's');
        if ($files > 0) {
            $parts[] = $files . ' upload file' . ($files === 1 ? '' : 's');
        }
        $progText = trim((string) ($row['programs_summary'] ?? ''));
        if ($progText !== '') {
            $parts[] = 'Includes: ' . (strlen($progText) > 120 ? substr($progText, 0, 117) . '…' : $progText);
        }

        return implode(' · ', $parts);
    }
}
