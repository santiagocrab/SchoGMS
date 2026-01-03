<?php
// Simple test to see what's really happening
echo "<h1>SCHOGMS LOGIN TEST</h1>";

// Test MongoDB connection
include 'conn_mongodb.php';

echo "<h2>1. MongoDB Connection Test:</h2>";
if ($mongodb && $mongodb->testConnection()) {
    echo "<p style='color: green; font-size: 18px;'>✅ MONGODB CONNECTED SUCCESSFULLY!</p>";
} else {
    echo "<p style='color: red; font-size: 18px;'>❌ MONGODB CONNECTION FAILED!</p>";
    echo "<p>This is why login is not working!</p>";
    exit;
}

echo "<h2>2. Chairman User Test:</h2>";
$chairman = $users->findOne(['name' => 'chairman']);

if ($chairman) {
    echo "<p style='color: green; font-size: 18px;'>✅ CHAIRMAN USER FOUND!</p>";
    echo "<p><strong>User Details:</strong></p>";
    echo "<ul>";
    echo "<li>ID: " . $chairman['user_id'] . "</li>";
    echo "<li>Name: " . $chairman['name'] . "</li>";
    echo "<li>Role: " . $chairman['role'] . "</li>";
    echo "<li>Status: " . $chairman['status'] . "</li>";
    echo "<li>Email: " . $chairman['email'] . "</li>";
    echo "</ul>";
} else {
    echo "<p style='color: red; font-size: 18px;'>❌ CHAIRMAN USER NOT FOUND!</p>";
    echo "<p>This is why login is not working!</p>";
    exit;
}

echo "<h2>3. Password Test:</h2>";
$test_password = "schogms123";
if (password_verify($test_password, $chairman['password'])) {
    echo "<p style='color: green; font-size: 18px;'>✅ PASSWORD IS CORRECT!</p>";
} else {
    echo "<p style='color: red; font-size: 18px;'>❌ PASSWORD IS WRONG!</p>";
    echo "<p>Stored hash: " . $chairman['password'] . "</p>";
    echo "<p>Testing password: " . $test_password . "</p>";
}

echo "<h2>4. Status Check:</h2>";
if ($chairman['status'] === 'active') {
    echo "<p style='color: green; font-size: 18px;'>✅ ACCOUNT IS ACTIVE!</p>";
} else {
    echo "<p style='color: red; font-size: 18px;'>❌ ACCOUNT IS NOT ACTIVE!</p>";
    echo "<p>Status: " . $chairman['status'] . "</p>";
}

echo "<h2>5. FINAL RESULT:</h2>";
if ($mongodb && $mongodb->testConnection() && 
    $chairman && 
    $chairman['status'] === 'active' && 
    password_verify($test_password, $chairman['password'])) {
    
    echo "<p style='color: green; font-size: 24px; font-weight: bold;'>🎉 EVERYTHING IS WORKING!</p>";
    echo "<p style='color: green; font-size: 18px;'>The login should work with:</p>";
    echo "<p><strong>Username: chairman</strong></p>";
    echo "<p><strong>Password: schogms123</strong></p>";
    
    // Try to set session and redirect
    session_start();
    $_SESSION['user_id'] = $chairman['user_id'];
    $_SESSION['username'] = $chairman['name'];
    $_SESSION['role'] = $chairman['role'];
    
    echo "<p style='color: blue; font-size: 18px;'>Session variables set successfully!</p>";
    echo "<p><a href='users/chairman/' style='background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>GO TO CHAIRMAN DASHBOARD</a></p>";
    
} else {
    echo "<p style='color: red; font-size: 24px; font-weight: bold;'>❌ SOMETHING IS WRONG!</p>";
    echo "<p>Check the errors above to see what's failing.</p>";
}

echo "<hr>";
echo "<h2>6. ALTERNATIVE LOGIN METHODS:</h2>";
echo "<p><strong>Try these direct links:</strong></p>";
echo "<p><a href='users/chairman/' target='_blank'>Direct Chairman Dashboard</a></p>";
echo "<p><a href='admin/' target='_blank'>Admin Dashboard</a></p>";

echo "<h2>7. BROWSER CACHE SOLUTION:</h2>";
echo "<p><strong>If everything above shows ✅ but login still fails:</strong></p>";
echo "<ol>";
echo "<li>Close your browser completely</li>";
echo "<li>Open a new incognito/private window</li>";
echo "<li>Go to: <a href='new_login.php'>new_login.php</a></li>";
echo "<li>Or try: <a href='login.php?v=" . time() . "'>login.php with timestamp</a></li>";
echo "</ol>";
?>



