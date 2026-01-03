<?php
// Include database connection and session files
include 'config/conn.php';
include 'config/session.php';

// Initialize response array
$response = array();

// Ensure `campus` (sheet_name) and `file_group` are set from session
$campus = isset($_SESSION['campus']) ? $conn->real_escape_string($_SESSION['campus']) : '';
$file_group = isset($_SESSION['course_program']) ? $conn->real_escape_string($_SESSION['course_program']) : '';

if (empty($campus) || empty($file_group)) {
    $response['error'] = "Campus or File Group not found in session.";
    echo json_encode($response);
    exit();
}

// Fetch student count per `file_group` for the selected campus
$studentQuery = "
    SELECT 
        rm.file_group, 
        cm.sheet_name, 
        cm.course_program_enrolled, 
        COUNT(DISTINCT cm.id) AS total_students
    FROM ched_masterlist cm
    LEFT JOIN registrar_master_list rm
        ON cm.lastname COLLATE utf8mb4_general_ci = rm.last_name COLLATE utf8mb4_general_ci 
        AND cm.firstname COLLATE utf8mb4_general_ci = rm.first_name COLLATE utf8mb4_general_ci
        AND (cm.middlename COLLATE utf8mb4_general_ci = rm.middle_name COLLATE utf8mb4_general_ci 
            OR cm.middlename IS NULL 
            OR rm.middle_name IS NULL 
            OR cm.middlename = '' 
            OR rm.middle_name = '')
    WHERE cm.sheet_name = ? 
    AND rm.file_group = ?
    GROUP BY rm.file_group, cm.sheet_name , cm.course_program_enrolled
    ORDER BY cm.sheet_name ASC, rm.file_group , cm.course_program_enrolled";

$stmt = $conn->prepare($studentQuery);
$stmt->bind_param("ss", $campus, $file_group);
$stmt->execute();
$result = $stmt->get_result();
$studentsData = [];
while ($row = $result->fetch_assoc()) {
    $studentsData[] = $row;
}
$stmt->close();

$response['students_data'] = $studentsData;

// Close the connection
$conn->close();

// Return the data as JSON
header('Content-Type: application/json');
echo json_encode($response);
?>
