<?php
session_start();

// If the user is not logged in, redirect to the logout page
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: logout.php');
    exit;
}

// Check if session has expired (optional - 8 hours)
$session_timeout = 8 * 60 * 60; // 8 hours in seconds
if (isset($_SESSION['login_time']) && (time() - $_SESSION['login_time']) > $session_timeout) {
    // Session expired
    session_destroy();
    header('Location: logout.php');
    exit;
}

// Include your database connection
include 'conn.php';

// If you want to include user details, you can fetch them from the session
$user_id = $_SESSION['admin_id']; // Assuming user_id is stored in the session

try {
    // Select user details from the database
    $sql = "SELECT * FROM admin WHERE admin_id = ?";
    $stmt = $conn->prepare($sql);
    
    if (!$stmt) {
        throw new Exception("Database error: " . $conn->error);
    }
    
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if the user exists
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        // Now you can access user details like $row['username'], $row['email'], etc.
        // Add more details as needed
    } else {
        // User not found in database, destroy session
        session_destroy();
        header('Location: logout.php');
        exit;
    }
} catch (Exception $e) {
    // Log error for debugging
    error_log("Session error: " . $e->getMessage());
    // Continue execution even if database query fails
} finally {
    // Close the database connection
    if (isset($stmt)) {
        $stmt->close();
    }
    if (isset($conn)) {
        $conn->close();
    }
}
?>
