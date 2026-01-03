<?php
include 'config/session.php';
// Include the database connection file
include 'config/conn.php'; // Modify with the actual connection path

// Initialize response array
$response = array();

// SQL queries to count the required data from registrar_master_list table
$totalRecordsQuery = "SELECT COUNT(*) AS total_records FROM registrar_master_list where campus = '$sheet_name'";
$totalCoursesQuery = "SELECT course, COUNT(*) AS count FROM registrar_master_list where campus = '$sheet_name' GROUP BY course";
$totalFileGroupsQuery = "SELECT file_group, COUNT(*) AS count FROM registrar_master_list where campus = '$sheet_name' GROUP BY file_group";
$totalFilenamesQuery = "SELECT filename, COUNT(*) AS count FROM registrar_master_list where campus = '$sheet_name' GROUP BY filename";

// SQL queries to count 'COR' and 'COG' categories from document_uploads table
$corCategoryQuery = "SELECT COUNT(*) AS cor_count FROM document_uploads WHERE category = 'COR' and campus = '$sheet_name'";
$cogCategoryQuery = "SELECT COUNT(*) AS cog_count FROM document_uploads WHERE category = 'COG' and campus = '$sheet_name'";

// Execute the queries
$totalRecordsResult = $conn->query($totalRecordsQuery);
$totalCoursesResult = $conn->query($totalCoursesQuery);
$totalFileGroupsResult = $conn->query($totalFileGroupsQuery);
$totalFilenamesResult = $conn->query($totalFilenamesQuery);
$corCategoryResult = $conn->query($corCategoryQuery);
$cogCategoryResult = $conn->query($cogCategoryQuery);

// Fetch results for each query
$totalRecords = $totalRecordsResult ? $totalRecordsResult->fetch_assoc()['total_records'] : 0;
$totalCourses = [];
while ($row = $totalCoursesResult->fetch_assoc()) {
    $totalCourses[] = $row;
}
$totalFileGroups = [];
while ($row = $totalFileGroupsResult->fetch_assoc()) {
    $totalFileGroups[] = $row;
}
$totalFilenames = [];
while ($row = $totalFilenamesResult->fetch_assoc()) {
    $totalFilenames[] = $row;
}
$corCount = $corCategoryResult ? $corCategoryResult->fetch_assoc()['cor_count'] : 0;
$cogCount = $cogCategoryResult ? $cogCategoryResult->fetch_assoc()['cog_count'] : 0;

// Store results in the response array
$response['total_records'] = $totalRecords;
$response['total_courses'] = $totalCourses;
$response['total_file_groups'] = $totalFileGroups;
$response['total_filenames'] = $totalFilenames;
$response['cor_count'] = $corCount;
$response['cog_count'] = $cogCount;

// Close the connection
$conn->close();

// Return the data as JSON
header('Content-Type: application/json');
echo json_encode($response);
?>
