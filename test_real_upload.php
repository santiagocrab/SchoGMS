<?php
/**
 * Real Upload Test
 * Test processing the actual College of Engineering CSV file
 */

header('Content-Type: application/json');
error_reporting(0);
ini_set('display_errors', 0);
ob_start();

try {
    require_once 'conn_mongodb.php';
    require_once 'users/vendor/autoload.php';
} catch (Exception $e) {
    ob_clean();
    echo json_encode(['success' => false, 'error' => 'Failed to load dependencies: ' . $e->getMessage()]);
    exit;
}

use PhpOffice\PhpSpreadsheet\IOFactory;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    ob_clean();
    
    // Get form data
    $campus = $_POST['session_campus'] ?? '';
    $file_group = $_POST['file_group'] ?? '';
    $academic_year = $_POST['academic_year'] ?? '';
    $semester = $_POST['semester'] ?? '';
    
    // Check file upload
    if (!isset($_FILES['excelFile']) || $_FILES['excelFile']['error'] !== UPLOAD_ERR_OK) {
        echo json_encode(['success' => false, 'error' => 'File upload error: ' . ($_FILES['excelFile']['error'] ?? 'No file uploaded')]);
        exit;
    }
    
    $file = $_FILES['excelFile'];
    
    try {
        $collection = $mongodb->collection('registrar_master_list');
        $initialCount = $collection->count();
        
        // Process the uploaded file
        $spreadsheet = IOFactory::load($file['tmp_name']);
        $worksheet = $spreadsheet->getActiveSheet();
        $data = $worksheet->toArray();
        
        $successCount = 0;
        $duplicateCount = 0;
        $errorCount = 0;
        
        // Skip header row (index 0)
        for ($i = 1; $i < count($data); $i++) {
            $row = $data[$i];
            
            // Skip empty rows
            if (empty(array_filter($row))) {
                continue;
            }
            
            // Check if ID number already exists
            $idNumber = $row[4] ?? ''; // Column E (index 4)
            if (!empty($idNumber)) {
                $existingRecord = $collection->findOne(['id_number' => $idNumber]);
                if ($existingRecord) {
                    $duplicateCount++;
                    continue;
                }
            }
            
            // Create document
            $document = [
                'campus' => $campus,
                'file_group' => $file_group,
                'academic_year' => $academic_year,
                'semester' => $semester,
                'filename' => $file['name'],
                'last_name' => $row[0] ?? '',           // Column A
                'first_name' => $row[1] ?? '',          // Column B
                'middle_name' => $row[2] ?? '',         // Column C
                'ext_name' => $row[3] ?? '',            // Column D
                'id_number' => $row[4] ?? '',           // Column E
                'gender' => $row[5] ?? '',              // Column F
                'student_type' => $row[6] ?? '',        // Column G
                'year_level' => $row[7] ?? '',          // Column H
                'attended' => $row[8] ?? '',            // Column I
                'course' => $row[9] ?? '',              // Column J
                'curriculum' => $row[10] ?? '',         // Column K
                'scholarship' => $row[11] ?? '',        // Column L
                'gpa' => $row[12] ?? '',                // Column M
                'cgpa' => $row[13] ?? '',               // Column N
                'pass_percentage' => $row[14] ?? '',    // Column O
                'grade_remarks' => $row[15] ?? '',      // Column P
                'enrolled' => $row[16] ?? '',           // Column Q
                'lec_unit' => $row[17] ?? '',           // Column R
                'lab_unit' => $row[18] ?? '',           // Column S
                'cor_printed' => $row[19] ?? '',        // Column T
                'billing_profile' => $row[20] ?? '',    // Column U
                'misc_fee_total' => $row[21] ?? '',     // Column V
                'misc_fee_paid' => $row[22] ?? '',      // Column W
                'tuition_fee_total' => $row[23] ?? '',  // Column X
                'tuition_fee_paid' => $row[24] ?? '',   // Column Y
                'street' => $row[25] ?? '',             // Column Z
                'barangay' => $row[26] ?? '',           // Column AA
                'municipality_city' => $row[27] ?? '',  // Column AB
                'province' => $row[28] ?? '',           // Column AC
                'zip_code' => $row[29] ?? '',           // Column AD
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ];
            
            $result = $collection->insertOne($document);
            if ($result) {
                $successCount++;
            } else {
                $errorCount++;
            }
        }
        
        $finalCount = $collection->count();
        
        echo json_encode([
            'success' => true,
            'message' => 'File processed successfully!',
            'stats' => [
                'total_rows_processed' => count($data) - 1, // Exclude header
                'records_inserted' => $successCount,
                'duplicates_skipped' => $duplicateCount,
                'errors' => $errorCount,
                'initial_count' => $initialCount,
                'final_count' => $finalCount,
                'records_added' => $finalCount - $initialCount
            ],
            'file_info' => [
                'name' => $file['name'],
                'size' => $file['size'],
                'campus' => $campus,
                'file_group' => $file_group,
                'academic_year' => $academic_year,
                'semester' => $semester
            ]
        ]);
        
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => 'Processing error: ' . $e->getMessage()]);
    }
    
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid request method']);
}
?>
