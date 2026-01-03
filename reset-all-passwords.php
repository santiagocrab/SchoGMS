<?php
/**
 * Reset All Passwords Script
 * This script resets all admin and user passwords to default values
 * 
 * WARNING: This will change ALL passwords in the database!
 */

include 'admin-12-02/config/conn.php';

// Default passwords
$default_admin_password = 'admin123';
$default_user_password = 'password123';

// Hash the passwords
$admin_password_hash = password_hash($default_admin_password, PASSWORD_DEFAULT);
$user_password_hash = password_hash($default_user_password, PASSWORD_DEFAULT);

echo "<h2>Password Reset Script</h2>";
echo "<p>This script will reset all passwords to default values.</p>";
echo "<hr>";

// Check if this is a POST request (confirmation)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm'])) {
    
    // Reset Admin Passwords
    echo "<h3>Resetting Admin Passwords...</h3>";
    $stmt = $conn->prepare("UPDATE admin SET password = ?");
    $stmt->bind_param("s", $admin_password_hash);
    
    if ($stmt->execute()) {
        $admin_count = $stmt->affected_rows;
        echo "<p style='color: green;'>✅ Reset $admin_count admin password(s)</p>";
        echo "<p><strong>Admin Default Password:</strong> <code>$default_admin_password</code></p>";
    } else {
        echo "<p style='color: red;'>❌ Error resetting admin passwords: " . $stmt->error . "</p>";
    }
    $stmt->close();
    
    // Reset User Passwords
    echo "<h3>Resetting User Passwords...</h3>";
    $stmt = $conn->prepare("UPDATE users SET password = ?");
    $stmt->bind_param("s", $user_password_hash);
    
    if ($stmt->execute()) {
        $user_count = $stmt->affected_rows;
        echo "<p style='color: green;'>✅ Reset $user_count user password(s)</p>";
        echo "<p><strong>User Default Password:</strong> <code>$default_user_password</code></p>";
    } else {
        echo "<p style='color: red;'>❌ Error resetting user passwords: " . $stmt->error . "</p>";
    }
    $stmt->close();
    
    // Also activate all users so they can log in
    echo "<h3>Activating All Users...</h3>";
    $stmt = $conn->prepare("UPDATE users SET status = 'active' WHERE status != 'active'");
    if ($stmt->execute()) {
        $activated_count = $stmt->affected_rows;
        echo "<p style='color: green;'>✅ Activated $activated_count user(s)</p>";
    }
    $stmt->close();
    
    echo "<hr>";
    echo "<h3 style='color: green;'>✅ Password Reset Complete!</h3>";
    echo "<div style='background: #e8f5e9; padding: 15px; border-radius: 5px; margin-top: 20px;'>";
    echo "<h4>Login Credentials:</h4>";
    echo "<p><strong>Admin Accounts:</strong></p>";
    echo "<ul>";
    
    // List all admin accounts
    $result = $conn->query("SELECT username FROM admin");
    while ($row = $result->fetch_assoc()) {
        echo "<li>Username: <strong>" . htmlspecialchars($row['username']) . "</strong> | Password: <strong>$default_admin_password</strong></li>";
    }
    
    echo "</ul>";
    echo "<p><strong>User Accounts:</strong></p>";
    echo "<p>All users can log in with their username and password: <strong>$default_user_password</strong></p>";
    echo "<p><em>Note: Check the users table in phpMyAdmin to see all usernames.</em></p>";
    echo "</div>";
    
} else {
    // Show confirmation form
    // Get counts
    $admin_result = $conn->query("SELECT COUNT(*) as count FROM admin");
    $admin_count = $admin_result->fetch_assoc()['count'];
    
    $user_result = $conn->query("SELECT COUNT(*) as count FROM users");
    $user_count = $user_result->fetch_assoc()['count'];
    
    echo "<div style='background: #fff3cd; padding: 15px; border-radius: 5px; margin: 20px 0;'>";
    echo "<h3>⚠️ Warning</h3>";
    echo "<p>This will reset passwords for:</p>";
    echo "<ul>";
    echo "<li><strong>$admin_count</strong> admin account(s)</li>";
    echo "<li><strong>$user_count</strong> user account(s)</li>";
    echo "</ul>";
    echo "<p><strong>New Passwords:</strong></p>";
    echo "<ul>";
    echo "<li>Admin: <code>$default_admin_password</code></li>";
    echo "<li>Users: <code>$default_user_password</code></li>";
    echo "</ul>";
    echo "</div>";
    
    echo "<form method='POST' style='margin: 20px 0;'>";
    echo "<input type='hidden' name='confirm' value='1'>";
    echo "<button type='submit' style='background: #dc3545; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; font-size: 16px;'>";
    echo "⚠️ Confirm: Reset All Passwords";
    echo "</button>";
    echo "</form>";
    
    // Show current accounts
    echo "<h3>Current Admin Accounts:</h3>";
    $result = $conn->query("SELECT admin_id, username FROM admin");
    echo "<table border='1' cellpadding='5' style='border-collapse: collapse;'>";
    echo "<tr><th>ID</th><th>Username</th></tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr><td>" . $row['admin_id'] . "</td><td>" . htmlspecialchars($row['username']) . "</td></tr>";
    }
    echo "</table>";
    
    echo "<h3>Sample User Accounts (first 10):</h3>";
    $result = $conn->query("SELECT user_id, name, role, status FROM users LIMIT 10");
    echo "<table border='1' cellpadding='5' style='border-collapse: collapse;'>";
    echo "<tr><th>ID</th><th>Name</th><th>Role</th><th>Status</th></tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr><td>" . $row['user_id'] . "</td><td>" . htmlspecialchars($row['name']) . "</td><td>" . htmlspecialchars($row['role']) . "</td><td>" . htmlspecialchars($row['status']) . "</td></tr>";
    }
    echo "</table>";
}

$conn->close();
?>

