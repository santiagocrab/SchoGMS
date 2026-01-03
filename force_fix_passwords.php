<?php
/**
 * FORCE FIX ALL PASSWORDS - This will definitely work
 */

require_once 'conn_mongodb.php';

$default_password = 'password123';
$password_hash = password_hash($default_password, PASSWORD_DEFAULT);

echo "<!DOCTYPE html><html><head><title>Force Fix Passwords</title>";
echo "<style>body{font-family:Arial;max-width:800px;margin:50px auto;padding:20px;}";
echo ".success{color:#28a745;font-weight:bold;} .error{color:#dc3545;font-weight:bold;}";
echo "pre{background:#f8f9fa;padding:15px;border-radius:5px;}</style></head><body>";
echo "<h1>🔧 Force Fix All Passwords</h1><hr>";

echo "<h2>Step 1: Getting All Users...</h2>";
echo "<pre>";

// Get ALL users from MongoDB
$allUsers = $users->find([]);
$userArray = [];

foreach ($allUsers as $user) {
    $userArray[] = $user;
}

echo "Found " . count($userArray) . " users in database\n\n";

if (count($userArray) == 0) {
    echo "<span class='error'>❌ NO USERS FOUND IN DATABASE!</span>\n";
    echo "You need to create users first or import them from MySQL.\n";
    echo "</pre></body></html>";
    exit;
}

echo "</pre>";

echo "<h2>Step 2: Updating All Passwords...</h2>";
echo "<pre>";

$updated = 0;
$errors = [];

foreach ($userArray as $user) {
    $userId = $user['user_id'] ?? null;
    $username = $user['name'] ?? 'Unknown';
    $role = $user['role'] ?? 'unknown';
    
    if ($userId === null) {
        echo "<span class='error'>✗ Skipping: $username (no user_id)</span>\n";
        continue;
    }
    
    try {
        // Force update - set password and status
        $updateResult = $users->updateOne(
            ['user_id' => $userId],
            ['$set' => [
                'password' => $password_hash,
                'status' => 'active'
            ]]
        );
        
        // Check result
        $modified = 0;
        if (is_array($updateResult)) {
            $modified = $updateResult['modifiedCount'] ?? 0;
        } elseif (is_object($updateResult) && method_exists($updateResult, 'getModifiedCount')) {
            $modified = $updateResult->getModifiedCount();
        }
        
        if ($modified > 0) {
            echo "<span class='success'>✓ Updated: $username (ID: $userId, Role: $role)</span>\n";
            $updated++;
        } else {
            // Try to verify the password was set
            $verifyUser = $users->findOne(['user_id' => $userId]);
            if ($verifyUser && isset($verifyUser['password'])) {
                if (password_verify($default_password, $verifyUser['password'])) {
                    echo "<span class='success'>✓ Already correct: $username (ID: $userId)</span>\n";
                } else {
                    // Force update again with different method
                    $users->updateOne(
                        ['user_id' => $userId],
                        ['$set' => ['password' => $password_hash]]
                    );
                    echo "<span class='success'>✓ Force updated: $username (ID: $userId)</span>\n";
                    $updated++;
                }
            } else {
                echo "<span class='error'>✗ Failed: $username (ID: $userId) - Could not verify</span>\n";
                $errors[] = $username;
            }
        }
    } catch (Exception $e) {
        echo "<span class='error'>✗ Error: $username - " . $e->getMessage() . "</span>\n";
        $errors[] = $username;
    }
}

echo "</pre>";

echo "<h2>Step 3: Verifying Passwords...</h2>";
echo "<pre>";

$verified = 0;
$failed = 0;

foreach ($userArray as $user) {
    $userId = $user['user_id'] ?? null;
    $username = $user['name'] ?? 'Unknown';
    
    if ($userId === null) continue;
    
    $testUser = $users->findOne(['user_id' => $userId]);
    if ($testUser && isset($testUser['password'])) {
        if (password_verify($default_password, $testUser['password'])) {
            echo "<span class='success'>✓ Verified: $username</span>\n";
            $verified++;
        } else {
            echo "<span class='error'>✗ Verification failed: $username</span>\n";
            $failed++;
        }
    } else {
        echo "<span class='error'>✗ No password found: $username</span>\n";
        $failed++;
    }
}

echo "</pre>";

echo "<h2>Step 4: Test Login...</h2>";
echo "<pre>";

// Test with first coordinator
$testCoordinator = $users->findOne(['role' => 'coordinator']);
if ($testCoordinator) {
    $testUsername = $testCoordinator['name'] ?? 'Unknown';
    $testPasswordHash = $testCoordinator['password'] ?? '';
    
    if ($testPasswordHash && password_verify($default_password, $testPasswordHash)) {
        echo "<span class='success'>✓ Test login would work for: $testUsername</span>\n";
        echo "   Username: $testUsername\n";
        echo "   Password: $default_password\n";
        echo "   Role: " . ($testCoordinator['role'] ?? 'N/A') . "\n";
    } else {
        echo "<span class='error'>✗ Test login failed for: $testUsername</span>\n";
    }
} else {
    echo "<span class='error'>✗ No coordinator found to test</span>\n";
}

echo "</pre>";

echo "<hr>";
echo "<h2>Summary</h2>";
echo "<ul>";
echo "<li><strong>Users found:</strong> " . count($userArray) . "</li>";
echo "<li><strong>Passwords updated:</strong> <span class='success'>$updated</span></li>";
echo "<li><strong>Passwords verified:</strong> <span class='success'>$verified</span></li>";
if ($failed > 0) {
    echo "<li><strong>Failed:</strong> <span class='error'>$failed</span></li>";
}
echo "</ul>";

if ($verified > 0) {
    echo "<div style='background:#d4edda;padding:20px;border-radius:5px;margin-top:20px;'>";
    echo "<h3 class='success'>✅ SUCCESS!</h3>";
    echo "<p><strong>You can now log in with:</strong></p>";
    echo "<ul>";
    echo "<li><strong>URL:</strong> <a href='index.php'>http://localhost/SchoGMS/index.php</a></li>";
    echo "<li><strong>Password for ALL users:</strong> <code>password123</code></li>";
    echo "</ul>";
    echo "<p><strong>Available usernames:</strong></p>";
    echo "<ul>";
    foreach ($userArray as $u) {
        if (isset($u['name']) && isset($u['role'])) {
            echo "<li>" . $u['name'] . " (" . $u['role'] . ")</li>";
        }
    }
    echo "</ul>";
    echo "<p><a href='index.php' style='background:#007bff;color:white;padding:10px 20px;text-decoration:none;border-radius:5px;'>Go to Login Page</a></p>";
    echo "</div>";
} else {
    echo "<div style='background:#f8d7da;padding:20px;border-radius:5px;margin-top:20px;'>";
    echo "<h3 class='error'>❌ ISSUE DETECTED</h3>";
    echo "<p>Passwords were not set correctly. Please check the MongoDB connection.</p>";
    echo "<p><a href='debug_login_mongodb.php'>Debug Login Issues</a></p>";
    echo "</div>";
}

echo "<hr>";
echo "<p><a href='index.php'>← Back to Login</a> | <a href='LOGIN-GUIDE.php'>Login Guide</a></p>";
echo "</body></html>";
?>

