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

    // Validate uploaded file
    if ($_FILES['excelFile']['error'] !== UPLOAD_ERR_OK) {
        $error = 'File upload error: ' . $_FILES['excelFile']['error'];
        logError($error);
        echo json_encode(['success' => false, 'error' => $error]);
        exit;
    }

    // Define uploads directory
    $uploadsDir = __DIR__ . '/uploads/';
    if (!is_dir($uploadsDir)) {
        if (!mkdir($uploadsDir, 0777, true)) {
            logError("Failed to create uploads directory: $uploadsDir");
            echo json_encode(['success' => false, 'error' => 'Failed to create uploads directory.']);
            exit;
        }
    }

    $uploadedFileName = basename($_FILES['excelFile']['name']);
    $targetFilePath = $uploadsDir . $uploadedFileName;

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
        $sheets = $spreadsheet->getAllSheets(); // Get all sheets

        // Begin the transaction
        $conn->autocommit(false); // Disable autocommit to manage the transaction manually

        // Prepare the SQL insert query
        $insertQuery = "
        INSERT INTO ched_masterlist_tes (
            sheet_name, seq, app_no,  lastname, firstname,  middlename, 
            batch_no, course_program_enrolled,  year_level, el,	cor,	form2,
            portal, unifastremarks, heiremarks, 
            filename, file_group
        ) VALUES (
            ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?
        )";

        $stmt = $conn->prepare($insertQuery);
        if (!$stmt) {
            logError('Failed to prepare SQL statement: ' . $conn->error);
            echo json_encode(['success' => false, 'error' => 'Failed to prepare SQL statement.']);
            exit;
        }

        // Process each sheet
        foreach ($sheets as $sheet) {
            $sheetName = $sheet->getTitle(); // Get sheet name
            $rows = $sheet->toArray(null, true, true, true); // Preserve all data

            // Ensure file contains enough rows
            if (count($rows) < 3) { // Since data starts at row 3
                continue; // Skip sheet if not enough rows
            }

            // Extract data starting from row 3 (skip headers)
            $dataRows = array_slice($rows, 3);

            foreach ($dataRows as $row) {
                if (empty($row['A'])) { // If column A is empty, stop processing further rows
                    break;
                }

                if (array_filter($row)) { // Skip empty rows
                    // Bind parameters and execute
                    $stmt->bind_param(
                        "sssssssssssssssss",  // Corresponds to 17 placeholders
                        $sheetName, // Sheet Name
                        $row['A'], // SEQ
                        $row['B'], // APP NO
                        $row['C'], // AWARD NO.
                        $row['D'], // LASTNAME
                        $row['E'], // FIRSTNAME
                        $row['F'], // EXTNAME
                        $row['G'], // MIDDLENAME
                        $row['H'], // SEX
                        $row['I'], // BIRTHDATE
                        $row['J'], // COURSE/PROGRAM ENROLLED
                        $row['K'], // YEAR LEVEL
                        $row['L'], // TOTAL UNITS ENROLLED
                        $row['M'], // STATUS OF ENROLLMENT
                        $row['N'], // REMARKS
                        $uploadedFileName, // filename (pass the file name)
                        $file_group  // file_group (pass the file group)
                    );

                    if (!$stmt->execute()) {
                        logError('Error executing query: ' . $stmt->error . ' | Row: ' . json_encode($row));
                        // If error occurs, roll back and exit
                        $conn->rollback();
                        echo json_encode(['success' => false, 'error' => 'Error executing query.']);
                        exit;
                    }
                }
            }
        }

        // Commit the transaction
        $conn->commit();

        // Clean up
        $stmt->close();
        $conn->close();
        // unlink($targetFilePath); // Remove uploaded file after processing

        echo json_encode(['success' => true, 'message' => 'Data successfully uploaded from all sheets to ched_masterlist.']);
    } catch (Exception $e) {
        // Rollback if an exception occurs
        $conn->rollback();
        logError('Error processing file: ' . $e->getMessage());
        echo json_encode(['success' => false, 'error' => 'Error processing file.']);
    }
} else {
    logError('Invalid request method.');
    echo json_encode(['success' => false, 'error' => 'Invalid request method.']);
}
?>
