<?php
// Include database connection and session files
include 'config/conn.php';
include 'config/session.php';

// Initialize response array
$response = array();

// Ensure campus (campus) and file_group are set from session
$campus = isset($_SESSION['campus']) ? $conn->real_escape_string($_SESSION['campus']) : '';
$file_group = isset($_SESSION['course_program']) ? $conn->real_escape_string($_SESSION['course_program']) : '';


if (empty($campus) || empty($file_group)) {
    $response['error'] = "Campus or File Group not found in session.";
    echo json_encode($response);
    exit();
}

// Fetch total students for the campus
$totalQuery = "
    SELECT COUNT(DISTINCT id) AS total_students
    FROM ched_masterlist_tes 
    WHERE campus = ?";
$stmt = $conn->prepare($totalQuery);
$stmt->bind_param("s", $campus);
$stmt->execute();
$totalResult = $stmt->get_result();
$totalRow = $totalResult->fetch_assoc();
$totalStudents = $totalRow['total_students'] ?? 0;
$stmt->close();

// Fetch student count per file_group for the selected campus
$studentQuery = "
    SELECT 
        rm.file_group, 
        cm.campus, 
        cm.course_program_enrolled, 
        COUNT(DISTINCT cm.id) AS total_students
    FROM ched_masterlist_tes cm
    LEFT JOIN registrar_master_list rm
        ON cm.lastname COLLATE utf8mb4_general_ci = rm.last_name COLLATE utf8mb4_general_ci 
        AND cm.firstname COLLATE utf8mb4_general_ci = rm.first_name COLLATE utf8mb4_general_ci
        AND (cm.middlename COLLATE utf8mb4_general_ci = rm.middle_name COLLATE utf8mb4_general_ci 
            OR cm.middlename IS NULL 
            OR rm.middle_name IS NULL 
            OR cm.middlename = '' 
            OR rm.middle_name = '')
    WHERE cm.campus = ? 
    AND rm.file_group = ? and course_program_enrolled != 'BACHELOR OF SCIENCE IN INDUSTRIAL TECHNOLOGY MAJOR IN CIVIL TECHNOLOGY' 
    and  course_program_enrolled != 'BACHELOR OF SCIENCE IN INDUSTRIAL TECHNOLOGY MAJOR IN AUTOMOTIVE TECHNOLOGY'
    GROUP BY rm.file_group, cm.campus , cm.course_program_enrolled
    ORDER BY cm.campus ASC, rm.file_group , cm.course_program_enrolled";

$stmt = $conn->prepare($studentQuery);
$stmt->bind_param("ss", $campus, $file_group);
$stmt->execute();
$result = $stmt->get_result();
$studentsData = [];
while ($row = $result->fetch_assoc()) {
    // Calculate percentage
    $row['percentage'] = ($totalStudents > 0) ? round(($row['total_students'] / $totalStudents) * 100) : 0;
    $studentsData[] = $row;
}
$stmt->close();

$response['students_data_tes'] = $studentsData;

// Close the connection
$conn->close();

// Return the data as JSON
header('Content-Type: application/json');
echo json_encode($response);

?>
