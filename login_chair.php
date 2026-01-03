<?php
session_start();
// Include database connection
include 'conn.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Retrieve form inputs
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    try {
        // Find user in MongoDB
        $user = $assigned_program_chairs->findOne(['program_chair' => $username]);
        
        if ($user) {
            // Verify the password
            if (password_verify($password, $user['password'])) {
                if ($user['status'] === 'pending') {
                    // If the account is not activated yet
                    header("Location: login-chair.php?ERROR=pending");
                    exit();
                }

                // Store user information in session
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['program_chair'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['course_program'] = $user['course_program'];
                $_SESSION['campus'] = $user['campus'];

                // Redirect the chair to their dashboard
                header("Location: users/chairman/");
                exit();
            } else {
                // Incorrect password
                header('Location: login-chair.php?ERROR=wrong_password');
                exit();
            }
        } else {
            // User not found
            header('Location: login-chair.php?ERROR=user_not_found');
            exit();
        }
    } catch (Exception $e) {
        // Database error
        header('Location: login-chair.php?ERROR=db_error');
        exit();
    }
}
?>
