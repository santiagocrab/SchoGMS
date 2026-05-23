<?php
/**
 * Update one masterlist student + optional COR/COG; sync CSV exports.
 */
header('Content-Type: application/json; charset=utf-8');

try {
    require_once __DIR__ . '/../config/session.php';
    require_once __DIR__ . '/inc/masterlist_edit.php';

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        echo json_encode(['success' => false, 'message' => 'Invalid request']);
        exit;
    }

    if (!($conn instanceof mysqli)) {
        echo json_encode(['success' => false, 'message' => 'Database unavailable']);
        exit;
    }

    $program = strtolower(trim((string) ($_POST['program'] ?? 'tdp')));
    $campus = trim((string) ($_POST['campus'] ?? ($sheet_name ?? '')));
    $id = (int) ($_POST['id'] ?? 0);

    if ($id <= 0 || $campus === '') {
        echo json_encode(['success' => false, 'message' => 'Invalid student or campus']);
        exit;
    }

    if ($program === 'tes') {
        $fields = [
            'seq' => trim((string) ($_POST['seq'] ?? '')),
            'app_no' => trim((string) ($_POST['app_no'] ?? '')),
            'lastname' => trim((string) ($_POST['lastname'] ?? '')),
            'firstname' => trim((string) ($_POST['firstname'] ?? '')),
            'ext' => trim((string) ($_POST['ext'] ?? '')),
            'middlename' => trim((string) ($_POST['middlename'] ?? '')),
            'sex' => trim((string) ($_POST['sex'] ?? '')),
            'course_program_enrolled' => trim((string) ($_POST['course_program_enrolled'] ?? '')),
            'year_level' => trim((string) ($_POST['year_level'] ?? '')),
            'street' => trim((string) ($_POST['street'] ?? '')),
            'town_city' => trim((string) ($_POST['town_city'] ?? '')),
            'contact' => trim((string) ($_POST['contact'] ?? '')),
            'batch_no' => trim((string) ($_POST['batch_no'] ?? '')),
        ];
    } else {
        $program = 'tdp';
        $fields = [
            'seq' => trim((string) ($_POST['seq'] ?? '')),
            'app_no' => trim((string) ($_POST['app_no'] ?? '')),
            'award_no' => trim((string) ($_POST['award_no'] ?? '')),
            'lastname' => trim((string) ($_POST['lastname'] ?? '')),
            'firstname' => trim((string) ($_POST['firstname'] ?? '')),
            'extname' => trim((string) ($_POST['extname'] ?? '')),
            'middlename' => trim((string) ($_POST['middlename'] ?? '')),
            'sex' => trim((string) ($_POST['sex'] ?? '')),
            'birthdate' => trim((string) ($_POST['birthdate'] ?? '')),
            'course_program_enrolled' => trim((string) ($_POST['course_program_enrolled'] ?? '')),
            'year_level' => trim((string) ($_POST['year_level'] ?? '')),
            'total_units_enrolled' => trim((string) ($_POST['total_units_enrolled'] ?? '')),
            'status_of_enrollment' => trim((string) ($_POST['status_of_enrollment'] ?? '')),
            'remarks' => trim((string) ($_POST['remarks'] ?? '')),
        ];
    }

    if ($fields['lastname'] === '' || $fields['firstname'] === '') {
        echo json_encode(['success' => false, 'message' => 'Last name and first name are required']);
        exit;
    }

    $update = schogms_update_masterlist_student($conn, $program, $id, $campus, $fields);
    if (!$update['success']) {
        echo json_encode($update);
        exit;
    }

    $student = schogms_masterlist_fetch_row($conn, $program, $id, $campus);
    if ($student === null) {
        echo json_encode(['success' => false, 'message' => 'Student not found after update']);
        exit;
    }

    $fileGroup = trim((string) ($student['file_group'] ?? ''));
    $docMessages = [];

    if (isset($_FILES['cor_file']) && (int) ($_FILES['cor_file']['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE) {
        $doc = schogms_replace_student_document($conn, $campus, 'COR', $student, $_FILES['cor_file'], $fileGroup);
        $docMessages[] = $doc['message'];
        if (!$doc['success']) {
            echo json_encode(['success' => false, 'message' => implode('; ', $docMessages)]);
            exit;
        }
    }

    if (isset($_FILES['cog_file']) && (int) ($_FILES['cog_file']['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE) {
        $doc = schogms_replace_student_document($conn, $campus, 'COG', $student, $_FILES['cog_file'], $fileGroup);
        $docMessages[] = $doc['message'];
        if (!$doc['success']) {
            echo json_encode(['success' => false, 'message' => implode('; ', $docMessages)]);
            exit;
        }
    }

    $csv = schogms_regenerate_masterlist_csv($conn, $program, $campus);
    echo json_encode([
        'success' => true,
        'message' => $update['message']
            . (count($docMessages) ? ' · ' . implode(' · ', $docMessages) : '')
            . ' · ' . $csv['message'],
        'csv_files' => array_map('basename', $csv['files']),
    ]);
} catch (Throwable $e) {
    echo json_encode(['success' => false, 'message' => 'Save failed: ' . $e->getMessage()]);
}
