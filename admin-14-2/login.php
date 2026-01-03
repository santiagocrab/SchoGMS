<?php
session_start(); // Start the session

include "config/conn.php"; // Include database connection

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Prepare and execute SQL query to fetch admin data
    $stmt = $conn->prepare("SELECT * FROM admin WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    // Check if a row is returned
    if ($result->num_rows == 1) {
        // Fetch the row
        $row = $result->fetch_assoc();
        // Verify password
        if (password_verify($password, $row['password'])) {
            // Admin login successful, create session
            $_SESSION['admin_id'] = $row['admin_id'];
            $_SESSION['username'] = $row['username'];
            $_SESSION['logged_in'] = true;
            header('Location: dashboard.php'); // Redirect to admin dashboard
            exit();
        } else {
            // Invalid password, redirect back to login page
            $_SESSION['login_error'] = 'Invalid username or password';
            header('Location: index.html');
            exit();
        }
    } else {
        // Username not found, redirect back to login page
        $_SESSION['login_error'] = 'Invalid username or password';
        header('Location: index.html');
        exit();
    }
}
// Close database connection
$conn->close();

?>
