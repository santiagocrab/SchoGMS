<?php
// Automatic cleanup system - runs automatically
include 'config/session.php';

// Function to automatically clean empty files
function autoCleanEmptyFiles() {
    $corDirs = [
        'uploads/COR/',
        'uploads/documents/ISULAN/2024-2025/1st Semester/COR/',
        'uploads/documents/ISULAN/2024-2025/2nd Semester/COR/',
        'uploads/documents/ISULAN/2023-2024/1st Semester/COR/',
        'uploads/documents/ISULAN/2023-2024/2nd Semester/COR/'
    ];
    
    $cleanedCount = 0;
    
    foreach ($corDirs as $corDir) {
        if (is_dir($corDir)) {
            $corFiles = scandir($corDir);
            foreach ($corFiles as $file) {
                if ($file != '.' && $file != '..' && is_file($corDir . $file)) {
                    $filePath = $corDir . $file;
                    $fileSize = filesize($filePath);
                    
                    if ($fileSize === 0) {
                        // Automatically delete empty files
                        if (unlink($filePath)) {
                            $cleanedCount++;
                        }
                    }
                }
            }
        }
    }
    
    return $cleanedCount;
}

// Run automatic cleanup
$cleanedFiles = autoCleanEmptyFiles();

// Log cleanup activity (optional)
if ($cleanedFiles > 0) {
    error_log("Auto-cleanup: Removed $cleanedFiles empty COR files");
}

// Return success response
echo json_encode(['success' => true, 'cleaned' => $cleanedFiles]);
?>












