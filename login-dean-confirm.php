<?php
session_start();
require_once __DIR__ . '/users/dean/config/conn.php';

// Check if required parameters are in the URL
if (!isset($_GET['username']) || !isset($_GET['email']) || !isset($_GET['campus'])) {
    header('Location: index.php?ERROR=1&msg=missing_parameters');
    exit();
}

// Get values from URL
$userName = urldecode($_GET['username']); 
$userEmail = urldecode($_GET['email']); 
$campus = urldecode($_GET['campus']);
$status = "active"; // Set status to active

// Find user by email
$fetchUserQuery = "SELECT id, dean, email, course_program, campus FROM assigned_dean WHERE email = ?";
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
    $updateQuery = "UPDATE assigned_dean SET status = ? WHERE email = ?";
    $updateStmt = $conn->prepare($updateQuery);
    $updateStmt->bind_param("ss", $status, $userEmail);
    $updateStmt->execute();
    $updateStmt->close();

    $_SESSION['auth_type'] = 'mysql_ad';
    $_SESSION['user_id'] = $user_id;
    $_SESSION['username'] = $program_chair;
    $_SESSION['user_email'] = $email;
    $_SESSION['course_program'] = $course_program;
    $_SESSION['campus'] = $campus;
    $_SESSION['role'] = 'dean';

    $conn->close();

    // Redirect to dashboard
    header("Location: users/dean/");
    exit();
}

// Redirect if user is not found
$stmt->close();
$conn->close();
header('Location: index.php?ERROR=1&msg=user_not_found');
exit();
?>
