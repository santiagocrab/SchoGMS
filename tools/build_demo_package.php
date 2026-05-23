<?php
/**
 * Build all presentation demo files under demo/files/ (manual upload kit).
 *
 * CLI:  /Applications/XAMPP/xamppfiles/bin/php tools/build_demo_package.php
 * Web:  http://localhost/SchoGMS/tools/build_demo_package.php?key=schogms_demo
 */
declare(strict_types=1);

$isCli = PHP_SAPI === 'cli';
if (!$isCli && (($_GET['key'] ?? '') !== 'schogms_demo')) {
    header('Content-Type: text/html; charset=utf-8');
    echo '<h1>Build demo package</h1><p>Add <code>?key=schogms_demo</code></p>';
    exit;
}

$root = dirname(__DIR__);
$manifest = require $root . '/demo/inc/demo_manifest.php';
$outBase = $root . '/demo/files';

function demo_out(string $msg): void
{
    echo $msg . PHP_EOL;
    if (PHP_SAPI !== 'cli') {
        echo '<br>';
    }
}

function demo_doc_basename(array $s): string
{
    $base = trim($s['lastname']) . ', ' . trim($s['firstname']);
    if (trim((string) ($s['middlename'] ?? '')) !== '') {
        $base .= ' ' . trim((string) $s['middlename']);
    }

    return $base;
}

function demo_write_pdf(string $path, string $label): void
{
    require_once dirname(__DIR__) . '/config/schogms_helpers.php';
    schogms_write_viewable_pdf($path, $label, 'SchoGMS demo package');
}

function demo_ensure_dir(string $path): void
{
    if (!is_dir($path)) {
        mkdir($path, 0755, true);
    }
}

/** @param list<list<string>> $rows */
function demo_write_csv(string $path, array $rows): void
{
    demo_ensure_dir(dirname($path));
    $fp = fopen($path, 'w');
    if (!$fp) {
        throw new RuntimeException('Cannot write ' . $path);
    }
    foreach ($rows as $row) {
        fputcsv($fp, $row);
    }
    fclose($fp);
}

/** @param list<list<string|null>> $rows */
function demo_write_xlsx(string $path, array $rows): void
{
    $autoload = dirname(__DIR__) . '/users/vendor/autoload.php';
    if (!is_readable($autoload)) {
        demo_write_csv(preg_replace('/\.xlsx$/i', '.csv', $path) ?: ($path . '.csv'), $rows);
        demo_out('  (no PhpSpreadsheet — wrote CSV instead of ' . basename($path) . ')');

        return;
    }
    require_once $autoload;
    $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    foreach ($rows as $rIdx => $row) {
        foreach ($row as $cIdx => $val) {
            $col = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($cIdx + 1);
            $sheet->setCellValue($col . ($rIdx + 1), $val);
        }
    }
    demo_ensure_dir(dirname($path));
    $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
    $writer->save($path);
}

$fg = $manifest['file_groups'];
$campus = $manifest['campus'];
$scholars = $manifest['scholars'];

demo_out('=== Building SchoGMS demo package ===');
demo_out('Output: demo/files/');
demo_out('Campus: ' . $campus);
demo_out('Scholars: ' . count($scholars));
demo_out('');

// --- 01 Registrar ---
$regRows = [[
    'Last Name', 'First Name', 'Middle Name', 'Extension Name', 'ID Number', 'Gender', 'Student Type',
    'Year Level', 'Attended', 'Course', 'Curriculum', 'Scholarship', 'GPA', 'CGPA', 'Pass Percentage',
    'Grade Remarks', 'Enrolled', 'Lecture Unit', 'Lab Unit', 'COR Printed',
]];
foreach ($scholars as $s) {
    $regRows[] = [
        $s['lastname'],
        $s['firstname'],
        $s['middlename'],
        $s['extname'],
        $s['id_number'],
        $s['sex'] === 'M' ? 'Male' : 'Female',
        'Regular',
        $s['year_level'] . 'nd Year',
        'Yes',
        $s['course'],
        '2024',
        'CHED',
        '1.75',
        '1.75',
        '95',
        'Passed',
        $s['status'] === 'Enrolled' ? 'Yes' : 'No',
        $s['units'],
        '6',
        'Yes',
    ];
}
$regPath = $outBase . '/01_registrar/registrar_masterlist_ACCESS_demo.xlsx';
demo_write_xlsx($regPath, $regRows);
demo_out('Wrote registrar masterlist → 01_registrar/');

