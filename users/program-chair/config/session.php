<?php
session_start();

// If the user is not logged in, redirect to the logout page
if (!isset($_SESSION['user_id'])) {
    header("Location: logout.php");
    exit;
}
// Include your database connection
include 'conn.php'; // Update with your actual database connection script

// If you want to include user details, you can fetch them from the session
$user_id = $_SESSION['user_id']; // Assuming user_id is stored in the session

// Select user details from the database
$sql = "SELECT * FROM assigned_program_chairs WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

// Check if the user exists
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $user_id = $row['id'];
    $campus = $row['campus'];
    $course_program = $row['course_program'];
    $program_chair = $row['program_chair'];
    $email = $row['email'];
    $password = $row['password'];
    $status = $row['status'];
    $assigned_at = $row['assigned_at'];
} else {
    echo "User not found.";
}

// Close the database connection
// $stmt->close();
// $conn->close();
?>