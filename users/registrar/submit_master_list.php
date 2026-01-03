<?php

// Turn off error display to prevent HTML output
error_reporting(0);
ini_set('display_errors', 0);

// Set JSON header first
header('Content-Type: application/json');

// Start output buffering to catch any unexpected output
ob_start();

// Require necessary dependencies
try {
    require '../../conn_mongodb.php';
    require '../vendor/autoload.php';
} catch (Exception $e) {
    ob_clean();
    echo json_encode(['success' => false, 'error' => 'Failed to load dependencies: ' . $e->getMessage()]);
    exit;
}

use PhpOffice\PhpSpreadsheet\IOFactory;

// Enable error logging but not display
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

    $sheet_name = isset($_POST['session_campus']) ? trim($_POST['session_campus']) : '';
    $file_group = isset($_POST['file_group']) ? trim($_POST['file_group']) : '';
    $academic_year = isset($_POST['academic_year']) ? trim($_POST['academic_year']) : '';
    $semester = isset($_POST['semester']) ? trim($_POST['semester']) : '';
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
    
    // Check if directory is writable
    if (!is_writable($uploadsDir)) {
        logError("Uploads directory is not writable: $uploadsDir");
        echo json_encode(['success' => false, 'error' => 'Uploads directory is not writable.']);
        exit;
    }

    $uploadedFileName = basename($_FILES['excelFile']['name']);
    $targetFilePath = $uploadsDir . $uploadedFileName;

    // Attempt to move uploaded file
    logError("Attempting to move file from: " . $_FILES['excelFile']['tmp_name'] . " to: " . $targetFilePath);
    logError("File size: " . $_FILES['excelFile']['size'] . " bytes");
    logError("Upload error code: " . $_FILES['excelFile']['error']);
    
    if (!move_uploaded_file($_FILES['excelFile']['tmp_name'], $targetFilePath)) {
        $error = 'Failed to save the uploaded file. Check server logs for details.';
        logError($error);
        logError("Upload directory permissions: " . substr(sprintf('%o', fileperms($uploadsDir)), -4));
        logError("Target file path: " . $targetFilePath);
        echo json_encode(['success' => false, 'error' => $error]);
        exit;
    }
    
    logError("File successfully moved to: " . $targetFilePath);

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

        // Get MongoDB collection
        $registrarCollection = $mongodb->collection('registrar_master_list');

        $successCount = 0;
        $errorCount = 0;
        $duplicateCount = 0;

        foreach ($dataRows as $row) {
            if (array_filter($row)) { // Skip empty rows
                // Check if ID number already exists
                $idNumber = $row['E'] ?? '';
                if (!empty($idNumber)) {
                    $existingRecord = $registrarCollection->findOne(['id_number' => $idNumber]);
                    if ($existingRecord) {
                        $duplicateCount++;
                        continue; // Skip this row as it's a duplicate
                    }
                }

                // Create document for MongoDB - Updated to match your CSV structure
                $document = [
                    'campus' => $sheet_name,
                    'file_group' => $file_group,
                    'academic_year' => $academic_year,
                    'semester' => $semester,
                    'filename' => $uploadedFileName,
                    'last_name' => $row['A'] ?? '',           // Last Name
                    'first_name' => $row['B'] ?? '',          // First Name
                    'middle_name' => $row['C'] ?? '',         // Middle Name
                    'ext_name' => $row['D'] ?? '',            // Ext. Name
                    'id_number' => $row['E'] ?? '',           // ID Number
                    'gender' => $row['F'] ?? '',              // Gender
                    'student_type' => $row['G'] ?? '',        // Student Type
                    'year_level' => $row['H'] ?? '',          // Year Level
                    'attended' => $row['I'] ?? '',            // Attended
                    'course' => $row['J'] ?? '',              // Course
                    'curriculum' => $row['K'] ?? '',          // Curriculum
                    'scholarship' => $row['L'] ?? '',         // Scholarship
                    'gpa' => $row['M'] ?? '',                 // GPA
                    'cgpa' => $row['N'] ?? '',                // CGPA
                    'pass_percentage' => $row['O'] ?? '',     // % Pass
                    'grade_remarks' => $row['P'] ?? '',       // Grade Remarks
                    'enrolled' => $row['Q'] ?? '',            // Enrolled
                    'lec_unit' => $row['R'] ?? '',            // Lec. Unit
                    'lab_unit' => $row['S'] ?? '',            // Lab. Unit
                    'cor_printed' => $row['T'] ?? '',         // COR Printed
                    'billing_profile' => $row['U'] ?? '',     // Billing Profile
                    'misc_fee_total' => $row['V'] ?? '',      // Misc. Fee Total
                    'misc_fee_paid' => $row['W'] ?? '',       // Misc. Fee Paid
                    'tuition_fee_total' => $row['X'] ?? '',   // Tuition Fee Total
                    'tuition_fee_paid' => $row['Y'] ?? '',    // Tuition Fee Paid
                    'street' => $row['Z'] ?? '',              // Street
                    'barangay' => $row['AA'] ?? '',           // Barangay
                    'municipality_city' => $row['AB'] ?? '',  // Municipality/City
                    'province' => $row['AC'] ?? '',           // Province
                    'zip_code' => $row['AD'] ?? '',           // Zip Code
                    'date_of_birth' => $row['AE'] ?? '',      // Date of Birth
                    'place_of_birth' => $row['AF'] ?? '',     // Place of Birth
                    'civil_status' => $row['AG'] ?? '',       // Civil Status
                    'tribe' => $row['AH'] ?? '',              // Tribe
                    'religion' => $row['AI'] ?? '',           // Religion
                    'year_admitted' => $row['AJ'] ?? '',      // Year Admitted
                    'semester_admitted' => $row['AK'] ?? '',  // Semester Admitted
                    'school_last_attended' => $row['AL'] ?? '', // School Last Attended
                    'year_last_attended' => $row['AM'] ?? '', // Year Last Attended
                    'semester_last_attended' => $row['AN'] ?? '', // Semester Last Attended
                    'high_school_graduated' => $row['AO'] ?? '', // High School Graduated
                    'exam_date' => $row['AP'] ?? '',          // Exam Date
                    'exam_rating' => $row['AQ'] ?? '',        // Exam Rating
                    'ref_number' => $row['AR'] ?? '',         // Ref. Number
                    'guardian' => $row['AS'] ?? '',           // Guardian
                    'guardian_address' => $row['AT'] ?? '',   // Address
                    'guardian_contact' => $row['AU'] ?? '',   // Contact Number
                    'blood_type' => $row['AV'] ?? '',         // Blood Type
                    'email_address' => $row['AW'] ?? '',      // Email Address
                    'mobile_number' => $row['AX'] ?? '',      // Mobile Number
                    'deped_number' => $row['AY'] ?? '',       // DEPED Number
                    'scholarship_grant' => $row['AZ'] ?? '',  // Scholarship Grant
                    'scholarship_allowance' => $row['BA'] ?? '', // Scholarship Allowance
                    'documents_submitted' => $row['BB'] ?? '', // Documents Submitted
                    'lacking_documents' => $row['BC'] ?? '',  // Lacking Documents
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ];

                try {
                    $result = $registrarCollection->insertOne($document);
                    if ($result) {
                        $successCount++;
                        logError('Successfully inserted document for ID: ' . ($row['E'] ?? 'unknown'));
                    } else {
                        $errorCount++;
                        logError('Error inserting document: ' . json_encode($row));
                    }
                } catch (Exception $e) {
                    $errorCount++;
                    logError('Error inserting document: ' . $e->getMessage() . ' | Row: ' . json_encode($row));
                }
            }
        }

        unlink($targetFilePath); // Remove uploaded file after processing

        $message = "Data processing completed successfully! ";
        $message .= "Records inserted: $successCount, ";
        $message .= "Duplicates skipped: $duplicateCount, ";
        $message .= "Errors: $errorCount";

        // Clean any unexpected output before sending JSON
        ob_clean();
        echo json_encode([
            'success' => true, 
            'message' => $message,
            'stats' => [
                'inserted' => $successCount,
                'duplicates' => $duplicateCount,
                'errors' => $errorCount,
                'total_rows_processed' => count($dataRows),
                'file_name' => $uploadedFileName,
                'campus' => $sheet_name,
                'file_group' => $file_group,
                'academic_year' => $academic_year,
                'semester' => $semester
            ]
        ]);
    } catch (Exception $e) {
        logError('Error processing file: ' . $e->getMessage());
        ob_clean();
        echo json_encode(['success' => false, 'error' => 'Error processing file: ' . $e->getMessage()]);
    }
} else {
    logError('Invalid request method.');
    ob_clean();
    echo json_encode(['success' => false, 'error' => 'Invalid request method.']);
}
?>