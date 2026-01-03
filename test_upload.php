<?php
/**
 * Test Upload Functionality
 */

echo "<h2>Upload Test</h2>";

// Test if the upload script exists and is accessible
$uploadScript = 'users/registrar/submit_master_list.php';

if (file_exists($uploadScript)) {
    echo "<p>✅ Upload script exists: {$uploadScript}</p>";
    
    // Check if it's readable
    if (is_readable($uploadScript)) {
        echo "<p>✅ Upload script is readable</p>";
    } else {
        echo "<p>❌ Upload script is not readable</p>";
    }
} else {
    echo "<p>❌ Upload script not found: {$uploadScript}</p>";
}

// Test MongoDB connection
try {
    require_once 'conn_mongodb.php';
    echo "<p>✅ MongoDB connection loaded</p>";
    
    $registrarCollection = $mongodb->collection('registrar_master_list');
    $count = $registrarCollection->count([]);
    echo "<p>📊 Current records in registrar_master_list: <strong>{$count}</strong></p>";
    
} catch (Exception $e) {
    echo "<p>❌ MongoDB connection error: " . $e->getMessage() . "</p>";
}

// Test uploads directory
$uploadsDir = 'users/registrar/uploads/';
if (is_dir($uploadsDir)) {
    echo "<p>✅ Uploads directory exists: {$uploadsDir}</p>";
    if (is_writable($uploadsDir)) {
        echo "<p>✅ Uploads directory is writable</p>";
    } else {
        echo "<p>❌ Uploads directory is not writable</p>";
    }
} else {
    echo "<p>❌ Uploads directory does not exist: {$uploadsDir}</p>";
    echo "<p>Creating uploads directory...</p>";
    if (mkdir($uploadsDir, 0755, true)) {
        echo "<p>✅ Uploads directory created successfully</p>";
    } else {
        echo "<p>❌ Failed to create uploads directory</p>";
    }
}

// Test PhpSpreadsheet
try {
    require_once 'users/vendor/autoload.php';
    echo "<p>✅ PhpSpreadsheet library loaded</p>";
} catch (Exception $e) {
    echo "<p>❌ PhpSpreadsheet library error: " . $e->getMessage() . "</p>";
}

echo "<h3>Test Complete!</h3>";
echo "<p><a href='users/registrar/masterlist.php'>Go to Registrar Masterlist</a></p>";
?>
