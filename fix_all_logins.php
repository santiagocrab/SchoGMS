<!DOCTYPE html>
<html>
<head>
    <title>Fix All Logins - SchoGMS</title>
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
        .success { color: #28a745; font-weight: bold; }
        .error { color: #dc3545; font-weight: bold; }
        .btn {
            background: #28a745;
            color: white;
            padding: 15px 30px;
            text-decoration: none;
            border-radius: 5px;
            display: inline-block;
            margin: 10px 5px;
            font-size: 16px;
        }
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
        <h1>🔧 Fix All Logins</h1>
        <p>This script will:</p>
        <ul>
            <li>Reset all MongoDB user passwords to <strong>password123</strong></li>
            <li>Activate all user accounts</li>
            <li>Verify all dashboards exist</li>
            <li>Test login functionality</li>
        </ul>
        <hr>
        
        <?php
        require_once 'conn_mongodb.php';
        
        $default_password = 'password123';
        $password_hash = password_hash($default_password, PASSWORD_DEFAULT);
        
        echo "<h2>Step 1: Resetting All Passwords...</h2>\n";
        echo "<pre>\n";
        
        // Find all users
        $allUsers = $users->find([]);
        
        $updated = 0;
        $failed = 0;
        $userList = [];
        
        foreach ($allUsers as $user) {
            $userId = $user['user_id'] ?? null;
            $username = $user['name'] ?? 'Unknown';
            $role = $user['role'] ?? 'unknown';
            
            if ($userId === null) {
                echo "<span class='error'>✗ Skipping user without ID: $username</span>\n";
                $failed++;
                continue;
            }
            
            try {
                // Update password and status
                $result = $users->updateOne(
                    ['user_id' => $userId],
                    ['$set' => [
                        'password' => $password_hash,
                        'status' => 'active',
                        'updated_at' => date('Y-m-d H:i:s')
                    ]]
                );
                
                $modifiedCount = isset($result['modifiedCount']) ? $result['modifiedCount'] : 0;
                
                if ($modifiedCount > 0) {
                    echo "<span class='success'>✓ Updated: $username (ID: $userId, Role: $role)</span>\n";
                    $updated++;
                } else {
                    echo "<span class='info'>- Already set: $username (ID: $userId, Role: $role)</span>\n";
                }
                
                $userList[] = [
                    'id' => $userId,
                    'name' => $username,
                    'role' => $role
                ];
            } catch (Exception $e) {
                echo "<span class='error'>✗ Error updating $username: " . $e->getMessage() . "</span>\n";
                $failed++;
            }
        }
        
        echo "</pre>\n";
        
        echo "<h2>Step 2: Verifying Dashboards...</h2>\n";
        echo "<pre>\n";
        
        $dashboards = [
            'coordinator' => 'users/coordinator/index.php',
            'registrar' => 'users/registrar/index.php',
            'chairman' => 'users/chairman/index.php',
            'director' => 'users/director/index.php',
            'dean' => 'users/dean/index.php',
            'program-head' => 'users/program-chair/index.php',
            'program-chair' => 'users/program-chair/index.php',
        ];
        
        $dashboardStatus = [];
        foreach ($dashboards as $role => $path) {
            $fullPath = __DIR__ . '/' . $path;
            if (file_exists($fullPath)) {
                echo "<span class='success'>✓ Dashboard exists: $role → $path</span>\n";
                $dashboardStatus[$role] = true;
            } else {
                echo "<span class='error'>✗ Dashboard missing: $role → $path</span>\n";
                $dashboardStatus[$role] = false;
            }
        }
        
        echo "</pre>\n";
        
        echo "<h2>Step 3: Testing Password Verification...</h2>\n";
        echo "<pre>\n";
        
        $testPassword = 'password123';
        $testCount = 0;
        $testPassed = 0;
        
        foreach ($userList as $user) {
            $testUser = $users->findOne(['user_id' => $user['id']]);
            if ($testUser && isset($testUser['password'])) {
                $testCount++;
                if (password_verify($testPassword, $testUser['password'])) {
                    echo "<span class='success'>✓ Password test passed: {$user['name']}</span>\n";
                    $testPassed++;
                } else {
                    echo "<span class='error'>✗ Password test failed: {$user['name']}</span>\n";
                }
            }
        }
        
        echo "</pre>\n";
        
        echo "<hr>\n";
        echo "<h2>Summary</h2>\n";
        echo "<ul>\n";
        echo "<li><strong>Users updated:</strong> <span class='success'>$updated</span></li>\n";
        echo "<li><strong>Password tests passed:</strong> <span class='success'>$testPassed / $testCount</span></li>\n";
        if ($failed > 0) {
            echo "<li><strong>Failed:</strong> <span class='error'>$failed</span></li>\n";
        }
        echo "</ul>\n";
        
        if ($updated > 0 && $testPassed == $testCount) {
            echo "<div style='background: #d4edda; padding: 20px; border-radius: 5px; margin-top: 20px;'>\n";
            echo "<h3 class='success'>✅ All Fixed!</h3>\n";
            echo "<p>All passwords have been reset and verified. You can now log in with:</p>\n";
            echo "<ul>\n";
            echo "<li><strong>Username:</strong> Any username from the list</li>\n";
            echo "<li><strong>Password:</strong> <code>password123</code></li>\n";
            echo "</ul>\n";
            echo "<p><a href='index.php' class='btn'>Go to Login Page</a></p>\n";
            echo "</div>\n";
        }
        
        echo "<hr>\n";
        echo "<p><a href='LOGIN-GUIDE.php'>← Back to Login Guide</a></p>\n";
        ?>
    </div>
</body>
</html>

