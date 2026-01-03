<?php
/**
 * Debug Upload Process
 * Test the upload functionality step by step
 */

require_once 'conn_mongodb.php';

echo "<h2>🔍 Upload Debug Test</h2>";

// Test 1: Check MongoDB connection
echo "<h3>1. MongoDB Connection Test</h3>";
try {
    $testCollection = $mongodb->collection('test_upload');
    $testResult = $testCollection->insertOne(['test' => 'connection', 'timestamp' => date('Y-m-d H:i:s')]);
    if ($testResult) {
        echo "✅ MongoDB connection working<br>";
        $testCollection->deleteOne(['test' => 'connection']);
    } else {
        echo "❌ MongoDB connection failed<br>";
    }
} catch (Exception $e) {
    echo "❌ MongoDB error: " . $e->getMessage() . "<br>";
}

// Test 2: Check uploads directory
echo "<h3>2. Uploads Directory Test</h3>";
$uploadsDir = __DIR__ . '/users/registrar/uploads/';
echo "Uploads directory: " . $uploadsDir . "<br>";
echo "Directory exists: " . (is_dir($uploadsDir) ? "✅ Yes" : "❌ No") . "<br>";
echo "Directory writable: " . (is_writable($uploadsDir) ? "✅ Yes" : "❌ No") . "<br>";

// Test 3: Check current data in registrar_master_list
echo "<h3>3. Current Data in Registrar Master List</h3>";
try {
    $registrarCollection = $mongodb->collection('registrar_master_list');
    $count = $registrarCollection->count();
    echo "Total records: " . $count . "<br>";
    
    // Get sample records
    $sampleRecords = $registrarCollection->find([], ['limit' => 5]);
    echo "<h4>Sample Records:</h4>";
    foreach ($sampleRecords as $record) {
        echo "- Campus: " . ($record['campus'] ?? 'N/A') . 
             ", File Group: " . ($record['file_group'] ?? 'N/A') . 
             ", Filename: " . ($record['filename'] ?? 'N/A') . "<br>";
    }
    
    // Get unique filenames
    $filenames = [];
    $allRecords = $registrarCollection->find([]);
    foreach ($allRecords as $record) {
        if (!empty($record['filename'])) {
            $filenames[$record['filename']] = true;
        }
    }
    
    echo "<h4>Unique Filenames in Database:</h4>";
    foreach (array_keys($filenames) as $filename) {
        echo "- " . $filename . "<br>";
    }
    
} catch (Exception $e) {
    echo "❌ Error reading data: " . $e->getMessage() . "<br>";
}

// Test 4: Check if PhpSpreadsheet is available
echo "<h3>4. PhpSpreadsheet Test</h3>";
if (class_exists('PhpOffice\PhpSpreadsheet\IOFactory')) {
    echo "✅ PhpSpreadsheet is available<br>";
} else {
    echo "❌ PhpSpreadsheet is NOT available<br>";
}

// Test 5: Simulate a simple upload
echo "<h3>5. Simulate Upload Test</h3>";
try {
    $testData = [
        'campus' => 'ISULAN',
        'file_group' => 'Test Upload',
        'filename' => 'Test File.csv',
        'academic_year' => '2022-2023',
        'semester' => '1st Semester',
        'last_name' => 'TEST',
        'first_name' => 'USER',
        'middle_name' => 'DEBUG',
        'id_number' => '99999',
        'created_at' => date('Y-m-d H:i:s'),
        'updated_at' => date('Y-m-d H:i:s')
    ];
    
    $result = $registrarCollection->insertOne($testData);
    if ($result) {
        echo "✅ Test data inserted successfully<br>";
        echo "Inserted ID: " . $result . "<br>";
        
        // Clean up test data
        $registrarCollection->deleteOne(['id_number' => '99999']);
        echo "✅ Test data cleaned up<br>";
    } else {
        echo "❌ Failed to insert test data<br>";
    }
} catch (Exception $e) {
    echo "❌ Test upload error: " . $e->getMessage() . "<br>";
}

echo "<h3>6. Upload Form Test</h3>";
echo '<form action="users/registrar/submit_master_list.php" method="POST" enctype="multipart/form-data">';
echo '<input type="hidden" name="session_campus" value="ISULAN">';
echo '<input type="hidden" name="file_group" value="Test Group">';
echo '<input type="hidden" name="academic_year" value="2022-2023">';
echo '<input type="hidden" name="semester" value="1st Semester">';
echo '<input type="file" name="excelFile" accept=".csv,.xlsx,.xls" required><br><br>';
echo '<button type="submit">Test Upload</button>';
echo '</form>';

echo "<br><a href='users/registrar/masterlist.php'>← Back to Masterlist</a>";
?>
