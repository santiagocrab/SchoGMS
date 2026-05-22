<?php
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../config/session.php';

$response = ['success' => false, 'message' => 'Invalid request.'];

if (($role ?? '') !== 'director') {
    $response['message'] = 'Access denied.';
    echo json_encode($response);
    exit;
}

$campus = trim((string) ($sheet_name ?? ''));
if ($campus === '') {
    $response['message'] = 'No campus assigned to your account.';
    echo json_encode($response);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = (int) $_POST['id'];
    if ($id <= 0) {
        $response['message'] = 'Invalid record.';
        echo json_encode($response);
        exit;
    }

    $stmt = $conn->prepare(
        'DELETE FROM assigned_dean WHERE id = ? AND UPPER(TRIM(campus)) = UPPER(TRIM(?))'
    );
    $stmt->bind_param('is', $id, $campus);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        $response = ['success' => true];
    } else {
        $response['message'] = 'Dean not found on your campus or already removed.';
    }
    $stmt->close();
}

echo json_encode($response);
