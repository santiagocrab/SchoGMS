<?php
// NEW LOGIN - Completely bypass caching issues
session_start();

// Include MongoDB connection
include 'conn_mongodb.php';

// Check if request method is POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // Find user in MongoDB
    $user = $users->findOne(['name' => $username]);
    
    if ($user && $user['status'] === 'active' && password_verify($password, $user['password'])) {
        // Login successful
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['username'] = $user['name'];
        $_SESSION['role'] = $user['role'];
        
        // Redirect based on role
        $redirect_url = "users/" . $user['role'] . "/";
        header("Location: $redirect_url");
        exit();
    } else {
        $error = "Error! Wrong username or password.";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>SchoGMS Login</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 20px; background: #f5f5f5; }
        .container { max-width: 400px; margin: 50px auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 0 20px rgba(0,0,0,0.1); }
        .form-group { margin: 15px 0; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        input[type="text"], input[type="password"] { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; box-sizing: border-box; }
        .btn { width: 100%; padding: 12px; background: #007bff; color: white; border: none; border-radius: 5px; cursor: pointer; font-size: 16px; }
        .btn:hover { background: #0056b3; }
        .error { background: #f8d7da; color: #721c24; padding: 10px; border-radius: 5px; margin-bottom: 15px; }
        .success { background: #d4edda; color: #155724; padding: 10px; border-radius: 5px; margin-bottom: 15px; }
        .debug { background: #e2e3e5; color: #383d41; padding: 10px; border-radius: 5px; margin-top: 15px; font-size: 12px; }
    </style>
</head>
<body>
    <div class="container">
        <h2 style="text-align: center; color: #333;">SchoGMS Login</h2>
        <p style="text-align: center; color: #666;">Enter your username and password to access.</p>
        
        <?php if (isset($error)): ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <form method="post">
            <div class="form-group">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" value="chairman" required>
            </div>
            
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" value="schogms123" required>
            </div>
            
            <button type="submit" class="btn">Sign In</button>
        </form>
        
        <div class="debug">
            <strong>Debug Info:</strong><br>
            MongoDB: <?php echo ($mongodb && $mongodb->testConnection()) ? '✅ Connected' : '❌ Failed'; ?><br>
            Chairman User: <?php 
                $chairman = $users->findOne(['name' => 'chairman']);
                if ($chairman) {
                    echo '✅ Found (Status: ' . $chairman['status'] . ')';
                } else {
                    echo '❌ Not Found';
                }
            ?><br>
            Password Test: <?php 
                if ($chairman && password_verify('schogms123', $chairman['password'])) {
                    echo '✅ Correct';
                } else {
                    echo '❌ Incorrect';
                }
            ?>
        </div>
    </div>
</body>
</html>



