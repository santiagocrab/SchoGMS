<?php
include 'config/session.php';

echo "<h3>🔧 FIX EMPTY COR FILES (0 BYTES)</h3>";

// Check all COR directories for empty files
$corDirs = [
    'uploads/COR/',
    'uploads/documents/ISULAN/2024-2025/1st Semester/COR/',
    'uploads/documents/ISULAN/2024-2025/2nd Semester/COR/',
    'uploads/documents/ISULAN/2023-2024/1st Semester/COR/',
    'uploads/documents/ISULAN/2023-2024/2nd Semester/COR/'
];

$emptyFiles = [];
$totalFiles = 0;
$emptyCount = 0;

echo "<h4>🔍 Scanning for Empty COR Files...</h4>";

foreach ($corDirs as $corDir) {
    if (is_dir($corDir)) {
        $corFiles = scandir($corDir);
        foreach ($corFiles as $file) {
            if ($file != '.' && $file != '..' && is_file($corDir . $file)) {
                $filePath = $corDir . $file;
                $fileSize = filesize($filePath);
                $totalFiles++;
                
                if ($fileSize === 0) {
                    $emptyFiles[] = [
                        'path' => $filePath,
                        'name' => $file,
                        'size' => $fileSize
                    ];
                    $emptyCount++;
                }
            }
        }
    }
}

echo "<div class='alert alert-info'>";
echo "<h5>📊 SCAN RESULTS:</h5>";
echo "<p><strong>Total COR Files Found:</strong> $totalFiles</p>";
echo "<p><strong>Empty Files (0 bytes):</strong> <span style='color: red; font-weight: bold;'>$emptyCount</span></p>";
echo "</div>";

if ($emptyCount > 0) {
    echo "<div class='alert alert-warning'>";
    echo "<h5>⚠️ EMPTY COR FILES FOUND!</h5>";
    echo "<p>These files are 0 bytes and cannot be viewed. They need to be deleted and re-uploaded.</p>";
    echo "</div>";
    
    echo "<h4>🗑️ Empty COR Files to Delete:</h4>";
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr><th>File Name</th><th>Size</th><th>Path</th><th>Action</th></tr>";
    
    foreach ($emptyFiles as $file) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($file['name']) . "</td>";
        echo "<td style='color: red; font-weight: bold;'>" . number_format($file['size']) . " bytes</td>";
        echo "<td>" . htmlspecialchars($file['path']) . "</td>";
        echo "<td>";
        echo "<a href='#' onclick='deleteEmptyFile(\"" . htmlspecialchars($file['path']) . "\")' class='btn btn-danger btn-sm'>Delete Empty File</a>";
        echo "</td>";
        echo "</tr>";
    }
    
    echo "</table>";
    
    echo "<div class='alert alert-success'>";
    echo "<h5>✅ SOLUTION:</h5>";
    echo "<ol>";
    echo "<li><strong>Delete all empty files</strong> using the buttons above</li>";
    echo "<li><strong>Re-upload the COR files</strong> using 'UPLOAD ALL 3,000+ COR FILES AT ONCE'</li>";
    echo "<li><strong>Check file sizes before upload</strong> - ensure they're not empty</li>";
    echo "<li><strong>Verify upload success</strong> - all files should be valid PDFs</li>";
    echo "</ol>";
    echo "</div>";
    
    // Bulk delete option
    echo "<div class='alert alert-danger'>";
    echo "<h5>🚨 BULK DELETE OPTION:</h5>";
    echo "<p>Delete ALL empty COR files at once:</p>";
    echo "<a href='#' onclick='deleteAllEmptyFiles()' class='btn btn-danger btn-lg'>🗑️ DELETE ALL EMPTY FILES</a>";
    echo "</div>";
    
} else {
    echo "<div class='alert alert-success'>";
    echo "<h5>✅ NO EMPTY FILES FOUND!</h5>";
    echo "<p>All COR files have content and can be viewed properly.</p>";
    echo "</div>";
}

// Show prevention tips
echo "<h4>🛡️ PREVENT EMPTY FILE UPLOADS:</h4>";
echo "<div class='alert alert-info'>";
echo "<h5>💡 TIPS TO AVOID EMPTY FILES:</h5>";
echo "<ol>";
echo "<li><strong>Check file sizes before upload</strong> - ensure COR files are not 0 bytes</li>";
echo "<li><strong>Use valid PDF files</strong> - ensure files are properly formatted PDFs</li>";
echo "<li><strong>Don't interrupt uploads</strong> - let the upload process complete</li>";
echo "<li><strong>Test a few files first</strong> - upload 5-10 files to test before large uploads</li>";
echo "<li><strong>Use the 'UPLOAD ALL' button</strong> - it's designed for large file uploads</li>";
echo "</ol>";
echo "</div>";

?>

<script>
function deleteEmptyFile(filePath) {
    if (confirm('Delete this empty COR file?\n\nFile: ' + filePath + '\n\nThis will permanently delete the empty file.')) {
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

function deleteAllEmptyFiles() {
    if (confirm('Delete ALL empty COR files?\n\nThis will permanently delete all 0-byte COR files.\n\nAre you sure?')) {
        var form = document.createElement('form');
        form.method = 'POST';
        form.action = 'delete_all_empty_files.php';
        
        document.body.appendChild(form);
        form.submit();
    }
}
</script>

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

<p><a href="cor-cog.php" class="btn btn-success">← Back to COR Interface</a></p>












