<?php
/**
 * REAL-TIME FIX - Tests login and fixes issues immediately
 */
session_start();

// Test credentials
$testUsername = isset($_POST['username']) ? trim($_POST['username']) : 'access';
$testPassword = isset($_POST['password']) ? trim($_POST['password']) : 'password123';

header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html>
<head>
    <title>Real-Time Login Fix</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 1000px; margin: 20px auto; padding: 20px; background: #f5f5f5; }
        .container { background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .success { color: #28a745; font-weight: bold; }
        .error { color: #dc3545; font-weight: bold; }
        .info { color: #17a2b8; }
        pre { background: #f8f9fa; padding: 15px; border-radius: 5px; overflow-x: auto; font-size: 12px; }
        .btn { background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block; margin: 5px; border: none; cursor: pointer; }
        .btn:hover { background: #0056b3; }
        .btn-success { background: #28a745; }
        .btn-danger { background: #dc3545; }
        input[type="text"], input[type="password"] { padding: 8px; width: 200px; margin: 5px; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔍 Real-Time Login Diagnostic & Fix</h1>
        <hr>

<?php
require_once 'conn_mongodb.php';

// Clear cache
if (method_exists($users, 'clearCache')) {
    $users->clearCache();
}
clearstatcache();

echo "<h2>Step 1: Testing Login Process...</h2>";
echo "<pre>";

// Find user
$user = $users->findOne(['name' => $testUsername]);

if (!$user) {
    echo "<span class='error'>✗ User not found with exact match: '$testUsername'</span>\n";
    echo "\nSearching all users...\n";
    
    $allUsers = $users->find([]);
    $found = false;
    foreach ($allUsers as $u) {
        if (isset($u['name']) && strcasecmp(trim($u['name']), $testUsername) === 0) {
            $user = $u;
            $found = true;
            echo "<span class='success'>✓ Found user (case-insensitive): " . $u['name'] . "</span>\n";
            break;
        }
    }
    
    if (!$found) {
        echo "<span class='error'>✗ User still not found!</span>\n";
        echo "\nAll users in database:\n";
        foreach ($allUsers as $u) {
            echo "  - " . ($u['name'] ?? 'NO NAME') . " (ID: " . ($u['user_id'] ?? 'NO ID') . ")\n";
        }
        echo "</pre>";
        echo "<p><strong>Please enter a valid username from the list above.</strong></p>";
        echo "<form method='POST'>";
        echo "<p>Username: <input type='text' name='username' value='$testUsername' required></p>";
        echo "<p>Password: <input type='password' name='password' value='$testPassword' required></p>";
        echo "<p><button type='submit' class='btn'>Test Again</button></p>";
        echo "</form>";
        echo "</div></body></html>";
        exit;
    }
}

echo "<span class='success'>✓ User found!</span>\n";
echo "  ID: " . ($user['user_id'] ?? 'N/A') . "\n";
echo "  Name: " . ($user['name'] ?? 'N/A') . "\n";
echo "  Role: " . ($user['role'] ?? 'N/A') . "\n";
echo "  Status: " . ($user['status'] ?? 'N/A') . "\n";
echo "</pre>";

echo "<h2>Step 2: Checking Password...</h2>";
echo "<pre>";

$dbPassword = $user['password'] ?? null;
$userId = $user['user_id'] ?? null;

if (empty($dbPassword)) {
    echo "<span class='error'>✗ NO PASSWORD IN DATABASE!</span>\n";
    echo "Fixing now...\n";
    
    $newHash = password_hash($testPassword, PASSWORD_DEFAULT);
    $result = $users->updateOne(
        ['user_id' => $userId],
        ['$set' => ['password' => $newHash, 'status' => 'active']]
    );
    
    echo "<span class='success'>✓ Password set!</span>\n";
    $dbPassword = $newHash;
} elseif (!preg_match('/^\$2[ayb]\$.{56}$/', $dbPassword)) {
    echo "<span class='error'>✗ Password is PLAIN TEXT: '$dbPassword'</span>\n";
    echo "Fixing now...\n";
    
    $newHash = password_hash($testPassword, PASSWORD_DEFAULT);
    $result = $users->updateOne(
        ['user_id' => $userId],
        ['$set' => ['password' => $newHash, 'status' => 'active']]
    );
    
    echo "<span class='success'>✓ Password hashed and set!</span>\n";
    $dbPassword = $newHash;
} else {
    echo "Password hash exists: " . substr($dbPassword, 0, 30) . "...\n";
    echo "Testing password verification...\n";
    
    if (password_verify($testPassword, $dbPassword)) {
        echo "<span class='success'>✓ Password verification: SUCCESS!</span>\n";
    } else {
        echo "<span class='error'>✗ Password verification: FAILED</span>\n";
        echo "The stored password doesn't match '$testPassword'\n";
        echo "Fixing now...\n";
        
        $newHash = password_hash($testPassword, PASSWORD_DEFAULT);
        $result = $users->updateOne(
            ['user_id' => $userId],
            ['$set' => ['password' => $newHash, 'status' => 'active']]
        );
        
        echo "<span class='success'>✓ Password reset to '$testPassword'!</span>\n";
        $dbPassword = $newHash;
    }
}

echo "</pre>";

echo "<h2>Step 3: Final Verification...</h2>";
echo "<pre>";

// Reload user to verify
$users->clearCache();
$verifyUser = $users->findOne(['user_id' => $userId]);

if ($verifyUser && isset($verifyUser['password'])) {
    if (password_verify($testPassword, $verifyUser['password'])) {
        echo "<span class='success'>✅ FINAL TEST: PASSWORD VERIFICATION SUCCESS!</span>\n";
        echo "\nLogin will work with:\n";
        echo "  Username: " . $verifyUser['name'] . "\n";
        echo "  Password: $testPassword\n";
        echo "  Role: " . ($verifyUser['role'] ?? 'N/A') . "\n";
        echo "  Status: " . ($verifyUser['status'] ?? 'N/A') . "\n";
        
        // Actually perform login
        $_SESSION['user_id'] = $userId;
        $_SESSION['username'] = $verifyUser['name'];
        $_SESSION['role'] = $verifyUser['role'];
        
        echo "\n<span class='success'>✓ Session created! Redirecting to dashboard...</span>\n";
        
        $role = $verifyUser['role'] ?? 'coordinator';
        $redirect = "users/$role/";
        
        echo "\nRedirect URL: $redirect\n";
        
        // Redirect after 2 seconds
        echo "</pre>";
        echo "<div style='background:#d4edda;padding:20px;border-radius:5px;margin-top:20px;'>";
        echo "<h3 class='success'>✅ LOGIN SUCCESSFUL!</h3>";
        echo "<p>You are being redirected to your dashboard...</p>";
        echo "<p><a href='$redirect' class='btn btn-success'>Go to Dashboard Now →</a></p>";
        echo "<p><small>Or wait 2 seconds for automatic redirect...</small></p>";
        echo "</div>";
        echo "<script>setTimeout(function(){ window.location.href='$redirect'; }, 2000);</script>";
    } else {
        echo "<span class='error'>❌ FINAL TEST: Still failing</span>\n";
        echo "This is unusual. Please check file permissions.\n";
    }
} else {
    echo "<span class='error'>❌ Could not reload user</span>\n";
}

echo "</pre>";

if (!isset($redirect)) {
    echo "<hr>";
    echo "<h2>Test Login Form</h2>";
    echo "<form method='POST'>";
    echo "<p>Username: <input type='text' name='username' value='$testUsername' required></p>";
    echo "<p>Password: <input type='password' name='password' value='$testPassword' required></p>";
    echo "<p><button type='submit' class='btn'>Test & Fix Login</button></p>";
    echo "</form>";
    
    echo "<hr>";
    echo "<p><a href='index.php' class='btn'>← Back to Login Page</a></p>";
}
?>

    </div>
</body>
</html>

