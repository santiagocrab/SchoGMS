<?php
// Test database connection script
// Run this after importing the database to verify everything works

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "schogms";

echo "Testing database connection...\n";
echo "Server: $servername\n";
echo "Username: $username\n";
echo "Database: $dbname\n\n";

// Create a connection to the database
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("❌ Connection failed: " . $conn->connect_error . "\n");
}

echo "✅ Connected successfully!\n\n";

// Test query
$result = $conn->query("SHOW TABLES");
if ($result) {
    $tableCount = $result->num_rows;
    echo "✅ Database contains $tableCount tables\n";
    
    if ($tableCount > 0) {
        echo "\nSample tables:\n";
        $count = 0;
        while (($row = $result->fetch_array()) && $count < 5) {
            echo "  - " . $row[0] . "\n";
            $count++;
        }
    }
} else {
    echo "⚠️  Could not list tables: " . $conn->error . "\n";
}

$conn->close();
echo "\n✅ Database connection test completed successfully!\n";
?>

