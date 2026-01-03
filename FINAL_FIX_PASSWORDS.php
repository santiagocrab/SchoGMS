<?php
/**
 * FINAL FIX - This will definitely work by fixing the JSON file directly
 */

$jsonFile = __DIR__ . '/mongodb_data/schogms/users.json';
$backupFile = $jsonFile . '.backup.' . date('Y-m-d_H-i-s');

header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html>
<head>
    <title>FINAL FIX - Passwords</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 900px; margin: 20px auto; padding: 20px; background: #f5f5f5; }
        .container { background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .success { color: #28a745; font-weight: bold; }
        .error { color: #dc3545; font-weight: bold; }
        .info { color: #17a2b8; }
        pre { background: #f8f9fa; padding: 15px; border-radius: 5px; overflow-x: auto; }
        .btn { background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block; margin: 5px; }
        .btn:hover { background: #0056b3; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔧 FINAL FIX - Direct JSON Password Fix</h1>
        <hr>

<?php
// Step 1: Create backup
if (file_exists($jsonFile)) {
    copy($jsonFile, $backupFile);
    echo "<p class='success'>✓ Backup created: " . basename($backupFile) . "</p>";
} else {
    echo "<p class='error'>❌ JSON file not found: $jsonFile</p>";
    echo "</div></body></html>";
    exit;
}

// Step 2: Read and fix JSON
echo "<h2>Step 1: Reading JSON File...</h2>";
echo "<pre>";

$jsonContent = file_get_contents($jsonFile);
$users = json_decode($jsonContent, true);

if (!$users || !is_array($users)) {
    echo "<span class='error'>❌ Failed to parse JSON file</span>\n";
    echo "</pre></div></body></html>";
    exit;
}

echo "Found " . count($users) . " user entries\n";
echo "</pre>";

// Step 3: Fix all passwords
echo "<h2>Step 2: Fixing All Passwords...</h2>";
echo "<pre>";

$newPassword = 'password123';
$passwordHash = password_hash($newPassword, PASSWORD_DEFAULT);

$fixed = 0;
$processed = [];
$uniqueUsers = [];

foreach ($users as $user) {
    $userId = $user['user_id'] ?? null;
    $username = $user['name'] ?? 'Unknown';
    
    if ($userId === null) {
        echo "<span class='error'>✗ Skipping user without ID: $username</span>\n";
        continue;
    }
    
    // Remove duplicates - keep only the first occurrence
    if (isset($processed[$userId])) {
        echo "<span class='info'>- Removing duplicate: $username (ID: $userId)</span>\n";
        continue;
    }
    
    $processed[$userId] = true;
    
    // Check current password
    $oldPassword = $user['password'] ?? '';
    $needsFix = false;
    
    // Check if it's plain text or wrong hash
    if (empty($oldPassword)) {
        $needsFix = true;
        echo "<span class='error'>✗ No password: $username</span>\n";
    } elseif (!preg_match('/^\$2[ayb]\$.{56}$/', $oldPassword)) {
        $needsFix = true;
        echo "<span class='error'>✗ Plain text password: $username (has: '$oldPassword')</span>\n";
    } elseif (!password_verify($newPassword, $oldPassword)) {
        $needsFix = true;
        echo "<span class='error'>✗ Wrong hash: $username</span>\n";
    }
    
    // Update user
    $user['password'] = $passwordHash;
    $user['status'] = 'active';
    $user['updated_at'] = date('Y-m-d H:i:s');
    
    $uniqueUsers[] = $user;
    
    if ($needsFix) {
        echo "<span class='success'>✓ Fixed: $username (ID: $userId)</span>\n";
        $fixed++;
    } else {
        echo "<span class='info'>- Already correct: $username (ID: $userId)</span>\n";
    }
}

echo "</pre>";

// Step 4: Save fixed JSON
echo "<h2>Step 3: Saving Fixed JSON File...</h2>";
echo "<pre>";

$jsonOutput = json_encode($uniqueUsers, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

if (file_put_contents($jsonFile, $jsonOutput)) {
    echo "<span class='success'>✓ JSON file saved successfully!</span>\n";
    echo "Total unique users: " . count($uniqueUsers) . "\n";
} else {
    echo "<span class='error'>✗ Failed to save JSON file. Check file permissions.</span>\n";
    echo "</pre></div></body></html>";
    exit;
}

echo "</pre>";

// Step 5: Clear cache by touching the file
touch($jsonFile);
clearstatcache();

// Step 6: Verify
echo "<h2>Step 4: Verifying All Passwords...</h2>";
echo "<pre>";

$verifyContent = file_get_contents($jsonFile);
$verifyUsers = json_decode($verifyContent, true);

$verified = 0;
$failed = [];

foreach ($verifyUsers as $user) {
    $username = $user['name'] ?? 'Unknown';
    $password = $user['password'] ?? '';
    
    if (password_verify($newPassword, $password)) {
        echo "<span class='success'>✓ Verified: $username</span>\n";
        $verified++;
    } else {
        echo "<span class='error'>✗ Failed: $username</span>\n";
        $failed[] = $username;
    }
}

echo "</pre>";

// Step 7: Test login simulation
echo "<h2>Step 5: Testing Login Simulation...</h2>";
echo "<pre>";

$testUsername = 'access';
$testUser = null;

foreach ($verifyUsers as $user) {
    if (isset($user['name']) && strcasecmp(trim($user['name']), $testUsername) === 0) {
        $testUser = $user;
        break;
    }
}

if ($testUser) {
    $testPasswordHash = $testUser['password'] ?? '';
    if (password_verify($newPassword, $testPasswordHash)) {
        echo "<span class='success'>✅ LOGIN TEST SUCCESS!</span>\n";
        echo "Username: " . $testUser['name'] . "\n";
        echo "Password: $newPassword\n";
        echo "Role: " . ($testUser['role'] ?? 'N/A') . "\n";
        echo "Status: " . ($testUser['status'] ?? 'N/A') . "\n";
    } else {
        echo "<span class='error'>❌ LOGIN TEST FAILED - Password verification failed</span>\n";
    }
} else {
    echo "<span class='error'>❌ LOGIN TEST FAILED - User 'access' not found</span>\n";
}

echo "</pre>";

echo "<hr>";
echo "<h2>Summary</h2>";
echo "<ul>";
echo "<li><strong>Total users:</strong> " . count($uniqueUsers) . "</li>";
echo "<li><strong>Passwords fixed:</strong> <span class='success'>$fixed</span></li>";
echo "<li><strong>Passwords verified:</strong> <span class='success'>$verified / " . count($verifyUsers) . "</span></li>";
if (count($failed) > 0) {
    echo "<li><strong>Failed:</strong> <span class='error'>" . implode(', ', $failed) . "</span></li>";
}
echo "</ul>";

if ($verified == count($verifyUsers) && $verified > 0) {
    echo "<div style='background:#d4edda;padding:20px;border-radius:5px;margin-top:20px;'>";
    echo "<h3 class='success'>✅ ALL PASSWORDS FIXED AND VERIFIED!</h3>";
    echo "<p><strong>You can now log in with:</strong></p>";
    echo "<ul>";
    echo "<li><strong>URL:</strong> <a href='index.php'>http://localhost/SchoGMS/index.php</a></li>";
    echo "<li><strong>Username:</strong> <code>access</code> (or any username from the list)</li>";
    echo "<li><strong>Password:</strong> <code>password123</code></li>";
    echo "</ul>";
    echo "<p><strong>Available usernames:</strong></p>";
    echo "<ul>";
    foreach ($verifyUsers as $u) {
        if (isset($u['name']) && isset($u['role'])) {
            echo "<li>" . htmlspecialchars($u['name']) . " (" . htmlspecialchars($u['role']) . ")</li>";
        }
    }
    echo "</ul>";
    echo "<p><a href='index.php' class='btn'>Try Login Now →</a></p>";
    echo "</div>";
} else {
    echo "<div style='background:#f8d7da;padding:20px;border-radius:5px;margin-top:20px;'>";
    echo "<h3 class='error'>⚠️ Some Issues Detected</h3>";
    echo "<p>Please check the output above. If issues persist, check file permissions.</p>";
    echo "</div>";
}

echo "<hr>";
echo "<p><a href='index.php' class='btn'>← Back to Login</a></p>";
?>
    </div>
</body>
</html>

