<?php
header('Content-Type: application/json; charset=utf-8');

require __DIR__ . '/config/session.php';
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

error_reporting(E_ALL);
ini_set('log_errors', '1');
ini_set('error_log', __DIR__ . '/error_log.txt');

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

$uploadedFileName = basename($_FILES['excelFile']['name']);
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
    $spreadsheet = IOFactory::load($targetFilePath);
    $sheet = $spreadsheet->getActiveSheet();
    $rows = $sheet->toArray(null, true, true, true);

    if (count($rows) < 3) {
        @unlink($targetFilePath);
        echo json_encode(['success' => false, 'error' => 'The file must have data starting at row 3.']);
        exit;
    }

    $dataRows = array_slice($rows, 2);
    $insertQuery = '
        INSERT INTO billing_table (
            last_name, first_name, scholarship_type, units_enrolled, course, campus, year_and_date_submitted_ched,
            amount, first_semester, second_semester, status, payment_scholarship_type, payment_amount,
            payment_year_and_date, payment_or_number, payment_amount_per_or, refund_first_sem, refund_second_sem,
            refund_year_and_date_released
        ) VALUES (
            ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?
        )';

    $stmt = $conn->prepare($insertQuery);
    if (!$stmt) {
        throw new RuntimeException('Prepare failed: ' . $conn->error);
    }

    $imported = 0;
    foreach ($dataRows as $row) {
        if (!array_filter($row, static fn($v) => trim((string) $v) !== '')) {
            continue;
        }

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

        $stmt->bind_param(
            'sssssssdssssdssddds',
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
            $refund_year_and_date_released
        );

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
