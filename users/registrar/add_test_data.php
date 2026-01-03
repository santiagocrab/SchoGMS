<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo "<h1>❌ Not logged in</h1>";
    echo "<p><a href='../login.php'>Go to Login</a></p>";
    exit;
}

echo "<h1>➕ Add Test Data to Masterlist</h1>";

try {
    require '../../conn_mongodb.php';
    
    $registrarCollection = $mongodb->collection('registrar_master_list');
    
    // Check current count
    $currentCount = $registrarCollection->count();
    echo "<p>📊 Current records: " . $currentCount . "</p>";
    
    if ($currentCount == 0) {
        echo "<p>⚠️ No records found. Adding test data...</p>";
        
        // Add some test records
        $testRecords = [
            [
                'last_name' => 'SANTOS',
                'first_name' => 'JUAN',
                'middle_name' => 'DELA CRUZ',
                'id_number' => '2024-0001',
                'gender' => 'Male',
                'student_type' => 'Regular',
                'year_level' => '1st Year',
                'course' => 'BS Computer Science',
                'campus' => 'ISULAN',
                'filename' => 'Test Data 2024',
                'file_group' => 'Test Group 1',
                'academic_year' => '2024-2025',
                'semester' => '1st Semester',
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'last_name' => 'GARCIA',
                'first_name' => 'MARIA',
                'middle_name' => 'SANTOS',
                'id_number' => '2024-0002',
                'gender' => 'Female',
                'student_type' => 'Regular',
                'year_level' => '2nd Year',
                'course' => 'BS Information Technology',
                'campus' => 'ISULAN',
                'filename' => 'Test Data 2024',
                'file_group' => 'Test Group 1',
                'academic_year' => '2024-2025',
                'semester' => '1st Semester',
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'last_name' => 'CRUZ',
                'first_name' => 'PEDRO',
                'middle_name' => 'MARTINEZ',
                'id_number' => '2024-0003',
                'gender' => 'Male',
                'student_type' => 'Regular',
                'year_level' => '3rd Year',
                'course' => 'BS Computer Engineering',
                'campus' => 'ISULAN',
                'filename' => 'Test Data 2024',
                'file_group' => 'Test Group 1',
                'academic_year' => '2024-2025',
                'semester' => '1st Semester',
                'created_at' => date('Y-m-d H:i:s')
            ]
        ];
        
        foreach ($testRecords as $record) {
            $result = $registrarCollection->insertOne($record);
            echo "<p>✅ Added: " . $record['last_name'] . ", " . $record['first_name'] . "</p>";
        }
        
        echo "<p>🎉 Test data added successfully!</p>";
        
    } else {
        echo "<p>✅ Records already exist. No need to add test data.</p>";
    }
    
    // Show final count
    $finalCount = $registrarCollection->count();
    echo "<p>📊 Final record count: " . $finalCount . "</p>";
    
} catch (Exception $e) {
    echo "<p>❌ Error: " . $e->getMessage() . "</p>";
}

echo "<hr>";
echo "<h3>🔗 Next Steps:</h3>";
echo "<p><a href='debug_masterlist.php' target='_blank'>🔍 Check Debug Info</a></p>";
echo "<p><a href='masterlist.php' target='_blank'>📋 Go to Masterlist</a></p>";
echo "<p><a href='cor-cog.php' target='_blank'>📤 Go to COR & COG Upload</a></p>";
?>
