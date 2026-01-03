<?php
/**
 * Simple Login Test
 */

// Include the connection
require_once 'conn.php';

echo "<h2>Simple Login Test</h2>";

// Test the registrar login
$email = 'registrarisulan@mail';
$password = 'schogms123';

echo "<h3>Testing Registrar Login</h3>";
echo "Email: {$email}<br>";
echo "Password: {$password}<br><br>";

// Use the database helper to authenticate
if (isset($dbHelper)) {
    $user = $dbHelper->authenticateUser($email, $password);
    
    if ($user) {
        echo "<div style='color: green; font-weight: bold;'>✅ LOGIN SUCCESSFUL!</div>";
        echo "<h4>User Information:</h4>";
        echo "Name: " . $user['name'] . "<br>";
        echo "Email: " . $user['email'] . "<br>";
        echo "Role: " . $user['role'] . "<br>";
        echo "Status: " . $user['status'] . "<br>";
        echo "Campus: " . ($user['campus'] ?? 'N/A') . "<br>";
    } else {
        echo "<div style='color: red; font-weight: bold;'>❌ LOGIN FAILED!</div>";
    }
} else {
    echo "<div style='color: red;'>❌ Database helper not available</div>";
}

echo "<h3>Test Complete!</h3>";
echo "<p><a href='index.php'>Go to Main Login Page</a></p>";
?>
