<?php
/**
 * Verified Scholars — billing Excel import (not Annex 7; use Submit Form for Annex 7).
 */
declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../../inc/schogms_billing_excel_import.php';
require_once __DIR__ . '/../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

if (!($conn instanceof mysqli)) {
    echo json_encode(['success' => false, 'error' => 'Database unavailable']);
    exit;
}

$logFile = __DIR__ . '/error_log.txt';

function logError(string $message): void
{
    global $logFile;
    error_log('[' . date('Y-m-d H:i:s') . '] ' . $message . PHP_EOL, 3, $logFile);
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
$targetFilePath = $uploadsDir . $uploadedFileName;

if (!move_uploaded_file($_FILES['excelFile']['tmp_name'], $targetFilePath)) {
    echo json_encode(['success' => false, 'error' => 'Failed to save the uploaded file.']);
    exit;
}

try {
    $tableCheck = $conn->query("SHOW TABLES LIKE 'billing_table'");
    if (!$tableCheck || $tableCheck->num_rows === 0) {
        throw new RuntimeException(
            'Billing table is not set up. Run database setup or import database/schogms (1).sql, then try again.'
        );
    }

    $spreadsheet = IOFactory::load($targetFilePath);
    $rows = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);

    if (count($rows) < 3) {
        throw new RuntimeException('The file does not contain enough rows (billing data starts at row 3).');
    }

    if (schogms_billing_looks_like_annex7($rows)) {
        throw new RuntimeException(
            'This file looks like Annex 7 (Submit Form). Upload it under Coordinator → Submit Form, not Verified Scholars billing import.'
        );
    }

    $dataRows = array_slice($rows, 2);
    $insertQuery = schogms_billing_import_sql();
    $stmt = $conn->prepare($insertQuery);
    if (!$stmt) {
        throw new RuntimeException('Failed to prepare import: ' . $conn->error);
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
            logError('Row insert failed: ' . $stmt->error);
        }
    }

    $stmt->close();
    @unlink($targetFilePath);

    echo json_encode([
        'success' => true,
        'message' => "Imported {$imported} billing record(s). Data was read from Excel row 3 onward.",
    ]);
} catch (Throwable $e) {
    logError('Billing upload: ' . $e->getMessage());
    if (isset($targetFilePath) && is_file($targetFilePath)) {
        @unlink($targetFilePath);
    }
    echo json_encode(['success' => false, 'error' => 'Error processing file: ' . $e->getMessage()]);
}
