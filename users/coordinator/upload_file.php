<?php
/**
 * Annex 7 upload — stores file for chairman review (no billing row import).
 */
declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../config/session.php';

if (($role ?? '') !== 'coordinator') {
    echo json_encode(['success' => false, 'error' => 'Access denied.']);
    exit;
}

if (!($conn instanceof mysqli)) {
    echo json_encode(['success' => false, 'error' => 'Database unavailable.']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Invalid request method.']);
    exit;
}

$user_id = trim((string) ($_POST['user_id'] ?? ''));
$email = trim((string) ($_POST['email'] ?? ''));
$campus = trim((string) ($_POST['campus'] ?? ''));

if (!isset($_FILES['excelFile']['tmp_name']) || !is_uploaded_file($_FILES['excelFile']['tmp_name'])) {
    echo json_encode(['success' => false, 'error' => 'Please choose a file to upload.']);
    exit;
}

if ($user_id === '' || $email === '' || $campus === '') {
    echo json_encode(['success' => false, 'error' => 'Missing user, email, or campus.']);
    exit;
}

$file = $_FILES['excelFile'];
$originalName = basename((string) $file['name']);
$fileExt = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
$allowedExtensions = ['xls', 'xlsx', 'csv'];

if (!in_array($fileExt, $allowedExtensions, true)) {
    echo json_encode(['success' => false, 'error' => 'Invalid file type. Use .xlsx, .xls, or .csv.']);
    exit;
}

$uploadDir = __DIR__ . '/uploads/annex7/';
if (!is_dir($uploadDir) && !mkdir($uploadDir, 0775, true)) {
    echo json_encode(['success' => false, 'error' => 'Could not create upload folder.']);
    exit;
}

$safeBase = preg_replace('/[^a-zA-Z0-9._-]+/', '_', pathinfo($originalName, PATHINFO_FILENAME)) ?: 'annex7';
$storedName = $safeBase . '_' . date('Ymd_His') . '.' . $fileExt;
$filePath = $uploadDir . $storedName;
$relativePath = 'uploads/annex7/' . $storedName;

$dup = $conn->prepare('SELECT COUNT(*) FROM file_submissions WHERE file_name = ? AND campus = ?');
$dup->bind_param('ss', $originalName, $campus);
$dup->execute();
$dup->bind_result($fileCount);
$dup->fetch();
$dup->close();

if ((int) $fileCount > 0) {
    echo json_encode([
        'success' => false,
        'error' => 'This file name was already submitted for your campus. Rename the file or remove the old submission.',
    ]);
    exit;
}

if (!move_uploaded_file($file['tmp_name'], $filePath)) {
    echo json_encode(['success' => false, 'error' => 'Failed to save the uploaded file.']);
    exit;
}

$stmt = $conn->prepare(
    'INSERT INTO file_submissions (user_id, user_email, campus, file_name, file_path, uploaded_at, status)
     VALUES (?, ?, ?, ?, ?, NOW(), ?)'
);
$status = 'Pending';
$stmt->bind_param('ssssss', $user_id, $email, $campus, $originalName, $relativePath, $status);

if ($stmt->execute()) {
    require_once __DIR__ . '/../../inc/schogms_notifications.php';
    $uploaderId = (int) ($_SESSION['user_id'] ?? $user_id);
    schogms_notify_annex7_submitted($conn, $campus, $originalName, [
        'id' => $uploaderId,
        'name' => trim((string) ($fullname ?? 'Coordinator')),
        'email' => $email,
    ]);

    echo json_encode([
        'success' => true,
        'message' => 'Annex 7 file submitted successfully. Status is Pending until the chairman approves it.',
    ]);
} else {
    @unlink($filePath);
    echo json_encode(['success' => false, 'error' => 'Database error: ' . $stmt->error]);
}
$stmt->close();
