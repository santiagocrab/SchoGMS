<?php
/**
 * DIRECT JSON FILE FIX - This will fix the users.json file directly
 */

$jsonFile = __DIR__ . '/mongodb_data/schogms/users.json';
$backupFile = $jsonFile . '.backup.' . date('Y-m-d_H-i-s');

echo "<!DOCTYPE html><html><head><title>Fix JSON Passwords</title>";
echo "<style>body{font-family:Arial;max-width:900px;margin:20px auto;padding:20px;}";
echo ".success{color:#28a745;font-weight:bold;} .error{color:#dc3545;font-weight:bold;}";
echo "pre{background:#f8f9fa;padding:15px;border-radius:5px;overflow-x:auto;}</style></head><body>";
echo "<h1>🔧 Direct JSON File Fix</h1><hr>";

// Create backup
if (file_exists($jsonFile)) {
    copy($jsonFile, $backupFile);
    echo "<p class='success'>✓ Backup created: " . basename($backupFile) . "</p>";
}

// Read JSON file
$jsonContent = file_get_contents($jsonFile);
$users = json_decode($jsonContent, true);

if (!$users || !is_array($users)) {
    echo "<p class='error'>❌ Failed to read JSON file or invalid format</p>";
    echo "</body></html>";
    exit;
}

echo "<h2>Step 1: Processing Users...</h2>";
echo "<pre>";

$newPassword = 'password123';
$passwordHash = password_hash($newPassword, PASSWORD_DEFAULT);

$fixed = [];
$processed = [];
$duplicates = [];

foreach ($users as $index => $user) {
    $userId = $user['user_id'] ?? null;
    $username = $user['name'] ?? 'Unknown';
    
    if ($userId === null) {
        echo "<span class='error'>✗ Skipping user without ID: $username</span>\n";
        continue;
    }
    
    // Check for duplicates
    if (isset($processed[$userId])) {
        echo "<span class='error'>✗ Duplicate user_id $userId: $username (removing duplicate)</span>\n";
        $duplicates[] = $index;
        continue;
    }
    
    $processed[$userId] = true;
    
    // Fix password
    $oldPassword = $user['password'] ?? '';
    $needsPasswordFix = false;
    
    // Check if password is plain text (doesn't start with $2y$)
    if (empty($oldPassword) || !preg_match('/^\$2[ayb]\$.{56}$/', $oldPassword)) {
        $needsPasswordFix = true;
        echo "<span class='error'>✗ Plain text password found: $username (ID: $userId)</span>\n";
    } else {
        // Check if password matches "password123"
        if (!password_verify($newPassword, $oldPassword)) {
            $needsPasswordFix = true;
            echo "<span class='error'>✗ Password doesn't match password123: $username (ID: $userId)</span>\n";
        }
    }
    
    // Update user
    $users[$index]['password'] = $passwordHash;
    $users[$index]['status'] = 'active';
    $users[$index]['updated_at'] = date('Y-m-d H:i:s');
    
    if ($needsPasswordFix) {
        echo "<span class='success'>✓ Fixed: $username (ID: $userId)</span>\n";
        $fixed[] = $username;
    } else {
        echo "<span class='info'>- Already correct: $username (ID: $userId)</span>\n";
    }
}

// Remove duplicates (in reverse order to maintain indices)
foreach (array_reverse($duplicates) as $index) {
    unset($users[$index]);
}

// Re-index array
$users = array_values($users);

echo "</pre>";

echo "<h2>Step 2: Saving Fixed JSON...</h2>";
echo "<pre>";

// Save JSON file
$jsonOutput = json_encode($users, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
if (file_put_contents($jsonFile, $jsonOutput)) {
    echo "<span class='success'>✓ JSON file saved successfully!</span>\n";
    echo "Total users: " . count($users) . "\n";
} else {
    echo "<span class='error'>✗ Failed to save JSON file</span>\n";
    echo "</pre></body></html>";
    exit;
}

echo "</pre>";

echo "<h2>Step 3: Verifying Fix...</h2>";
echo "<pre>";

// Reload and verify
$verifyContent = file_get_contents($jsonFile);
$verifyUsers = json_decode($verifyContent, true);

$verified = 0;
$failed = 0;

foreach ($verifyUsers as $user) {
    $username = $user['name'] ?? 'Unknown';
    $password = $user['password'] ?? '';
    
    if (password_verify($newPassword, $password)) {
        echo "<span class='success'>✓ Verified: $username</span>\n";
        $verified++;
    } else {
        echo "<span class='error'>✗ Verification failed: $username</span>\n";
        $failed++;
    }
}

echo "</pre>";

echo "<hr>";
echo "<h2>Summary</h2>";
echo "<ul>";
echo "<li><strong>Users processed:</strong> " . count($processed) . "</li>";
echo "<li><strong>Passwords fixed:</strong> <span class='success'>" . count($fixed) . "</span></li>";
echo "<li><strong>Duplicates removed:</strong> " . count($duplicates) . "</li>";
echo "<li><strong>Passwords verified:</strong> <span class='success'>$verified</span></li>";
if ($failed > 0) {
    echo "<li><strong>Failed:</strong> <span class='error'>$failed</span></li>";
}
echo "</ul>";

if ($verified > 0 && $failed == 0) {
    echo "<div style='background:#d4edda;padding:20px;border-radius:5px;margin-top:20px;'>";
    echo "<h3 class='success'>✅ SUCCESS! All Passwords Fixed!</h3>";
    echo "<p><strong>You can now log in with:</strong></p>";
    echo "<ul>";
    echo "<li><strong>URL:</strong> <a href='index.php'>http://localhost/SchoGMS/index.php</a></li>";
    echo "<li><strong>Password for ALL users:</strong> <code>password123</code></li>";
    echo "</ul>";
    echo "<p><strong>Fixed users:</strong></p>";
    echo "<ul>";
    foreach ($fixed as $name) {
        echo "<li>$name</li>";
    }
    echo "</ul>";
    echo "<p><a href='index.php' style='background:#007bff;color:white;padding:10px 20px;text-decoration:none;border-radius:5px;'>Try Login Now</a></p>";
    echo "</div>";
} else {
    echo "<div style='background:#f8d7da;padding:20px;border-radius:5px;margin-top:20px;'>";
    echo "<h3 class='error'>⚠️ Some Issues Remain</h3>";
    echo "<p>Please check the output above for details.</p>";
    echo "</div>";
}

echo "<hr>";
echo "<p><a href='index.php'>← Back to Login</a></p>";
echo "</body></html>";
?>

