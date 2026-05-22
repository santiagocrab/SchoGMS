<?php
session_start();
require 'config/conn.php';
require __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/../inc/admin_user_create.php';

date_default_timezone_set('Asia/Manila');

$response = ['success' => false];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userName = trim($_POST['userName'] ?? '');
    $userEmail = trim($_POST['userEmail'] ?? '');
    $userRole = trim($_POST['userRole'] ?? '');
    $userCampus = trim($_POST['userCampus'] ?? '');

    $response = schogms_admin_create_user($conn, $userName, $userEmail, $userRole, $userCampus);
    $conn->close();
}

echo json_encode($response);
