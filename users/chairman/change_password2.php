<?php

require 'config/conn.php'; // Ensure your database connection file is included
require 'config/session.php';
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'User not logged in.']);
    exit;
}

$user_id = $_SESSION['user_id'];
$currentPassword = $_POST['currentPassword'];
$newPassword = $_POST['newPassword'];
$confirmPassword = $_POST['confirmPassword'];

// Check if new password and confirm password match
if ($newPassword !== $confirmPassword) {
    echo json_encode(['status' => 'error', 'message' => 'New passwords do not match.']);
    exit;
}

// Fetch current password from database
$query = "SELECT password FROM users WHERE user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    $stmt->bind_result($hashedPassword);
    $stmt->fetch();

    // Verify current password
    if (!password_verify($currentPassword, $hashedPassword)) {
        echo json_encode(['status' => 'error', 'message' => 'Current password is incorrect.']);
        exit;
    }

    // Hash the new password
    $newHashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

    // Update password in database
    $updateQuery = "UPDATE users SET password = ? WHERE user_id = ?";
    $updateStmt = $conn->prepare($updateQuery);
    $updateStmt->bind_param("si", $newHashedPassword, $user_id);

    if ($updateStmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Password changed successfully.']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to update password.']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'User not found.']);
}
?>
