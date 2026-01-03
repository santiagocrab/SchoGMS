<?php
include 'config/session.php';

echo "<h3>🔧 FIX: Corrupted COR Files</h3>";

// Check COR files in multiple directories
$corDirs = [
    'uploads/COR/',
    'uploads/documents/ISULAN/2024-2025/1st Semester/COR/',
    'uploads/documents/ISULAN/2024-2025/2nd Semester/COR/',
    'uploads/documents/ISULAN/2023-2024/1st Semester/COR/',
    'uploads/documents/ISULAN/2023-2024/2nd Semester/COR/'
];

echo "<h4>Scanning for Corrupted COR Files...</h4>";

$corruptedFiles = [];
$totalFiles = 0;

foreach ($corDirs as $corDir) {
    if (is_dir($corDir)) {
        $corFiles = scandir($corDir);
        foreach ($corFiles as $file) {
            if ($file != '.' && $file != '..' && is_file($corDir . $file)) {
                $filePath = $corDir . $file;
                $totalFiles++;
                
                // Check if it's a valid PDF
                $fileInfo = shell_exec("file '$filePath' 2>/dev/null");
                $isValidPDF = strpos($fileInfo, 'PDF document') !== false && strpos($fileInfo, '0 pages') === false;
                
                if (!$isValidPDF) {
                    $corruptedFiles[] = $filePath;
                }
            }
        }
    }
}

echo "<p><strong>Total COR Files Scanned:</strong> $totalFiles</p>";
echo "<p><strong>Corrupted Files Found:</strong> " . count($corruptedFiles) . "</p>";

if (count($corruptedFiles) > 0) {
    echo "<h4>Corrupted COR Files:</h4>";
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr><th>File Path</th><th>File Size</th><th>Status</th><th>Action</th></tr>";
    
    foreach ($corruptedFiles as $filePath) {
        $fileSize = filesize($filePath);
        $fileName = basename($filePath);
        
        echo "<tr>";
        echo "<td>" . htmlspecialchars($filePath) . "</td>";
        echo "<td>" . number_format($fileSize) . " bytes</td>";
        echo "<td style='color: red;'>❌ Corrupted/Empty PDF</td>";
        echo "<td>";
        echo "<a href='#' onclick='deleteCorruptedFile(\"" . htmlspecialchars($filePath) . "\")' style='color: red; margin-right: 10px;'>Delete</a>";
        echo "<a href='#' onclick='markForReupload(\"" . htmlspecialchars($filePath) . "\")' style='color: orange;'>Mark for Re-upload</a>";
        echo "</td>";
        echo "</tr>";
    }
    
    echo "</table>";
    
    echo "<div class='alert alert-warning mt-3'>";
    echo "<h5>⚠️ Action Required:</h5>";
    echo "<p>These COR files are corrupted or empty. You have two options:</p>";
    echo "<ul>";
    echo "<li><strong>Delete:</strong> Remove the corrupted file (you can re-upload later)</li>";
    echo "<li><strong>Mark for Re-upload:</strong> Keep the file but mark it for replacement</li>";
    echo "</ul>";
    echo "</div>";
    
} else {
    echo "<div class='alert alert-success'>";
    echo "<h5>✅ All COR Files are Valid!</h5>";
    echo "<p>No corrupted COR files found. All PDF documents are working properly.</p>";
    echo "</div>";
}

?>

<script>
function deleteCorruptedFile(filePath) {
    if (confirm('Are you sure you want to delete this corrupted COR file?\n\n' + filePath)) {
        // Create a form to delete the file
        var form = document.createElement('form');
        form.method = 'POST';
        form.action = 'delete_corrupted_file.php';
        
        var input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'file_path';
        input.value = filePath;
        
        form.appendChild(input);
        document.body.appendChild(form);
        form.submit();
    }
}

function markForReupload(filePath) {
    if (confirm('Mark this file for re-upload?\n\n' + filePath)) {
        // Create a form to mark the file
        var form = document.createElement('form');
        form.method = 'POST';
        form.action = 'mark_for_reupload.php';
        
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

