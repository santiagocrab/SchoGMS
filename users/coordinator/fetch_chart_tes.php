<?php
// Include database connection and session files
include '../config/conn.php';
include '../config/session.php';

// Set timeouts
set_time_limit(5);

// Initialize response array with defaults
$response = array(
    'total_records' => 0,
    'total_records_tes' => 0,
    'total_courses' => [],
    'total_file_groups' => [],
    'total_filenames' => []
);

// Get campus from session
$campus = isset($sheet_name) ? $conn->real_escape_string($sheet_name) : '';

try {
    // 1️⃣ Query to count total records (simplified)
    if (!empty($campus)) {
        $totalRecordsQuery = "SELECT COUNT(*) AS total_records_tes FROM ched_masterlist_tes WHERE campus = '$campus'";
        $totalRecordsResult = @$conn->query($totalRecordsQuery);
        $response['total_records_tes'] = $totalRecordsResult ? $totalRecordsResult->fetch_assoc()['total_records_tes'] : 0;
    }

    // 2️⃣ Query to get distinct courses with count (limited to top 10 for performance)
    $totalCourses = [];
    if (!empty($campus)) {
        $stmt = $conn->prepare("SELECT course_program_enrolled, COUNT(*) AS count FROM ched_masterlist_tes WHERE campus = ? GROUP BY course_program_enrolled ORDER BY count DESC LIMIT 10");
        if ($stmt) {
            $stmt->bind_param("s", $campus);
            $stmt->execute();
            $result = $stmt->get_result();
            while ($row = $result->fetch_assoc()) {
                $totalCourses[] = $row;
            }
            $stmt->close();
        }
    }
    $response['total_courses'] = $totalCourses;

    // 3️⃣ Query to get distinct file groups with count (limited to top 10)
    $totalFileGroups = [];
    if (!empty($campus)) {
        $fileGroupsQuery = "SELECT file_group, COUNT(*) AS count FROM ched_masterlist_tes WHERE campus = '$campus' GROUP BY file_group ORDER BY count DESC LIMIT 10";
        $result = @$conn->query($fileGroupsQuery);
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $totalFileGroups[] = $row;
            }
        }
    }
    $response['total_file_groups'] = $totalFileGroups;

    // 4️⃣ Skip filenames query (not needed, slows down page)
    $response['total_filenames'] = [];

} catch (Exception $e) {
    // Return empty response on error
    error_log("Chart TES fetch error: " . $e->getMessage());
}

// Close the connection
$conn->close();

// Return the data as JSON
header('Content-Type: application/json');
echo json_encode($response);
?>
