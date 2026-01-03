<?php
// Start the session
include 'session.php';
// Include the database connection file
require_once 'conn.php'; // Modify with the actual connection path

// Check if the user is logged in and has the role of 'chairman'
if (isset($_SESSION['role']) && $_SESSION['role'] === 'chairman') {
    // SQL query to count the total records in the ched_masterlist table
    $sql = "SELECT COUNT(*) AS total_records FROM ched_masterlist";

    // Execute the query
    $result = $conn->query($sql);

    // Check if the query was successful
    if ($result) {
        // Fetch the row containing the count
        $row = $result->fetch_assoc();
        // Output the total count of records
        echo "Total records in ched_masterlist: " . $row['total_records'];
    } else {
        // If the query failed, output an error message
        echo "Error: " . $conn->error;
    }
} else {
    // If the user is not a chairman, display an access denied message
    echo "Access denied: You do not have permission to view this information.";
}

// Close the connection
$conn->close();
?>
