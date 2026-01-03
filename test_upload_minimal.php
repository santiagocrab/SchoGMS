<?php
/**
 * Minimal Upload Test
 * Test upload with minimal code
 */

// Turn off all error reporting to prevent HTML output
error_reporting(0);
ini_set('display_errors', 0);

// Set JSON header first
header('Content-Type: application/json');

// Start output buffering to catch any unexpected output
ob_start();

try {
    require_once 'conn_mongodb.php';
} catch (Exception $e) {
    ob_clean();
    echo json_encode(['success' => false, 'error' => 'Database connection failed: ' . $e->getMessage()]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Clean any unexpected output
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
    
    // Test MongoDB connection
    try {
        $collection = $mongodb->collection('registrar_master_list');
        $count = $collection->count();
        
        // Try to insert a test record
        $testDoc = [
            'campus' => $campus,
            'file_group' => $file_group,
            'filename' => $file['name'],
            'academic_year' => $academic_year,
            'semester' => $semester,
            'last_name' => 'TEST',
            'first_name' => 'UPLOAD',
            'middle_name' => 'MINIMAL',
            'id_number' => '99999',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ];
        
        $result = $collection->insertOne($testDoc);
        if ($result) {
            // Check new count
            $newCount = $collection->count();
            
            // Clean up test record
            $collection->deleteOne(['id_number' => '99999']);
            
            echo json_encode([
                'success' => true, 
                'message' => 'Upload test successful',
                'details' => [
                    'campus' => $campus,
                    'file_group' => $file_group,
                    'academic_year' => $academic_year,
                    'semester' => $semester,
                    'file_name' => $file['name'],
                    'file_size' => $file['size'],
                    'initial_count' => $count,
                    'new_count' => $newCount
                ]
            ]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Failed to insert test record']);
        }
        
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => 'MongoDB error: ' . $e->getMessage()]);
    }
    
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid request method']);
}
?>
