<?php
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

if (!($conn instanceof mysqli)) {
    echo json_encode(['success' => false, 'error' => 'Database unavailable']);
    exit;
}

// Enable error reporting for debugging purposes
error_reporting(E_ALL);
ini_set('log_errors', '1');
ini_set('error_log', __DIR__ . '/error_log.txt');

$logFile = __DIR__ . '/error_log.txt'; // Path to error log file

function logError($message)
{
    global $logFile;
    $timestamp = date('Y-m-d H:i:s');
    error_log("[$timestamp] $message" . PHP_EOL, 3, $logFile);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate uploaded file
    if (!isset($_FILES['excelFile']['tmp_name']) || empty($_FILES['excelFile']['tmp_name'])) {
        $error = 'No file uploaded.';
        logError($error);
        echo json_encode(['success' => false, 'error' => $error]);
        exit;
    }

    // Debugging: Log temporary file details
    error_log("Temp file: " . $_FILES['excelFile']['tmp_name']);
    error_log("Original file name: " . $_FILES['excelFile']['name']);

    // Define uploads directory
    $uploadsDir = __DIR__ . '/uploads/';
    if (!is_dir($uploadsDir)) {
        if (!mkdir($uploadsDir, 0777, true)) {
            error_log("Failed to create uploads directory: $uploadsDir");
            $error = 'Failed to create uploads directory.';
            logError($error);
            echo json_encode(['success' => false, 'error' => $error]);
            exit;
        }
    }
    
    $uploadedFileName = basename($_FILES['excelFile']['name']);
    $targetFilePath = $uploadsDir . $uploadedFileName;

    // Debugging: Log target path
    error_log("Target file path: $targetFilePath");

    // Attempt to move uploaded file
    if (!move_uploaded_file($_FILES['excelFile']['tmp_name'], $targetFilePath)) {
        $error = 'Failed to save the uploaded file.';
        logError($error);
        echo json_encode(['success' => false, 'error' => $error]);
        exit;
    }

    try {
        // Load the Excel file
        $spreadsheet = IOFactory::load($targetFilePath);
        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray(null, true, true, true); // Preserve all data and avoid empty rows

        // Ensure file contains enough rows
        if (count($rows) < 3) {
            unlink($targetFilePath); // Remove invalid file
            $error = 'The file does not contain enough rows.';
            logError($error);
            echo json_encode(['success' => false, 'error' => $error]);
            exit;
        }

        // Extract data starting from row 3
        $dataRows = array_slice($rows, 2);

        // Prepare the SQL insert query
        $insertQuery = "
            INSERT INTO billing_table (
                last_name, first_name, scholarship_type, units_enrolled, course, campus, year_and_date_submitted_ched,
                amount, first_semester, second_semester, status, payment_scholarship_type, payment_amount,
                payment_year_and_date, payment_or_number, payment_amount_per_or, refund_first_sem, refund_second_sem,
                refund_year_and_date_released
            ) VALUES (
                ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?
            )
        ";

        $stmt = $conn->prepare($insertQuery);
        if (!$stmt) {
            $error = 'Failed to prepare SQL statement: ' . $conn->error;
            logError($error);
            echo json_encode(['success' => false, 'error' => $error]);
            exit;
        }
        foreach ($dataRows as $row) {
            if (array_filter($row)) { // Skip empty rows
                // Map Excel data to variables
                $last_name = $row['A'] ?? 'N/A';
                $first_name = $row['B'] ?? 'N/A';
                $scholarship_type = $row['C'] ?? 'N/A';
                $units_enrolled = isset($row['D']) ? intval($row['D']) : 0;
                $course = $row['E'] ?? 'N/A';
                $campus = $row['F'] ?? 'N/A';
                $year_and_date_submitted_ched = isset($row['G']) ? date('Y-m-d', strtotime($row['G'])) : null;
                $amount = isset($row['H']) ? floatval(str_replace(',', '', $row['H'])) : 0.0;
                $first_semester = $row['I'] ?? 'N/A';
                $second_semester = $row['J'] ?? 'N/A';
                $status = $row['K'] ?? 'N/A';
                $payment_scholarship_type = $row['L'] ?? 'N/A';
                $payment_amount = isset($row['M']) ? floatval(str_replace(',', '', $row['M'])) : 0.0;
                $payment_year_and_date = isset($row['N']) ? date('Y-m-d', strtotime($row['N'])) : null;
                $payment_or_number = $row['O'] ?? 'N/A';
                $payment_amount_per_or = isset($row['P']) ? floatval(str_replace(',', '', $row['P'])) : 0.0;
                $refund_first_sem = isset($row['Q']) ? floatval(str_replace(',', '', $row['Q'])) : 0.0;
                $refund_second_sem = isset($row['R']) ? floatval(str_replace(',', '', $row['R'])) : 0.0;
                $refund_year_and_date_released = isset($row['S']) ? date('Y-m-d', strtotime($row['S'])) : null;

                // Bind variables to the prepared statement
                $stmt->bind_param(
                    "sssssssdssssdssddds",
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

                // Execute the query
                if (!$stmt->execute()) {
                    logError('Error executing query: ' . $stmt->error . ' | Row: ' . json_encode($row));
                }
            }
        }

        $stmt->close();
        unlink($targetFilePath);

        echo json_encode([
            'success' => true,
            'message' => 'Billing records imported successfully. Rows from Excel row 3 onward were processed.',
        ]);
    } catch (Exception $e) {
        $error = 'Error processing file: ' . $e->getMessage();
        logError($error);
        echo json_encode(['success' => false, 'error' => $error]);
    }
} else {
    $error = 'Invalid request method.';
    logError($error);
    echo json_encode(['success' => false, 'error' => $error]);
}
?>