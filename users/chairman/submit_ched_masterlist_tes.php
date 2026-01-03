<?php
header('Content-Type: application/json');

// Require necessary dependencies
require 'config/conn.php';
require '../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

// Enable error reporting for debugging
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
    $file_group = filter_input(INPUT_POST, 'file_group', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $campus = filter_input(INPUT_POST, 'campus', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
  

    // Validate uploaded file
    if ($_FILES['excelFile']['error'] !== UPLOAD_ERR_OK) {
        $error = 'File upload error: ' . $_FILES['excelFile']['error'];
        logError($error);
        echo json_encode(['success' => false, 'error' => $error]);
        exit;
    }

    // Define uploads directory
    $uploadsDir = __DIR__ . '/uploads/';
    if (!is_dir($uploadsDir) && !mkdir($uploadsDir, 0777, true)) {
        logError("Failed to create uploads directory: $uploadsDir");
        echo json_encode(['success' => false, 'error' => 'Failed to create uploads directory.']);
        exit;
    }

    $uploadedFileName = basename($_FILES['excelFile']['name']);
    $targetFilePath = $uploadsDir . $uploadedFileName;

    // Attempt to move uploaded file
    if (!move_uploaded_file($_FILES['excelFile']['tmp_name'], $targetFilePath)) {
        logError('Failed to save the uploaded file.');
        echo json_encode(['success' => false, 'error' => 'Failed to save the uploaded file.']);
        exit;
    }

    try {
        // Load the Excel file
        $spreadsheet = IOFactory::load($targetFilePath);
        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray(null, true, true, true);

        // Ensure file contains enough rows
        if (count($rows) < 2) {
            logError('No data found in the uploaded file.');
            echo json_encode(['success' => false, 'error' => 'No data found in the uploaded file.']);
            exit;
        }

        // Begin the transaction
        $conn->autocommit(false);

        // Prepare the SQL insert query
        $insertQuery = "INSERT INTO ched_masterlist_tes (
            seq, app_no, lastname, firstname, ext, middlename, sex, course_program_enrolled, year_level, street,
            town_city, contact, batch_no,  campus, filename, file_group
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ? )";

        $stmt = $conn->prepare($insertQuery);
        if (!$stmt) {
            logError('Failed to prepare SQL statement: ' . $conn->error);
            echo json_encode(['success' => false, 'error' => 'Failed to prepare SQL statement.']);
            exit;
        }

        // Extract data starting from row 2 (skip headers)
        $dataRows = array_slice($rows, 1);
        foreach ($dataRows as $row) {
            if (empty($row['A'])) break; // Stop processing if column A is empty
            if (array_filter($row)) { // Ensure row is not empty
                $stmt->bind_param(
                    "ssssssssssssssss",  // Corresponds to 16 placeholders
                    $row['A'], // SEQ
                    $row['B'], // APP NO
                    $row['C'], // LASTNAME
                    $row['D'], // FIRSTNAME
                    $row['E'], // EXTNAME
                    $row['F'], // MIDDLENAME
                    $row['G'], // SEX
                    $row['H'], // COURSE/PROGRAM ENROLLED
                    $row['I'], // YEAR LEVEL
                    $row['J'], // STREET
                    $row['K'], // TOWN/CITY
                    $row['L'], // CONTACT
                    $row['M'], // BATCH NO
                    $campus,
                    $uploadedFileName, // Filename
                    $file_group  // File group
                );

                if (!$stmt->execute()) {
                    logError('Error executing query: ' . $stmt->error . ' | Row: ' . json_encode($row));
                    $conn->rollback();
                    echo json_encode(['success' => false, 'error' => 'Error executing query.']);
                    exit;
                }
            }
        }

        // Commit the transaction
        $conn->commit();
        $stmt->close();
        $conn->close();
        // unlink($targetFilePath); // Remove uploaded file after processing

        echo json_encode(['success' => true, 'message' => 'Data successfully uploaded using campus column.']);
    } catch (Exception $e) {
        $conn->rollback();
        logError('Error processing file: ' . $e->getMessage());
        echo json_encode(['success' => false, 'error' => 'Error processing file.']);
    }
} else {
    logError('Invalid request method.');
    echo json_encode(['success' => false, 'error' => 'Invalid request method.']);
}
?>
