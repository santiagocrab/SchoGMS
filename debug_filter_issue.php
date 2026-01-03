<?php
/**
 * Debug Filter Issue
 * Check why College of Engineering is not showing in filter dropdown
 */

require_once 'conn_mongodb.php';

echo "<h2>🔍 Debug Filter Issue</h2>";

try {
    $registrarCollection = $mongodb->collection('registrar_master_list');
    
    // Get total count
    $totalCount = $registrarCollection->count();
    echo "<h3>1. Database Status</h3>";
    echo "Total records in database: <strong>$totalCount</strong><br>";
    
    // Get all unique filenames
    echo "<h3>2. All Filenames in Database</h3>";
    $filenames = [];
    $allRecords = $registrarCollection->find([]);
    
    foreach ($allRecords as $record) {
        if (!empty($record['filename'])) {
            $filenames[$record['filename']] = true;
        }
    }
    
    echo "Unique filenames found:<br>";
    foreach (array_keys($filenames) as $filename) {
        echo "- " . htmlspecialchars($filename) . "<br>";
    }
    
    // Get all unique file groups
    echo "<h3>3. All File Groups in Database</h3>";
    $fileGroups = [];
    $allRecords = $registrarCollection->find([]);
    
    foreach ($allRecords as $record) {
        if (!empty($record['file_group'])) {
            $fileGroups[$record['file_group']] = true;
        }
    }
    
    echo "Unique file groups found:<br>";
    foreach (array_keys($fileGroups) as $fileGroup) {
        echo "- " . htmlspecialchars($fileGroup) . "<br>";
    }
    
    // Check specifically for College of Engineering
    echo "<h3>4. College of Engineering Records</h3>";
    $engineeringRecords = $registrarCollection->find(['file_group' => 'College of Engineering']);
    $engineeringCount = 0;
    
    foreach ($engineeringRecords as $record) {
        $engineeringCount++;
        if ($engineeringCount <= 3) { // Show first 3 records
            echo "Record $engineeringCount:<br>";
            echo "- Filename: " . htmlspecialchars($record['filename'] ?? 'N/A') . "<br>";
            echo "- File Group: " . htmlspecialchars($record['file_group'] ?? 'N/A') . "<br>";
            echo "- Campus: " . htmlspecialchars($record['campus'] ?? 'N/A') . "<br>";
            echo "- Student: " . htmlspecialchars($record['last_name'] ?? 'N/A') . ", " . htmlspecialchars($record['first_name'] ?? 'N/A') . "<br>";
            echo "<br>";
        }
    }
    
    echo "Total College of Engineering records: <strong>$engineeringCount</strong><br>";
    
    // Check for filename-based records
    echo "<h3>5. Records with 'College of Engineering' in filename</h3>";
    $filenameRecords = $registrarCollection->find(['filename' => 'Masterlist - College of Engineering.csv']);
    $filenameCount = 0;
    
    foreach ($filenameRecords as $record) {
        $filenameCount++;
    }
    
    echo "Records with filename 'Masterlist - College of Engineering.csv': <strong>$filenameCount</strong><br>";
    
    // Test the exact filter generation logic
    echo "<h3>6. Filter Generation Test</h3>";
    $categories = [];
    $fileGroups = [];
    
    $allRecords = $registrarCollection->find([]);
    foreach ($allRecords as $record) {
        if (!empty($record['filename'])) {
            $categories[$record['filename']] = $record['filename'];
        }
        if (!empty($record['file_group'])) {
            $fileGroups[$record['file_group']] = $record['file_group'];
        }
    }
    
    ksort($categories);
    ksort($fileGroups);
    
    echo "Categories that should appear in dropdown:<br>";
    foreach ($categories as $category) {
        echo "- " . htmlspecialchars($category) . "<br>";
    }
    
    echo "<br>File groups that should appear in dropdown:<br>";
    foreach ($fileGroups as $fileGroup) {
        echo "- " . htmlspecialchars($fileGroup) . "<br>";
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "<br>";
}

echo "<br><a href='users/registrar/masterlist.php'>← Back to Masterlist</a>";
?>
