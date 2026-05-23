<?php
/**
 * Registrar masterlist upload → MySQL registrar_master_list.
 */
declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');
ob_start();

require_once __DIR__ . '/../../config/schogms_helpers.php';
require_once __DIR__ . '/config/conn.php';
require_once __DIR__ . '/../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function registrar_upload_json(bool $success, string $message, array $extra = []): void
{
    ob_clean();
    echo json_encode(array_merge([
        'success' => $success,
        'message' => $message,
        'error' => $success ? '' : $message,
    ], $extra));
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    registrar_upload_json(false, 'Invalid request method.');
}

if (!($conn instanceof mysqli)) {
    registrar_upload_json(false, 'Database connection unavailable.');
}

if (!isset($_FILES['excelFile']) || (int) ($_FILES['excelFile']['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
    registrar_upload_json(false, 'File upload failed. Select an Excel or CSV file.');
}

$campus = trim((string) ($_POST['session_campus'] ?? $_SESSION['sheet_name'] ?? ''));
$fileGroup = trim((string) ($_POST['file_group'] ?? ''));
$academicYear = trim((string) ($_POST['academic_year'] ?? ''));
$semester = trim((string) ($_POST['semester'] ?? ''));

if ($campus === '') {
    registrar_upload_json(false, 'Campus is missing. Log out and sign in again as registrar.');
}
if ($fileGroup === '') {
    registrar_upload_json(false, 'File group name is required.');
}

$uploadsDir = __DIR__ . '/uploads/masterlist/';
$dirCheck = schogms_ensure_writable_upload_dir($uploadsDir);
if (!$dirCheck['ok']) {
    registrar_upload_json(false, $dirCheck['error']);
}

$uploadedFileName = basename((string) $_FILES['excelFile']['name']);
$targetFilePath = $uploadsDir . time() . '_' . preg_replace('/[^a-zA-Z0-9._-]+/', '_', $uploadedFileName);

if (!move_uploaded_file((string) $_FILES['excelFile']['tmp_name'], $targetFilePath)) {
    registrar_upload_json(false, 'Could not save the uploaded file on the server.');
}

try {
    $spreadsheet = IOFactory::load($targetFilePath);
    $rows = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);

    if (count($rows) < 2) {
        @unlink($targetFilePath);
        registrar_upload_json(false, 'The file does not contain enough rows (need header + data).');
    }

    $dataStart = 1;
    foreach (array_slice($rows, 0, 5) as $idx => $headerRow) {
        if (!is_array($headerRow)) {
            continue;
        }
        $a = strtolower(schogms_spreadsheet_cell($headerRow, 'A'));
        if ($a === 'last name' || $a === 'lastname' || str_contains($a, 'last')) {
            $dataStart = $idx + 1;
            break;
        }
    }

    $dataRows = array_slice($rows, $dataStart);

    $sql = 'INSERT INTO registrar_master_list (
        campus, file_group, filename, last_name, first_name, middle_name, ext_name, id_number, gender, student_type,
        year_level, attended, course, curriculum, scholarship, gpa, cgpa, pass_percentage,
        grade_remarks, enrolled, lec_unit, lab_unit, cor_printed, billing_profile, misc_fee_total,
        misc_fee_paid, tuition_fee_total, tuition_fee_paid, street, barangay, municipality_city,
        province, zip_code, date_of_birth, place_of_birth, civil_status, tribe, religion,
        year_admitted, semester_admitted, school_last_attended, year_last_attended,
        semester_last_attended, high_school_graduated, exam_date, exam_rating, ref_number,
        guardian, guardian_address, guardian_contact, blood_type, email_address, mobile_number,
        deped_number, scholarship_grant, scholarship_allowance, documents_submitted, lacking_documents
    ) VALUES (
        ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?
    )';

    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        @unlink($targetFilePath);
        registrar_upload_json(false, 'Database prepare failed: ' . $conn->error);
    }

    $inserted = 0;
    $skipped = 0;
    $errors = 0;

    $conn->begin_transaction();

    foreach ($dataRows as $row) {
        if (!is_array($row) || !array_filter($row, static fn($v) => trim((string) $v) !== '')) {
            continue;
        }

        $lastName = schogms_spreadsheet_cell($row, 'A');
        $firstName = schogms_spreadsheet_cell($row, 'B');
        if ($lastName === '' && $firstName === '') {
            continue;
        }

        $idNumber = schogms_spreadsheet_cell($row, 'E');
        if ($idNumber !== '') {
            $dup = $conn->prepare(
                'SELECT id FROM registrar_master_list WHERE campus = ? AND id_number = ? LIMIT 1'
            );
            if ($dup) {
                $dup->bind_param('ss', $campus, $idNumber);
                $dup->execute();
                $dupRes = $dup->get_result();
                if ($dupRes && $dupRes->num_rows > 0) {
                    $skipped++;
                    $dup->close();
                    continue;
                }
                $dup->close();
            }
        }

        $middleName = schogms_spreadsheet_cell($row, 'C');
        $extName = schogms_spreadsheet_cell($row, 'D');
        $gender = schogms_spreadsheet_cell($row, 'F');
        $studentType = schogms_spreadsheet_cell($row, 'G');
        $yearLevel = schogms_spreadsheet_cell($row, 'H');
        $attended = schogms_spreadsheet_cell($row, 'I');
        $course = schogms_spreadsheet_cell($row, 'J');
        $curriculum = schogms_spreadsheet_cell($row, 'K');
        $scholarship = schogms_spreadsheet_cell($row, 'L');
        $gpa = schogms_spreadsheet_cell($row, 'M');
        $cgpa = schogms_spreadsheet_cell($row, 'N');
        $passPct = schogms_spreadsheet_cell($row, 'O');
        $gradeRemarks = schogms_spreadsheet_cell($row, 'P');
        $enrolled = schogms_spreadsheet_cell($row, 'Q');
        $lecUnit = schogms_spreadsheet_cell($row, 'R');
        $labUnit = schogms_spreadsheet_cell($row, 'S');
        $corPrinted = schogms_spreadsheet_cell($row, 'T');
        $billingProfile = schogms_spreadsheet_cell($row, 'U');
        $miscFeeTotal = schogms_spreadsheet_cell($row, 'V');
        $miscFeePaid = schogms_spreadsheet_cell($row, 'W');
        $tuitionFeeTotal = schogms_spreadsheet_cell($row, 'X');
        $tuitionFeePaid = schogms_spreadsheet_cell($row, 'Y');
        $street = schogms_spreadsheet_cell($row, 'Z');
        $barangay = schogms_spreadsheet_cell($row, 'AA');
        $municipality = schogms_spreadsheet_cell($row, 'AB');
        $province = schogms_spreadsheet_cell($row, 'AC');
        $zipCode = schogms_spreadsheet_cell($row, 'AD');
        $dob = schogms_spreadsheet_cell($row, 'AE');
        $pob = schogms_spreadsheet_cell($row, 'AF');
        $civilStatus = schogms_spreadsheet_cell($row, 'AG');
        $tribe = schogms_spreadsheet_cell($row, 'AH');
        $religion = schogms_spreadsheet_cell($row, 'AI');
        $yearAdmitted = schogms_spreadsheet_cell($row, 'AJ');
        $semAdmitted = schogms_spreadsheet_cell($row, 'AK');
        $schoolLast = schogms_spreadsheet_cell($row, 'AL');
        $yearLast = schogms_spreadsheet_cell($row, 'AM');
        $semLast = schogms_spreadsheet_cell($row, 'AN');
        $hsGrad = schogms_spreadsheet_cell($row, 'AO');
        $examDate = schogms_spreadsheet_cell($row, 'AP');
        $examRating = schogms_spreadsheet_cell($row, 'AQ');
        $refNumber = schogms_spreadsheet_cell($row, 'AR');
        $guardian = schogms_spreadsheet_cell($row, 'AS');
        $guardianAddr = schogms_spreadsheet_cell($row, 'AT');
        $guardianContact = schogms_spreadsheet_cell($row, 'AU');
        $bloodType = schogms_spreadsheet_cell($row, 'AV');
        $email = schogms_spreadsheet_cell($row, 'AW');
        $mobile = schogms_spreadsheet_cell($row, 'AX');
        $deped = schogms_spreadsheet_cell($row, 'AY');
        $schGrant = schogms_spreadsheet_cell($row, 'AZ');
        $schAllow = schogms_spreadsheet_cell($row, 'BA');
        $docsSubmitted = schogms_spreadsheet_cell($row, 'BB');
        $lackingDocs = schogms_spreadsheet_cell($row, 'BC');

        if ($enrolled === '' && $attended !== '') {
            $enrolled = $attended;
        }
        if ($yearAdmitted === '' && $academicYear !== '') {
            $yearAdmitted = $academicYear;
        }
        if ($semAdmitted === '' && $semester !== '') {
            $semAdmitted = $semester;
        }

        $stmt->bind_param(
            'ssssssssssssssssssssssssssssssssssssssssssssssssssssssssss',
            $campus,
            $fileGroup,
            $uploadedFileName,
            $lastName,
            $firstName,
            $middleName,
            $extName,
            $idNumber,
            $gender,
            $studentType,
            $yearLevel,
            $attended,
            $course,
            $curriculum,
            $scholarship,
            $gpa,
            $cgpa,
            $passPct,
            $gradeRemarks,
            $enrolled,
            $lecUnit,
            $labUnit,
            $corPrinted,
            $billingProfile,
            $miscFeeTotal,
            $miscFeePaid,
            $tuitionFeeTotal,
            $tuitionFeePaid,
            $street,
            $barangay,
            $municipality,
            $province,
            $zipCode,
            $dob,
            $pob,
            $civilStatus,
            $tribe,
            $religion,
            $yearAdmitted,
            $semAdmitted,
            $schoolLast,
            $yearLast,
            $semLast,
            $hsGrad,
            $examDate,
            $examRating,
            $refNumber,
            $guardian,
            $guardianAddr,
            $guardianContact,
            $bloodType,
            $email,
            $mobile,
            $deped,
            $schGrant,
            $schAllow,
            $docsSubmitted,
            $lackingDocs
        );

        if ($stmt->execute()) {
            $inserted++;
        } else {
            $errors++;
        }
    }

    $stmt->close();
    $conn->commit();
    @unlink($targetFilePath);

    if ($inserted === 0 && $errors === 0) {
        registrar_upload_json(false, 'No student rows were imported. Check column headers (Last Name in column A).');
    }

    $msg = "Imported {$inserted} scholar(s) for {$campus}.";
    if ($skipped > 0) {
        $msg .= " Skipped {$skipped} duplicate ID(s).";
    }
    if ($errors > 0) {
        $msg .= " {$errors} row(s) failed.";
    }

    registrar_upload_json($inserted > 0, $msg, [
        'stats' => [
            'inserted' => $inserted,
            'duplicates' => $skipped,
            'errors' => $errors,
            'file_group' => $fileGroup,
            'campus' => $campus,
        ],
    ]);
} catch (Throwable $e) {
    if ($conn instanceof mysqli) {
        $conn->rollback();
    }
    @unlink($targetFilePath);
    schogms_log_error('Registrar masterlist upload: ' . $e->getMessage());
    registrar_upload_json(false, 'Error processing file: ' . $e->getMessage());
}
