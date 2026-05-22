<?php
header('Content-Type: application/json; charset=utf-8');

require __DIR__ . '/../config/session.php';
require __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../../config/mail.php';
require_once __DIR__ . '/../../inc/campus_access.php';

$response = ['success' => false];

if (($role ?? '') !== 'coordinator') {
    $response['message'] = 'Access denied.';
    echo json_encode($response);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $response['message'] = 'Invalid request.';
    echo json_encode($response);
    exit;
}

$userName = trim($_POST['director_name'] ?? '');
$userEmail = trim($_POST['director_email'] ?? '');
$campus = trim($_POST['campus'] ?? '');

if ($userName === '' || $userEmail === '' || $campus === '') {
    $response['message'] = 'Director name, email, and campus are required.';
    echo json_encode($response);
    exit;
}

$allowedCampuses = schogms_campus_catalog_names();
$campusOk = false;
foreach ($allowedCampuses as $c) {
    if (strcasecmp($c, $campus) === 0) {
        $campus = $c;
        $campusOk = true;
        break;
    }
}
if (!$campusOk) {
    $response['message'] = 'Invalid campus selected.';
    echo json_encode($response);
    exit;
}

schogms_ensure_campus_access_tables($conn);

$stmt = $conn->prepare(
    "SELECT user_id FROM users WHERE role = 'director' AND UPPER(TRIM(campus)) = UPPER(TRIM(?)) AND status = 'active' LIMIT 1"
);
$stmt->bind_param('s', $campus);
$stmt->execute();
$stmt->store_result();
if ($stmt->num_rows > 0) {
    $stmt->close();
    $response['message'] = 'An active director is already assigned to this campus. Remove or deactivate them first.';
    echo json_encode($response);
    exit;
}
$stmt->close();

$stmt = $conn->prepare('SELECT user_id FROM users WHERE LOWER(TRIM(email)) = LOWER(TRIM(?)) LIMIT 1');
$stmt->bind_param('s', $userEmail);
$stmt->execute();
$stmt->store_result();
if ($stmt->num_rows > 0) {
    $stmt->close();
    $response['message'] = 'Email is already registered.';
    echo json_encode($response);
    exit;
}
$stmt->close();

$defaultPassword = schogms_default_user_password();
$hashedPassword = password_hash($defaultPassword, PASSWORD_DEFAULT);

$sql = "INSERT INTO users (name, email, role, campus, password, email_verified, status)
        VALUES (?, ?, 'director', ?, ?, 1, 'active')";
$stmt = $conn->prepare($sql);
$stmt->bind_param('ssss', $userName, $userEmail, $campus, $hashedPassword);

if ($stmt->execute()) {
    $html = schogms_email_role_assignment([
        'role_title' => 'Campus Director Assignment',
        'name' => $userName,
        'email' => $userEmail,
        'course' => 'Campus: ' . $campus,
        'campus' => $campus,
        'password' => $defaultPassword,
        'confirm_url' => schogms_app_base_url() . '/index.php',
    ]);
    $sent = schogms_send_mail(
        $userEmail,
        'Director Assignment — SchoGMS Login Details',
        $html,
        $userName,
        'SchoGMS'
    );
    $response['success'] = true;
    $response['message'] = $sent['ok']
        ? 'Director created successfully. Login email sent.'
        : 'Director created but email could not be sent. Share credentials manually.';
    $response['email_sent'] = $sent['ok'];
} else {
    $response['message'] = 'Error creating director: ' . $conn->error;
}

$stmt->close();
echo json_encode($response);
