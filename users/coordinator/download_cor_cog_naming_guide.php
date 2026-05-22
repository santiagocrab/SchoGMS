<?php
require_once __DIR__ . '/../config/session.php';

$category = strtoupper(trim((string) ($_GET['category'] ?? 'COR')));
if (!in_array($category, ['COR', 'COG'], true)) {
    $category = 'COR';
}

$lines = [
    'SchoGMS — Coordinator ' . $category . ' file naming guide',
    '',
    'One file per scholar. Accepted: .pdf, .jpg, .jpeg, .png',
    '',
    'Required filename pattern:',
    '  LASTNAME, FIRSTNAME MIDDLENAME.pdf',
    '',
    'Examples (correct):',
    '  ABACARO, ROSE ANN PIQUE.pdf',
    '  DELA CRUZ, JUAN CARLOS.pdf',
    '  ABAD, AL BASSER PAÑARES.pdf',
    '',
    'Examples (incorrect — will not match masterlist):',
    '  juan_delacruz.pdf',
    '  COR-12345.pdf',
    '  Juan Dela Cruz.pdf',
    '',
    'Campus: ' . trim((string) ($sheet_name ?? '')),
    'Category: ' . $category,
    'Suggested file group: ' . $category . ' ' . ucfirst(strtolower(trim((string) ($sheet_name ?? 'Campus')))),
    '',
    'Upload via COR or COG page → Upload File button.',
];

header('Content-Type: text/plain; charset=utf-8');
header('Content-Disposition: attachment; filename="' . $category . '_naming_guide.txt"');
echo implode("\n", $lines);
