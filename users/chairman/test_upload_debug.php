<?php
include 'config/session.php';

echo "<h2>CHED TDP Upload Debug Test</h2>";

// Test database connection
if ($conn->connect_error) {
    echo "<p style='color: red;'>❌ Database connection failed: " . $conn->connect_error . "</p>";
    exit;
} else {
    echo "<p style='color: green;'>✅ Database connection successful!</p>";
}

// Test if tables exist
$tables = ['ched_masterlist', 'ched_upload_log'];
foreach ($tables as $table) {
    $result = $conn->query("SHOW TABLES LIKE '$table'");
    if ($result->num_rows > 0) {
        echo "<p style='color: green;'>✅ Table '$table' exists</p>";
    } else {
        echo "<p style='color: red;'>❌ Table '$table' does not exist</p>";
    }
}

// Test upload directory
$upload_dir = '../../uploads/ched_tdp/';
if (is_dir($upload_dir)) {
    if (is_writable($upload_dir)) {
        echo "<p style='color: green;'>✅ Upload directory exists and is writable: $upload_dir</p>";
    } else {
        echo "<p style='color: red;'>❌ Upload directory exists but is not writable: $upload_dir</p>";
    }
} else {
    echo "<p style='color: red;'>❌ Upload directory does not exist: $upload_dir</p>";
}

// Test PhpSpreadsheet
try {
    require_once '../vendor/autoload.php';
    echo "<p style='color: green;'>✅ PhpSpreadsheet library is available</p>";
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ PhpSpreadsheet library not found: " . $e->getMessage() . "</p>";
}

// Show current table structure
echo "<h3>Current ched_masterlist table structure:</h3>";
$result = $conn->query("DESCRIBE ched_masterlist");
if ($result) {
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row['Field'] . "</td>";
        echo "<td>" . $row['Type'] . "</td>";
        echo "<td>" . $row['Null'] . "</td>";
        echo "<td>" . $row['Key'] . "</td>";
        echo "<td>" . $row['Default'] . "</td>";
        echo "<td>" . $row['Extra'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p style='color: red;'>❌ Could not describe table structure: " . $conn->error . "</p>";
}

echo "<br><div style='background: #f8f9fa; padding: 15px; border-radius: 5px; margin: 20px 0;'>";
echo "<h3>🔧 Next Steps:</h3>";
echo "<ol>";
echo "<li><a href='update_database_schema.php'>Update Database Schema</a> - Add missing fields</li>";
echo "<li><a href='upload_ched_tdp.php'>Try Upload Again</a> - Test the upload process</li>";
echo "<li>Check error logs if upload still fails</li>";
echo "</ol>";
echo "</div>";
?>
