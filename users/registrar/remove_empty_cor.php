<?php
include 'config/session.php';

echo "<h3>🗑️ REMOVE EMPTY COR FILES</h3>";

// Check COR files in multiple directories
$corDirs = [
    'uploads/COR/',
    'uploads/documents/ISULAN/2024-2025/1st Semester/COR/',
    'uploads/documents/ISULAN/2024-2025/2nd Semester/COR/',
    'uploads/documents/ISULAN/2023-2024/1st Semester/COR/',
    'uploads/documents/ISULAN/2023-2024/2nd Semester/COR/'
];

echo "<h4>Scanning for Empty COR Files...</h4>";

$emptyFiles = [];
$totalFiles = 0;
$deletedCount = 0;

foreach ($corDirs as $corDir) {
    if (is_dir($corDir)) {
        $corFiles = scandir($corDir);
        foreach ($corFiles as $file) {
            if ($file != '.' && $file != '..' && is_file($corDir . $file)) {
                $filePath = $corDir . $file;
                $fileSize = filesize($filePath);
                $totalFiles++;
                
                if ($fileSize === 0) {
                    $emptyFiles[] = $filePath;
                }
            }
        }
    }
}

echo "<p><strong>Total COR Files Scanned:</strong> $totalFiles</p>";
echo "<p><strong>Empty Files Found:</strong> " . count($emptyFiles) . "</p>";

if (count($emptyFiles) > 0) {
    echo "<h4>Empty COR Files Found:</h4>";
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr><th>File Path</th><th>Size</th><th>Action</th></tr>";
    
    foreach ($emptyFiles as $filePath) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($filePath) . "</td>";
        echo "<td style='color: red;'>0 bytes (EMPTY)</td>";
        echo "<td>";
        echo "<a href='#' onclick='deleteEmptyFile(\"" . htmlspecialchars($filePath) . "\")' style='color: red;'>Delete Empty File</a>";
        echo "</td>";
        echo "</tr>";
    }
    
    echo "</table>";
    
    echo "<div class='alert alert-warning mt-3'>";
    echo "<h5>⚠️ Empty COR Files Detected!</h5>";
    echo "<p>These COR files are empty (0 bytes) and cannot be viewed. You should:</p>";
    echo "<ul>";
    echo "<li><strong>Delete empty files</strong> - Remove the empty COR files</li>";
    echo "<li><strong>Re-upload valid COR documents</strong> - Upload proper COR files for these students</li>";
    echo "</ul>";
    echo "</div>";
    
    // Auto-delete empty files
    echo "<h4>Auto-Deleting Empty Files...</h4>";
    foreach ($emptyFiles as $filePath) {
        if (unlink($filePath)) {
            $deletedCount++;
            echo "<p style='color: green;'>✅ Deleted: " . htmlspecialchars($filePath) . "</p>";
        } else {
            echo "<p style='color: red;'>❌ Failed to delete: " . htmlspecialchars($filePath) . "</p>";
        }
    }
    
    echo "<div class='alert alert-success mt-3'>";
    echo "<h5>✅ Cleanup Complete!</h5>";
    echo "<p><strong>Deleted $deletedCount empty COR files.</strong></p>";
    echo "<p>You can now re-upload valid COR documents for these students.</p>";
    echo "</div>";
    
} else {
    echo "<div class='alert alert-success'>";
    echo "<h5>✅ No Empty COR Files Found!</h5>";
    echo "<p>All COR files have content. The issue might be elsewhere.</p>";
    echo "</div>";
}

// Show remaining COR files
echo "<h4>Remaining COR Files:</h4>";
$remainingFiles = 0;
foreach ($corDirs as $corDir) {
    if (is_dir($corDir)) {
        $corFiles = scandir($corDir);
        foreach ($corFiles as $file) {
            if ($file != '.' && $file != '..' && is_file($corDir . $file)) {
                $filePath = $corDir . $file;
                $fileSize = filesize($filePath);
                if ($fileSize > 0) {
                    $remainingFiles++;
                }
            }
        }
    }
}

echo "<p><strong>Valid COR Files Remaining:</strong> $remainingFiles</p>";

?>

<script>
function deleteEmptyFile(filePath) {
    if (confirm('Are you sure you want to delete this empty COR file?\n\n' + filePath)) {
        // Create a form to delete the file
        var form = document.createElement('form');
        form.method = 'POST';
        form.action = 'delete_empty_file.php';
        
        var input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'file_path';
        input.value = filePath;
        
        form.appendChild(input);
        document.body.appendChild(form);
        form.submit();
    }
}
</script>

<p><a href="masterlist.php" class="btn btn-primary">← Back to Masterlist</a></p>
<p><a href="test_cor_files.php" class="btn btn-secondary">← Test COR Files</a></p>












