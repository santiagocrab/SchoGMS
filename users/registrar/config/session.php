<?php
session_start();

// If the user is not logged in, redirect to the logout page
if (!isset($_SESSION['user_id'])) {
    header("Location: logout.php");
    exit;
}

// Include MongoDB connection
require '../../conn_mongodb.php';

// If you want to include user details, you can fetch them from the session
$user_id = $_SESSION['user_id']; // Assuming user_id is stored in the session

// Select user details from MongoDB
try {
    $usersCollection = $mongodb->collection('users');
    $user = $usersCollection->findOne(['user_id' => (int)$user_id]);
    
    if ($user) {
        $user_id = $user['user_id'];  
        $fullname = $user['name']; 
        $email = $user['email']; 
        $sheet_name = $user['campus']; 
        $role = $user['role'];
    } else {
        echo "User not found.";
    }
} catch (Exception $e) {
    echo "Database error: " . $e->getMessage();
}
?>
