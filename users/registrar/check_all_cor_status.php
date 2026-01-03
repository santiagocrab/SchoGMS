<?php
include 'config/session.php';

echo "<h3>📊 COMPLETE COR STATUS CHECK</h3>";

// Check all COR directories
$corDirs = [
    'uploads/COR/',
    'uploads/documents/ISULAN/2024-2025/1st Semester/COR/',
    'uploads/documents/ISULAN/2024-2025/2nd Semester/COR/',
    'uploads/documents/ISULAN/2023-2024/1st Semester/COR/',
    'uploads/documents/ISULAN/2023-2024/2nd Semester/COR/'
];

$allCorFiles = [];
$totalFiles = 0;
$validFiles = 0;
$emptyFiles = 0;
$corruptedFiles = 0;
$missingFiles = 0;

echo "<h4>🔍 Scanning All COR Directories...</h4>";

foreach ($corDirs as $corDir) {
    if (is_dir($corDir)) {
        $corFiles = scandir($corDir);
        foreach ($corFiles as $file) {
            if ($file != '.' && $file != '..' && is_file($corDir . $file)) {
                $filePath = $corDir . $file;
                $fileSize = filesize($filePath);
                $totalFiles++;
                
                $status = 'Unknown';
                $color = 'black';
                
                if ($fileSize === 0) {
                    $status = 'EMPTY (0 bytes)';
                    $color = 'red';
                    $emptyFiles++;
                } else {
                    // Check if it's a valid PDF
                    $fileInfo = shell_exec("file '$filePath' 2>/dev/null");
                    if (strpos($fileInfo, 'PDF document') !== false && strpos($fileInfo, '0 pages') === false) {
                        $status = 'VALID PDF';
                        $color = 'green';
                        $validFiles++;
                    } else {
                        $status = 'CORRUPTED PDF';
                        $color = 'orange';
                        $corruptedFiles++;
                    }
                }
                
                $allCorFiles[] = [
                    'path' => $filePath,
                    'name' => $file,
                    'size' => $fileSize,
                    'status' => $status,
                    'color' => $color
                ];
            }
        }
    } else {
        echo "<p style='color: red;'>❌ Directory not found: $corDir</p>";
    }
}

echo "<h4>📈 COR Files Summary:</h4>";
echo "<div class='alert alert-info'>";
echo "<p><strong>Total COR Files Found:</strong> $totalFiles</p>";
echo "<p><strong>Valid PDFs:</strong> <span style='color: green;'>$validFiles</span></p>";
echo "<p><strong>Empty Files (0 bytes):</strong> <span style='color: red;'>$emptyFiles</span></p>";
echo "<p><strong>Corrupted Files:</strong> <span style='color: orange;'>$corruptedFiles</span></p>";
echo "</div>";

if ($emptyFiles > 0 || $corruptedFiles > 0) {
    echo "<div class='alert alert-warning'>";
    echo "<h5>⚠️ Issues Found with COR Files!</h5>";
    echo "<p><strong>$emptyFiles files are empty</strong> and <strong>$corruptedFiles files are corrupted</strong>.</p>";
    echo "<p>These files cannot be viewed and need to be fixed or re-uploaded.</p>";
    echo "</div>";
}

// Show detailed file status
echo "<h4>📋 Detailed COR File Status:</h4>";
echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
echo "<tr><th>File Name</th><th>Size</th><th>Status</th><th>Test Link</th><th>Action</th></tr>";

foreach ($allCorFiles as $file) {
    echo "<tr>";
    echo "<td>" . htmlspecialchars($file['name']) . "</td>";
    echo "<td>" . number_format($file['size']) . " bytes</td>";
    echo "<td style='color: " . $file['color'] . ";'>" . $file['status'] . "</td>";
    
    if ($file['status'] === 'VALID PDF') {
        $viewUrl = 'view_document.php?file=' . urlencode($file['path']) . '&type=COR';
        echo "<td><a href='$viewUrl' target='_blank' style='color: blue;'>Test View</a></td>";
        echo "<td style='color: green;'>✅ Working</td>";
    } else {
        echo "<td style='color: red;'>❌ Cannot View</td>";
        echo "<td>";
        if ($file['status'] === 'EMPTY (0 bytes)') {
            echo "<a href='#' onclick='deleteEmptyFile(\"" . htmlspecialchars($file['path']) . "\")' style='color: red;'>Delete Empty</a>";
        } else {
            echo "<a href='#' onclick='deleteCorruptedFile(\"" . htmlspecialchars($file['path']) . "\")' style='color: orange;'>Delete Corrupted</a>";
        }
        echo "</td>";
    }
    
    echo "</tr>";
}

echo "</table>";

// Check if there are students without COR files
echo "<h4>👥 Students Without COR Files:</h4>";
try {
    $registrarCollection = $mongodb->collection('registrar_master_list');
    $students = $registrarCollection->find([], ['limit' => 10, 'sort' => ['last_name' => 1]]);
    
    $studentsWithoutCOR = [];
    foreach ($students as $student) {
        $lastName = strtoupper(trim($student['last_name'] ?? ''));
        $firstName = strtoupper(trim($student['first_name'] ?? ''));
        $studentName = $lastName . ', ' . $firstName;
        
        $hasCOR = false;
        foreach ($allCorFiles as $file) {
            if ($file['status'] === 'VALID PDF') {
                $fileName = strtoupper(pathinfo($file['name'], PATHINFO_FILENAME));
                if (strpos($fileName, $lastName) !== false) {
                    $hasCOR = true;
                    break;
                }
            }
        }
        
        if (!$hasCOR) {
            $studentsWithoutCOR[] = $studentName;
        }
    }
    
    if (count($studentsWithoutCOR) > 0) {
        echo "<div class='alert alert-warning'>";
        echo "<h5>⚠️ Students Without COR Files:</h5>";
        echo "<ul>";
        foreach ($studentsWithoutCOR as $student) {
            echo "<li>" . htmlspecialchars($student) . "</li>";
        }
        echo "</ul>";
        echo "<p>These students need COR documents uploaded.</p>";
        echo "</div>";
    } else {
        echo "<div class='alert alert-success'>";
        echo "<h5>✅ All Students Have COR Files!</h5>";
        echo "<p>Every student in the database has at least one COR file.</p>";
        echo "</div>";
    }
    
} catch (Exception $e) {
    echo "<p>❌ Error checking students: " . $e->getMessage() . "</p>";
}

// Upload verification system
echo "<h4>📤 Upload Verification System:</h4>";
echo "<div class='alert alert-info'>";
echo "<h5>How to Ensure All COR Files Are Properly Uploaded:</h5>";
echo "<ol>";
echo "<li><strong>Use the 'UPLOAD ALL 3,000+ COR FILES AT ONCE' button</strong> for large uploads</li>";
echo "<li><strong>Check this status page</strong> after each upload to verify files</li>";
echo "<li><strong>Look for 'VALID PDF' status</strong> - these are working COR files</li>";
echo "<li><strong>Delete empty/corrupted files</strong> and re-upload them</li>";
echo "<li><strong>Test each COR link</strong> to ensure it opens properly</li>";
echo "</ol>";
echo "</div>";

?>

<script>
function deleteEmptyFile(filePath) {
    if (confirm('Delete this empty COR file?\n\n' + filePath)) {
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

function deleteCorruptedFile(filePath) {
    if (confirm('Delete this corrupted COR file?\n\n' + filePath)) {
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
<p><a href="cor-cog.php" class="btn btn-secondary">← Back to COR Interface</a></p>
<p><a href="upload_all_cor.php" class="btn btn-success">📤 Upload All COR Files</a></p>












