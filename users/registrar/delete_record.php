<?php
/**
 * Delete Record with Password Verification
 * Handles secure deletion of registrar masterlist records
 */

header('Content-Type: application/json');
error_reporting(0);
ini_set('display_errors', 0);
ob_start();

try {
    require '../../conn_mongodb.php';
} catch (Exception $e) {
    ob_clean();
    echo json_encode(['success' => false, 'error' => 'Database connection failed: ' . $e->getMessage()]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    ob_clean();
    
    // Get POST data
    $recordId = $_POST['record_id'] ?? '';
    $password = $_POST['password'] ?? '';
    
    // Validate input
    if (empty($recordId)) {
        echo json_encode(['success' => false, 'error' => 'Record ID is required']);
        exit;
    }
    
    if (empty($password)) {
        echo json_encode(['success' => false, 'error' => 'Password is required']);
        exit;
    }
    
    try {
        // Verify password against current user session
        // For now, we'll use a simple password check
        // In production, you should verify against the actual user's password
        $validPasswords = [
            'admin123',      // Default admin password
            'registrar123',  // Registrar password
            'password123'    // Test password
        ];
        
        if (!in_array($password, $validPasswords)) {
            echo json_encode(['success' => false, 'error' => 'Invalid password. Access denied.']);
            exit;
        }
        
        // Get the record before deletion for logging
        $registrarCollection = $mongodb->collection('registrar_master_list');
        $recordToDelete = $registrarCollection->findOne(['_id' => $recordId]);
        
        if (!$recordToDelete) {
            // Try alternative ID field
            $recordToDelete = $registrarCollection->findOne(['id' => $recordId]);
        }
        
        if (!$recordToDelete) {
            echo json_encode(['success' => false, 'error' => 'Record not found']);
            exit;
        }
        
        // Log the deletion attempt
        $logData = [
            'action' => 'DELETE_RECORD',
            'record_id' => $recordId,
            'student_name' => ($recordToDelete['last_name'] ?? '') . ', ' . ($recordToDelete['first_name'] ?? ''),
            'campus' => $recordToDelete['campus'] ?? '',
            'file_group' => $recordToDelete['file_group'] ?? '',
            'deleted_by' => 'registrar_user', // You can get this from session
            'deleted_at' => date('Y-m-d H:i:s'),
            'ip_address' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
        ];
        
        // Save deletion log
        $logCollection = $mongodb->collection('deletion_logs');
        $logCollection->insertOne($logData);
        
        // Delete the record
        $deleteResult = $registrarCollection->deleteOne(['_id' => $recordId]);
        
        if (!$deleteResult) {
            // Try alternative ID field
            $deleteResult = $registrarCollection->deleteOne(['id' => $recordId]);
        }
        
        if ($deleteResult && $deleteResult['deletedCount'] > 0) {
            echo json_encode([
                'success' => true,
                'message' => 'Record deleted successfully',
                'deleted_record' => [
                    'id' => $recordId,
                    'student_name' => ($recordToDelete['last_name'] ?? '') . ', ' . ($recordToDelete['first_name'] ?? ''),
                    'campus' => $recordToDelete['campus'] ?? '',
                    'file_group' => $recordToDelete['file_group'] ?? ''
                ]
            ]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Failed to delete record. Record may not exist.']);
        }
        
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => 'Database error: ' . $e->getMessage()]);
    }
    
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid request method']);
}
?>
