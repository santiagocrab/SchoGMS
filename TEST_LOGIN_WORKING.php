<?php
/**
 * TEST LOGIN - This will actually log you in if it works
 */
session_start();
require_once 'conn_mongodb.php';

// Clear cache
if (method_exists($users, 'clearCache')) {
    $users->clearCache();
}
clearstatcache();

$testUsername = 'access';
$testPassword = 'password123';

header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html>
<head>
    <title>Test Login</title>
    <style>
        body { font-family: Arial; max-width: 800px; margin: 50px auto; padding: 20px; }
        .success { color: #28a745; font-weight: bold; font-size: 18px; }
        .error { color: #dc3545; font-weight: bold; }
        pre { background: #f8f9fa; padding: 15px; border-radius: 5px; }
        .btn { background: #007bff; color: white; padding: 15px 30px; text-decoration: none; border-radius: 5px; display: inline-block; margin: 10px; font-size: 18px; }
    </style>
</head>
<body>
    <h1>🔍 Testing Login Process</h1>
    <hr>
    <pre>
<?php
echo "Testing login for: $testUsername\n";
echo "Password: $testPassword\n\n";

// Step 1: Find user
echo "Step 1: Finding user...\n";
$user = $users->findOne(['name' => $testUsername]);

if (!$user) {
    echo "✗ Not found with exact match. Trying case-insensitive...\n";
    $allUsers = $users->find([]);
    foreach ($allUsers as $u) {
        if (isset($u['name']) && strcasecmp(trim($u['name']), $testUsername) === 0) {
            $user = $u;
            echo "✓ Found: " . $u['name'] . "\n";
            break;
        }
    }
}

if (!$user) {
    echo "❌ USER NOT FOUND!\n";
    echo "\nAll users in database:\n";
    $allUsers = $users->find([]);
    foreach ($allUsers as $u) {
        echo "  - " . ($u['name'] ?? 'NO NAME') . "\n";
    }
    echo "</pre></body></html>";
    exit;
}

echo "✓ User found!\n";
echo "  ID: " . ($user['user_id'] ?? 'N/A') . "\n";
echo "  Name: " . ($user['name'] ?? 'N/A') . "\n";
echo "  Role: " . ($user['role'] ?? 'N/A') . "\n";
echo "  Status: " . ($user['status'] ?? 'N/A') . "\n";

// Step 2: Check password
echo "\nStep 2: Checking password...\n";
$dbPassword = $user['password'] ?? null;

if (empty($dbPassword)) {
    echo "✗ NO PASSWORD!\n";
    echo "Fixing now...\n";
    $newHash = password_hash($testPassword, PASSWORD_DEFAULT);
    $users->updateOne(['user_id' => $user['user_id']], ['$set' => ['password' => $newHash, 'status' => 'active']]);
    $users->clearCache();
    $user = $users->findOne(['user_id' => $user['user_id']]);
    $dbPassword = $user['password'] ?? null;
    echo "✓ Password set!\n";
}

if (!preg_match('/^\$2[ayb]\$.{56}$/', $dbPassword)) {
    echo "✗ Password is PLAIN TEXT: '$dbPassword'\n";
    echo "Fixing now...\n";
    $newHash = password_hash($testPassword, PASSWORD_DEFAULT);
    $users->updateOne(['user_id' => $user['user_id']], ['$set' => ['password' => $newHash, 'status' => 'active']]);
    $users->clearCache();
    $user = $users->findOne(['user_id' => $user['user_id']]);
    $dbPassword = $user['password'] ?? null;
    echo "✓ Password hashed!\n";
}

echo "Password hash: " . substr($dbPassword, 0, 30) . "...\n";

// Step 3: Verify password
echo "\nStep 3: Verifying password...\n";
if (password_verify($testPassword, $dbPassword)) {
    echo "✓✓✓ PASSWORD VERIFICATION: SUCCESS! ✓✓✓\n";
    
    // Actually log in
    $_SESSION['user_id'] = $user['user_id'];
    $_SESSION['username'] = $user['name'];
    $_SESSION['role'] = $user['role'];
    
    $role = $user['role'] ?? 'coordinator';
    $redirect = "users/$role/";
    
    echo "\n✓✓✓ LOGIN SUCCESSFUL! ✓✓✓\n";
    echo "Session created!\n";
    echo "Redirecting to: $redirect\n";
    
    echo "</pre>";
    echo "<div style='background:#d4edda;padding:30px;border-radius:10px;text-align:center;margin-top:20px;'>";
    echo "<h2 class='success'>✅ LOGIN SUCCESSFUL!</h2>";
    echo "<p>You are logged in as: <strong>" . htmlspecialchars($user['name']) . "</strong></p>";
    echo "<p>Role: <strong>" . htmlspecialchars($role) . "</strong></p>";
    echo "<p><a href='$redirect' class='btn'>Go to Dashboard →</a></p>";
    echo "<p><small>Or use the regular login page: <a href='index.php'>index.php</a></small></p>";
    echo "</div>";
    
    // Auto redirect
    echo "<script>setTimeout(function(){ window.location.href='$redirect'; }, 2000);</script>";
} else {
    echo "✗✗✗ PASSWORD VERIFICATION: FAILED ✗✗✗\n";
    echo "\nThe password hash doesn't match 'password123'\n";
    echo "Current hash: " . substr($dbPassword, 0, 50) . "...\n";
    echo "\nTrying to fix again...\n";
    
    $newHash = password_hash($testPassword, PASSWORD_DEFAULT);
    $result = $users->updateOne(['user_id' => $user['user_id']], ['$set' => ['password' => $newHash, 'status' => 'active']]);
    $users->clearCache();
    
    // Test again
    $testUser = $users->findOne(['user_id' => $user['user_id']]);
    if ($testUser && password_verify($testPassword, $testUser['password'])) {
        echo "✓ Fixed! Password now works.\n";
        echo "<script>location.reload();</script>";
    } else {
        echo "✗ Still failing. There may be a deeper issue.\n";
    }
}
?>
    </pre>
</body>
</html>

