<?php
// Test the updated config file
require_once 'admin-12-02/config/conn.php';

if ($conn) {
    echo "✅ Config file connection successful!\n";
    echo "Database: schogms\n";
    
    // Test a simple query
    $result = $conn->query("SELECT COUNT(*) as count FROM admin");
    if ($result) {
        $row = $result->fetch_assoc();
        echo "✅ Admin table accessible - contains " . $row['count'] . " record(s)\n";
    }
    
    $conn->close();
} else {
    echo "❌ Config file connection failed\n";
}
?>


