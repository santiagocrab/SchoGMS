<?php
/**
 * DIRECT JSON FILE FIX - Bypasses MongoDB and fixes JSON directly
 */

$jsonPath = __DIR__ . '/mongodb_data/schogms/users.json';
$backupPath = $jsonPath . '.backup.' . date('YmdHis');

header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html>
<head>
    <title>Direct JSON Fix</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 900px; margin: 20px auto; padding: 20px; background: #f5f5f5; }
        .container { background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .success { color: #28a745; font-weight: bold; }
        .error { color: #dc3545; font-weight: bold; }
        pre { background: #f8f9fa; padding: 15px; border-radius: 5px; overflow-x: auto; font-size: 11px; }
        .btn { background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block; margin: 5px; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔧 Direct JSON File Fix</h1>
        <hr>

<?php
echo "<h2>Step 1: Checking File...</h2>";
echo "<pre>";

if (!file_exists($jsonPath)) {
    echo "<span class='error'>❌ JSON file not found: $jsonPath</span>\n";
    echo "</pre></div></body></html>";
    exit;
}

echo "File exists: <span class='success'>✓</span>\n";
echo "File path: $jsonPath\n";
echo "File size: " . filesize($jsonPath) . " bytes\n";
echo "File permissions: " . substr(sprintf('%o', fileperms($jsonPath)), -4) . "\n";
echo "Writable: " . (is_writable($jsonPath) ? "<span class='success'>✓ Yes</span>" : "<span class='error'>✗ No</span>") . "\n";

// Create backup
copy($jsonPath, $backupPath);
echo "<span class='success'>✓ Backup created: " . basename($backupPath) . "</span>\n";
echo "</pre>";

echo "<h2>Step 2: Reading JSON File...</h2>";
echo "<pre>";

$content = file_get_contents($jsonPath);
$users = json_decode($content, true);

if (json_last_error() !== JSON_ERROR_NONE) {
    echo "<span class='error'>❌ JSON Error: " . json_last_error_msg() . "</span>\n";
    echo "</pre></div></body></html>";
    exit;
}

echo "Users found: " . count($users) . "\n";
echo "</pre>";

echo "<h2>Step 3: Fixing All Passwords...</h2>";
echo "<pre>";

$newPassword = 'password123';
$passwordHash = password_hash($newPassword, PASSWORD_DEFAULT);

$fixed = 0;
$processed = [];
$fixedUsers = [];

foreach ($users as $index => $user) {
    $userId = $user['user_id'] ?? null;
    $username = $user['name'] ?? 'Unknown';
    
    if ($userId === null) {
        echo "<span class='error'>✗ Skipping: $username (no user_id)</span>\n";
        continue;
    }
    
    // Remove duplicates
    if (isset($processed[$userId])) {
        echo "<span class='error'>✗ Duplicate removed: $username (ID: $userId)</span>\n";
        continue;
    }
    
    $processed[$userId] = true;
    
    // Check current password
    $oldPassword = $user['password'] ?? '';
    $needsFix = false;
    
    if (empty($oldPassword)) {
        $needsFix = true;
        echo "<span class='error'>✗ No password: $username</span>\n";
    } elseif (!preg_match('/^\$2[ayb]\$.{56}$/', $oldPassword)) {
        $needsFix = true;
        echo "<span class='error'>✗ Plain text: $username (value: '$oldPassword')</span>\n";
    } elseif (!password_verify($newPassword, $oldPassword)) {
        $needsFix = true;
        echo "<span class='error'>✗ Wrong hash: $username</span>\n";
    }
    
    // ALWAYS set to correct password and active status
    $user['password'] = $passwordHash;
    $user['status'] = 'active';
    $user['updated_at'] = date('Y-m-d H:i:s');
    
    $fixedUsers[] = $user;
    
    if ($needsFix) {
        echo "<span class='success'>✓ Fixed: $username (ID: $userId)</span>\n";
        $fixed++;
    } else {
        echo "<span class='info'>- OK: $username (ID: $userId)</span>\n";
    }
}

echo "</pre>";

echo "<h2>Step 4: Writing Fixed JSON...</h2>";
echo "<pre>";

// Write JSON with proper formatting
$jsonOutput = json_encode($fixedUsers, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

// Try to write
if (file_put_contents($jsonPath, $jsonOutput) === false) {
    echo "<span class='error'>❌ Failed to write file!</span>\n";
    echo "Trying with chmod...\n";
    chmod($jsonPath, 0666);
    if (file_put_contents($jsonPath, $jsonOutput) === false) {
        echo "<span class='error'>❌ Still failed. Check file permissions manually.</span>\n";
        echo "</pre></div></body></html>";
        exit;
    }
}

// Update file modification time to clear cache
touch($jsonPath);
clearstatcache();

echo "<span class='success'>✓ JSON file written successfully!</span>\n";
echo "Total users: " . count($fixedUsers) . "\n";
echo "</pre>";

echo "<h2>Step 5: Verifying Fix...</h2>";
echo "<pre>";

// Re-read to verify
$verifyContent = file_get_contents($jsonPath);
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

echo "<h2>Step 6: Testing Specific User...</h2>";
echo "<pre>";

$testUser = null;
foreach ($verifyUsers as $user) {
    if (isset($user['name']) && strcasecmp(trim($user['name']), 'access') === 0) {
        $testUser = $user;
        break;
    }
}

if ($testUser) {
    echo "Found user: " . $testUser['name'] . "\n";
    echo "Password hash: " . substr($testUser['password'], 0, 30) . "...\n";
    
    if (password_verify($newPassword, $testUser['password'])) {
        echo "<span class='success'>✅ PASSWORD VERIFICATION: SUCCESS!</span>\n";
        echo "You can now log in with:\n";
        echo "  Username: " . $testUser['name'] . "\n";
        echo "  Password: $newPassword\n";
    } else {
        echo "<span class='error'>❌ PASSWORD VERIFICATION: FAILED</span>\n";
        echo "This should not happen. The hash might be corrupted.\n";
    }
} else {
    echo "<span class='error'>❌ User 'access' not found</span>\n";
}

echo "</pre>";

echo "<hr>";
echo "<h2>Summary</h2>";
echo "<ul>";
echo "<li><strong>Users processed:</strong> " . count($fixedUsers) . "</li>";
echo "<li><strong>Passwords fixed:</strong> <span class='success'>$fixed</span></li>";
echo "<li><strong>Verified:</strong> <span class='success'>$verified / " . count($verifyUsers) . "</span></li>";
if (count($failed) > 0) {
    echo "<li><strong>Failed:</strong> <span class='error'>" . implode(', ', $failed) . "</span></li>";
}
echo "</ul>";

if ($verified == count($verifyUsers) && $verified > 0) {
    echo "<div style='background:#d4edda;padding:20px;border-radius:5px;margin-top:20px;'>";
    echo "<h3 class='success'>✅ SUCCESS! All Passwords Fixed!</h3>";
    echo "<p><strong>Try logging in now:</strong></p>";
    echo "<ul>";
    echo "<li>URL: <a href='index.php'>http://localhost/SchoGMS/index.php</a></li>";
    echo "<li>Username: <code>access</code></li>";
    echo "<li>Password: <code>password123</code></li>";
    echo "</ul>";
    echo "<p><a href='index.php' class='btn'>Go to Login →</a></p>";
    echo "</div>";
} else {
    echo "<div style='background:#fff3cd;padding:20px;border-radius:5px;margin-top:20px;'>";
    echo "<h3>⚠️ Some Issues</h3>";
    echo "<p>File was updated but some verifications failed. Try logging in anyway.</p>";
    echo "<p><a href='index.php' class='btn'>Try Login</a></p>";
    echo "</div>";
}

echo "<hr>";
echo "<p><a href='index.php' class='btn'>← Back to Login</a></p>";
?>
    </div>
</body>
</html>

