<?php
include 'config/session.php';

echo "<h3>🗑️ DELETING ALL EMPTY COR FILES</h3>";

// Check all COR directories for empty files
$corDirs = [
    'uploads/COR/',
    'uploads/documents/ISULAN/2024-2025/1st Semester/COR/',
    'uploads/documents/ISULAN/2024-2025/2nd Semester/COR/',
    'uploads/documents/ISULAN/2023-2024/1st Semester/COR/',
    'uploads/documents/ISULAN/2023-2024/2nd Semester/COG/'
];

$deletedCount = 0;
$errorCount = 0;

echo "<h4>🔍 Scanning and Deleting Empty Files...</h4>";

foreach ($corDirs as $corDir) {
    if (is_dir($corDir)) {
        $corFiles = scandir($corDir);
        foreach ($corFiles as $file) {
            if ($file != '.' && $file != '..' && is_file($corDir . $file)) {
                $filePath = $corDir . $file;
                $fileSize = filesize($filePath);
                
                if ($fileSize === 0) {
                    if (unlink($filePath)) {
                        echo "<p style='color: green;'>✅ Deleted: " . htmlspecialchars($file) . " (0 bytes)</p>";
                        $deletedCount++;
                    } else {
                        echo "<p style='color: red;'>❌ Failed to delete: " . htmlspecialchars($file) . "</p>";
                        $errorCount++;
                    }
                }
            }
        }
    }
}

echo "<div class='alert alert-success'>";
echo "<h5>✅ DELETION COMPLETE!</h5>";
echo "<p><strong>Files Deleted:</strong> $deletedCount</p>";
echo "<p><strong>Errors:</strong> $errorCount</p>";
echo "</div>";

if ($deletedCount > 0) {
    echo "<div class='alert alert-info'>";
    echo "<h5>📤 NEXT STEPS:</h5>";
    echo "<ol>";
    echo "<li><strong>Re-upload your COR files</strong> using 'UPLOAD ALL 3,000+ COR FILES AT ONCE'</li>";
    echo "<li><strong>Check file sizes before upload</strong> - ensure they're not empty</li>";
    echo "<li><strong>Verify upload success</strong> - all files should be valid PDFs</li>";
    echo "<li><strong>Test COR links</strong> in masterlist to ensure they work</li>";
    echo "</ol>";
    echo "</div>";
}

?>

<div class="row mt-4">
    <div class="col-md-4">
        <a href="upload_all_cor.php" class="btn btn-primary btn-block">📤 Re-upload COR Files</a>
    </div>
    <div class="col-md-4">
        <a href="check_all_cor_status.php" class="btn btn-info btn-block">🔍 Check COR Status</a>
    </div>
    <div class="col-md-4">
        <a href="masterlist.php" class="btn btn-secondary btn-block">📋 Back to Masterlist</a>
    </div>
</div>

<p><a href="fix_empty_cor_files.php" class="btn btn-warning">← Back to Fix Empty Files</a></p>












