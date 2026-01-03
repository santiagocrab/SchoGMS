<?php
// Include database connection and session files
include 'config/conn.php';
include 'config/session.php';

// Initialize response array
$response = array();

// Get campus and file_group from session
$campus = isset($_SESSION['campus']) ? $conn->real_escape_string($_SESSION['campus']) : '';
$file_group = isset($_SESSION['course_program']) ? $conn->real_escape_string($_SESSION['course_program']) : '';

if (empty($campus) || empty($file_group)) {
    $response['error'] = "Campus or File Group not found in session.";
    echo json_encode($response);
    exit();
}

// Fetch distinct `course_program_enrolled` and student count
$courseQuery = "
    SELECT 
        cm.course_program_enrolled, 
        COUNT(DISTINCT cm.id) AS total_students
    FROM ched_masterlist cm
    WHERE cm.sheet_name = ?
    GROUP BY cm.course_program_enrolled
    ORDER BY cm.course_program_enrolled ASC";

$stmt = $conn->prepare($courseQuery);
$stmt->bind_param("s", $campus);
$stmt->execute();
$result = $stmt->get_result();
$courseData = [];

while ($row = $result->fetch_assoc()) {
    $courseData[] = $row;
}
$stmt->close();

$response['courses_data'] = $courseData;

// Close connection
$conn->close();

// Return JSON response
header('Content-Type: application/json');
echo json_encode($response);
?>
