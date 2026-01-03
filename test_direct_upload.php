<?php
/**
 * Direct Upload Test
 * Test the upload script directly
 */

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>🎯 Direct Upload Test</h2>";

// Test 1: Check if we can access the upload script
echo "<h3>1. Upload Script Access Test</h3>";
$uploadScript = __DIR__ . '/users/registrar/submit_master_list.php';
echo "Upload script path: $uploadScript<br>";
echo "Script exists: " . (file_exists($uploadScript) ? "✅ Yes" : "❌ No") . "<br>";
echo "Script readable: " . (is_readable($uploadScript) ? "✅ Yes" : "❌ No") . "<br>";

// Test 2: Check MongoDB connection
echo "<h3>2. MongoDB Connection Test</h3>";
try {
    require_once 'conn_mongodb.php';
    $registrarCollection = $mongodb->collection('registrar_master_list');
    $count = $registrarCollection->count();
    echo "Current record count: <strong>$count</strong><br>";
} catch (Exception $e) {
    echo "❌ MongoDB error: " . $e->getMessage() . "<br>";
}

// Test 3: Create a test CSV file
echo "<h3>3. Create Test CSV File</h3>";
$testCsvContent = "Last Name,First Name,Middle Name,Ext. Name,ID Number,Gender,Student Type,Year Level,Attended,Course,Curriculum,Scholarship,GPA,CGPA,% Pass,Grade Remarks,Enrolled,Lec. Unit,Lab. Unit,COR Printed,Billing Profile,Misc. Fee Total,Misc. Fee Paid,Tuition Fee Total,Tuition Fee Paid,Street,Barangay,Municipality/City,Province,Zip Code\n";
$testCsvContent .= "ENGINEERING,JOHN,SMITH,,12345,Male,Regular,3rd year,1st semester; 2nd semester,Bachelor of Science in Engineering,2020,None,2.5,2.3,85,Pass,Yes,3,1,Yes,Profile A,5000,5000,15000,15000,123 Main St,Barangay 1,Isulan,Sultan Kudarat,9806\n";
$testCsvContent .= "ENGINEERING,MARY,JOHNSON,,12346,Female,Regular,2nd year,1st semester; 2nd semester,Bachelor of Science in Engineering,2020,None,2.8,2.6,90,Pass,Yes,3,1,Yes,Profile A,5000,5000,15000,15000,456 Oak St,Barangay 2,Isulan,Sultan Kudarat,9806\n";

$testCsvFile = __DIR__ . '/test_engineering_upload.csv';
if (file_put_contents($testCsvFile, $testCsvContent)) {
    echo "✅ Test CSV file created: test_engineering_upload.csv<br>";
    echo "File size: " . filesize($testCsvFile) . " bytes<br>";
} else {
    echo "❌ Failed to create test CSV file<br>";
}

// Test 4: Simulate the upload process
echo "<h3>4. Simulate Upload Process</h3>";
if (file_exists($testCsvFile)) {
    // Create a temporary file for upload simulation
    $tempFile = tempnam(sys_get_temp_dir(), 'upload_test');
    copy($testCsvFile, $tempFile);
    
    // Simulate $_FILES array
    $_FILES['excelFile'] = [
        'name' => 'test_engineering_upload.csv',
        'type' => 'text/csv',
        'tmp_name' => $tempFile,
        'error' => UPLOAD_ERR_OK,
        'size' => filesize($tempFile)
    ];
    
    // Simulate $_POST array
    $_POST['session_campus'] = 'ISULAN';
    $_POST['file_group'] = 'College of Engineering';
    $_POST['academic_year'] = '2022-2023';
    $_POST['semester'] = '1st Semester';
    
    echo "Simulated upload data:<br>";
    echo "- Campus: " . $_POST['session_campus'] . "<br>";
    echo "- File Group: " . $_POST['file_group'] . "<br>";
    echo "- Academic Year: " . $_POST['academic_year'] . "<br>";
    echo "- Semester: " . $_POST['semester'] . "<br>";
    echo "- File: " . $_FILES['excelFile']['name'] . "<br>";
    
    // Test 5: Try to process the file
    echo "<h3>5. Process Upload</h3>";
    try {
        // Include the upload script
        ob_start();
        include $uploadScript;
        $output = ob_get_clean();
        
        echo "Upload script output:<br>";
        echo "<pre>" . htmlspecialchars($output) . "</pre>";
        
        // Check if data was added
        $newCount = $registrarCollection->count();
        echo "New record count: <strong>$newCount</strong><br>";
        
        if ($newCount > $count) {
            echo "✅ Records were added!<br>";
        } else {
            echo "❌ No records were added<br>";
        }
        
    } catch (Exception $e) {
        echo "❌ Error processing upload: " . $e->getMessage() . "<br>";
    }
    
    // Clean up
    unlink($tempFile);
    unlink($testCsvFile);
    echo "✅ Test files cleaned up<br>";
}

echo "<br><a href='users/registrar/masterlist.php'>← Back to Masterlist</a>";
?>
