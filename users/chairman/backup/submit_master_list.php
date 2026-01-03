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
$file_group = filter_input(INPUT_POST, 'file_group', FILTER_SANITIZE_SPECIAL_CHARS);

    // Validate uploaded file
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
        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray(null, true, true, true); // Preserve all data

        // Ensure file contains enough rows
        if (count($rows) < 2) {
            unlink($targetFilePath); // Remove invalid file
            $error = 'The file does not contain enough rows.';
            logError($error);
            echo json_encode(['success' => false, 'error' => $error]);
            exit;
        }

        // Extract data starting from row 2 (skip headers)
        $dataRows = array_slice($rows, 1);

        // Establish database connection
        include 'config/conn.php';

        // Prepare the SQL insert query
        $insertQuery = "
            INSERT INTO registrar_master_list (
                file_group, filename, last_name, first_name, middle_name, ext_name, id_number, gender, student_type,
                year_level, attended, course, curriculum, scholarship, gpa, cgpa, pass_percentage,
                grade_remarks, enrolled, lec_unit, lab_unit, cor_printed, billing_profile, misc_fee_total,
                misc_fee_paid, tuition_fee_total, tuition_fee_paid, street, barangay, municipality_city,
                province, zip_code, date_of_birth, place_of_birth, civil_status, tribe, religion,
                year_admitted, semester_admitted, school_last_attended, year_last_attended,
                semester_last_attended, high_school_graduated, exam_date, exam_rating, ref_number,
                guardian, guardian_address, guardian_contact, blood_type, email_address, mobile_number,
                deped_number, scholarship_grant, scholarship_allowance, documents_submitted, lacking_documents
            ) VALUES (
                ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?
            )
        ";

        $stmt = $conn->prepare($insertQuery);
        if (!$stmt) {
            logError('Failed to prepare SQL statement: ' . $conn->error);
            echo json_encode(['success' => false, 'error' => 'Failed to prepare SQL statement.']);
            exit;
        }

        foreach ($dataRows as $row) {
            if (array_filter($row)) { // Skip empty rows
                // Map Excel data to database columns (A to BA)
                $stmt->bind_param(
                    "ssssssssssssssddddsdsdsdddddsssssssssssssssssssssssssssss",
                    $file_group,
                    $uploadedFileName,
                    $row['A'],
                    $row['B'],
                    $row['C'],
                    $row['D'],
                    $row['E'],
                    $row['F'],
                    $row['G'],
                    $row['H'],
                    $row['I'],
                    $row['J'],
                    $row['K'],
                    $row['L'],
                    $row['M'],
                    $row['N'],
                    $row['O'],
                    $row['P'],
                    $row['Q'],
                    $row['R'],
                    $row['S'],
                    $row['T'],
                    $row['U'],
                    $row['V'],
                    $row['W'],
                    $row['X'],
                    $row['Y'],
                    $row['Z'],
                    $row['AA'],
                    $row['AB'],
                    $row['AC'],
                    $row['AD'],
                    $row['AE'],
                    $row['AF'],
                    $row['AG'],
                    $row['AH'],
                    $row['AI'],
                    $row['AJ'],
                    $row['AK'],
                    $row['AL'],
                    $row['AM'],
                    $row['AN'],
                    $row['AO'],
                    $row['AP'],
                    $row['AQ'],
                    $row['AR'],
                    $row['AS'],
                    $row['AT'],
                    $row['AU'],
                    $row['AV'],
                    $row['AW'],
                    $row['AX'],
                    $row['AY'],
                    $row['AZ'],
                    $row['BA'],
                    $row['BB'],
                    $row['BC'],
                );

                if (!$stmt->execute()) {
                    logError('Error executing query: ' . $stmt->error . ' | Row: ' . json_encode($row));
                }
            }
        }

        $stmt->close();
        $conn->close();
        unlink($targetFilePath); // Remove uploaded file after processing

        echo json_encode(['success' => true, 'message' => 'Data successfully uploaded to registrar_master_list.']);
    } catch (Exception $e) {
        logError('Error processing file: ' . $e->getMessage());
        echo json_encode(['success' => false, 'error' => 'Error processing file.']);
    }
} else {
    logError('Invalid request method.');
    echo json_encode(['success' => false, 'error' => 'Invalid request method.']);
}
?>