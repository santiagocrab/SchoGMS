<!DOCTYPE html>
<html>
<head>
    <title>Debug MongoDB Login</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 1000px;
            margin: 20px auto;
            padding: 20px;
            background: #f5f5f5;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background: #007bff;
            color: white;
        }
        .success { color: #28a745; }
        .error { color: #dc3545; }
        .info { color: #17a2b8; }
        pre {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            overflow-x: auto;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Debug MongoDB Login</h1>
        <hr>
        
        <?php
        require_once 'conn_mongodb.php';
        
        echo "<h2>All Users in Database:</h2>\n";
        
        // Get all users
        $allUsers = $users->find([]);
        
        echo "<table>\n";
        echo "<tr><th>ID</th><th>Username</th><th>Role</th><th>Status</th><th>Password Hash (first 20 chars)</th><th>Test password123</th></tr>\n";
        
        $testPassword = 'password123';
        $foundUsers = [];
        
        foreach ($allUsers as $user) {
            $userId = $user['user_id'] ?? 'N/A';
            $username = $user['name'] ?? 'N/A';
            $role = $user['role'] ?? 'N/A';
            $status = $user['status'] ?? 'N/A';
            $passwordHash = $user['password'] ?? 'NO PASSWORD';
            $passwordPreview = substr($passwordHash, 0, 20) . '...';
            
            // Test password
            $passwordMatch = false;
            if ($passwordHash !== 'NO PASSWORD') {
                $passwordMatch = password_verify($testPassword, $passwordHash);
            }
            
            $matchStatus = $passwordMatch ? '<span class="success">✓ MATCH</span>' : '<span class="error">✗ NO MATCH</span>';
            
            echo "<tr>";
            echo "<td>$userId</td>";
            echo "<td><strong>$username</strong></td>";
            echo "<td>$role</td>";
            echo "<td>$status</td>";
            echo "<td>$passwordPreview</td>";
            echo "<td>$matchStatus</td>";
            echo "</tr>\n";
            
            $foundUsers[] = [
                'id' => $userId,
                'name' => $username,
                'role' => $role,
                'status' => $status,
                'password_match' => $passwordMatch
            ];
        }
        
        echo "</table>\n";
        
        echo "<hr>\n";
        echo "<h2>Test Login:</h2>\n";
        
        if (isset($_POST['test_username']) && isset($_POST['test_password'])) {
            $testUsername = trim($_POST['test_username']);
            $testPassword = trim($_POST['test_password']);
            
            echo "<h3>Testing Login for: <strong>$testUsername</strong></h3>\n";
            echo "<pre>\n";
            
            // Try to find user
            $user = $users->findOne(['name' => $testUsername]);
            
            if (!$user) {
                // Try case-insensitive
                $allUsers = $users->find([]);
                foreach ($allUsers as $u) {
                    if (isset($u['name']) && strcasecmp(trim($u['name']), $testUsername) === 0) {
                        $user = $u;
                        echo "Found user (case-insensitive match)\n";
                        break;
                    }
                }
            }
            
            if ($user) {
                echo "✓ User found!\n";
                echo "  ID: " . ($user['user_id'] ?? 'N/A') . "\n";
                echo "  Name: " . ($user['name'] ?? 'N/A') . "\n";
                echo "  Role: " . ($user['role'] ?? 'N/A') . "\n";
                echo "  Status: " . ($user['status'] ?? 'N/A') . "\n";
                
                $dbPassword = $user['password'] ?? null;
                if ($dbPassword) {
                    echo "  Password hash exists: Yes\n";
                    echo "  Testing password verification...\n";
                    
                    if (password_verify($testPassword, $dbPassword)) {
                        echo "  <span class='success'>✓ PASSWORD VERIFICATION: SUCCESS!</span>\n";
                    } else {
                        echo "  <span class='error'>✗ PASSWORD VERIFICATION: FAILED</span>\n";
                        echo "  The password you entered does not match the stored hash.\n";
                    }
                } else {
                    echo "  <span class='error'>✗ NO PASSWORD HASH IN DATABASE</span>\n";
                }
            } else {
                echo "<span class='error'>✗ User not found!</span>\n";
                echo "Available usernames:\n";
                foreach ($foundUsers as $u) {
                    echo "  - " . $u['name'] . " (" . $u['role'] . ")\n";
                }
            }
            
            echo "</pre>\n";
        }
        
        echo "<hr>\n";
        echo "<h2>Test Login Form:</h2>\n";
        echo "<form method='POST'>\n";
        echo "<p><label>Username: <input type='text' name='test_username' value='access' required></label></p>\n";
        echo "<p><label>Password: <input type='password' name='test_password' value='password123' required></label></p>\n";
        echo "<p><button type='submit'>Test Login</button></p>\n";
        echo "</form>\n";
        
        echo "<hr>\n";
        echo "<h2>Quick Fix:</h2>\n";
        echo "<p>If passwords don't match, run the reset script:</p>\n";
        echo "<p><a href='reset-mongodb-passwords.php' style='background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block;'>Reset All Passwords</a></p>\n";
        ?>
    </div>
</body>
</html>

