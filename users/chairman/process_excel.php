<?php
/**
 * Chairman — billing Excel import (Verified Scholars billing layout, columns A–S).
 */
declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');

require __DIR__ . '/config/session.php';
require_once __DIR__ . '/../../inc/schogms_billing_excel_import.php';
require __DIR__ . '/../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

if (($role ?? '') !== 'chairman') {
    echo json_encode(['success' => false, 'error' => 'Access denied.']);
    exit;
}

if (!($conn instanceof mysqli)) {
    echo json_encode(['success' => false, 'error' => 'Database unavailable']);
    exit;
}

function chairmanBillingLog(string $message): void
{
    error_log('[' . date('Y-m-d H:i:s') . '] ' . $message . PHP_EOL, 3, __DIR__ . '/error_log.txt');
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Invalid request method.']);
    exit;
}

if (empty($_FILES['excelFile']['tmp_name'])) {
    echo json_encode(['success' => false, 'error' => 'No file uploaded.']);
    exit;
}

$uploadsDir = __DIR__ . '/uploads/billing/';
if (!is_dir($uploadsDir) && !mkdir($uploadsDir, 0775, true)) {
    echo json_encode(['success' => false, 'error' => 'Failed to create uploads directory.']);
    exit;
}

$uploadedFileName = basename((string) $_FILES['excelFile']['name']);
$ext = strtolower(pathinfo($uploadedFileName, PATHINFO_EXTENSION));
if (!in_array($ext, ['xlsx', 'xls'], true)) {
    echo json_encode(['success' => false, 'error' => 'Please upload .xlsx or .xls only.']);
    exit;
}

$targetFilePath = $uploadsDir . $uploadedFileName;
if (!move_uploaded_file($_FILES['excelFile']['tmp_name'], $targetFilePath)) {
    echo json_encode(['success' => false, 'error' => 'Failed to save the uploaded file.']);
    exit;
}

try {
    $tableCheck = $conn->query("SHOW TABLES LIKE 'billing_table'");
    if (!$tableCheck || $tableCheck->num_rows === 0) {
        throw new RuntimeException('Billing table is not set up on this server.');
    }

    $rows = IOFactory::load($targetFilePath)->getActiveSheet()->toArray(null, true, true, true);

    if (count($rows) < 3) {
        throw new RuntimeException('The file must have data starting at row 3.');
    }

    if (schogms_billing_looks_like_annex7($rows)) {
        throw new RuntimeException(
            'This file looks like Annex 7. Coordinators should upload it under Submit Form, not billing import.'
        );
    }

    $dataRows = array_slice($rows, 2);
    $stmt = $conn->prepare(schogms_billing_import_sql());
    if (!$stmt) {
        throw new RuntimeException('Prepare failed: ' . $conn->error);
    }

    $imported = 0;
    foreach ($dataRows as $row) {
        if (!array_filter($row, static fn($v) => trim((string) $v) !== '')) {
            continue;
        }
        [$types, $values] = schogms_billing_row_bind_values($row);
        $stmt->bind_param($types, ...$values);
        if ($stmt->execute()) {
            $imported++;
        } else {
            chairmanBillingLog('Row insert failed: ' . $stmt->error);
        }
    }

    $stmt->close();
    @unlink($targetFilePath);

    echo json_encode([
        'success' => true,
        'message' => "Imported {$imported} billing record(s). Data was read from Excel row 3 onward.",
    ]);
} catch (Throwable $e) {
    chairmanBillingLog('Billing upload: ' . $e->getMessage());
    if (isset($targetFilePath) && is_file($targetFilePath)) {
        @unlink($targetFilePath);
    }
    echo json_encode(['success' => false, 'error' => 'Error processing file: ' . $e->getMessage()]);
}
