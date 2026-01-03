<?php
session_start();

// If the user is not logged in, redirect to the logout page
if (!isset($_SESSION['logged_in'])) {
    header('Location: logout.php');
    exit;
}

// Include your database connection
include 'conn.php'; // Update with your actual database connection script

// If you want to include user details, you can fetch them from the session
$user_id = $_SESSION['admin_id']; // Assuming user_id is stored in the session

// Select user details from the database
$sql = "SELECT * FROM admin WHERE admin_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

// Check if the user exists
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    // Now you can access user details like $row['username'], $row['email'], etc.
    // Add more details as needed
} else {
    echo "User not found.";
}

// Close the database connection
$stmt->close();
$conn->close();
?>
