<?php
// Your database connection parameters
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "schogms";

// Create a connection to the database
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
// echo "Connected successfully";
?>