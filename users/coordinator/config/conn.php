<?php
// Your database connection parameters
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "schogms";

// Create a connection to the database with timeout settings
$conn = new mysqli($servername, $username, $password, $dbname);

// Set connection timeout
$conn->options(MYSQLI_OPT_CONNECT_TIMEOUT, 5);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set query timeout (using MySQL's max_execution_time if available, otherwise skip)
// Note: max_execution_time is a PHP setting, not MySQL. Use PHP's ini_set instead.
@$conn->query("SET SESSION max_execution_time = 10"); // Suppress error if not supported
// echo "Connected successfully";
register_shutdown_function(function() use ($conn) {
    if ($conn) {
        $conn->close();
    }
});
?>