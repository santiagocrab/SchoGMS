<?php
// Script to check the actual password hash and test different passwords
include "config/conn.php";

echo "<h2>Password Hash Check</h2>";

// Get the actual password hash from database
$stmt = $conn->prepare("SELECT password FROM admin WHERE username = 'admin'");
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $stored_hash = $row['password'];
    
    echo "<p><strong>Stored password hash:</strong> " . $stored_hash . "</p>";
    
    // Test common passwords
    $test_passwords = [
        'admin',
        'admin123',
        'password',
        '123456',
        'admin@123',
        'Admin123',
        'ADMIN',
        'ADMIN123'
    ];
    
    echo "<h3>Testing Common Passwords:</h3>";
    $found = false;
    
    foreach ($test_passwords as $test_pwd) {
        if (password_verify($test_pwd, $stored_hash)) {
            echo "<p style='color: green;'>✅ <strong>MATCH FOUND!</strong> Password: '$test_pwd'</p>";
            $found = true;
            break;
        } else {
            echo "<p style='color: red;'>❌ Password '$test_pwd' does not match</p>";
        }
    }
    
    if (!$found) {
        echo "<p style='color: orange;'>⚠️ None of the common passwords matched.</p>";
        echo "<p>The password might be something else or the hash might be corrupted.</p>";
    }
    
    // Check hash info
    $hash_info = password_get_info($stored_hash);
    echo "<h3>Hash Information:</h3>";
    echo "<p>Algorithm: " . $hash_info['algoName'] . "</p>";
    echo "<p>Cost: " . $hash_info['options']['cost'] . "</p>";
    
} else {
    echo "<p style='color: red;'>❌ Admin user not found!</p>";
}

$stmt->close();
$conn->close();
?> 