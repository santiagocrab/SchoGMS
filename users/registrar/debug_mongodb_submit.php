<?php
echo "<h2>MongoDB Submit Debug</h2>";
echo "<p>Request Method: " . $_SERVER['REQUEST_METHOD'] . "</p>";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    echo "<h3>POST Data Received:</h3>";
    echo "<pre>" . print_r($_POST, true) . "</pre>";
    echo "<h3>FILES Data Received:</h3>";
    echo "<pre>" . print_r($_FILES, true) . "</pre>";
    
    // Check specific fields
    $campus = $_POST['session_campus'] ?? 'ISULAN';
    $category = $_POST['category'] ?? 'COR';
    $academic_year = $_POST['academic_year'] ?? '';
    $semester = $_POST['semester'] ?? '';
    
    echo "<h3>Field Values:</h3>";
    echo "Campus: '$campus'<br>";
    echo "Category: '$category'<br>";
    echo "Academic Year: '$academic_year'<br>";
    echo "Semester: '$semester'<br>";
    
    // Check if files were uploaded
    $fileCount = 0;
    if (isset($_FILES['fileUpload']) && is_array($_FILES['fileUpload']['name'])) {
        $fileCount = count($_FILES['fileUpload']['name']);
    }
    echo "Files uploaded: $fileCount<br>";
    
    if (empty($academic_year)) {
        echo "<p style='color: red;'>ERROR: Academic Year is empty!</p>";
    } else {
        echo "<p style='color: green;'>SUCCESS: Academic Year has value!</p>";
    }
    
    if (empty($semester)) {
        echo "<p style='color: red;'>ERROR: Semester is empty!</p>";
    } else {
        echo "<p style='color: green;'>SUCCESS: Semester has value!</p>";
    }
    
    if ($fileCount === 0) {
        echo "<p style='color: red;'>ERROR: No files uploaded!</p>";
    } else {
        echo "<p style='color: green;'>SUCCESS: Files uploaded!</p>";
    }
    
    // If all fields are valid, show success message
    if (!empty($academic_year) && !empty($semester) && $fileCount > 0) {
        echo "<div style='background: #d4edda; border: 1px solid #c3e6cb; color: #155724; padding: 15px; margin: 10px 0; border-radius: 5px;'>";
        echo "<h4>✅ Form Submission Successful!</h4>";
        echo "<p>All required fields are filled and files are uploaded.</p>";
        echo "<p>Now testing MongoDB connection...</p>";
        
        // Test MongoDB connection
        try {
            include 'config/session.php';
            $documentCollection = $mongodb->collection('document_uploads');
            echo "<p style='color: green;'>✅ MongoDB connection successful!</p>";
            echo "<p>Collection: document_uploads</p>";
            echo "<p>Database: schogms</p>";
        } catch (Exception $e) {
            echo "<p style='color: red;'>❌ MongoDB connection failed: " . $e->getMessage() . "</p>";
        }
        
        echo "<p><a href='test_mongodb_upload.php' style='color: #155724;'>← Back to Test Form</a></p>";
        echo "</div>";
    }
} else {
    echo "<p>No POST data received.</p>";
    echo "<p><a href='test_mongodb_upload.php'>← Back to Test Form</a></p>";
}
?>