// --- 02 Coordinator TDP ---
$tdpRows = [
    ['CHED TDP Masterlist — DEMO PRESENTATION (ACCESS)', null, null, null, null, null, null, null, null, null, null, null, null, null],
    [null, null, null, null, null, null, null, null, null, null, null, null, null, null],
    ['SEQ', 'APP NO', 'AWARD NO', 'LASTNAME', 'FIRSTNAME', 'EXTNAME', 'MIDDLENAME', 'SEX', 'BIRTHDATE', 'COURSE', 'YEAR LEVEL', 'TOTAL UNITS ENROLLED', 'STATUS OF ENROLLMENT', 'REMARKS'],
];
foreach ($scholars as $s) {
    $tdpRows[] = [
        $s['seq'], $s['app_no'], $s['award_no'], $s['lastname'], $s['firstname'], $s['extname'],
        $s['middlename'], $s['sex'], $s['birthdate'], $s['course'], $s['year_level'], $s['units'],
        $s['status'], $s['remarks'],
    ];
}
demo_write_xlsx($outBase . '/02_coordinator/ched_tdp_ACCESS_SY2024-2025_demo.xlsx', $tdpRows);
demo_out('Wrote CHED TDP masterlist → 02_coordinator/');

// --- 03 Coordinator TES (simplified columns) ---
$tesRows = [
    ['CHED TES Masterlist — DEMO PRESENTATION', null, null, null, null, null, null, null, null, null, null, null, null, null, null, null],
    ['SEQ', 'APP NO', 'LASTNAME', 'FIRSTNAME', 'EXT', 'MIDDLENAME', 'SEX', 'COURSE', 'YEAR LEVEL', 'STREET', 'TOWN/CITY', 'CONTACT', 'BATCH NO', null, null, null],
];
foreach ($scholars as $s) {
    $tesRows[] = [
        $s['seq'], 'TES-' . $s['seq'], $s['lastname'], $s['firstname'], $s['extname'], $s['middlename'],
        $s['sex'], $s['course'], $s['year_level'], '100 Demo St', 'Esperanza', $s['contact'], $s['batch_no'],
    ];
}
demo_write_xlsx($outBase . '/02_coordinator/ched_tes_ACCESS_demo.xlsx', $tesRows);
demo_out('Wrote CHED TES masterlist → 02_coordinator/');

// --- Annex 7 ---
$annexRows = [
    ['Annex 7 - Scholarship Grant Utilization Report (DEMO PRESENTATION)', null, null, null, null, null],
    ['Last Name', 'First Name', 'Scholarship Type', 'Units Enrolled', 'Course', 'Campus'],
];
foreach ($scholars as $s) {
    $annexRows[] = [
        $s['lastname'],
        $s['firstname'],
        'TDP',
        $s['units'],
        $s['course'],
        $campus,
    ];
}
demo_write_xlsx($outBase . '/02_coordinator/annex7_ACCESS_demo.xlsx', $annexRows);
demo_write_csv($outBase . '/02_coordinator/annex7_ACCESS_demo.csv', $annexRows);
demo_out('Wrote Annex 7 sample → 02_coordinator/');

// --- COR / COG PDFs (filenames must match scholar names) ---
$corDir = $outBase . '/03_cor_cog/COR';
$cogDir = $outBase . '/03_cor_cog/COG';
foreach ($scholars as $s) {
    $base = demo_doc_basename($s);
    $corName = $base . '.pdf';
    $cogName = $base . '.pdf';
    demo_write_pdf($corDir . '/' . $corName, "COR {$base} {$campus}");
    demo_write_pdf($cogDir . '/' . $cogName, "COG {$base} {$campus}");
}
demo_out('Wrote ' . (count($scholars) * 2) . ' PDFs → 03_cor_cog/COR and COG/');

