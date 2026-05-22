<?php
/**
 * Coordinator COR / COG document upload (MySQL document_uploads).
 */
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../config/session.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
    exit;
}

if (!($conn instanceof mysqli)) {
    echo json_encode(['success' => false, 'message' => 'Database connection unavailable.']);
    exit;
}

$category = strtoupper(trim((string) ($_POST['category'] ?? '')));
$fileGroup = trim((string) ($_POST['fileGroup'] ?? ''));
$campus = trim((string) ($_POST['campus'] ?? ($sheet_name ?? '')));

if (!in_array($category, ['COR', 'COG'], true)) {
    echo json_encode(['success' => false, 'message' => 'Invalid document category.']);
    exit;
}
if ($fileGroup === '') {
    echo json_encode(['success' => false, 'message' => 'File group is required.']);
    exit;
}
if ($campus === '') {
    echo json_encode(['success' => false, 'message' => 'Campus is required.']);
    exit;
}

$files = $_FILES['fileUpload'] ?? null;
if (!$files || !is_array($files['name']) || count(array_filter($files['name'])) === 0) {
    echo json_encode(['success' => false, 'message' => 'Select at least one file to upload.']);
    exit;
}

$allowedExt = ['pdf', 'jpg', 'jpeg', 'png'];
$maxBytes = 15 * 1024 * 1024;
$uploadRoot = __DIR__ . '/uploads/' . $category . '/';
if (!is_dir($uploadRoot) && !mkdir($uploadRoot, 0755, true) && !is_dir($uploadRoot)) {
    echo json_encode(['success' => false, 'message' => 'Could not create upload directory.']);
    exit;
}

$successFiles = [];
$errorFiles = [];

$stmt = $conn->prepare(
    'INSERT INTO document_uploads (campus, file_group, category, file_name, file_path) VALUES (?, ?, ?, ?, ?)'
);
if (!$stmt) {
    echo json_encode(['success' => false, 'message' => 'Database error.']);
    exit;
}

foreach ($files['name'] as $index => $originalName) {
    $originalName = trim((string) $originalName);
    if ($originalName === '') {
        continue;
    }
    $tmp = $files['tmp_name'][$index] ?? '';
    $err = (int) ($files['error'][$index] ?? UPLOAD_ERR_NO_FILE);
    $size = (int) ($files['size'][$index] ?? 0);

    if ($err !== UPLOAD_ERR_OK || !is_uploaded_file($tmp)) {
        $errorFiles[] = $originalName . ' (upload failed)';
        continue;
    }
    if ($size <= 0 || $size > $maxBytes) {
        $errorFiles[] = $originalName . ' (invalid or too large; max 15 MB)';
        continue;
    }

    $ext = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
    if (!in_array($ext, $allowedExt, true)) {
        $errorFiles[] = $originalName . ' (only PDF, JPG, PNG allowed)';
        continue;
    }

    $safeBase = preg_replace('/[^a-zA-Z0-9,_\-\. ]+/', '_', basename($originalName));
    $storedName = $safeBase;
    $destPath = $uploadRoot . $storedName;
    if (file_exists($destPath)) {
        $storedName = time() . '_' . $safeBase;
        $destPath = $uploadRoot . $storedName;
    }

    if (!move_uploaded_file($tmp, $destPath)) {
        $errorFiles[] = $originalName . ' (could not save file)';
        continue;
    }

    $dbPath = 'uploads/' . $category . '/' . $storedName;
    $dbFileName = basename($originalName);

    $stmt->bind_param('sssss', $campus, $fileGroup, $category, $dbFileName, $dbPath);
    if ($stmt->execute()) {
        $successFiles[] = $dbFileName;
    } else {
        @unlink($destPath);
        $errorFiles[] = $originalName . ' (database insert failed)';
    }
}

$stmt->close();

if (count($successFiles) === 0) {
    $detail = count($errorFiles) > 0 ? implode('; ', array_slice($errorFiles, 0, 5)) : 'No files processed.';
    echo json_encode(['success' => false, 'message' => 'Upload failed. ' . $detail]);
    exit;
}

$message = count($successFiles) . ' file(s) uploaded successfully.';
if (count($errorFiles) > 0) {
    $message .= ' Some files had errors: ' . implode('; ', array_slice($errorFiles, 0, 5));
}

echo json_encode([
    'success' => true,
    'message' => $message,
    'uploaded' => count($successFiles),
]);
