<?php
// Include session configuration
include 'config/session.php';

// Set longer execution time for large uploads
set_time_limit(300); // 5 minutes
ini_set('memory_limit', '512M');

// Create uploads directory if it doesn't exist
$uploadDir = 'uploads/';
if (!file_exists($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

// Create documents subdirectory
$documentsDir = $uploadDir . 'documents/';
if (!file_exists($documentsDir)) {
    mkdir($documentsDir, 0755, true);
}

$response = ['success' => false, 'message' => '', 'uploaded_files' => [], 'stats' => ['total' => 0, 'success' => 0, 'failed' => 0]];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Get form data
        $campus = $_POST['session_campus'] ?? 'ISULAN';
        $category = $_POST['category'] ?? 'COR';
        $academic_year = $_POST['academic_year'] ?? '';
        $semester = $_POST['semester'] ?? '';
        
        // Validate required fields
        if (empty($academic_year)) {
            throw new Exception('Academic Year is required');
        }
        
        if (empty($semester)) {
            throw new Exception('Semester is required');
        }
        
        // Check if files were uploaded
        if (!isset($_FILES['fileUpload']) || empty($_FILES['fileUpload']['name'][0])) {
            throw new Exception('No files uploaded');
        }
        
        $uploadedFiles = [];
        $fileCount = count($_FILES['fileUpload']['name']);
        $successCount = 0;
        $failedCount = 0;
        
        // Limit to 100 files per batch
        $maxFiles = min($fileCount, 100);
        
        // Process each uploaded file
        for ($i = 0; $i < $maxFiles; $i++) {
            $fileName = $_FILES['fileUpload']['name'][$i];
            $fileTmpName = $_FILES['fileUpload']['tmp_name'][$i];
            $fileSize = $_FILES['fileUpload']['size'][$i];
            $fileError = $_FILES['fileUpload']['error'][$i];
            $fileType = $_FILES['fileUpload']['type'][$i];
            
            // Skip empty files
            if ($fileError === UPLOAD_ERR_NO_FILE) {
                continue;
            }
            
            // Check for upload errors
            if ($fileError !== UPLOAD_ERR_OK) {
                $failedCount++;
                continue;
            }
            
            // Validate file type
            $allowedTypes = ['application/pdf', 'image/jpeg', 'image/jpg', 'image/png'];
            if (!in_array($fileType, $allowedTypes)) {
                $failedCount++;
                continue;
            }
            
            // Validate file size (max 10MB)
            if ($fileSize > 10 * 1024 * 1024) {
                $failedCount++;
                continue;
            }
            
            try {
                // Create unique filename
                $fileExtension = pathinfo($fileName, PATHINFO_EXTENSION);
                $baseName = pathinfo($fileName, PATHINFO_FILENAME);
                $uniqueFileName = $baseName . '_' . time() . '_' . $i . '.' . $fileExtension;
                
                // Create directory structure: uploads/documents/campus/academic_year/semester/category/
                $targetDir = $documentsDir . $campus . '/' . $academic_year . '/' . $semester . '/' . $category . '/';
                if (!file_exists($targetDir)) {
                    mkdir($targetDir, 0755, true);
                }
                
                $targetPath = $targetDir . $uniqueFileName;
                
                // Move uploaded file
                if (move_uploaded_file($fileTmpName, $targetPath)) {
                    $uploadedFiles[] = [
                        'original_name' => $fileName,
                        'saved_name' => $uniqueFileName,
                        'file_path' => $targetPath,
                        'file_size' => $fileSize,
                        'file_type' => $fileType,
                        'campus' => $campus,
                        'category' => $category,
                        'academic_year' => $academic_year,
                        'semester' => $semester,
                        'uploaded_at' => date('Y-m-d H:i:s'),
                        'uploaded_by' => $fullname ?? 'Registrar'
                    ];
                    $successCount++;
                } else {
                    $failedCount++;
                }
            } catch (Exception $e) {
                $failedCount++;
                continue;
            }
        }
        
        // Save upload record to JSON file
        $recordFile = $uploadDir . 'uploaded_documents.json';
        $existingRecords = [];
        
        if (file_exists($recordFile)) {
            $existingRecords = json_decode(file_get_contents($recordFile), true) ?? [];
        }
        
        // Add new records
        $existingRecords = array_merge($existingRecords, $uploadedFiles);
        
        // Save updated records
        if (file_put_contents($recordFile, json_encode($existingRecords, JSON_PRETTY_PRINT))) {
            $response['success'] = true;
            $response['message'] = "Successfully uploaded {$successCount} files";
            $response['uploaded_files'] = $uploadedFiles;
            $response['stats'] = [
                'total' => $maxFiles,
                'success' => $successCount,
                'failed' => $failedCount
            ];
        } else {
            throw new Exception("Failed to save upload record");
        }
        
    } catch (Exception $e) {
        $response['message'] = $e->getMessage();
    }
} else {
    $response['message'] = 'Invalid request method';
}

// Return JSON response
header('Content-Type: application/json');
echo json_encode($response);
?>
