<?php
// Create a simple test login page to bypass any caching issues
session_start();

// Include MongoDB connection
include 'conn_mongodb.php';

// Check if request method is POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Retrieve form inputs
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // Find user in MongoDB
    $user = $users->findOne(['name' => $username]);
    
    if ($user) {
        // Extract user data
        $user_id = $user['user_id'];
        $db_username = $user['name'];
        $db_password = $user['password'];
        $db_role = $user['role'];
        $db_status = $user['status'];

        // Check if user is restricted or pending
        if ($db_status === 'restricted') {
            $error_message = 'Your account is restricted. Please contact support.';
        } elseif ($db_status === 'pending') {
            $error_message = 'Your account is pending approval. Did you receive a 2FA verification code.';
        } elseif ($db_status !== 'active') {
            $error_message = 'Your account is inactive. Please contact the administrator.';
        } else {
            // Verify the password
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
                        $error_message = 'Error! Wrong username or password.';
                        break;
                }
                exit();
            } else {
                $error_message = 'Error! Wrong username or password.';
            }
        }
    } else {
        $error_message = 'Error! Wrong username or password.';
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Test Login - SchoGMS</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 50px; }
        .form-group { margin: 15px 0; }
        .form-control { padding: 10px; width: 300px; border: 1px solid #ccc; }
        .btn { padding: 10px 20px; background: #007bff; color: white; border: none; cursor: pointer; }
        .alert { padding: 15px; margin: 15px 0; border-radius: 5px; }
        .alert-danger { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .alert-success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
    </style>
</head>
<body>
    <h1>Test Login - SchoGMS</h1>
    <p>This is a test login page to bypass any caching issues.</p>
    
    <?php if (isset($error_message)): ?>
        <div class="alert alert-danger"><?php echo $error_message; ?></div>
    <?php endif; ?>
    
    <form method="post">
        <div class="form-group">
            <label>Username:</label><br>
            <input class="form-control" type="text" name="username" value="chairman" required>
        </div>
        <div class="form-group">
            <label>Password:</label><br>
            <input class="form-control" type="password" name="password" value="schogms123" required>
        </div>
        <button type="submit" class="btn">Sign In</button>
    </form>
    
    <hr>
    <h3>Debug Information:</h3>
    <p><strong>MongoDB Connection:</strong> 
        <?php 
        if ($mongodb && $mongodb->testConnection()) {
            echo '<span style="color: green;">✅ Connected</span>';
        } else {
            echo '<span style="color: red;">❌ Failed</span>';
        }
        ?>
    </p>
    
    <p><strong>Chairman User Check:</strong>
        <?php
        $chairmanUser = $users->findOne(['name' => 'chairman']);
        if ($chairmanUser) {
            echo '<span style="color: green;">✅ Found (Status: ' . $chairmanUser['status'] . ')</span>';
        } else {
            echo '<span style="color: red;">❌ Not Found</span>';
        }
        ?>
    </p>
    
    <p><strong>Password Test:</strong>
        <?php
        if ($chairmanUser && password_verify('schogms123', $chairmanUser['password'])) {
            echo '<span style="color: green;">✅ Password Correct</span>';
        } else {
            echo '<span style="color: red;">❌ Password Incorrect</span>';
        }
        ?>
    </p>
</body>
</html>



