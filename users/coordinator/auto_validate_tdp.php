<?php
/**
 * Auto-Validation for TDP
 * Reads student info from COR (or stored metadata) and verifies course/year level
 * Auto-fills Annex Form 2 fields if validation passes
 */

require '../config/conn.php';
require '../vendor/autoload.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Invalid request method']);
    exit;
}

$student_id = isset($_POST['student_id']) ? intval($_POST['student_id']) : 0;
$sheet_name = isset($_POST['sheet_name']) ? $conn->real_escape_string($_POST['sheet_name']) : '';

if (empty($student_id) || empty($sheet_name)) {
    echo json_encode(['success' => false, 'error' => 'Missing required parameters']);
    exit;
}

try {
    // Get student data from CHED masterlist
    $query = "SELECT cm.*, rm.course, rm.year_level, rm.email_address, rm.semester, rm.academic_year
              FROM ched_masterlist cm
              LEFT JOIN registrar_master_list rm
                ON cm.lastname COLLATE utf8mb4_general_ci = rm.last_name COLLATE utf8mb4_general_ci 
                AND cm.firstname COLLATE utf8mb4_general_ci = rm.first_name COLLATE utf8mb4_general_ci
                AND (cm.middlename COLLATE utf8mb4_general_ci = rm.middle_name COLLATE utf8mb4_general_ci 
                     OR cm.middlename IS NULL 
                     OR rm.middle_name IS NULL 
                     OR cm.middlename = '' 
                     OR rm.middle_name = '')
              WHERE cm.id = $student_id AND cm.sheet_name = '$sheet_name'
              LIMIT 1";
    
    $result = $conn->query($query);
    
    if (!$result || $result->num_rows === 0) {
        echo json_encode(['success' => false, 'error' => 'Student not found']);
        exit;
    }
    
    $student = $result->fetch_assoc();
    
    // Get COR document path for this student
    $corQuery = "SELECT file_path, file_name FROM document_uploads 
                 WHERE category = 'COR' 
                 AND campus = '$sheet_name'
                 AND file_name LIKE '" . $conn->real_escape_string($student['lastname']) . ", " . $conn->real_escape_string($student['firstname']) . "%'
                 LIMIT 1";
    
    $corResult = $conn->query($corQuery);
    $hasCOR = $corResult && $corResult->num_rows > 0;
    
    // Verification: Check if course and year level match
    $courseMatch = false;
    $yearLevelMatch = false;
    
    if (!empty($student['course']) && !empty($student['course_program_enrolled'])) {
        // Normalize course names for comparison (case-insensitive, remove extra spaces)
        $regCourse = trim(strtolower($student['course']));
        $chedCourse = trim(strtolower($student['course_program_enrolled']));
        $courseMatch = ($regCourse === $chedCourse || strpos($regCourse, $chedCourse) !== false || strpos($chedCourse, $regCourse) !== false);
    }
    
    if (!empty($student['year_level']) && !empty($student['year_level'])) {
        $regYearLevel = trim($student['year_level']);
        $chedYearLevel = trim($student['year_level']);
        $yearLevelMatch = ($regYearLevel === $chedYearLevel);
    }
    
    // Prepare validation result
    $validationResult = [
        'success' => true,
        'student_id' => $student_id,
        'student_name' => $student['lastname'] . ', ' . $student['firstname'] . ' ' . $student['middlename'],
        'has_cor' => $hasCOR,
        'course_match' => $courseMatch,
        'year_level_match' => $yearLevelMatch,
        'validation_passed' => ($courseMatch && $yearLevelMatch),
        'data' => [
            'ched_course' => $student['course_program_enrolled'] ?? '',
            'registrar_course' => $student['course'] ?? '',
            'ched_year_level' => $student['year_level'] ?? '',
            'registrar_year_level' => $student['year_level'] ?? '',
            'email_address' => $student['email_address'] ?? '',
            'semester' => $student['semester'] ?? '',
            'academic_year' => $student['academic_year'] ?? ''
        ]
    ];
    
    // If validation passes, update the ched_masterlist record
    if ($validationResult['validation_passed']) {
        $updateQuery = "UPDATE ched_masterlist 
                        SET validation_status = 'Validated',
                            validated_by = '" . $conn->real_escape_string($sheet_name) . "',
                            validated_at = NOW()
                        WHERE id = $student_id";
        
        if ($conn->query($updateQuery)) {
            $validationResult['updated'] = true;
            $validationResult['message'] = 'Student validated successfully. Data ready for Annex Form 2.';
        } else {
            $validationResult['updated'] = false;
            $validationResult['message'] = 'Validation passed but update failed: ' . $conn->error;
        }
    } else {
        $validationResult['message'] = 'Validation failed: Course or Year Level mismatch.';
    }
    
    echo json_encode($validationResult);
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}

$conn->close();
?>



