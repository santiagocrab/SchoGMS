<?php
/**
 * JSON API for in-app notifications (chairman, coordinator, registrar).
 */
header('Content-Type: application/json; charset=utf-8');

require __DIR__ . '/config/session.php';
require_once __DIR__ . '/../inc/schogms_notifications.php';

$userId = (int) ($user_id ?? $_SESSION['user_id'] ?? 0);
$role = strtolower(trim((string) ($role ?? $_SESSION['role'] ?? '')));

if ($userId < 1 || !schogms_notifications_role_show_bell($role)) {
    echo json_encode(['success' => false, 'error' => 'Access denied.']);
    exit;
}

if (!isset($conn) || !($conn instanceof mysqli)) {
    $cfg = require __DIR__ . '/../config/schogms_mysql.php';
    $conn = new mysqli($cfg['host'], $cfg['username'], $cfg['password'], $cfg['database']);
    $conn->set_charset('utf8mb4');
}

$action = $_SERVER['REQUEST_METHOD'] === 'POST'
    ? trim((string) ($_POST['action'] ?? ''))
    : trim((string) ($_GET['action'] ?? 'list'));

if ($action === 'mark_read' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int) ($_POST['id'] ?? 0);
    schogms_notifications_mark_read($conn, $userId, $id > 0 ? $id : null);
    echo json_encode([
        'success' => true,
        'unread' => schogms_notifications_unread_count($conn, $userId),
    ]);
    exit;
}

if ($action === 'mark_all_read' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    schogms_notifications_mark_read($conn, $userId, null);
    echo json_encode(['success' => true, 'unread' => 0]);
    exit;
}

$items = schogms_notifications_list($conn, $userId, 30);
$out = [];
foreach ($items as $row) {
    $out[] = [
        'id' => (int) ($row['id'] ?? 0),
        'type' => (string) ($row['type'] ?? ''),
        'title' => (string) ($row['title'] ?? ''),
        'message' => (string) ($row['message'] ?? ''),
        'link_url' => (string) ($row['link_url'] ?? ''),
        'is_read' => (int) ($row['is_read'] ?? 0),
        'created_at' => (string) ($row['created_at'] ?? ''),
    ];
}

echo json_encode([
    'success' => true,
    'unread' => schogms_notifications_unread_count($conn, $userId),
    'items' => $out,
]);
