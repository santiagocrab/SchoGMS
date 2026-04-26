<?php
// Start session only if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// If the user is not logged in, redirect to the logout page
if (!isset($_SESSION['user_id'])) {
    // Only redirect if headers haven't been sent
    if (!headers_sent()) {
        header("Location: logout.php");
        exit;
    } else {
        echo "<script>window.location.href='logout.php';</script>";
        exit;
    }
}
// MongoDB users collection (local config/conn.php is MySQL-only and does not define $users)
require_once __DIR__ . '/../../../conn_mongodb.php';

// If you want to include user details, you can fetch them from the session
$user_id = $_SESSION['user_id']; // Assuming user_id is stored in the session

// Get user details from MongoDB
try {
    $user = $users->findOne(['user_id' => (int)$user_id]);
    
    if ($user) {
        $user_id = $user['user_id'];  
        $fullname = $user['name'] ?? $user['fullname'] ?? 'Unknown'; 
        $email = $user['email'] ?? ''; 
        $sheet_name = $user['campus'] ?? ''; 
        $role = $user['role'] ?? 'user';
    } else {
        echo "User not found.";
    }
} catch (Exception $e) {
    echo "Error fetching user: " . $e->getMessage();
}

// Close the database connection
// $stmt->close();
// $conn->close();
?>
