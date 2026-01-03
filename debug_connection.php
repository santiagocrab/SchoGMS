<?php
/**
 * Debug MongoDB Connection
 */

echo "<h2>MongoDB Connection Debug</h2>";

// Check current directory
echo "<h3>Current Directory</h3>";
echo "Current directory: " . __DIR__ . "<br>";

// Check if mongodb_data directory exists
$dataDir = __DIR__ . '/mongodb_data/schogms';
echo "<h3>Data Directory Check</h3>";
echo "Data directory path: {$dataDir}<br>";

if (file_exists($dataDir)) {
    echo "✅ Data directory exists<br>";
} else {
    echo "❌ Data directory does not exist<br>";
}

if (is_dir($dataDir)) {
    echo "✅ Data directory is a directory<br>";
} else {
    echo "❌ Data directory is not a directory<br>";
}

if (is_writable($dataDir)) {
    echo "✅ Data directory is writable<br>";
} else {
    echo "❌ Data directory is not writable<br>";
}

// Check permissions
echo "<h3>Directory Permissions</h3>";
$perms = fileperms($dataDir);
echo "Permissions: " . decoct($perms & 0777) . "<br>";

// Try to create a test file
echo "<h3>Test File Creation</h3>";
$testFile = $dataDir . '/test.txt';
if (file_put_contents($testFile, 'test') !== false) {
    echo "✅ Can create files in data directory<br>";
    unlink($testFile); // Clean up
} else {
    echo "❌ Cannot create files in data directory<br>";
}

// Test the SimpleMongoDB class directly
echo "<h3>SimpleMongoDB Class Test</h3>";
try {
    require_once 'mongodb_simple.php';
    
    $mongodb = new SimpleMongoDB('schogms');
    echo "✅ SimpleMongoDB instance created<br>";
    
    $connectionTest = $mongodb->testConnection();
    echo "Connection test result: " . ($connectionTest ? 'true' : 'false') . "<br>";
    
    if ($connectionTest) {
        echo "✅ Connection test passed<br>";
    } else {
        echo "❌ Connection test failed<br>";
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "<br>";
}

echo "<h3>Debug Complete!</h3>";
?>
