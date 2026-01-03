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
    
    // Check if file exists
    if (!file_exists($filePath)) {
        die('File not found');
    }
    
    // Delete the file
    if (unlink($filePath)) {
        echo "<h3>✅ File Deleted Successfully</h3>";
        echo "<p><strong>Deleted:</strong> " . htmlspecialchars($filePath) . "</p>";
        echo "<p>The corrupted COR file has been removed. You can now re-upload a valid COR document for this student.</p>";
    } else {
        echo "<h3>❌ Failed to Delete File</h3>";
        echo "<p>Could not delete: " . htmlspecialchars($filePath) . "</p>";
        echo "<p>Please check file permissions or try again.</p>";
    }
    
    echo "<p><a href='fix_corrupted_cor.php' class='btn btn-primary'>← Back to Fix Corrupted COR</a></p>";
    echo "<p><a href='masterlist.php' class='btn btn-secondary'>← Back to Masterlist</a></p>";
    
} else {
    die('Invalid request method');
}
?>