// --- Chairman billing sample ---
$billRows = [
    [
        'Last Name', 'First Name', 'Scholarship Type', 'Units Enrolled', 'Course', 'Campus',
        'Year & Date Submitted (CHED)', 'Amount', 'First Semester', 'Second Semester', 'Status',
        'Payment Scholarship Type', 'Payment Amount', 'Payment Year & Date', 'Payment OR Number',
    ],
];
foreach ($scholars as $i => $s) {
    $billRows[] = [
        $s['lastname'],
        $s['firstname'],
        'CHED TDP',
        $s['units'],
        $s['course'],
        $campus,
        '2024-08-15',
        '15000',
        'PAID',
        'ON PROCESS',
        'Active',
        'TDP',
        '15000',
        '2025-01-10',
        'DEMO-PRESENT-OR-' . sprintf('%03d', $i + 1),
    ];
}
demo_write_xlsx($outBase . '/04_chairman/billing_verified_scholars_ACCESS_demo.xlsx', $billRows);
demo_out('Wrote billing sample → 04_chairman/');

// --- Manifest JSON for UI / scripts ---
$meta = [
    'generated_at' => date('c'),
    'campus' => $campus,
    'file_groups' => $fg,
    'scholars' => array_map(static function (array $s): array {
        return [
            'display_name' => demo_doc_basename($s),
            'cor_file' => demo_doc_basename($s) . '.pdf',
            'cog_file' => demo_doc_basename($s) . '.pdf',
            'seq' => $s['seq'],
        ];
    }, $scholars),
    'upload_order' => [
        ['step' => 1, 'role' => 'registrar', 'file' => '01_registrar/registrar_masterlist_ACCESS_demo.xlsx', 'file_group' => $fg['registrar'], 'when' => 'Start of demo — before coordinator validation'],
        ['step' => 2, 'role' => 'coordinator', 'file' => '02_coordinator/ched_tdp_ACCESS_SY2024-2025_demo.xlsx', 'file_group' => $fg['tdp'], 'when' => 'After registrar — triggers chairman pending file group'],
        ['step' => 3, 'role' => 'coordinator', 'file' => '02_coordinator/ched_tes_ACCESS_demo.xlsx', 'file_group' => $fg['tes'], 'when' => 'Optional — TES tab demo'],
        ['step' => 4, 'role' => 'coordinator', 'file' => '03_cor_cog/COR/*.pdf', 'file_group' => $fg['cor'], 'when' => 'After TDP masterlist — Requirements → COR bulk upload'],
        ['step' => 5, 'role' => 'coordinator', 'file' => '03_cor_cog/COG/*.pdf', 'file_group' => $fg['cog'], 'when' => 'After COR — COG bulk upload'],
        ['step' => 6, 'role' => 'coordinator', 'file' => '02_coordinator/annex7_ACCESS_demo.xlsx', 'file_group' => $fg['annex7'], 'when' => 'Submit Form — Annex 7 to chairman'],
        ['step' => 7, 'role' => 'chairman', 'file' => '(UI only)', 'file_group' => $fg['tdp'], 'when' => 'Review → File groups → Approve pending TDP'],
        ['step' => 8, 'role' => 'chairman', 'file' => '04_chairman/billing_verified_scholars_ACCESS_demo.xlsx', 'file_group' => $fg['billing'], 'when' => 'Verified scholars — import billing (if enabled)'],
    ],
];
file_put_contents(
    $outBase . '/manifest.json',
    json_encode($meta, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)
);

// --- Quick reference text in each folder ---
$readme = <<<TXT
SchoGMS presentation demo — ACCESS campus
File groups (type exactly in the upload form):
  Registrar: {$fg['registrar']}
  TDP:       {$fg['tdp']}
  TES:       {$fg['tes']}
  COR:       {$fg['cor']}
  COG:       {$fg['cog']}
  Annex 7:   {$fg['annex7']}

See demo/DEMO_GUIDE.md for account logins and step-by-step timing.
TXT;
foreach (['01_registrar', '02_coordinator', '03_cor_cog', '04_chairman'] as $sub) {
    file_put_contents($outBase . '/' . $sub . '/README.txt', $readme);
}

demo_out('');
demo_out('=== DONE ===');
demo_out('Open: demo/DEMO_GUIDE.md');
demo_out('Files: demo/files/');
