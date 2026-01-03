<?php
// Script to reset admin password
include "config/conn.php";

echo "<h2>Reset Admin Password</h2>";

// Test database connection
if ($conn->connect_error) {
    echo "<p style='color: red;'>Database connection failed: " . $conn->connect_error . "</p>";
    exit;
} else {
    echo "<p style='color: green;'>Database connection successful!</p>";
}

// New password
$new_password = "admin123";
$hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

// Update admin password
$stmt = $conn->prepare("UPDATE admin SET password = ? WHERE username = 'admin'");
if ($stmt) {
    $stmt->bind_param("s", $hashed_password);
    
    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            echo "<p style='color: green;'>Admin password updated successfully!</p>";
            echo "<p><strong>New credentials:</strong></p>";
            echo "<p>Username: admin</p>";
            echo "<p>Password: " . $new_password . "</p>";
            echo "<p><strong>Please delete this file after use for security!</strong></p>";
        } else {
            echo "<p style='color: orange;'>No rows were updated. Admin user might not exist.</p>";
            
            // Try to create admin user if it doesn't exist
            $insert_stmt = $conn->prepare("INSERT INTO admin (username, password) VALUES (?, ?)");
            if ($insert_stmt) {
                $insert_stmt->bind_param("ss", "admin", $hashed_password);
                if ($insert_stmt->execute()) {
                    echo "<p style='color: green;'>Admin user created successfully!</p>";
                    echo "<p><strong>New credentials:</strong></p>";
                    echo "<p>Username: admin</p>";
                    echo "<p>Password: " . $new_password . "</p>";
                } else {
                    echo "<p style='color: red;'>Error creating admin user: " . $insert_stmt->error . "</p>";
                }
                $insert_stmt->close();
            }
        }
    } else {
        echo "<p style='color: red;'>Error updating password: " . $stmt->error . "</p>";
    }
    $stmt->close();
} else {
    echo "<p style='color: red;'>Error preparing statement: " . $conn->error . "</p>";
}

$conn->close();
?> 