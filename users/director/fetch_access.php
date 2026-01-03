<?php
// Include database connection
include '../config/conn.php';
include '../config/session.php';

// Initialize response array
$response = array();

// Get campus from session or default to empty
$campus = isset($_SESSION['campus']) ? $conn->real_escape_string($_SESSION['campus']) : '';

// 1️⃣ **Fetch Distinct File Groups Based on Campus**
$fileGroups = [];
$query = "SELECT DISTINCT file_group FROM registrar_master_list WHERE campus = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $campus);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $fileGroups[] = $row['file_group'];
}
$stmt->close();

// Store results in the response
$response['file_groups'] = $fileGroups;

// Close the connection
$conn->close();

// Return JSON response
header('Content-Type: application/json');
echo json_encode($response);
?>
