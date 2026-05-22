<?php
header('Content-Type: application/json; charset=utf-8');

require __DIR__ . '/../config/session.php';

$response = ['success' => false];

if (($role ?? '') !== 'coordinator') {
    $response['message'] = 'Access denied.';
    echo json_encode($response);
    exit;
}

$id = (int) ($_POST['id'] ?? 0);
if ($id < 1) {
    $response['message'] = 'Invalid director id.';
    echo json_encode($response);
    exit;
}

$stmt = $conn->prepare("DELETE FROM users WHERE user_id = ? AND role = 'director' LIMIT 1");
$stmt->bind_param('i', $id);
if ($stmt->execute() && $stmt->affected_rows > 0) {
    $response['success'] = true;
    $response['message'] = 'Director removed.';
} else {
    $response['message'] = 'Director not found or could not be deleted.';
}
$stmt->close();

echo json_encode($response);
