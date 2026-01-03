<?php
// Test script to verify admin login credentials
include "config/conn.php";

echo "<h2>Admin Login Test</h2>";

// Test database connection
if ($conn->connect_error) {
    echo "<p style='color: red;'>Database connection failed: " . $conn->connect_error . "</p>";
    exit;
} else {
    echo "<p style='color: green;'>Database connection successful!</p>";
}

// Test admin table
$result = $conn->query("SELECT * FROM admin");
if ($result) {
    echo "<p style='color: green;'>Admin table exists and is accessible!</p>";
    echo "<p>Number of admin records: " . $result->num_rows . "</p>";
    
    if ($result->num_rows > 0) {
        echo "<h3>Admin Records:</h3>";
        while ($row = $result->fetch_assoc()) {
            echo "<p>ID: " . $row['admin_id'] . ", Username: " . $row['username'] . "</p>";
        }
    }
} else {
    echo "<p style='color: red;'>Error accessing admin table: " . $conn->error . "</p>";
}

// Test password verification
$test_password = "admin123"; // This should be the plain text password
$stmt = $conn->prepare("SELECT password FROM admin WHERE username = 'admin'");
if ($stmt) {
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $hashed_password = $row['password'];
        
        if (password_verify($test_password, $hashed_password)) {
            echo "<p style='color: green;'>Password verification successful!</p>";
        } else {
            echo "<p style='color: red;'>Password verification failed!</p>";
            echo "<p>Hashed password in database: " . $hashed_password . "</p>";
        }
    } else {
        echo "<p style='color: red;'>Admin user not found!</p>";
    }
    $stmt->close();
} else {
    echo "<p style='color: red;'>Error preparing statement: " . $conn->error . "</p>";
}

$conn->close();
?> 