<!DOCTYPE html>
<html>
<head>
    <title>Reset MongoDB Passwords</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background: #f5f5f5;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #333;
        }
        .success {
            color: #28a745;
            font-weight: bold;
        }
        .error {
            color: #dc3545;
            font-weight: bold;
        }
        .info {
            color: #17a2b8;
        }
        pre {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            overflow-x: auto;
        }
        button {
            background: #007bff;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }
        button:hover {
            background: #0056b3;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Reset MongoDB User Passwords</h1>
        <p>This script will reset all user passwords in MongoDB to: <strong>password123</strong></p>
        <hr>
        
        <?php
        require_once 'conn_mongodb.php';
        
        $default_password = 'password123';
        $password_hash = password_hash($default_password, PASSWORD_DEFAULT);
        
        echo "<h2>Processing...</h2>\n";
        echo "<pre>\n";
        
        // Find all users
        $allUsers = $users->find([]);
        
        $updated = 0;
        $failed = 0;
        $results = [];
        
        foreach ($allUsers as $user) {
            $userId = $user['user_id'];
            $username = $user['name'] ?? 'Unknown';
            $role = $user['role'] ?? 'unknown';
            
            try {
                // Update password
                $result = $users->updateOne(
                    ['user_id' => $userId],
                    ['$set' => [
                        'password' => $password_hash,
                        'status' => 'active', // Also activate all users
                        'updated_at' => date('Y-m-d H:i:s')
                    ]]
                );
                
                // Handle array return type (SimpleFastMongoDB returns array)
                $modifiedCount = isset($result['modifiedCount']) ? $result['modifiedCount'] : 0;
                
                if ($modifiedCount > 0) {
                    echo "<span class='success'>✓ Updated: $username (ID: $userId, Role: $role)</span>\n";
                    $updated++;
                    $results[] = "✓ $username ($role)";
                } else {
                    echo "<span class='info'>- No change: $username (ID: $userId, Role: $role) - password already set</span>\n";
                    $results[] = "- $username ($role) - already set";
                }
            } catch (Exception $e) {
                echo "<span class='error'>✗ Error updating $username: " . $e->getMessage() . "</span>\n";
                $failed++;
                $results[] = "✗ $username: " . $e->getMessage();
            }
        }
        
        echo "</pre>\n";
        echo "<hr>\n";
        echo "<h2>Summary</h2>\n";
        echo "<ul>\n";
        echo "<li><strong>Updated:</strong> <span class='success'>$updated</span></li>\n";
        if ($failed > 0) {
            echo "<li><strong>Failed:</strong> <span class='error'>$failed</span></li>\n";
        }
        echo "</ul>\n";
        
        if ($updated > 0 || $failed == 0) {
            echo "<div style='background: #d4edda; padding: 15px; border-radius: 5px; margin-top: 20px;'>\n";
            echo "<strong class='success'>✓ Success!</strong> All user passwords have been reset to: <strong>password123</strong><br>\n";
            echo "All users have been activated and can now log in.\n";
            echo "</div>\n";
        }
        
        echo "<hr>\n";
        echo "<p><a href='index.php'>← Back to Login</a></p>\n";
        ?>
    </div>
</body>
</html>

