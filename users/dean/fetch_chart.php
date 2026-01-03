<?php 
include 'config/conn.php';
include 'config/session.php';

// Initialize response array
$response = [];

// Get session values safely
$campus = isset($_SESSION['campus']) ? $conn->real_escape_string($_SESSION['campus']) : '';
$file_group = isset($_SESSION['course_program']) ? $conn->real_escape_string($_SESSION['course_program']) : '';

if (empty($campus) || empty($file_group)) {
    $response['error'] = "Campus or File Group not found in session.";
    echo json_encode($response);
    exit;
}

// 1️⃣ Total students in ched_masterlist by campus
$totalQuery = "SELECT COUNT(DISTINCT id) AS total_students FROM ched_masterlist WHERE sheet_name = ?";
$stmt = $conn->prepare($totalQuery);
$stmt->bind_param("s", $campus);
$stmt->execute();
$result = $stmt->get_result();
$totalStudents = ($row = $result->fetch_assoc()) ? (int)$row['total_students'] : 0;
$response['ched_total_students'] = $totalStudents;
$stmt->close();

// 2️⃣ Total students in registrar_master_list with matching file_group
$regQuery = "SELECT COUNT(DISTINCT id) AS total_registrar FROM registrar_master_list WHERE file_group = ?";
$stmt = $conn->prepare($regQuery);
$stmt->bind_param("s", $file_group);
$stmt->execute();
$result = $stmt->get_result();
$totalRegistrar = ($row = $result->fetch_assoc()) ? (int)$row['total_registrar'] : 0;
$response['registrar_total_students'] = $totalRegistrar;
$stmt->close();

// 3️⃣ Matched students per course (with percentage)
if ($totalStudents > 0) {
    $studentQuery = "
        SELECT 
            rm.file_group, 
            cm.course_program_enrolled, 
            COUNT(DISTINCT cm.id) AS total_students
        FROM ched_masterlist cm
        LEFT JOIN registrar_master_list rm
            ON cm.lastname COLLATE utf8mb4_general_ci = rm.last_name COLLATE utf8mb4_general_ci 
            AND cm.firstname COLLATE utf8mb4_general_ci = rm.first_name COLLATE utf8mb4_general_ci
            AND (
                cm.middlename COLLATE utf8mb4_general_ci = rm.middle_name COLLATE utf8mb4_general_ci 
                OR cm.middlename IS NULL OR cm.middlename = '' 
                OR rm.middle_name IS NULL OR rm.middle_name = ''
            )
        WHERE cm.sheet_name = ? AND rm.file_group = ?
        GROUP BY cm.course_program_enrolled";
    
    $stmt = $conn->prepare($studentQuery);
    $stmt->bind_param("ss", $campus, $file_group);
    $stmt->execute();
    $result = $stmt->get_result();

    $studentsData = [];
    while ($row = $result->fetch_assoc()) {
        $row['percentage'] = round(($row['total_students'] / $totalStudents) * 100, 2); // Based on ched_masterlist
        $studentsData[] = $row;
    }
    $stmt->close();

    $response['students_data_tdp'] = $studentsData;

    // Also include a total percentage match
    $matchedTotal = array_sum(array_column($studentsData, 'total_students'));
    $response['matched_total'] = $matchedTotal;

} else {
    $response['error'] = "No students found in the total campus list.";
}

// Final output
$conn->close();
header('Content-Type: application/json');
echo json_encode($response);

?>

