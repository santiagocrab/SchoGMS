<?php
include 'config/session.php';

echo "<h3>🔍 TEST: COR Files Validity Check</h3>";

// Check COR files in multiple directories
$corDirs = [
    'uploads/COR/',
    'uploads/documents/ISULAN/2024-2025/1st Semester/COR/',
    'uploads/documents/ISULAN/2024-2025/2nd Semester/COR/',
    'uploads/documents/ISULAN/2023-2024/1st Semester/COR/',
    'uploads/documents/ISULAN/2023-2024/2nd Semester/COR/'
];

echo "<h4>COR Files Status Check:</h4>";
echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
echo "<tr><th>File Path</th><th>File Size</th><th>PDF Status</th><th>Test Link</th></tr>";

$totalFiles = 0;
$validFiles = 0;
$invalidFiles = 0;

foreach ($corDirs as $corDir) {
    if (is_dir($corDir)) {
        $corFiles = scandir($corDir);
        foreach ($corFiles as $file) {
            if ($file != '.' && $file != '..' && is_file($corDir . $file)) {
                $filePath = $corDir . $file;
                $fileSize = filesize($filePath);
                $totalFiles++;
                
                echo "<tr>";
                echo "<td>" . htmlspecialchars($filePath) . "</td>";
                echo "<td>" . number_format($fileSize) . " bytes</td>";
                
                // Check if it's a valid PDF
                $fileInfo = shell_exec("file '$filePath' 2>/dev/null");
                $isValidPDF = strpos($fileInfo, 'PDF document') !== false && strpos($fileInfo, '0 pages') === false;
                
                if ($isValidPDF) {
                    echo "<td style='color: green;'>✅ Valid PDF</td>";
                    $validFiles++;
                } else {
                    echo "<td style='color: red;'>❌ Invalid/Empty PDF</td>";
                    $invalidFiles++;
                }
                
                // Test link
                echo "<td><a href='view_document.php?file=" . urlencode($filePath) . "&type=COR' target='_blank' style='color: blue;'>Test View</a></td>";
                echo "</tr>";
            }
        }
    }
}

echo "</table>";

echo "<h4>Summary:</h4>";
echo "<p><strong>Total COR Files:</strong> $totalFiles</p>";
echo "<p><strong>Valid PDFs:</strong> $validFiles</p>";
echo "<p><strong>Invalid/Empty PDFs:</strong> $invalidFiles</p>";

if ($invalidFiles > 0) {
    echo "<div class='alert alert-warning'>";
    echo "<h5>⚠️ Warning: $invalidFiles COR files are invalid or empty!</h5>";
    echo "<p>These files may have been corrupted during upload or are empty PDFs.</p>";
    echo "<p>You may need to re-upload these COR documents.</p>";
    echo "</div>";
}

// Test specific ALVARADO files
echo "<h4>ALVARADO Files Test:</h4>";
$alvaradoFiles = [
    'uploads/documents/ISULAN/2024-2025/1st Semester/COR/ALVARADO, RACHEL CAMPOS_1758523285_150.pdf',
    'uploads/documents/ISULAN/2024-2025/1st Semester/COR/ALVARADO, MARJORIE GRATUITO_1758523285_149.pdf',
    'uploads/documents/ISULAN/2024-2025/1st Semester/COR/ALVARADO, RIOU JADE TIZON_1758523285_151.pdf'
];

echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
echo "<tr><th>ALVARADO File</th><th>Status</th><th>Test Link</th></tr>";

foreach ($alvaradoFiles as $file) {
    if (file_exists($file)) {
        $fileSize = filesize($file);
        $fileInfo = shell_exec("file '$file' 2>/dev/null");
        $isValidPDF = strpos($fileInfo, 'PDF document') !== false && strpos($fileInfo, '0 pages') === false;
        
        echo "<tr>";
        echo "<td>" . htmlspecialchars(basename($file)) . "</td>";
        echo "<td>" . ($isValidPDF ? "✅ Valid PDF ($fileSize bytes)" : "❌ Invalid PDF ($fileSize bytes)") . "</td>";
        echo "<td><a href='view_document.php?file=" . urlencode($file) . "&type=COR' target='_blank' style='color: blue;'>Test View</a></td>";
        echo "</tr>";
    } else {
        echo "<tr>";
        echo "<td>" . htmlspecialchars(basename($file)) . "</td>";
        echo "<td style='color: red;'>❌ File not found</td>";
        echo "<td>-</td>";
        echo "</tr>";
    }
}

echo "</table>";

echo "<p><a href='masterlist.php' class='btn btn-primary'>← Back to Masterlist</a></p>";
?>

