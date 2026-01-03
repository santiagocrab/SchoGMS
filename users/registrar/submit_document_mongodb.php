<?php
// Include session configuration and MongoDB connection
include 'config/session.php';

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

$response = ['success' => false, 'message' => '', 'uploaded_files' => []];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Get form data
        $campus = $_POST['session_campus'] ?? $_POST['campus_backup'] ?? 'ISULAN';
        $category = $_POST['category'] ?? $_POST['category_backup'] ?? 'COR';
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
        
        // Get MongoDB collections
        $documentCollection = $mongodb->collection('document_uploads');
        
        // Process each uploaded file
        for ($i = 0; $i < $fileCount; $i++) {
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
                throw new Exception("Upload error for file: $fileName");
            }
            
            // Validate file type
            $allowedTypes = ['application/pdf', 'image/jpeg', 'image/jpg', 'image/png'];
            if (!in_array($fileType, $allowedTypes)) {
                throw new Exception("Invalid file type for: $fileName. Only PDF, JPG, and PNG files are allowed.");
            }
            
            // Validate file size (max 10MB)
            if ($fileSize > 10 * 1024 * 1024) {
                throw new Exception("File too large: $fileName. Maximum size is 10MB.");
            }
            
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
                // Create document record for MongoDB
                $documentRecord = [
                    'id' => uniqid(),
                    'campus' => $campus,
                    'category' => $category,
                    'academic_year' => $academic_year,
                    'semester' => $semester,
                    'original_name' => $fileName,
                    'file_name' => $uniqueFileName,
                    'file_path' => $targetPath,
                    'file_size' => $fileSize,
                    'file_type' => $fileType,
                    'uploaded_at' => date('Y-m-d H:i:s'),
                    'uploaded_by' => $fullname ?? 'Registrar',
                    'uploaded_by_id' => $user_id ?? null,
                    'status' => 'active',
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ];
                
                // Insert into MongoDB
                $insertResult = $documentCollection->insertOne($documentRecord);
                
                if ($insertResult) {
                    $uploadedFiles[] = $documentRecord;
                } else {
                    throw new Exception("Failed to save document record to MongoDB: $fileName");
                }
            } else {
                throw new Exception("Failed to save file: $fileName");
            }
        }
        
        $response['success'] = true;
        $response['message'] = "Successfully uploaded " . count($uploadedFiles) . " files to MongoDB";
        $response['uploaded_files'] = $uploadedFiles;
        
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
