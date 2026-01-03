<?php
/**
 * Detailed Upload Debug
 * Test every step of the upload process
 */

require_once 'conn_mongodb.php';

echo "<h2>🔍 Detailed Upload Debug</h2>";

// Test 1: Check current data
echo "<h3>1. Current Database Status</h3>";
try {
    $registrarCollection = $mongodb->collection('registrar_master_list');
    $count = $registrarCollection->count();
    echo "Total records in database: <strong>$count</strong><br>";
    
    // Get unique filenames
    $filenames = [];
    $allRecords = $registrarCollection->find([]);
    foreach ($allRecords as $record) {
        if (!empty($record['filename'])) {
            $filenames[$record['filename']] = true;
        }
    }
    
    echo "<h4>Current Filenames in Database:</h4>";
    foreach (array_keys($filenames) as $filename) {
        echo "- " . $filename . "<br>";
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "<br>";
}

// Test 2: Check uploads directory
echo "<h3>2. Upload Directory Status</h3>";
$uploadsDir = __DIR__ . '/users/registrar/uploads/';
echo "Uploads directory: " . $uploadsDir . "<br>";
echo "Directory exists: " . (is_dir($uploadsDir) ? "✅ Yes" : "❌ No") . "<br>";
echo "Directory writable: " . (is_writable($uploadsDir) ? "✅ Yes" : "❌ No") . "<br>";

// List files in uploads directory
echo "<h4>Files in uploads directory:</h4>";
$files = scandir($uploadsDir);
foreach ($files as $file) {
    if ($file != '.' && $file != '..') {
        echo "- " . $file . "<br>";
    }
}

// Test 3: Test MongoDB insert directly
echo "<h3>3. Direct MongoDB Insert Test</h3>";
try {
    $testData = [
        'campus' => 'ISULAN',
        'file_group' => 'Test College of Engineering',
        'filename' => 'Test College of Engineering.csv',
        'academic_year' => '2022-2023',
        'semester' => '1st Semester',
        'last_name' => 'TEST',
        'first_name' => 'ENGINEERING',
        'middle_name' => 'STUDENT',
        'id_number' => '88888',
        'created_at' => date('Y-m-d H:i:s'),
        'updated_at' => date('Y-m-d H:i:s')
    ];
    
    $result = $registrarCollection->insertOne($testData);
    if ($result) {
        echo "✅ Test data inserted successfully<br>";
        
        // Check if it was actually added
        $newCount = $registrarCollection->count();
        echo "New record count: <strong>$newCount</strong><br>";
        
        // Clean up test data
        $registrarCollection->deleteOne(['id_number' => '88888']);
        echo "✅ Test data cleaned up<br>";
    } else {
        echo "❌ Failed to insert test data<br>";
    }
} catch (Exception $e) {
    echo "❌ Test insert error: " . $e->getMessage() . "<br>";
}

// Test 4: Check PhpSpreadsheet
echo "<h3>4. PhpSpreadsheet Status</h3>";
if (class_exists('PhpOffice\PhpSpreadsheet\IOFactory')) {
    echo "✅ PhpSpreadsheet is available<br>";
} else {
    echo "❌ PhpSpreadsheet is NOT available<br>";
    echo "This might be the issue!<br>";
}

// Test 5: Create a simple test CSV file
echo "<h3>5. Create Test CSV File</h3>";
$testCsvContent = "Last Name,First Name,Middle Name,Ext. Name,ID Number,Gender,Student Type,Year Level,Attended,Course,Curriculum,Scholarship,GPA,CGPA,% Pass,Grade Remarks,Enrolled,Lec. Unit,Lab. Unit,COR Printed,Billing Profile,Misc. Fee Total,Misc. Fee Paid,Tuition Fee Total,Tuition Fee Paid,Street,Barangay,Municipality/City,Province,Zip Code\n";
$testCsvContent .= "ENGINEERING,JOHN,SMITH,,12345,Male,Regular,3rd year,1st semester; 2nd semester,Bachelor of Science in Engineering,2020,None,2.5,2.3,85,Pass,Yes,3,1,Yes,Profile A,5000,5000,15000,15000,123 Main St,Barangay 1,Isulan,Sultan Kudarat,9806\n";

$testCsvFile = $uploadsDir . 'test_engineering.csv';
if (file_put_contents($testCsvFile, $testCsvContent)) {
    echo "✅ Test CSV file created: test_engineering.csv<br>";
    
    // Test reading the CSV
    if (class_exists('PhpOffice\PhpSpreadsheet\IOFactory')) {
        try {
            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($testCsvFile);
            $worksheet = $spreadsheet->getActiveSheet();
            $data = $worksheet->toArray();
            echo "✅ CSV file read successfully. Rows: " . count($data) . "<br>";
            
            // Show first row
            if (count($data) > 1) {
                $firstDataRow = $data[1]; // Skip header
                echo "First data row: " . implode(', ', array_slice($firstDataRow, 0, 5)) . "...<br>";
            }
        } catch (Exception $e) {
            echo "❌ Error reading CSV: " . $e->getMessage() . "<br>";
        }
    }
    
    // Clean up test file
    unlink($testCsvFile);
    echo "✅ Test CSV file cleaned up<br>";
} else {
    echo "❌ Failed to create test CSV file<br>";
}

// Test 6: Simulate the actual upload process
echo "<h3>6. Simulate Upload Process</h3>";
echo '<form action="users/registrar/submit_master_list.php" method="POST" enctype="multipart/form-data" target="_blank">';
echo '<input type="hidden" name="session_campus" value="ISULAN">';
echo '<input type="hidden" name="file_group" value="College of Engineering">';
echo '<input type="hidden" name="academic_year" value="2022-2023">';
echo '<input type="hidden" name="semester" value="1st Semester">';
echo '<input type="file" name="excelFile" accept=".csv,.xlsx,.xls" required><br><br>';
echo '<button type="submit">Test Upload (Opens in New Tab)</button>';
echo '</form>';

echo "<br><a href='users/registrar/masterlist.php'>← Back to Masterlist</a>";
?>
