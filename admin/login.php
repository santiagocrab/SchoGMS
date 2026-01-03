<?php
session_start(); // Start the session

include "config/conn.php"; // Include database connection

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve and sanitize form data
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    // Validate input
    if (empty($username) || empty($password)) {
        header('Location: index.html?error=' . urlencode('Please enter both username and password'));
        exit();
    }

    try {
        // Prepare and execute SQL query to fetch admin data
        $stmt = $conn->prepare("SELECT * FROM admin WHERE username = ?");
        if (!$stmt) {
            throw new Exception("Database error: " . $conn->error);
        }
        
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
                $_SESSION['login_time'] = time();
                
                // Close statement and connection
                $stmt->close();
                $conn->close();
                
                header('Location: dashboard.php'); // Redirect to admin dashboard
                exit();
            } else {
                // Invalid password
                header('Location: index.html?error=' . urlencode('Invalid username or password'));
                exit();
            }
        } else {
            // Username not found
            header('Location: index.html?error=' . urlencode('Invalid username or password'));
            exit();
        }
    } catch (Exception $e) {
        // Log error for debugging (in production, you might want to log this to a file)
        error_log("Login error: " . $e->getMessage());
        header('Location: index.html?error=' . urlencode('System error. Please try again later.'));
        exit();
    } finally {
        // Ensure statement and connection are closed
        if (isset($stmt)) {
            $stmt->close();
        }
        if (isset($conn)) {
            $conn->close();
        }
    }
}

// If not POST request, redirect to login page
header('Location: index.html');
exit();
?>
