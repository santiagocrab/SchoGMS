<?php
// Force login fix - bypass all caching issues
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
<html dir="ltr">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Scholarship and Grants Management System | SchoGMS</title>
    <link rel="icon" type="image/png" sizes="16x16" href="assets/images/logo.png">
    <link href="dist/css/style.min.css" rel="stylesheet">
    <style>
        .auth-wrapper {
            position: relative;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            overflow: hidden;
        }
        .auth-box {
            width: 100%;
            max-width: 450px;
            background: rgba(255, 255, 255, 0.9);
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0px 5px 15px rgba(0, 0, 0, 0.2);
            position: relative;
            z-index: 1;
        }
        .error-message {
            background: #ffdddd;
            color: #a94442;
            border: 1px solid #ebccd1;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 10px;
        }
        .success-message {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <div class="auth-wrapper">
        <div class="auth-box">
            <!-- Logo -->
            <div class="text-center mb-3">
                <img src="assets/images/image2.png" style="width: 90px;" alt="Homepage">
            </div>
            <div class="text-center mb-3">
                <img src="assets/images/logo.png" style="width: 300px;" alt="Homepage">
            </div>
            <p class="text-center text-dark">Enter your username and password to access.</p>

            <!-- Display Error Message if Login Fails -->
            <?php if (isset($error_message)): ?>
                <div class="alert alert-danger text-center"><?php echo $error_message; ?></div>
            <?php endif; ?>

            <!-- Login Form -->
            <form method="post" action="force_login_fix.php">
                <div class="form-group">
                    <label class="text-dark" for="uname">Username</label>
                    <input class="form-control" id="uname" type="text" name="username" placeholder="Enter your username" value="chairman" required>
                </div>
                <div class="form-group">
                    <label class="text-dark" for="pwd">Password</label>
                    <input class="form-control" id="pwd" type="password" name="password" placeholder="Enter your password" value="schogms123" required>
                </div>

                <button type="submit" class="btn btn-dark btn-block">Sign In</button>
            </form>
            
            <div class="text-center mt-3">
                <p><strong>This is the FIXED login page</strong></p>
                <p>Credentials are pre-filled: chairman / schogms123</p>
            </div>
        </div>
    </div>

    <!-- Required JS -->
    <script src="assets/libs/jquery/dist/jquery.min.js"></script>
    <script src="assets/libs/popper.js/dist/umd/popper.min.js"></script>
    <script src="assets/libs/bootstrap/dist/js/bootstrap.min.js"></script>
</body>
</html>



