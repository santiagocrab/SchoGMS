<?php
header('Content-Type: application/json');

require __DIR__ . '/../config/session.php';
require __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/inc/ched_masterlist_import.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

if (($role ?? '') !== 'coordinator') {
    echo json_encode(['success' => false, 'error' => 'Access denied.']);
    exit;
}

error_reporting(E_ALL);
ini_set('log_errors', '1');
ini_set('error_log', __DIR__ . '/error_log.txt');

$logFile = __DIR__ . '/error_log.txt';

function logError(string $message): void
{
    global $logFile;
    error_log('[' . date('Y-m-d H:i:s') . '] ' . $message . PHP_EOL, 3, $logFile);
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    logError('Invalid request method.');
    echo json_encode(['success' => false, 'error' => 'Invalid request method.']);
    exit;
}

$file_group = trim((string) ($_POST['file_group'] ?? ''));
$campusSheet = trim((string) ($_POST['sheet_name'] ?? ($sheet_name ?? '')));

if ($file_group === '') {
    echo json_encode(['success' => false, 'error' => 'File group is required.']);
    exit;
}
if ($campusSheet === '') {
    echo json_encode(['success' => false, 'error' => 'Campus is not set on your account.']);
    exit;
}
if (!isset($_FILES['excelFile']) || $_FILES['excelFile']['error'] !== UPLOAD_ERR_OK) {
    $code = $_FILES['excelFile']['error'] ?? 'none';
    logError('File upload error: ' . $code);
    echo json_encode(['success' => false, 'error' => 'File upload failed.']);
    exit;
}

$uploadsDir = __DIR__ . '/uploads/';
if (!is_dir($uploadsDir) && !mkdir($uploadsDir, 0777, true)) {
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
    $spreadsheet = IOFactory::load($targetFilePath);
    $sheet = $spreadsheet->getActiveSheet();
    $rows = $sheet->toArray(null, true, true, true);

    if (count($rows) < 4) {
        unlink($targetFilePath);
        echo json_encode(['success' => false, 'error' => 'The file does not contain enough rows.']);
        exit;
    }

    $layout = schogms_ched_tdp_import_layout($rows);
    $dataRows = array_slice($rows, $layout['data_start']);

    $insertQuery = '
        INSERT INTO ched_masterlist (
            sheet_name, seq, app_no, award_no, lastname, firstname, extname, middlename,
            sex, birthdate, course_program_enrolled, year_level,
            total_units_enrolled, status_of_enrollment, remarks,
            filename, file_group
        ) VALUES (
            ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?
        )';

    $stmt = $conn->prepare($insertQuery);
    if (!$stmt) {
        logError('Failed to prepare SQL: ' . $conn->error);
        echo json_encode(['success' => false, 'error' => 'Failed to prepare database statement.']);
        exit;
    }

    $conn->begin_transaction();
    $inserted = 0;

    foreach ($dataRows as $row) {
        if (!is_array($row)) {
            continue;
        }
        $seq = schogms_ched_tdp_row_cell($row, 'A');
        if ($seq === '') {
            break;
        }
        if (!array_filter($row, static fn($v) => trim((string) $v) !== '')) {
            continue;
        }

        $appNo = schogms_ched_tdp_row_cell($row, 'B');
        $awardNo = schogms_ched_tdp_row_cell($row, 'C');
        $lastname = schogms_ched_tdp_row_cell($row, 'D');
        $firstname = schogms_ched_tdp_row_cell($row, 'E');
        $extname = schogms_ched_tdp_row_cell($row, 'F');
        $middlename = schogms_ched_tdp_row_cell($row, 'G');
        $sex = schogms_ched_tdp_row_cell($row, 'H');
        $birthdate = schogms_ched_tdp_row_cell($row, 'I');
        $course = schogms_ched_tdp_row_cell($row, 'J');
        $yearLevel = schogms_ched_tdp_row_cell($row, 'K');
        $units = schogms_ched_tdp_row_cell($row, $layout['units_col']);
        $status = schogms_ched_tdp_row_cell($row, $layout['status_col']);
        $remarks = schogms_ched_tdp_row_cell($row, $layout['remarks_col']);

        $stmt->bind_param(
            'sssssssssssssssss',
            $campusSheet,
            $seq,
            $appNo,
            $awardNo,
            $lastname,
            $firstname,
            $extname,
            $middlename,
            $sex,
            $birthdate,
            $course,
            $yearLevel,
            $units,
            $status,
            $remarks,
            $uploadedFileName,
            $file_group
        );

        if (!$stmt->execute()) {
            $conn->rollback();
            logError('Insert failed: ' . $stmt->error);
            echo json_encode(['success' => false, 'error' => 'Error saving masterlist row.']);
            exit;
        }
        $inserted++;
    }

    $conn->commit();
    $stmt->close();
    unlink($targetFilePath);

    require_once __DIR__ . '/../../inc/schogms_file_group_meta.php';
    schogms_file_group_meta_register($conn, 'tdp', $campusSheet, $file_group, 'pending', schogms_file_group_meta_uploader_from_session());

    echo json_encode([
        'success' => true,
        'message' => "Uploaded {$inserted} record(s) for campus {$campusSheet}.",
        'inserted' => $inserted,
    ]);
} catch (Throwable $e) {
    if ($conn instanceof mysqli) {
        $conn->rollback();
    }
    if (is_file($targetFilePath)) {
        unlink($targetFilePath);
    }
    logError('Error processing file: ' . $e->getMessage());
    echo json_encode(['success' => false, 'error' => 'Error processing file.']);
}
