<?php
// Debug script to test login process step by step
session_start();
include "config/conn.php";

echo "<h2>Login Debug Process</h2>";

// Test database connection
if ($conn->connect_error) {
    echo "<p style='color: red;'>❌ Database connection failed: " . $conn->connect_error . "</p>";
    exit;
} else {
    echo "<p style='color: green;'>✅ Database connection successful!</p>";
}

// Test credentials (replace with your actual credentials)
$test_username = "admin";
$test_password = "admin123";

echo "<h3>Testing with credentials:</h3>";
echo "<p>Username: " . $test_username . "</p>";
echo "<p>Password: " . $test_password . "</p>";

// Step 1: Check if admin table exists
$result = $conn->query("SHOW TABLES LIKE 'admin'");
if ($result->num_rows > 0) {
    echo "<p style='color: green;'>✅ Admin table exists</p>";
} else {
    echo "<p style='color: red;'>❌ Admin table does not exist!</p>";
    exit;
}

// Step 2: Check admin records
$result = $conn->query("SELECT * FROM admin");
echo "<p>Total admin records: " . $result->num_rows . "</p>";

if ($result->num_rows > 0) {
    echo "<h3>Admin Records:</h3>";
    while ($row = $result->fetch_assoc()) {
        echo "<p>ID: " . $row['admin_id'] . ", Username: " . $row['username'] . "</p>";
        echo "<p>Password Hash: " . $row['password'] . "</p>";
    }
} else {
    echo "<p style='color: red;'>❌ No admin records found!</p>";
    exit;
}

// Step 3: Test exact login query
echo "<h3>Testing Login Query:</h3>";
$stmt = $conn->prepare("SELECT * FROM admin WHERE username = ?");
if (!$stmt) {
    echo "<p style='color: red;'>❌ Prepare statement failed: " . $conn->error . "</p>";
    exit;
}

$stmt->bind_param("s", $test_username);
$stmt->execute();
$result = $stmt->get_result();

echo "<p>Query executed. Found rows: " . $result->num_rows . "</p>";

if ($result->num_rows == 1) {
    $row = $result->fetch_assoc();
    echo "<p style='color: green;'>✅ Username found in database</p>";
    echo "<p>Stored password hash: " . $row['password'] . "</p>";
    
    // Step 4: Test password verification
    echo "<h3>Testing Password Verification:</h3>";
    if (password_verify($test_password, $row['password'])) {
        echo "<p style='color: green;'>✅ Password verification successful!</p>";
        echo "<p>Login should work!</p>";
    } else {
        echo "<p style='color: red;'>❌ Password verification failed!</p>";
        echo "<p>This means the password hash doesn't match the input password.</p>";
        
        // Let's try to create a new hash and compare
        $new_hash = password_hash($test_password, PASSWORD_DEFAULT);
        echo "<p>New hash for '$test_password': " . $new_hash . "</p>";
        
        // Test if the stored hash is valid
        if (password_get_info($row['password'])['algoName'] === 'unknown') {
            echo "<p style='color: red;'>❌ The stored password hash appears to be invalid!</p>";
        } else {
            echo "<p style='color: orange;'>⚠️ The stored hash is valid but doesn't match the test password.</p>";
        }
    }
} else {
    echo "<p style='color: red;'>❌ Username not found in database!</p>";
}

$stmt->close();
$conn->close();
?> 