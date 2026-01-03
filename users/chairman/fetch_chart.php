<?php
// Include the database connection file
include 'config/conn.php';

// Start session to determine user role
session_start();
$role = $_SESSION['role'] ?? ''; // Get role from session

// Determine table based on role
$table = ($role === 'chairman') ? 'ched_masterlist' : 'registrar_master_list';

// Initialize response array
$response = [];

// Function to fetch grouped data
function fetchGroupedData($conn, $query) {
    $data = [];
    $result = $conn->query($query);
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
    return $data;
}

// SQL queries
$totalRecordsQuery = "SELECT COUNT(*) AS total_records FROM $table";
$totalCampusesQuery = "SELECT sheet_name AS campus, COUNT(*) AS count FROM $table GROUP BY sheet_name";
$totalBatchesQuery = "SELECT file_group AS ched_tdp_batch, COUNT(*) AS count FROM $table GROUP BY file_group";

// SQL queries to count 'COR' and 'COG' categories from document_uploads table
$corCategoryQuery = "SELECT COUNT(*) AS cor_count FROM document_uploads WHERE category = 'COR'";
$cogCategoryQuery = "SELECT COUNT(*) AS cog_count FROM document_uploads WHERE category = 'COG'";

// Execute queries
$totalRecordsResult = $conn->query($totalRecordsQuery);
$totalCampuses = fetchGroupedData($conn, $totalCampusesQuery);
$totalBatches = fetchGroupedData($conn, $totalBatchesQuery);
$corCategoryResult = $conn->query($corCategoryQuery);
$cogCategoryResult = $conn->query($cogCategoryQuery);

// Fetch results
$totalRecords = $totalRecordsResult ? $totalRecordsResult->fetch_assoc()['total_records'] : 0;
$corCount = $corCategoryResult ? $corCategoryResult->fetch_assoc()['cor_count'] : 0;
$cogCount = $cogCategoryResult ? $cogCategoryResult->fetch_assoc()['cog_count'] : 0;

// Store results in the response array
$response['total_records'] = $totalRecords;
$response['total_campuses'] = $totalCampuses;  // sheet_name as campus
$response['total_batches'] = $totalBatches;   // file_group as CHED TDP Batch
$response['cor_count'] = $corCount;
$response['cog_count'] = $cogCount;

// Close the connection
$conn->close();

// Return the data as JSON
header('Content-Type: application/json');
echo json_encode($response);
?>
