<?php
// Include session configuration and MongoDB connection
include 'config/session.php';

// Set unlimited execution time for ALL files upload
set_time_limit(0); // No time limit for unlimited files
ini_set('memory_limit', '8192M'); // 8GB for unlimited files
ini_set('max_file_uploads', '0'); // Unlimited file uploads
ini_set('post_max_size', '0'); // No post size limit
ini_set('upload_max_filesize', '0'); // No file size limit
ini_set('max_input_vars', '0'); // No input vars limit
ini_set('max_input_time', '0'); // No input time limit

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
        // Debug: Log what we're receiving for large uploads
        $debug_info = [
            'POST_count' => count($_POST),
            'POST_keys' => array_keys($_POST),
            'FILES_count' => count($_FILES),
            'FILES_keys' => array_keys($_FILES),
            'content_length' => $_SERVER['CONTENT_LENGTH'] ?? 'unknown',
            'post_max_size' => ini_get('post_max_size'),
            'upload_max_filesize' => ini_get('upload_max_filesize'),
            'max_file_uploads' => ini_get('max_file_uploads')
        ];
        
        // Get form data
        $campus = $_POST['session_campus'] ?? 'ISULAN';
        $category = $_POST['category'] ?? 'COR';
        $academic_year = $_POST['academic_year'] ?? '';
        $semester = $_POST['semester'] ?? '';
        
        // Validate required fields
        if (empty($academic_year)) {
            $response['message'] = 'Academic Year is required. Debug info: ' . json_encode($debug_info);
            echo json_encode($response);
            exit;
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
        
        // No limit - upload ALL files
        $maxFiles = $fileCount;
        
        // Get MongoDB collections
        $documentCollection = $mongodb->collection('document_uploads');
        
        // First, clean up existing duplicates for this A.Y and semester
        $existingFilter = [
            'campus' => $campus,
            'category' => $category,
            'academic_year' => $academic_year,
            'semester' => $semester
        ];
        
        // Get existing documents to check for duplicates
        $existingDocs = $documentCollection->find($existingFilter);
        $existingNames = [];
        foreach ($existingDocs as $doc) {
            $originalName = $doc['original_name'] ?? '';
            $existingNames[] = strtolower(trim($originalName));
        }
        
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
            
            // Check if file is empty (0 bytes) - REJECT EMPTY FILES
            if ($fileSize === 0) {
                $failedCount++;
                continue;
            }
            
            // Check for duplicate names (case-insensitive)
            $currentFileName = strtolower(trim($fileName));
            if (in_array($currentFileName, $existingNames)) {
                // Skip duplicate - don't upload
                $failedCount++;
                continue;
            }
            
            // Validate file type
            $allowedTypes = ['application/pdf', 'image/jpeg', 'image/jpg', 'image/png', 'application/x-rar-compressed', 'application/rar', 'application/octet-stream'];
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
                        $successCount++;
                        // Add to existing names to prevent duplicates in same upload
                        $existingNames[] = $currentFileName;
                        
                        // AUTO-EXTRACT RAR FILES
                        if ($fileExtension === 'rar') {
                            try {
                                $extractDir = 'uploads/extracted/' . pathinfo($fileName, PATHINFO_FILENAME) . '/';
                                if (!is_dir($extractDir)) {
                                    mkdir($extractDir, 0755, true);
                                }
                                
                                // Try to extract using unrar command
                                $command = "unrar x -o+ \"" . escapeshellarg($targetPath) . "\" \"" . escapeshellarg($extractDir) . "\" 2>&1";
                                exec($command, $output, $returnCode);
                                
                                if ($returnCode === 0) {
                                    // Get extracted files
                                    $extractedFiles = glob($extractDir . '*');
                                    $extractedCount = 0;
                                    
                                    foreach ($extractedFiles as $extractedFile) {
                                        if (is_file($extractedFile)) {
                                            $extractedFileName = basename($extractedFile);
                                            $extractedFileExtension = strtolower(pathinfo($extractedFile, PATHINFO_EXTENSION));
                                            
                                            // Only process PDF files from extraction
                                            if ($extractedFileExtension === 'pdf') {
                                                $corDir = 'uploads/COR/';
                                                if (!is_dir($corDir)) {
                                                    mkdir($corDir, 0755, true);
                                                }
                                                
                                                $corPath = $corDir . $extractedFileName;
                                                if (copy($extractedFile, $corPath)) {
                                                    // Add extracted COR to database
                                                    $corDocument = [
                                                        'id' => uniqid(),
                                                        'campus' => $campus,
                                                        'category' => 'COR',
                                                        'academic_year' => $academic_year,
                                                        'semester' => $semester,
                                                        'original_name' => $extractedFileName,
                                                        'file_name' => $extractedFileName,
                                                        'file_path' => $corPath,
                                                        'file_size' => filesize($corPath),
                                                        'file_type' => 'application/pdf',
                                                        'uploaded_at' => date('Y-m-d H:i:s'),
                                                        'uploaded_by' => $fullname ?? 'Registrar (Auto-extracted)',
                                                        'uploaded_by_id' => $user_id ?? null,
                                                        'status' => 'active',
                                                        'created_at' => date('Y-m-d H:i:s'),
                                                        'updated_at' => date('Y-m-d H:i:s'),
                                                        'extracted_from' => $fileName
                                                    ];
                                                    
                                                    $documentCollection->insertOne($corDocument);
                                                    $extractedCount++;
                                                }
                                            }
                                        }
                                    }
                                    
                                    // Update the response to show extraction results
                                    if (!isset($response['extraction_results'])) {
                                        $response['extraction_results'] = [];
                                    }
                                    $response['extraction_results'][] = [
                                        'rar_file' => $fileName,
                                        'extracted_count' => $extractedCount,
                                        'extract_dir' => $extractDir
                                    ];
                                }
                            } catch (Exception $e) {
                                // Log extraction error but don't fail the upload
                                error_log("RAR extraction failed for $fileName: " . $e->getMessage());
                            }
                        }
                        
                    } else {
                        $failedCount++;
                    }
                } else {
                    $failedCount++;
                }
            } catch (Exception $e) {
                $failedCount++;
                continue;
            }
        }
        
        // Sort all documents alphabetically by last name after upload
        $allDocs = $documentCollection->find($existingFilter);
        $sortedDocs = [];
        foreach ($allDocs as $doc) {
            $sortedDocs[] = $doc;
        }
        
        // Sort by original name (alphabetically by last name)
        usort($sortedDocs, function($a, $b) {
            $nameA = strtolower($a['original_name'] ?? '');
            $nameB = strtolower($b['original_name'] ?? '');
            return strcmp($nameA, $nameB);
        });
        
        // Update the order in database (optional - for display purposes)
        // Note: MongoDB doesn't guarantee order, but we can add a sort_order field
        $sortOrder = 1;
        foreach ($sortedDocs as $doc) {
            $documentCollection->updateOne(
                ['id' => $doc['id']],
                ['$set' => ['sort_order' => $sortOrder]]
            );
            $sortOrder++;
        }
        
        $response['success'] = true;
        
        // Build success message with extraction results
        $message = "Successfully uploaded {$successCount} files to MongoDB. Documents sorted alphabetically by last name.";
        
        if (isset($response['extraction_results']) && count($response['extraction_results']) > 0) {
            $totalExtracted = 0;
            foreach ($response['extraction_results'] as $result) {
                $totalExtracted += $result['extracted_count'];
            }
            $message .= " AUTO-EXTRACTED: {$totalExtracted} COR documents from RAR files.";
        }
        
        $response['message'] = $message;
        $response['uploaded_files'] = $uploadedFiles;
        $response['stats'] = [
            'total' => $maxFiles,
            'success' => $successCount,
            'failed' => $failedCount,
            'duplicates_skipped' => $failedCount - ($maxFiles - $successCount)
        ];
        
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
