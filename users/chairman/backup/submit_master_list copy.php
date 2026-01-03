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

// ✅ Name validation function (allows letters, Ññ, accents, hyphens, and spaces)
function validateName($name)
{
    return preg_match("/^[\p{L} \-\.]+$/u", $name);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $file_group = htmlspecialchars(trim($_POST['file_group']), ENT_QUOTES, 'UTF-8');

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

        if (count($rows) < 2) {
            unlink($targetFilePath);
            $error = 'The file does not contain enough rows.';
            logError($error);
            echo json_encode(['success' => false, 'error' => $error]);
            exit;
        }

        $dataRows = array_slice($rows, 1); // Extract data starting from row 2

        include 'config/conn.php';

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

                // ✅ Apply validation to names before inserting into DB
                function cleanName($name)
                {
                    $name = trim($name);
                    return preg_replace("/[^ \p{L}\-.'’]/u", "", $name); // Remove unwanted characters
                }

                // Apply cleaning before inserting into the database
                $last_name = cleanName($row['A'] ?? '');
                $first_name = cleanName($row['B'] ?? '');
                $middle_name = cleanName($row['C'] ?? '');
                $ext_name = cleanName($row['D'] ?? '');


                if (!validateName($last_name) || !validateName($first_name) || !validateName($middle_name) || !validateName($ext_name)) {
                    logError("Invalid name detected: '$last_name', '$first_name', '$middle_name', '$ext_name'");
                    echo json_encode([
                        'success' => false,
                        'error' => "Invalid characters found in: $last_name, $first_name"
                    ]);
                    exit;
                }


                // Bind values to the SQL statement
                $stmt->bind_param(
                    "ssssssssssssssddddsdsdsdddddsssssssssssssssssssssssssssss",
                    $file_group,
                    $uploadedFileName,
                    $last_name,
                    $first_name,
                    $middle_name,
                    $ext_name,
                    $row['E'],  // id_number
                    $row['F'],  // gender
                    $row['G'],  // student_type
                    $row['H'],  // year_level
                    $row['I'],  // attended
                    $row['J'],  // course
                    $row['K'],  // curriculum
                    $row['L'],  // scholarship
                    $row['M'],  // gpa
                    $row['N'],  // cgpa
                    $row['O'],  // pass_percentage
                    $row['P'],  // grade_remarks
                    $row['Q'],  // enrolled
                    $row['R'],  // lec_unit
                    $row['S'],  // lab_unit
                    $row['T'],  // cor_printed
                    $row['U'],  // billing_profile
                    $row['V'],  // misc_fee_total
                    $row['W'],  // misc_fee_paid
                    $row['X'],  // tuition_fee_total
                    $row['Y'],  // tuition_fee_paid
                    $row['Z'],  // street
                    $row['AA'], // barangay
                    $row['AB'], // municipality_city
                    $row['AC'], // province
                    $row['AD'], // zip_code
                    $row['AE'], // date_of_birth
                    $row['AF'], // place_of_birth
                    $row['AG'], // civil_status
                    $row['AH'], // tribe
                    $row['AI'], // religion
                    $row['AJ'], // year_admitted
                    $row['AK'], // semester_admitted
                    $row['AL'], // school_last_attended
                    $row['AM'], // year_last_attended
                    $row['AN'], // semester_last_attended
                    $row['AO'], // high_school_graduated
                    $row['AP'], // exam_date
                    $row['AQ'], // exam_rating
                    $row['AR'], // ref_number
                    $row['AS'], // guardian
                    $row['AT'], // guardian_address
                    $row['AU'], // guardian_contact
                    $row['AV'], // blood_type
                    $row['AW'], // email_address
                    $row['AX'], // mobile_number
                    $row['AY'], // deped_number
                    $row['AZ'], // scholarship_grant
                    $row['BA'], // scholarship_allowance
                    $row['BB'], // documents_submitted
                    $row['BC']  // lacking_documents
                );

                if (!$stmt->execute()) {
                    logError('Error executing query: ' . $stmt->error);
                }
            }
        }

        $stmt->close();
        $conn->close();
        unlink($targetFilePath);

        echo json_encode(['success' => true, 'message' => 'Data successfully uploaded to registrar_master_list.']);
    } catch (Exception $e) {
        logError('Error processing file: ' . $e->getMessage());
        echo json_encode(['success' => false, 'error' => 'Error processing file.']);
    }
}
?>