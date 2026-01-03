<?php
session_start();
// Include MongoDB database connection
include 'conn_mongodb.php';

// Clear MongoDB cache to ensure fresh data
if (method_exists($users, 'clearCache')) {
    $users->clearCache();
}

// Check if request method is POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Retrieve form inputs
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // Find user in MongoDB - try exact match first, then case-insensitive
    $user = $users->findOne(['name' => $username]);
    
    // If not found, try case-insensitive search
    if (!$user) {
        $allUsers = $users->find([]);
        foreach ($allUsers as $u) {
            if (isset($u['name']) && strcasecmp(trim($u['name']), $username) === 0) {
                $user = $u;
                break;
            }
        }
    }
    
    if ($user) {
        // Extract user data
        $user_id = $user['user_id'];
        $db_username = $user['name'];
        $db_password = $user['password'];
        $db_role = $user['role'];
        $db_status = $user['status'];

        // **Check if user is restricted or pending**
        // Allow coordinators to log in regardless of status (for now)
        if ($db_role !== 'coordinator') {
            if ($db_status === 'restricted') {
                header("Location: index.php?ERROR=restricted");
                exit();
            } elseif ($db_status === 'pending') {
                header("Location: index.php?ERROR=pending");
                exit();
            } elseif ($db_status !== 'active') { // Ensure only "active" users can log in
                header("Location: index.php?ERROR=inactive");
                exit();
            }
        }

        // Verify the password
        // Check if password hash exists
        if (empty($db_password)) {
            // No password set, redirect with error
            header('Location: index.php?ERROR=1&msg=nopassword');
            exit();
        }
        
        if (password_verify($password, $db_password)) {
            // Store user information in the session
            $_SESSION['user_id'] = $user_id;
            $_SESSION['username'] = $db_username;
            $_SESSION['role'] = $db_role;

            // Redirect based on the user's role
            switch ($db_role) {
                case 'coordinator':
                    header("Location: users/coordinator/");
                    break;
                case 'chairman':
                    header("Location: users/chairman/");
                    break;
                case 'registrar':
                    header("Location: users/registrar/");
                    break;
                case 'program-head':
                case 'program-chair':
                    header("Location: users/program-chair/");
                    break;
                case 'director':
                    header("Location: users/director/");
                    break;
                case 'dean':
                    header("Location: users/dean/");
                    break;
                default:
                    header("Location: index.php?ERROR=1");
                    break;
            }
            exit();
        } else {
            // Incorrect password - log for debugging
            error_log("Login failed for user: $username - Password verification failed");
            header('Location: index.php?ERROR=1&msg=wrongpassword');
            exit();
        }
    } else {
        // Username not found - log for debugging
        error_log("Login failed: Username '$username' not found in database");
        header('Location: index.php?ERROR=1&msg=usernotfound');
        exit();
    }
}
?>
