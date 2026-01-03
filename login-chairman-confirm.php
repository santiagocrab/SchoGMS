<?php
session_start();
require 'conn.php';  // Include database connection

// Check if required parameters are in the URL
if (!isset($_GET['username']) || !isset($_GET['email']) || !isset($_GET['campus'])) {
    header("Location: login-chair.php?ERROR=missing_parameters");
    exit();
}

// Get values from URL
$userName = urldecode($_GET['username']); 
$userEmail = urldecode($_GET['email']); 
$campus = urldecode($_GET['campus']);
$status = "active"; // Set status to active

// Find user by email
$fetchUserQuery = "SELECT id, program_chair, email, course_program, campus FROM assigned_program_chairs WHERE email = ?";
$stmt = $conn->prepare($fetchUserQuery);
$stmt->bind_param("s", $userEmail);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    // User exists, update their status
    $stmt->bind_result($user_id, $program_chair, $email, $course_program, $campus);
    $stmt->fetch();
    $stmt->close();

    // Update user status
    $updateQuery = "UPDATE assigned_program_chairs SET status = ? WHERE email = ?";
    $updateStmt = $conn->prepare($updateQuery);
    $updateStmt->bind_param("ss", $status, $userEmail);
    $updateStmt->execute();
    $updateStmt->close();

    // Store session variables
    $_SESSION['user_id'] = $user_id;
    $_SESSION['username'] = $program_chair;
    $_SESSION['user_email'] = $email;
    $_SESSION['course_program'] = $course_program;
    $_SESSION['campus'] = $campus;

    $conn->close();

    // Redirect to dashboard
    header("Location: users/program-chair/");
    exit();
}

// Redirect if user is not found
$stmt->close();
$conn->close();
header("Location: login-chair.php?ERROR=user_not_found");
exit();
?>
