<?php
header('Content-Type: application/json; charset=utf-8');

try {
    require_once __DIR__ . '/../config/session.php';
    require_once __DIR__ . '/inc/masterlist_edit.php';
    require_once __DIR__ . '/inc/masterlist_rows.php';
} catch (Throwable $e) {
    echo json_encode(['success' => false, 'message' => 'Server error: ' . $e->getMessage()]);
    exit;
}

$id = (int) ($_GET['id'] ?? 0);
$program = strtolower(trim((string) ($_GET['program'] ?? 'tdp')));
$campus = trim((string) ($_GET['campus'] ?? ($sheet_name ?? '')));

if ($id <= 0 || $campus === '' || !($conn instanceof mysqli)) {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
    exit;
}

$row = schogms_masterlist_fetch_row($conn, $program, $id, $campus);
if ($row === null) {
    echo json_encode(['success' => false, 'message' => 'Student not found']);
    exit;
}

$docIndex = schogms_coordinator_document_index($conn, $campus);
$docs = schogms_coordinator_resolve_doc($docIndex, $row);

echo json_encode([
    'success' => true,
    'row' => $row,
    'has_cor' => $docs['has_cor'],
    'has_cog' => $docs['has_cog'],
    'cor_path' => $docs['cor_path'],
    'cog_path' => $docs['cog_path'],
]);
