<?php
/**
 * Chairman TDP masterlist upload → MySQL ched_masterlist (same layout as coordinator/chairman bulk import).
 */
require __DIR__ . '/config/session.php';
require __DIR__ . '/../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

if (($role ?? '') !== 'chairman') {
    header('Location: upload_ched_tdp.php?error=access_denied');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: upload_ched_tdp.php');
    exit;
}

$academicYear = trim($_POST['academic_year'] ?? '');
$semester = trim($_POST['semester'] ?? '');
$fileGroup = trim($_POST['file_group'] ?? '');
$campus = trim($_POST['campus'] ?? '');

if ($academicYear === '' || $semester === '' || $fileGroup === '' || $campus === '') {
    header('Location: upload_ched_tdp.php?error=missing_fields');
    exit;
}

$fileGroupFull = $fileGroup . ' (' . $academicYear . ', ' . $semester . ')';

if (!isset($_FILES['excel_file']) || $_FILES['excel_file']['error'] !== UPLOAD_ERR_OK) {
    $code = $_FILES['excel_file']['error'] ?? 'none';
    header('Location: upload_ched_tdp.php?error=file_upload_failed&details=' . urlencode((string) $code));
    exit;
}

$file = $_FILES['excel_file'];
$ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
if (!in_array($ext, ['xlsx', 'xls', 'csv'], true)) {
    header('Location: upload_ched_tdp.php?error=invalid_file_type');
    exit;
}

if ($file['size'] > 10 * 1024 * 1024) {
    header('Location: upload_ched_tdp.php?error=file_too_large');
    exit;
}

$uploadsDir = __DIR__ . '/uploads/';
if (!is_dir($uploadsDir) && !mkdir($uploadsDir, 0775, true)) {
    header('Location: upload_ched_tdp.php?error=file_move_failed&details=' . urlencode('Cannot create uploads folder'));
    exit;
}

$uploadedFileName = basename($file['name']);
$targetFilePath = $uploadsDir . $uploadedFileName;

if (!move_uploaded_file($file['tmp_name'], $targetFilePath)) {
    header('Location: upload_ched_tdp.php?error=file_move_failed');
    exit;
}

$logFile = __DIR__ . '/error_log.txt';
function chairmanUploadLog(string $message): void
{
    global $logFile;
    error_log('[' . date('Y-m-d H:i:s') . '] ' . $message . PHP_EOL, 3, $logFile);
}

try {
    $spreadsheet = IOFactory::load($targetFilePath);
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
        throw new RuntimeException('Prepare failed: ' . $conn->error);
    }

    $conn->begin_transaction();
    $inserted = 0;
    $skipped = 0;

    foreach ($spreadsheet->getAllSheets() as $sheet) {
        $rows = $sheet->toArray(null, true, true, true);
        if (count($rows) < 3) {
            continue;
        }
        $dataRows = array_slice($rows, 3);
        $sheetName = $campus;

        foreach ($dataRows as $row) {
            if (empty(trim((string) ($row['A'] ?? '')))) {
                break;
            }
            if (!array_filter($row, static fn($v) => trim((string) $v) !== '')) {
                $skipped++;
                continue;
            }

            $stmt->bind_param(
                'sssssssssssssssss',
                $sheetName,
                (string) ($row['A'] ?? ''),
                (string) ($row['B'] ?? ''),
                (string) ($row['C'] ?? ''),
                (string) ($row['D'] ?? ''),
                (string) ($row['E'] ?? ''),
                (string) ($row['F'] ?? ''),
                (string) ($row['G'] ?? ''),
                (string) ($row['H'] ?? ''),
                (string) ($row['I'] ?? ''),
                (string) ($row['J'] ?? ''),
                (string) ($row['K'] ?? ''),
                (string) ($row['L'] ?? ''),
                (string) ($row['M'] ?? ''),
                (string) ($row['N'] ?? ''),
                $uploadedFileName,
                $fileGroupFull
            );

            if (!$stmt->execute()) {
                throw new RuntimeException('Insert failed: ' . $stmt->error);
            }
            $inserted++;
        }
    }

    $stmt->close();
    $conn->commit();

    require_once __DIR__ . '/../../inc/schogms_file_group_meta.php';
    schogms_file_group_meta_register($conn, 'tdp', $campus, $fileGroupFull, 'approved', schogms_file_group_meta_uploader_from_session());

    @unlink($targetFilePath);

    $msg = "Uploaded {$inserted} scholar record(s) for {$campus}.";
    if ($skipped > 0) {
        $msg .= " Skipped {$skipped} empty row(s).";
    }
    header('Location: upload_ched_tdp.php?success=' . urlencode($msg));
    exit;
} catch (Throwable $e) {
    $conn->rollback();
    chairmanUploadLog('Chairman TDP upload: ' . $e->getMessage());
    if (isset($targetFilePath) && is_file($targetFilePath)) {
        @unlink($targetFilePath);
    }
    header('Location: upload_ched_tdp.php?error=processing_failed&details=' . urlencode($e->getMessage()));
    exit;
}
