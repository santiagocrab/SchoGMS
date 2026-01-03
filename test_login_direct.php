<?php
/**
 * Direct Login Test - This will show exactly what's wrong
 */
session_start();
require_once 'conn_mongodb.php';

echo "<!DOCTYPE html><html><head><title>Direct Login Test</title>";
echo "<style>body{font-family:Arial;max-width:900px;margin:20px auto;padding:20px;}";
echo ".success{color:#28a745;font-weight:bold;} .error{color:#dc3545;font-weight:bold;}";
echo "pre{background:#f8f9fa;padding:15px;border-radius:5px;overflow-x:auto;}";
echo "table{border-collapse:collapse;width:100%;margin:20px 0;}";
echo "th,td{border:1px solid #ddd;padding:8px;text-align:left;}";
echo "th{background:#007bff;color:white;}</style></head><body>";
echo "<h1>🔍 Direct Login Test</h1><hr>";

// Test credentials
$testUsername = isset($_GET['username']) ? $_GET['username'] : 'access';
$testPassword = isset($_GET['password']) ? $_GET['password'] : 'password123';

echo "<h2>Testing Login For:</h2>";
echo "<ul>";
echo "<li><strong>Username:</strong> $testUsername</li>";
echo "<li><strong>Password:</strong> $testPassword</li>";
echo "</ul>";

echo "<h2>Step 1: Finding User in Database...</h2>";
echo "<pre>";

// Try to find user
$user = $users->findOne(['name' => $testUsername]);

if (!$user) {
    echo "<span class='error'>✗ User not found with exact match: '$testUsername'</span>\n";
    echo "\nTrying case-insensitive search...\n";
    
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
        echo "----------------------------------------\n";
        foreach ($allUsers as $u) {
            echo "  - " . ($u['name'] ?? 'NO NAME') . " (ID: " . ($u['user_id'] ?? 'NO ID') . ", Role: " . ($u['role'] ?? 'NO ROLE') . ")\n";
        }
        echo "</pre></body></html>";
        exit;
    }
}

echo "<span class='success'>✓ User found!</span>\n";
echo "  User ID: " . ($user['user_id'] ?? 'N/A') . "\n";
echo "  Username: " . ($user['name'] ?? 'N/A') . "\n";
echo "  Role: " . ($user['role'] ?? 'N/A') . "\n";
echo "  Status: " . ($user['status'] ?? 'N/A') . "\n";
echo "</pre>";

echo "<h2>Step 2: Checking Password...</h2>";
echo "<pre>";

$dbPassword = $user['password'] ?? null;

if (empty($dbPassword)) {
    echo "<span class='error'>✗ NO PASSWORD SET IN DATABASE!</span>\n";
    echo "\nThis is the problem! The user has no password.\n";
    echo "We need to set the password.\n";
} else {
    echo "Password hash exists: " . substr($dbPassword, 0, 20) . "...\n";
    echo "Testing password verification...\n";
    
    if (password_verify($testPassword, $dbPassword)) {
        echo "<span class='success'>✓ PASSWORD VERIFICATION: SUCCESS!</span>\n";
        echo "\nThe password is correct. Login should work.\n";
    } else {
        echo "<span class='error'>✗ PASSWORD VERIFICATION: FAILED</span>\n";
        echo "\nThe password you entered does NOT match the stored hash.\n";
        echo "We need to reset the password.\n";
    }
}

echo "</pre>";

echo "<h2>Step 3: Fixing Password...</h2>";
echo "<pre>";

$newPasswordHash = password_hash($testPassword, PASSWORD_DEFAULT);
$userId = $user['user_id'] ?? null;

if ($userId) {
    $result = $users->updateOne(
        ['user_id' => $userId],
        ['$set' => [
            'password' => $newPasswordHash,
            'status' => 'active'
        ]]
    );
    
    $modified = is_array($result) ? ($result['modifiedCount'] ?? 0) : 0;
    
    if ($modified > 0) {
        echo "<span class='success'>✓ Password updated successfully!</span>\n";
    } else {
        // Verify it was set
        $verifyUser = $users->findOne(['user_id' => $userId]);
        if ($verifyUser && isset($verifyUser['password'])) {
            if (password_verify($testPassword, $verifyUser['password'])) {
                echo "<span class='success'>✓ Password is now correct!</span>\n";
            } else {
                echo "<span class='error'>✗ Password update may have failed</span>\n";
            }
        }
    }
    
    // Test again
    $finalUser = $users->findOne(['user_id' => $userId]);
    if ($finalUser && isset($finalUser['password'])) {
        if (password_verify($testPassword, $finalUser['password'])) {
            echo "<span class='success'>✓ FINAL TEST: Password verification SUCCESS!</span>\n";
        } else {
            echo "<span class='error'>✗ FINAL TEST: Password verification FAILED</span>\n";
        }
    }
} else {
    echo "<span class='error'>✗ Cannot update - no user_id</span>\n";
}

echo "</pre>";

echo "<h2>Step 4: Test Actual Login...</h2>";
echo "<pre>";

// Simulate the login process
$testUser = $users->findOne(['name' => $testUsername]);
if (!$testUser) {
    $allUsers = $users->find([]);
    foreach ($allUsers as $u) {
        if (isset($u['name']) && strcasecmp(trim($u['name']), $testUsername) === 0) {
            $testUser = $u;
            break;
        }
    }
}

if ($testUser) {
    $testPasswordHash = $testUser['password'] ?? null;
    if ($testPasswordHash && password_verify($testPassword, $testPasswordHash)) {
        echo "<span class='success'>✅ LOGIN WOULD SUCCEED!</span>\n";
        echo "\nYou can now log in with:\n";
        echo "  Username: " . ($testUser['name'] ?? 'N/A') . "\n";
        echo "  Password: $testPassword\n";
        echo "  Role: " . ($testUser['role'] ?? 'N/A') . "\n";
        echo "  Will redirect to: users/" . ($testUser['role'] ?? 'unknown') . "/\n";
    } else {
        echo "<span class='error'>❌ LOGIN WOULD FAIL</span>\n";
        echo "Password verification failed.\n";
    }
} else {
    echo "<span class='error'>❌ LOGIN WOULD FAIL</span>\n";
    echo "User not found.\n";
}

echo "</pre>";

echo "<hr>";
echo "<h2>Quick Actions</h2>";
echo "<p>";
echo "<a href='force_fix_passwords.php' style='background:#dc3545;color:white;padding:10px 20px;text-decoration:none;border-radius:5px;margin:5px;'>Fix ALL Passwords</a> ";
echo "<a href='index.php' style='background:#007bff;color:white;padding:10px 20px;text-decoration:none;border-radius:5px;margin:5px;'>Try Login</a> ";
echo "<a href='?username=access&password=password123' style='background:#28a745;color:white;padding:10px 20px;text-decoration:none;border-radius:5px;margin:5px;'>Test Again</a>";
echo "</p>";

echo "</body></html>";
?>

