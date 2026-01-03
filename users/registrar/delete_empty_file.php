<?php
include 'config/session.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $filePath = $_POST['file_path'] ?? '';
    
    if (empty($filePath)) {
        die('No file path provided');
    }
    
    // Security: Check if file path is within allowed directories
    $allowedDirs = [
        'uploads/COR/',
        'uploads/COG/',
        'uploads/documents/ISULAN/2024-2025/1st Semester/COR/',
        'uploads/documents/ISULAN/2024-2025/1st Semester/COG/',
        'uploads/documents/ISULAN/2024-2025/2nd Semester/COR/',
        'uploads/documents/ISULAN/2024-2025/2nd Semester/COG/',
        'uploads/documents/ISULAN/2023-2024/1st Semester/COR/',
        'uploads/documents/ISULAN/2023-2024/1st Semester/COG/',
        'uploads/documents/ISULAN/2023-2024/2nd Semester/COR/',
        'uploads/documents/ISULAN/2023-2024/2nd Semester/COG/'
    ];
    
    $isValidPath = false;
    foreach ($allowedDirs as $allowedDir) {
        if (strpos($filePath, $allowedDir) === 0) {
            $isValidPath = true;
            break;
        }
    }
    
    if (!$isValidPath) {
        die('Invalid file path');
    }
    
    // Check if file exists and is empty
    if (!file_exists($filePath)) {
        die('File not found');
    }
    
    $fileSize = filesize($filePath);
    if ($fileSize > 0) {
        die('File is not empty');
    }
    
    // Delete the empty file
    if (unlink($filePath)) {
        echo "<h3>✅ Empty File Deleted Successfully</h3>";
        echo "<p><strong>Deleted:</strong> " . htmlspecialchars($filePath) . "</p>";
        echo "<p>The empty COR file has been removed. You can now re-upload a valid COR document for this student.</p>";
    } else {
        echo "<h3>❌ Failed to Delete File</h3>";
        echo "<p>Could not delete: " . htmlspecialchars($filePath) . "</p>";
        echo "<p>Please check file permissions or try again.</p>";
    }
    
    echo "<p><a href='remove_empty_cor.php' class='btn btn-primary'>← Back to Remove Empty COR</a></p>";
    echo "<p><a href='masterlist.php' class='btn btn-secondary'>← Back to Masterlist</a></p>";
    
} else {
    die('Invalid request method');
}
?>












