<?php
/**
 * Single-student TDP validation (legacy AJAX). Prefer bulk_validate_tdp.php.
 */
require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/inc/tdp_bulk_validate.php';

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Invalid request method']);
    exit;
}

if (!($conn instanceof mysqli)) {
    echo json_encode(['success' => false, 'error' => 'Database connection unavailable.']);
    exit;
}

$student_id = (int) ($_POST['student_id'] ?? 0);
$sheet_name = trim((string) ($_POST['sheet_name'] ?? ($sheet_name ?? '')));

if ($student_id <= 0 || $sheet_name === '') {
    echo json_encode(['success' => false, 'error' => 'Missing required parameters']);
    exit;
}

$stmt = $conn->prepare(
    'SELECT cm.*, rm.course AS reg_course, rm.year_level AS reg_year_level, rm.email_address AS reg_email_address
     FROM ched_masterlist cm
     LEFT JOIN registrar_master_list rm
       ON cm.lastname COLLATE utf8mb4_general_ci = rm.last_name COLLATE utf8mb4_general_ci
      AND cm.firstname COLLATE utf8mb4_general_ci = rm.first_name COLLATE utf8mb4_general_ci
      AND (cm.middlename COLLATE utf8mb4_general_ci = rm.middle_name COLLATE utf8mb4_general_ci
           OR cm.middlename IS NULL OR rm.middle_name IS NULL OR cm.middlename = \'\' OR rm.middle_name = \'\')
     WHERE cm.id = ? AND cm.sheet_name = ?
     LIMIT 1'
);
if (!$stmt) {
    echo json_encode(['success' => false, 'error' => 'Query failed']);
    exit;
}
$stmt->bind_param('is', $student_id, $sheet_name);
$stmt->execute();
$row = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$row) {
    echo json_encode(['success' => false, 'error' => 'Student not found']);
    exit;
}

$docIndex = schogms_coordinator_document_index($conn, $sheet_name);
$check = schogms_tdp_row_validation($row, $docIndex);

$status = $check['passed'] ? 'Validated' : 'Failed';
$upd = $conn->prepare('UPDATE ched_masterlist SET validation_status = ? WHERE id = ? AND sheet_name = ?');
if ($upd) {
    $upd->bind_param('sis', $status, $student_id, $sheet_name);
    $upd->execute();
    $upd->close();
}

echo json_encode([
    'success' => true,
    'validation_passed' => $check['passed'],
    'course_match' => $check['course_match'],
    'year_level_match' => $check['year_level_match'],
    'has_cor' => $check['has_cor'],
    'message' => $check['passed']
        ? 'Student validated successfully.'
        : 'Validation failed: course or year level mismatch.',
    'data' => [
        'ched_course' => $row['course_program_enrolled'] ?? '',
        'registrar_course' => $row['reg_course'] ?? '',
        'ched_year_level' => $row['year_level'] ?? '',
        'registrar_year_level' => $row['reg_year_level'] ?? '',
        'email_address' => $row['reg_email_address'] ?? '',
    ],
]);
