<?php
/**
 * Import verified scholars billing Excel into billing_table (registrar campus uploads).
 */
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/config/session.php';
require_once __DIR__ . '/../../inc/schogms_billing_excel_import.php';
require_once __DIR__ . '/../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

if (($role ?? '') !== 'registrar') {
    echo json_encode(['success' => false, 'error' => 'Access denied.']);
    exit;
}

if (!($conn instanceof mysqli)) {
    echo json_encode(['success' => false, 'error' => 'Database unavailable.']);
    exit;
}

$registrarCampus = trim((string) ($sheet_name ?? ''));

$logFile = __DIR__ . '/error_log.txt';

function registrar_billing_log(string $message): void
{
    global $logFile;
    error_log('[' . date('Y-m-d H:i:s') . '] ' . $message . PHP_EOL, 3, $logFile);
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Invalid request method.']);
    exit;
}

if (!isset($_FILES['excelFile']['tmp_name']) || $_FILES['excelFile']['tmp_name'] === '') {
    echo json_encode(['success' => false, 'error' => 'No file uploaded.']);
    exit;
}

$tableCheck = $conn->query("SHOW TABLES LIKE 'billing_table'");
if (!$tableCheck || $tableCheck->num_rows === 0) {
    echo json_encode(['success' => false, 'error' => 'Billing table is not set up. Run database migrations or contact the administrator.']);
    exit;
}

$uploadsDir = __DIR__ . '/uploads/';
$writable = schogms_ensure_writable_upload_dir($uploadsDir);
if (!$writable['ok']) {
    echo json_encode(['success' => false, 'error' => $writable['error']]);
    exit;
}

$uploadedFileName = basename((string) $_FILES['excelFile']['name']);
$targetFilePath = $uploadsDir . $uploadedFileName;

if (!move_uploaded_file($_FILES['excelFile']['tmp_name'], $targetFilePath)) {
    echo json_encode(['success' => false, 'error' => 'Failed to save the uploaded file.']);
    exit;
}

try {
    $spreadsheet = IOFactory::load($targetFilePath);
    $sheet = $spreadsheet->getActiveSheet();
    $rows = $sheet->toArray(null, true, true, true);

    if (count($rows) < 3) {
        unlink($targetFilePath);
        echo json_encode(['success' => false, 'error' => 'The file does not contain enough rows (data starts at row 3).']);
        exit;
    }

    if (schogms_billing_looks_like_annex7($rows)) {
        unlink($targetFilePath);
        echo json_encode([
            'success' => false,
            'error' => 'This file looks like Annex 7. Coordinators upload Annex 7 under Submit Form, not billing import.',
        ]);
        exit;
    }

    $dataRows = array_slice($rows, 2);

    $stmt = $conn->prepare(schogms_billing_import_sql());
    if (!$stmt) {
        unlink($targetFilePath);
        echo json_encode(['success' => false, 'error' => 'Failed to prepare import: ' . $conn->error]);
        exit;
    }

    $imported = 0;
    $skipped = 0;

    foreach ($dataRows as $row) {
        if (!array_filter($row)) {
            continue;
        }

        [$types, $values] = schogms_billing_row_bind_values($row);
        $campus = trim((string) $values[5]);
        if ($campus === '' || strtoupper($campus) === 'N/A') {
            $values[5] = $registrarCampus !== '' ? $registrarCampus : 'N/A';
            $campus = (string) $values[5];
        }

        if ($registrarCampus !== '' && strcasecmp($campus, $registrarCampus) !== 0) {
            $skipped++;
            continue;
        }

        $stmt->bind_param($types, ...$values);

        if ($stmt->execute()) {
            $imported++;
        } else {
            registrar_billing_log('Insert failed: ' . $stmt->error);
        }
    }

    $stmt->close();
    unlink($targetFilePath);

    $msg = $imported . ' billing row(s) imported.';
    if ($skipped > 0) {
        $msg .= ' ' . $skipped . ' row(s) skipped (campus does not match your assigned campus).';
    }

    echo json_encode(['success' => $imported > 0, 'message' => $msg, 'imported' => $imported, 'skipped' => $skipped]);
} catch (Throwable $e) {
    if (is_file($targetFilePath)) {
        unlink($targetFilePath);
    }
    registrar_billing_log($e->getMessage());
    echo json_encode(['success' => false, 'error' => 'Error processing file: ' . $e->getMessage()]);
}
