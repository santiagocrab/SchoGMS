<?php
// Quick script to check what file paths are stored in MongoDB
require_once 'conn_mongodb.php';

$documentCollection = $mongodb->collection('document_uploads');
$docs = $documentCollection->find(['category' => 'COR'], ['limit' => 5]);

echo "<h2>COR Documents in Database:</h2>";
echo "<table border='1' style='border-collapse: collapse;'>";
echo "<tr><th>Original Name</th><th>File Name</th><th>File Path (Stored)</th><th>File Exists?</th></tr>";

foreach ($docs as $doc) {
    $filePath = $doc['file_path'] ?? 'N/A';
    $fileName = $doc['file_name'] ?? 'N/A';
    $originalName = $doc['original_name'] ?? 'N/A';
    
    // Check if file exists
    $exists = false;
    $actualPath = null;
    
    // Try the stored path
    if (file_exists($filePath)) {
        $exists = true;
        $actualPath = $filePath;
    } else {
        // Try relative to __DIR__
        $testPath = __DIR__ . '/' . ltrim($filePath, '/');
        if (file_exists($testPath)) {
            $exists = true;
            $actualPath = $testPath;
        }
    }
    
    echo "<tr>";
    echo "<td>" . htmlspecialchars($originalName) . "</td>";
    echo "<td>" . htmlspecialchars($fileName) . "</td>";
    echo "<td>" . htmlspecialchars($filePath) . "</td>";
    echo "<td>" . ($exists ? "✅ YES ($actualPath)" : "❌ NO") . "</td>";
    echo "</tr>";
}

echo "</table>";

// Check uploads directory structure
echo "<h2>Uploads Directory Structure:</h2>";
$uploadsDir = __DIR__ . '/uploads';
if (is_dir($uploadsDir)) {
    echo "<p>✅ Uploads directory exists: " . htmlspecialchars($uploadsDir) . "</p>";
    
    // List subdirectories
    $dirs = glob($uploadsDir . '/*', GLOB_ONLYDIR);
    echo "<p><strong>Subdirectories:</strong></p><ul>";
    foreach ($dirs as $dir) {
        echo "<li>" . htmlspecialchars(basename($dir)) . "</li>";
    }
    echo "</ul>";
} else {
    echo "<p>❌ Uploads directory does not exist: " . htmlspecialchars($uploadsDir) . "</p>";
}
?>


