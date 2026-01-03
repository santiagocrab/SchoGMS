<?php
// Test script to check actual file paths
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'conn_mongodb.php';

echo "<!DOCTYPE html><html><head><title>File Path Test</title>";
echo "<style>
    body { font-family: Arial, sans-serif; margin: 20px; }
    table { border-collapse: collapse; width: 100%; margin: 20px 0; }
    th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
    th { background-color: #4CAF50; color: white; }
    .error { color: red; }
    .success { color: green; }
</style></head><body>";

echo "<h2>File Path Test</h2>";

// Get a sample COR document
$documentCollection = $mongodb->collection('document_uploads');
$corDocs = $documentCollection->find(['category' => 'COR'], ['limit' => 5]);

echo "<h3>Sample COR Documents from Database:</h3>";
echo "<table border='1'>";
echo "<tr><th>Original Name</th><th>File Path (DB)</th><th>File Exists?</th><th>Correct Web Path</th></tr>";

foreach ($corDocs as $doc) {
    $filePath = $doc['file_path'] ?? 'N/A';
    $originalName = $doc['original_name'] ?? 'N/A';
    
    // Check if file exists at stored path
    $exists = file_exists($filePath);
    
    // Try different path variations
    $paths = [
        'Original' => $filePath,
        '/SchoGMS/' . ltrim($filePath, '/') => '/SchoGMS/' . ltrim($filePath, '/'),
        '/' . ltrim($filePath, '/') => '/' . ltrim($filePath, '/'),
        __DIR__ . '/' . $filePath => __DIR__ . '/' . $filePath,
    ];
    
    // Also try without uploads prefix if it starts with uploads
    if (strpos($filePath, 'uploads/') === 0) {
        $paths['Without uploads/'] = str_replace('uploads/', '', $filePath);
    }
    
    echo "<tr>";
    echo "<td>" . htmlspecialchars($originalName) . "</td>";
    echo "<td>" . htmlspecialchars($filePath) . "</td>";
    echo "<td>" . ($exists ? "<span class='success'>✅ YES</span>" : "<span class='error'>❌ NO</span>") . "</td>";
    echo "<td>";
    echo "<ul>";
    foreach ($paths as $label => $testPath) {
        $testExists = file_exists($testPath);
        $status = $testExists ? "✅ EXISTS" : "❌ NOT FOUND";
        echo "<li><strong>$label:</strong> " . htmlspecialchars($testPath) . " - $status</li>";
    }
    echo "</ul>";
    echo "</td>";
    echo "</tr>";
}

echo "</table>";

// Check document root
echo "<h3>Server Information:</h3>";
echo "<p><strong>DOCUMENT_ROOT:</strong> " . htmlspecialchars($_SERVER['DOCUMENT_ROOT'] ?? 'NOT SET') . "</p>";
echo "<p><strong>SCRIPT_NAME:</strong> " . htmlspecialchars($_SERVER['SCRIPT_NAME'] ?? 'NOT SET') . "</p>";
echo "<p><strong>REQUEST_URI:</strong> " . htmlspecialchars($_SERVER['REQUEST_URI'] ?? 'NOT SET') . "</p>";
echo "<p><strong>__DIR__:</strong> " . htmlspecialchars(__DIR__) . "</p>";

// Check if uploads directory exists
$uploadsDir = __DIR__ . '/uploads';
echo "<p><strong>Uploads directory exists:</strong> " . (is_dir($uploadsDir) ? "✅ YES" : "❌ NO") . "</p>";
if (is_dir($uploadsDir)) {
    echo "<p><strong>Uploads directory path:</strong> " . htmlspecialchars($uploadsDir) . "</p>";
    echo "<p><strong>Uploads directory is readable:</strong> " . (is_readable($uploadsDir) ? "✅ YES" : "❌ NO") . "</p>";
}

echo "</body></html>";
?>


