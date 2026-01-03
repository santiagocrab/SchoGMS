<?php
/**
 * Direct File Test
 * Test writing directly to the JSON file to bypass MongoDB implementation issues
 */

echo "<h2>🔧 Direct File Test</h2>";

$dataFile = __DIR__ . '/mongodb_data/schogms/registrar_master_list.json';

echo "<h3>1. Current File Status</h3>";
echo "File path: $dataFile<br>";
echo "File exists: " . (file_exists($dataFile) ? "✅ Yes" : "❌ No") . "<br>";

if (file_exists($dataFile)) {
    $fileSize = filesize($dataFile);
    echo "File size: " . number_format($fileSize) . " bytes<br>";
    
    // Read current data
    $content = file_get_contents($dataFile);
    $data = json_decode($content, true);
    
    if ($data === null) {
        echo "❌ JSON decode error: " . json_last_error_msg() . "<br>";
    } else {
        echo "Current record count: " . count($data) . "<br>";
        
        // Add a test record directly
        echo "<h3>2. Adding Test Record Directly</h3>";
        
        $testRecord = [
            'id' => count($data) + 1,
            'campus' => 'ISULAN',
            'file_group' => 'College of Engineering',
            'filename' => 'Masterlist - College of Engineering.csv',
            'academic_year' => '2022-2023',
            'semester' => '1st Semester',
            'last_name' => 'DIRECT',
            'first_name' => 'TEST',
            'middle_name' => 'RECORD',
            'id_number' => '77777',
            'gender' => 'Male',
            'student_type' => 'Regular',
            'year_level' => '3rd year',
            'attended' => '1st semester; 2nd semester',
            'course' => 'Bachelor of Science in Engineering',
            'curriculum' => 2020,
            'scholarship' => 'None',
            'gpa' => 2.5,
            'cgpa' => 2.3,
            'pass_percentage' => 85,
            'grade_remarks' => 'Pass',
            'enrolled' => 'Yes',
            'lec_unit' => 3,
            'lab_unit' => 1,
            'cor_printed' => 'Yes',
            'billing_profile' => 'Profile A',
            'misc_fee_total' => 5000,
            'misc_fee_paid' => 5000,
            'tuition_fee_total' => 15000,
            'tuition_fee_paid' => 15000,
            'street' => '123 Test St',
            'barangay' => 'Test Barangay',
            'municipality_city' => 'Isulan',
            'province' => 'Sultan Kudarat',
            'zip_code' => '9806',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
            '_id' => 'direct_test_' . time()
        ];
        
        // Add the test record
        $data[] = $testRecord;
        
        // Write back to file
        $newContent = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        
        if (file_put_contents($dataFile, $newContent)) {
            echo "✅ Test record added directly to file<br>";
            echo "New record count: " . count($data) . "<br>";
            
            // Verify it was written
            $verifyContent = file_get_contents($dataFile);
            if (strpos($verifyContent, 'College of Engineering') !== false) {
                echo "✅ File now contains 'College of Engineering'<br>";
            } else {
                echo "❌ File still does not contain 'College of Engineering'<br>";
            }
            
            // Check file size
            $newFileSize = filesize($dataFile);
            echo "New file size: " . number_format($newFileSize) . " bytes<br>";
            
        } else {
            echo "❌ Failed to write to file<br>";
        }
    }
}

echo "<h3>3. Test MongoDB After Direct Write</h3>";
try {
    require_once 'conn_mongodb.php';
    $collection = $mongodb->collection('registrar_master_list');
    $count = $collection->count();
    echo "MongoDB count after direct write: $count<br>";
    
    // Check for College of Engineering
    $engineeringRecords = $collection->find(['file_group' => 'College of Engineering']);
    $engineeringCount = 0;
    foreach ($engineeringRecords as $record) {
        $engineeringCount++;
    }
    echo "College of Engineering records found: $engineeringCount<br>";
    
} catch (Exception $e) {
    echo "❌ MongoDB error: " . $e->getMessage() . "<br>";
}

echo "<br><a href='debug_filter_issue.php'>← Check Filter Issue Again</a><br>";
echo "<a href='users/registrar/masterlist.php'>← Back to Masterlist</a>";
?>
