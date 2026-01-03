<?php
// Turn off error display to prevent HTML output
error_reporting(0);
ini_set('display_errors', 0);

// Set JSON header first
header('Content-Type: application/json');

// Start output buffering to catch any unexpected output
ob_start();

try {
    require '../../conn_mongodb.php';
    
    // Get parameters
    $fileGroup = isset($_GET['file_group']) ? trim($_GET['file_group']) : '';
    $category = isset($_GET['category']) ? trim($_GET['category']) : '';
    $campus = isset($_GET['campus']) ? trim($_GET['campus']) : '';
    $password = isset($_GET['password']) ? trim($_GET['password']) : '';
    
    if (empty($fileGroup) && empty($category)) {
        throw new Exception('Missing required parameters');
    }
    
    // Validate password
    $validPasswords = ['admin123', 'registrar123', 'password123', 'delete123'];
    if (empty($password) || !in_array($password, $validPasswords)) {
        throw new Exception('Invalid password. Please enter the correct password to delete records.');
    }
    
    $deletedCount = 0;
    $message = '';
    
    // Delete from registrar_master_list if file_group is provided
    if (!empty($fileGroup)) {
        $registrarCollection = $mongodb->collection('registrar_master_list');
        $result = $registrarCollection->deleteMany(['file_group' => $fileGroup, 'campus' => $campus]);
        $deletedCount += $result['deletedCount'] ?? 0;
        $message = "Deleted {$deletedCount} records from Registrar Masterlist for file group: {$fileGroup}";
    }
    
    // Delete from document_uploads if category is provided
    if (!empty($category)) {
        $documentCollection = $mongodb->collection('document_uploads');
        $result = $documentCollection->deleteMany(['category' => $category, 'campus' => $campus]);
        $deletedCount += $result['deletedCount'] ?? 0;
        $message = "Deleted {$deletedCount} records from Document Uploads for category: {$category}";
    }
    
    // Clean any unexpected output before sending JSON
    ob_clean();
    
    echo json_encode([
        'success' => true,
        'message' => $message,
        'deleted_count' => $deletedCount
    ]);
    
} catch (Exception $e) {
    // Clean any unexpected output before sending JSON
    ob_clean();
    
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}
?>
