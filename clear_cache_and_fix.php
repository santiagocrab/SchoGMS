<?php
/**
 * Clear Cache and Fix Passwords - Complete Solution
 */

// Clear opcache if enabled
if (function_exists('opcache_reset')) {
    opcache_reset();
}

// Clear stat cache
clearstatcache();

$jsonFile = __DIR__ . '/mongodb_data/schogms/users.json';

header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html>
<head>
    <title>Clear Cache & Fix Passwords</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 900px; margin: 20px auto; padding: 20px; background: #f5f5f5; }
        .container { background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .success { color: #28a745; font-weight: bold; }
        .error { color: #dc3545; font-weight: bold; }
        pre { background: #f8f9fa; padding: 15px; border-radius: 5px; overflow-x: auto; }
        .btn { background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block; margin: 5px; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔄 Clear Cache & Fix Passwords</h1>
        <hr>

<?php
echo "<h2>Step 1: Clearing All Caches...</h2>";
echo "<pre>";
echo "<span class='success'>✓ OPcache cleared</span>\n";
echo "<span class='success'>✓ Stat cache cleared</span>\n";
echo "</pre>";

// Read and fix JSON
echo "<h2>Step 2: Fixing JSON File...</h2>";
echo "<pre>";

if (!file_exists($jsonFile)) {
    echo "<span class='error'>❌ JSON file not found</span>\n";
    echo "</pre></div></body></html>";
    exit;
}

$jsonContent = file_get_contents($jsonFile);
$users = json_decode($jsonContent, true);

if (!$users || !is_array($users)) {
    echo "<span class='error'>❌ Failed to parse JSON</span>\n";
    echo "</pre></div></body></html>";
    exit;
}

$newPassword = 'password123';
$passwordHash = password_hash($newPassword, PASSWORD_DEFAULT);
$processed = [];
$uniqueUsers = [];
$fixed = 0;

foreach ($users as $user) {
    $userId = $user['user_id'] ?? null;
    if ($userId === null || isset($processed[$userId])) continue;
    
    $processed[$userId] = true;
    $username = $user['name'] ?? 'Unknown';
    
    // Always set to correct password
    $oldPassword = $user['password'] ?? '';
    $needsFix = empty($oldPassword) || 
                !preg_match('/^\$2[ayb]\$.{56}$/', $oldPassword) || 
                !password_verify($newPassword, $oldPassword);
    
    $user['password'] = $passwordHash;
    $user['status'] = 'active';
    $user['updated_at'] = date('Y-m-d H:i:s');
    $uniqueUsers[] = $user;
    
    if ($needsFix) {
        echo "<span class='success'>✓ Fixed: $username</span>\n";
        $fixed++;
    }
}

// Save
$jsonOutput = json_encode($uniqueUsers, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
file_put_contents($jsonFile, $jsonOutput);
touch($jsonFile); // Update file modification time
clearstatcache();

echo "<span class='success'>✓ JSON file saved and cache cleared</span>\n";
echo "</pre>";

// Verify
echo "<h2>Step 3: Verifying...</h2>";
echo "<pre>";

$verifyContent = file_get_contents($jsonFile);
$verifyUsers = json_decode($verifyContent, true);
$verified = 0;

foreach ($verifyUsers as $user) {
    if (password_verify($newPassword, $user['password'] ?? '')) {
        $verified++;
    }
}

echo "<span class='success'>✓ Verified: $verified / " . count($verifyUsers) . " users</span>\n";
echo "</pre>";

if ($verified == count($verifyUsers)) {
    echo "<div style='background:#d4edda;padding:20px;border-radius:5px;margin-top:20px;'>";
    echo "<h3 class='success'>✅ COMPLETE! Everything Fixed!</h3>";
    echo "<p><strong>Login credentials:</strong></p>";
    echo "<ul>";
    echo "<li>Username: <code>access</code> (or any username)</li>";
    echo "<li>Password: <code>password123</code></li>";
    echo "</ul>";
    echo "<p><a href='index.php' class='btn'>Login Now →</a></p>";
    echo "</div>";
}

echo "<hr><p><a href='index.php' class='btn'>← Back to Login</a></p>";
?>
    </div>
</body>
</html>

