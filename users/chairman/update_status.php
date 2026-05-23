<?php
/**
 * Chairman: approve / decline Annex 7 (file_submissions).
 */
declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');

require __DIR__ . '/config/session.php';
require_once __DIR__ . '/../../inc/schogms_annex7.php';
require_once __DIR__ . '/../../inc/schogms_notifications.php';

if (($role ?? '') !== 'chairman') {
    echo json_encode(['success' => false, 'error' => 'Access denied.']);
    exit;
}

if (!($conn instanceof mysqli)) {
    echo json_encode(['success' => false, 'error' => 'Database unavailable.']);
    exit;
}

$response = ['success' => false];

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['file_id'], $_POST['status'])) {
    echo json_encode(['success' => false, 'error' => 'Invalid request.']);
    exit;
}

$fileId = (int) $_POST['file_id'];
$status = schogms_annex7_normalize_status((string) $_POST['status']);

if ($fileId < 1 || !in_array($status, ['Approved', 'Pending', 'Rejected'], true)) {
    echo json_encode(['success' => false, 'error' => 'Invalid data provided.']);
    exit;
}

$row = schogms_annex7_fetch($conn, $fileId);
if ($row === null) {
    echo json_encode(['success' => false, 'error' => 'Submission not found.']);
    exit;
}

$updateStmt = $conn->prepare('UPDATE file_submissions SET status = ? WHERE id = ?');
if (!$updateStmt) {
    echo json_encode(['success' => false, 'error' => 'Database error.']);
    exit;
}
$updateStmt->bind_param('si', $status, $fileId);

if ($updateStmt->execute()) {
    $response['success'] = true;
    $reviewer = trim((string) ($fullname ?? 'Chairman'));
    if ($status === 'Approved' || $status === 'Rejected') {
        schogms_notify_annex7_reviewed($conn, $fileId, $status, $reviewer);
    }
} else {
    $response['error'] = 'Database error: ' . $updateStmt->error;
}

$updateStmt->close();
echo json_encode($response);
