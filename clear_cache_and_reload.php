<?php
/**
 * Clear Cache and Reload Database
 * Force reload of MongoDB data to ensure latest records are visible
 */

require_once 'conn_mongodb.php';

echo "<h2>🔄 Clear Cache and Reload Database</h2>";

try {
    $registrarCollection = $mongodb->collection('registrar_master_list');
    
    echo "<h3>1. Before Cache Clear</h3>";
    $countBefore = $registrarCollection->count();
    echo "Record count before: <strong>$countBefore</strong><br>";
    
    // Force reload by accessing the data directly
    echo "<h3>2. Force Reload Data</h3>";
    
    // Clear any potential cache by creating a new instance
    $mongodb2 = new SimpleFastMongoDB('schogms');
    $registrarCollection2 = $mongodb2->collection('registrar_master_list');
    
    $countAfter = $registrarCollection2->count();
    echo "Record count after reload: <strong>$countAfter</strong><br>";
    
    // Check if the file exists and its size
    $dataFile = __DIR__ . '/mongodb_data/schogms/registrar_master_list.json';
    echo "<h3>3. File Information</h3>";
    echo "Data file path: " . $dataFile . "<br>";
    echo "File exists: " . (file_exists($dataFile) ? "✅ Yes" : "❌ No") . "<br>";
    
    if (file_exists($dataFile)) {
        $fileSize = filesize($dataFile);
        echo "File size: " . number_format($fileSize) . " bytes<br>";
        
        // Check file modification time
        $modTime = filemtime($dataFile);
        echo "Last modified: " . date('Y-m-d H:i:s', $modTime) . "<br>";
        
        // Read first few lines to see content
        $content = file_get_contents($dataFile);
        $lines = explode("\n", $content);
        echo "Total lines in file: " . count($lines) . "<br>";
        
        // Check if file contains College of Engineering
        if (strpos($content, 'College of Engineering') !== false) {
            echo "✅ File contains 'College of Engineering'<br>";
        } else {
            echo "❌ File does NOT contain 'College of Engineering'<br>";
        }
    }
    
    // Try to manually add a test record to see if it persists
    echo "<h3>4. Test Record Addition</h3>";
    $testRecord = [
        'campus' => 'ISULAN',
        'file_group' => 'Test Cache Clear',
        'filename' => 'test_cache_clear.csv',
        'academic_year' => '2022-2023',
        'semester' => '1st Semester',
        'last_name' => 'CACHE',
        'first_name' => 'TEST',
        'middle_name' => 'RECORD',
        'id_number' => '88888',
        'created_at' => date('Y-m-d H:i:s'),
        'updated_at' => date('Y-m-d H:i:s')
    ];
    
    $result = $registrarCollection2->insertOne($testRecord);
    if ($result) {
        echo "✅ Test record inserted successfully<br>";
        
        // Check count again
        $countAfterTest = $registrarCollection2->count();
        echo "Record count after test insert: <strong>$countAfterTest</strong><br>";
        
        // Clean up test record
        $registrarCollection2->deleteOne(['id_number' => '88888']);
        echo "✅ Test record cleaned up<br>";
    } else {
        echo "❌ Failed to insert test record<br>";
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "<br>";
}

echo "<br><a href='debug_filter_issue.php'>← Check Filter Issue Again</a><br>";
echo "<a href='users/registrar/masterlist.php'>← Back to Masterlist</a>";
?>
