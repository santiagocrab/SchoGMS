<?php
// Include database connection and session files
include 'config/conn.php';
include 'config/session.php';

// Initialize response array
$response = array();

if (isset($row['campus'])) {
    $campus = $conn->real_escape_string($row['campus']); // Secure against SQL injection
} else {
    $campus = ''; // Default value if session variable is not set
}
// Get campus from session or set default
// $campus = isset($_SESSION['campus']) ? $conn->real_escape_string($_SESSION['campus']) : '';

// 1️⃣ **Total Students in `ched_masterlist` (Filtered by Campus)**
$totalRecordsQuery = "SELECT COUNT(*) AS total_records FROM ched_masterlist_tes WHERE campus = ?";
$stmt = $conn->prepare($totalRecordsQuery);
$stmt->bind_param("s", $campus);
$stmt->execute();
$result = $stmt->get_result();
$response['total_records'] = $result->fetch_assoc()['total_records'] ?? 0;
$stmt->close();

// 2️⃣ **Count Students Per Course (Filtered by Campus)**
$totalCourses = [];
$coursesQuery = "SELECT course_program_enrolled AS course, COUNT(*) AS count FROM ched_masterlist_tes WHERE campus = ? GROUP BY course_program_enrolled";
$stmt = $conn->prepare($coursesQuery);
$stmt->bind_param("s", $campus);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $totalCourses[] = $row;
}
$stmt->close();
$response['total_courses'] = $totalCourses;

// Close the connection
$conn->close();

// Return the data as JSON
header('Content-Type: application/json');
echo json_encode($response);
?>



<?php
// // Include database connection and session files
// include '../config/conn.php'; // Adjust the path if needed
// include '../config/session.php'; // Ensure session management is required

// // Initialize response array
// $response = array();

// // Check if session variable `sheet_name` exists and assign it to `$campus`
// if (isset($row['campus'])) {
//     $campus = $conn->real_escape_string($row['campus']); // Secure against SQL injection
// } else {
//     $campus = ''; // Default value if session variable is not set
// }

// // 1️⃣ Query to count total records
// $totalRecordsQuery = "SELECT COUNT(*) AS total_records FROM ched_masterlist";
// $totalRecordsResult = $conn->query($totalRecordsQuery);
// $response['total_records'] = $totalRecordsResult ? $totalRecordsResult->fetch_assoc()['total_records'] : 0;

// // 2️⃣ Query to get distinct courses with count (Filtered by sheet_name if session is set)
// $totalCourses = [];
// if (!empty($campus)) {
//     $stmt = $conn->prepare("SELECT course_program_enrolled, COUNT(*) AS count FROM ched_masterlist WHERE sheet_name = ? GROUP BY course_program_enrolled");
//     $stmt->bind_param("s", $campus);
//     $stmt->execute();
//     $result = $stmt->get_result();
//     while ($row = $result->fetch_assoc()) {
//         $totalCourses[] = $row;
//     }
//     $stmt->close();
// }
// $response['total_courses'] = $totalCourses;

// // 3️⃣ Query to get distinct file groups with count
// $totalFileGroups = [];
// $fileGroupsQuery = "SELECT file_group, COUNT(*) AS count FROM ched_masterlist where sheet_name = '$campus' GROUP BY file_group";
// $result = $conn->query($fileGroupsQuery);
// while ($row = $result->fetch_assoc()) {
//     $totalFileGroups[] = $row;
// }
// $response['total_file_groups'] = $totalFileGroups;

// // 4️⃣ Query to get distinct filenames with count
// $totalFilenames = [];
// $filenameQuery = "SELECT filename, COUNT(filename) AS count FROM ched_masterlist GROUP BY filename";
// $result = $conn->query($filenameQuery);
// while ($row = $result->fetch_assoc()) {
//     $totalFilenames[] = $row;
// }
// $response['total_filenames'] = $totalFilenames;

// // Close the connection
// $conn->close();

// // Return the data as JSON
// header('Content-Type: application/json');
// echo json_encode($response);
?>
