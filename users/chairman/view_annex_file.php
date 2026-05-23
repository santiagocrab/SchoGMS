<?php
/**
 * Stream an Annex 7 submission for in-browser preview (chairman only).
 */
require __DIR__ . '/config/session.php';

if (($role ?? '') !== 'chairman') {
    http_response_code(403);
    exit('Access denied.');
}

$id = (int) ($_GET['id'] ?? 0);
if ($id < 1) {
    http_response_code(400);
    exit('Invalid file id.');
}

$stmt = $conn->prepare(
    'SELECT file_name, file_path FROM file_submissions WHERE id = ? LIMIT 1'
);
$stmt->bind_param('i', $id);
$stmt->execute();
$row = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$row) {
    http_response_code(404);
    exit('File not found.');
}

$relative = str_replace('\\', '/', (string) ($row['file_path'] ?? ''));
if ($relative === '' || str_contains($relative, '..')) {
    http_response_code(400);
    exit('Invalid file path.');
}

$coordinatorRoot = realpath(__DIR__ . '/../coordinator');
$filePath = realpath(__DIR__ . '/../coordinator/' . $relative);

if ($coordinatorRoot === false || $filePath === false || !is_file($filePath)) {
    http_response_code(404);
    exit('File missing on server.');
}

if (!str_starts_with($filePath, $coordinatorRoot)) {
    http_response_code(403);
    exit('Path not allowed.');
}

$fileName = (string) ($row['file_name'] ?? basename($filePath));
$ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

$types = [
    'csv'  => 'text/csv; charset=UTF-8',
    'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
    'xls'  => 'application/vnd.ms-excel',
];
$contentType = $types[$ext] ?? 'application/octet-stream';

header('Content-Type: ' . $contentType);
header('Content-Length: ' . (string) filesize($filePath));
header('Content-Disposition: inline; filename="' . rawurlencode($fileName) . '"');
header('X-Content-Type-Options: nosniff');
header('Cache-Control: private, max-age=60');

readfile($filePath);
exit;
