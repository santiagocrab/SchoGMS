<?php
// Include the database connection file
include 'session.php';
require_once 'conn.php'; // Modify with the actual connection path

// SQL query to count the total records in the ched_masterlist table
$sql = "SELECT COUNT(*) AS total_records FROM ched_masterlist where sheet_name = '$sheet_name'";

// Execute the query
$result = $conn->query($sql);

// Check if the query was successful
if ($result) {
    // Fetch the row containing the count
    $row = $result->fetch_assoc();
    // Output the total count of records
    // echo "Total records in ched_masterlist: " . $row['total_records'];
} else {
    // If the query failed, output an error message
    echo "Error: " . $conn->error;
}
// Close the connection
$conn->close();
?>
<?php
// Include the database connection file
include 'session.php';
require_once 'conn.php'; // Modify with the actual connection path

// SQL query to count distinct course_program_enrolled
$sql = "SELECT COUNT(DISTINCT course_program_enrolled) AS total_courses FROM ched_masterlist where campus = '$sheet_name'";

// Execute the query
$result = $conn->query($sql);

// Check if the query was successful
if ($result) {
    // Fetch the row containing the count
    $row = $result->fetch_assoc();
    // Output the total count of distinct course_program_enrolled
    echo "Total distinct course/programs enrolled: " . $row['total_courses'];
} else {
    // If the query failed, output an error message
    echo "Error: " . $conn->error;
}

// Close the connection
$conn->close();
?>
<?php
// Include the database connection file
include 'session.php';
require_once 'conn.php'; // Modify with the actual connection path

// SQL query to count distinct file_group
$sql = "SELECT COUNT(DISTINCT file_group) AS total_file_groups FROM ched_masterlist where sheet_name = '$sheet_name'";

// Execute the query
$result = $conn->query($sql);

// Check if the query was successful
if ($result) {
    // Fetch the row containing the count
    $row = $result->fetch_assoc();
    // Output the total count of distinct file_groups
    echo "Total distinct file groups: " . $row['total_file_groups'];
} else {
    // If the query failed, output an error message
    echo "Error: " . $conn->error;
}

// Close the connection
$conn->close();
?>
<?php
// Include the database connection file
require_once 'conn.php'; // Modify with the actual connection path

// SQL query to count distinct filenames
$sql = "SELECT COUNT(DISTINCT filename) AS total_filenames FROM ched_masterlist";

// Execute the query
$result = $conn->query($sql);

// Check if the query was successful
if ($result) {
    // Fetch the row containing the count
    $row = $result->fetch_assoc();
    // Output the total count of distinct filenames
    // echo "Total distinct filenames: " . $row['total_filenames'];
} else {
    // If the query failed, output an error message
    echo "Error: " . $conn->error;
}
// Close the connection
$conn->close();
?>
<?php
// Include the database connection file
require_once 'conn.php'; // Modify with the actual connection path

// SQL query to count distinct filenames
$sql = "SELECT COUNT(DISTINCT filename) AS total_filenames FROM ched_masterlist";

// Execute the query
$result = $conn->query($sql);

// Check if the query was successful
if ($result) {
    // Fetch the row containing the count
    $row = $result->fetch_assoc();
    // Output the total count of distinct filenames
    echo "Total distinct filenames: " . $row['total_filenames'];
} else {
    // If the query failed, output an error message
    echo "Error: " . $conn->error;
}
// Close the connection
$conn->close();
?>

<?php
// Include the database connection file
include 'session.php';
require_once 'conn.php'; // Modify with the actual connection path

// SQL query to count distinct filenames
$sql = "SELECT COUNT(DISTINCT filename) AS total_filenames FROM ched_masterlist where campus = '$sheet_name' and role = '$role'";

// Execute the query
$result = $conn->query($sql);

// Check if the query was successful
if ($result) {
    // Fetch the row containing the count
    $row = $result->fetch_assoc();
    // Output the total count of distinct filenames
    // echo "Total distinct filenames: " . $row['total_filenames'];
} else {
    // If the query failed, output an error message
    echo "Error: " . $conn->error;
}
// Close the connection
$conn->close();
?>