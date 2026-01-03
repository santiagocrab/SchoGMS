<?php
require 'config/conn.php';
include 'config/session.php';

// Initialize response array
$response = array();

// Get campus and selected course from request
$campus = isset($_SESSION['campus']) ? $conn->real_escape_string($_SESSION['campus']) : '';
$selectedCourse = isset($_GET['course_program_enrolled']) ? $conn->real_escape_string($_GET['course_program_enrolled']) : '';

if (empty($campus) || empty($selectedCourse)) {
    $response['error'] = "Campus or Course Program not found.";
    echo json_encode($response);
    exit();
}

// Fetch student names per course
$studentQuery = "
    SELECT 
        cm.lastname, 
        cm.firstname, 
        cm.middlename, 
        cm.course_program_enrolled
    FROM ched_masterlist cm
    WHERE cm.sheet_name = ? 
    AND cm.course_program_enrolled = ?";

$stmt = $conn->prepare($studentQuery);
$stmt->bind_param("ss", $campus, $selectedCourse);
$stmt->execute();
$result = $stmt->get_result();
$studentsData = [];

while ($row = $result->fetch_assoc()) {
    $studentsData[] = $row;
}
$stmt->close();

$response['students_data'] = $studentsData;

// Close connection
$conn->close();

// Return JSON response
header('Content-Type: application/json');
echo json_encode($response);
?>
