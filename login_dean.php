<?php
session_start();
// Include database connection
include 'conn.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Retrieve form inputs
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // Prepare the SQL query to fetch user
    $sql = "SELECT id, dean, email, password, status, course_program, campus 
            FROM assigned_dean 
            WHERE dean = ?";
    
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            // Bind result variables
            $stmt->bind_result($user_id, $program_chair, $email, $db_password, $status, $course_program, $campus);
            $stmt->fetch();

            // Verify the password
            if (password_verify($password, $db_password)) {
                if ($status === 'pending') {
                    // If the account is not activated yet
                    header("Location: login-dean.php?ERROR=pending");
                    exit();
                }

                // Store user information in session
                $_SESSION['user_id'] = $user_id;
                $_SESSION['username'] = $program_chair;
                $_SESSION['user_email'] = $email;
                $_SESSION['course_program'] = $course_program;
                $_SESSION['campus'] = $campus;

                // Redirect the chair to their dashboard
                header("Location: users/dean/");
                exit();
            } else {
                // Incorrect password
                header('Location: login-dean.php?ERROR=wrong_password');
                exit();
            }
        } else {
            // Email not found
            header('Location: login-dean.php?ERROR=user_not_found');
            exit();
        }

        $stmt->close();
    } else {
        // SQL error
        header('Location: login-dean.php?ERROR=sql_error');
        exit();
    }
}

$conn->close();
?>
