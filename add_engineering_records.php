<?php
/**
 * Add Engineering Records
 * Add multiple College of Engineering records directly to test the system
 */

echo "<h2>🔧 Add Engineering Records Test</h2>";

$dataFile = __DIR__ . '/mongodb_data/schogms/registrar_master_list.json';

// Read current data
$content = file_get_contents($dataFile);
$data = json_decode($content, true);

if ($data === null) {
    echo "❌ JSON decode error: " . json_last_error_msg() . "<br>";
    exit;
}

echo "<h3>1. Current Status</h3>";
echo "Current record count: " . count($data) . "<br>";

// Add 5 test engineering records
echo "<h3>2. Adding 5 Test Engineering Records</h3>";

for ($i = 1; $i <= 5; $i++) {
    $testRecord = [
        'id' => count($data) + $i,
        'campus' => 'ISULAN',
        'file_group' => 'College of Engineering',
        'filename' => 'Masterlist - College of Engineering.csv',
        'academic_year' => '2022-2023',
        'semester' => '1st Semester',
        'last_name' => 'ENGINEERING' . $i,
        'first_name' => 'STUDENT' . $i,
        'middle_name' => 'TEST',
        'id_number' => '1000' . $i,
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
        '_id' => 'engineering_test_' . $i . '_' . time()
    ];
    
    $data[] = $testRecord;
    echo "Added record $i: ENGINEERING$i STUDENT$i<br>";
}

// Write back to file
$newContent = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

if (file_put_contents($dataFile, $newContent)) {
    echo "<h3>3. Success!</h3>";
    echo "✅ 5 engineering records added to file<br>";
    echo "New record count: " . count($data) . "<br>";
    
    // Verify it was written
    $verifyContent = file_get_contents($dataFile);
    $engineeringCount = substr_count($verifyContent, 'College of Engineering');
    echo "College of Engineering references in file: $engineeringCount<br>";
    
} else {
    echo "❌ Failed to write to file<br>";
}

echo "<h3>4. Test MongoDB</h3>";
try {
    require_once 'conn_mongodb.php';
    $collection = $mongodb->collection('registrar_master_list');
    $count = $collection->count();
    echo "MongoDB count: $count<br>";
    
    // Check for College of Engineering
    $engineeringRecords = $collection->find(['file_group' => 'College of Engineering']);
    $engineeringCount = 0;
    foreach ($engineeringRecords as $record) {
        $engineeringCount++;
    }
    echo "College of Engineering records in MongoDB: $engineeringCount<br>";
    
} catch (Exception $e) {
    echo "❌ MongoDB error: " . $e->getMessage() . "<br>";
}

echo "<br><a href='users/registrar/masterlist.php'>← Check Masterlist</a><br>";
echo "<a href='debug_filter_issue.php'>← Check Filter Issue</a>";
?>
