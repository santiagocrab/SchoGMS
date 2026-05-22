<?php
require __DIR__ . '/config/session.php';
require_once __DIR__ . '/../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

if (($role ?? '') !== 'chairman') {
    header('HTTP/403');
    echo 'Access denied.';
    exit;
}

if (!class_exists(Spreadsheet::class)) {
    header('HTTP/500');
    echo 'PhpSpreadsheet is not available.';
    exit;
}

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$sheet->setTitle('Verified Scholars');

$sheet->setCellValue('A1', 'SchoGMS — Verified scholars / billing upload');
$sheet->setCellValue('A2', 'Data starts row 3. Do not reorder columns A–S.');

$headers = [
    'A3' => 'Last name',
    'B3' => 'First name',
    'C3' => 'Scholarship type',
    'D3' => 'Units enrolled',
    'E3' => 'Course',
    'F3' => 'Campus',
    'G3' => 'Year & date submitted (CHED)',
    'H3' => 'Amount',
    'I3' => 'First semester',
    'J3' => 'Second semester',
    'K3' => 'Status',
    'L3' => 'Payment scholarship type',
    'M3' => 'Payment amount',
    'N3' => 'Payment year & date',
    'O3' => 'Payment OR number',
    'P3' => 'Payment amount per OR',
    'Q3' => 'Refund 1st sem',
    'R3' => 'Refund 2nd sem',
    'S3' => 'Refund date released',
];
foreach ($headers as $cell => $label) {
    $sheet->setCellValue($cell, $label);
}

$sheet->fromArray(
    [
        'Dela Cruz',
        'Juan',
        'TDP',
        21,
        'BS Information Technology',
        'CAMPUS',
        date('Y-m-d'),
        7500,
        'Paid',
        'N/A',
        'Verified',
        'TDP',
        7500,
        date('Y-m-d'),
        'OR-00001',
        7500,
        0,
        0,
        null,
    ],
    null,
    'A4'
);

foreach (range('A', 'S') as $col) {
    $sheet->getColumnDimension($col)->setAutoSize(true);
}

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment; filename="verified_scholars_billing_template.xlsx"');
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;
