<?php
include 'config/session.php';

echo "<h3>🔗 TEST: COR Link Functionality</h3>";

// Test a specific COR file
$testFile = 'uploads/documents/ISULAN/2024-2025/1st Semester/COR/ABACARO, ROSE ANN PIQUE_1758523285_0.pdf';

echo "<h4>Testing COR File Access:</h4>";
echo "<p><strong>Test File:</strong> " . htmlspecialchars($testFile) . "</p>";

// Check if file exists
if (file_exists($testFile)) {
    echo "<p style='color: green;'>✅ File exists</p>";
    echo "<p><strong>File Size:</strong> " . number_format(filesize($testFile)) . " bytes</p>";
    
    // Test the view_document.php link
    $viewUrl = 'view_document.php?file=' . urlencode($testFile) . '&type=COR';
    echo "<p><strong>View URL:</strong> <a href='$viewUrl' target='_blank' style='color: blue;'>$viewUrl</a></p>";
    
    // Test if it's a valid PDF
    $fileInfo = shell_exec("file '$testFile' 2>/dev/null");
    echo "<p><strong>File Info:</strong> " . htmlspecialchars($fileInfo) . "</p>";
    
    if (strpos($fileInfo, 'PDF document') !== false) {
        echo "<p style='color: green;'>✅ Valid PDF file</p>";
    } else {
        echo "<p style='color: red;'>❌ Invalid PDF file</p>";
    }
    
} else {
    echo "<p style='color: red;'>❌ File not found</p>";
}

// Test multiple COR files
echo "<h4>Available COR Files (First 10):</h4>";
$corDir = 'uploads/documents/ISULAN/2024-2025/1st Semester/COR/';
if (is_dir($corDir)) {
    $files = scandir($corDir);
    $pdfFiles = array_filter($files, function($file) {
        return $file != '.' && $file != '..' && pathinfo($file, PATHINFO_EXTENSION) == 'pdf';
    });
    
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr><th>File Name</th><th>Size</th><th>Test Link</th></tr>";
    
    $count = 0;
    foreach ($pdfFiles as $file) {
        if ($count >= 10) break;
        
        $filePath = $corDir . $file;
        $fileSize = filesize($filePath);
        $viewUrl = 'view_document.php?file=' . urlencode($filePath) . '&type=COR';
        
        echo "<tr>";
        echo "<td>" . htmlspecialchars($file) . "</td>";
        echo "<td>" . number_format($fileSize) . " bytes</td>";
        echo "<td><a href='$viewUrl' target='_blank' style='color: blue;'>Test View</a></td>";
        echo "</tr>";
        
        $count++;
    }
    
    echo "</table>";
}

echo "<p><a href='masterlist.php' class='btn btn-primary'>← Back to Masterlist</a></p>";
?>

