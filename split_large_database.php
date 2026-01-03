<?php
/**
 * Database Splitter
 * Splits large collections into smaller chunks for better performance
 */

require_once 'conn_mongodb.php';

echo "<h2>Database Splitter - Performance Optimizer</h2>";

// Check if we need to split the registrar masterlist
$registrarFile = __DIR__ . '/mongodb_data/schogms/registrar_master_list.json';
$fileSize = file_exists($registrarFile) ? filesize($registrarFile) : 0;
$fileSizeMB = round($fileSize / 1024 / 1024, 2);

echo "<h3>Current Database Status</h3>";
echo "Registrar masterlist file size: <strong>{$fileSizeMB}MB</strong><br>";

if ($fileSizeMB > 1) { // If file is larger than 1MB
    echo "<div style='background: #fff3cd; padding: 15px; border-radius: 8px; margin: 20px 0;'>";
    echo "<h4>⚠️ Large File Detected</h4>";
    echo "<p>The registrar masterlist is quite large ({$fileSizeMB}MB). This can cause slow loading.</p>";
    echo "<p><strong>Recommendation:</strong> Split into smaller chunks for better performance.</p>";
    echo "</div>";
    
    // Split the file
    echo "<h3>🔄 Splitting Database...</h3>";
    
    $content = file_get_contents($registrarFile);
    $data = json_decode($content, true);
    
    if ($data && is_array($data)) {
        $chunkSize = 1000; // 1000 records per chunk
        $chunks = array_chunk($data, $chunkSize);
        $chunkCount = count($chunks);
        
        echo "Splitting {$chunkCount} records into " . count($chunks) . " chunks of {$chunkSize} records each...<br><br>";
        
        // Create backup
        $backupFile = $registrarFile . '.backup.' . date('Y-m-d-H-i-s');
        copy($registrarFile, $backupFile);
        echo "✅ Backup created: " . basename($backupFile) . "<br>";
        
        // Save chunks
        foreach ($chunks as $index => $chunk) {
            $chunkFile = __DIR__ . '/mongodb_data/schogms/registrar_master_list_chunk_' . ($index + 1) . '.json';
            file_put_contents($chunkFile, json_encode($chunk, JSON_UNESCAPED_UNICODE));
            echo "✅ Chunk " . ($index + 1) . " saved (" . count($chunk) . " records)<br>";
        }
        
        // Create a master index file
        $indexData = [
            'total_records' => count($data),
            'chunk_size' => $chunkSize,
            'chunk_count' => count($chunks),
            'created_at' => date('Y-m-d H:i:s'),
            'chunks' => []
        ];
        
        foreach ($chunks as $index => $chunk) {
            $indexData['chunks'][] = [
                'chunk_number' => $index + 1,
                'file' => 'registrar_master_list_chunk_' . ($index + 1) . '.json',
                'record_count' => count($chunk),
                'first_id' => $chunk[0]['id'] ?? null,
                'last_id' => end($chunk)['id'] ?? null
            ];
        }
        
        $indexFile = __DIR__ . '/mongodb_data/schogms/registrar_master_list_index.json';
        file_put_contents($indexFile, json_encode($indexData, JSON_PRETTY_PRINT));
        echo "✅ Master index created<br><br>";
        
        // Remove original large file
        unlink($registrarFile);
        echo "✅ Original large file removed<br><br>";
        
        echo "<div style='background: #d4edda; padding: 15px; border-radius: 8px; margin: 20px 0;'>";
        echo "<h4>✅ Database Split Complete!</h4>";
        echo "<p><strong>Benefits:</strong></p>";
        echo "<ul>";
        echo "<li>Faster loading (only load needed chunks)</li>";
        echo "<li>Better memory usage</li>";
        echo "<li>Improved search performance</li>";
        echo "<li>Easier backup and maintenance</li>";
        echo "</ul>";
        echo "</p>";
        echo "</div>";
        
    } else {
        echo "❌ Error: Could not parse registrar masterlist data<br>";
    }
    
} else {
    echo "<div style='background: #d4edda; padding: 15px; border-radius: 8px; margin: 20px 0;'>";
    echo "<h4>✅ Database Size is Optimal</h4>";
    echo "<p>The registrar masterlist file size ({$fileSizeMB}MB) is within acceptable limits.</p>";
    echo "<p>No splitting needed at this time.</p>";
    echo "</div>";
}

// Performance test after optimization
echo "<h3>🚀 Performance Test After Optimization</h3>";
$start = microtime(true);
$testCount = $dbHelper->countRecords('registrar_master_list');
$end = microtime(true);
$time = round(($end - $start) * 1000, 2);

echo "Count operation: <strong>{$time}ms</strong> for <strong>{$testCount}</strong> records<br>";

$start = microtime(true);
$testResults = $dbHelper->getRegistrarMasterlistPaginated([], 1, 25);
$end = microtime(true);
$time = round(($end - $start) * 1000, 2);

echo "Pagination query: <strong>{$time}ms</strong> for 25 records<br><br>";

echo "<h3>📊 Optimization Complete!</h3>";
echo "<p><strong>Next Steps:</strong></p>";
echo "<ul>";
echo "<li><a href='test_ultra_performance.php'>Run Ultra Performance Test</a></li>";
echo "<li><a href='registrar_masterlist_fast.php'>Try Fast Masterlist Page</a></li>";
echo "<li><a href='index.php'>Back to Main Application</a></li>";
echo "</ul>";
?>
