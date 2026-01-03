<?php
/**
 * Test Documents Page
 * Simple test to check what's causing the loading issue
 */

echo "<h2>🔍 Testing Documents Page</h2>";

echo "<h3>1. Testing MongoDB Connection</h3>";
try {
    require_once 'conn_mongodb.php';
    echo "✅ MongoDB connection loaded successfully<br>";
    
    $registrarCollection = $mongodb->collection('registrar_master_list');
    $count = $registrarCollection->count();
    echo "✅ Registrar collection accessible - Count: $count<br>";
    
    $documentCollection = $mongodb->collection('document_uploads');
    $docCount = $documentCollection->count();
    echo "✅ Document collection accessible - Count: $docCount<br>";
    
} catch (Exception $e) {
    echo "❌ MongoDB error: " . $e->getMessage() . "<br>";
}

echo "<h3>2. Testing Session</h3>";
try {
    require_once 'users/registrar/config/session.php';
    echo "✅ Session loaded successfully<br>";
    echo "Session campus: " . ($sheet_name ?? 'Not set') . "<br>";
} catch (Exception $e) {
    echo "❌ Session error: " . $e->getMessage() . "<br>";
}

echo "<h3>3. Testing File Includes</h3>";
$files_to_test = [
    'users/registrar/documents_uploaded.php'
];

foreach ($files_to_test as $file) {
    if (file_exists($file)) {
        echo "✅ File exists: $file<br>";
        
        // Check for syntax errors
        $output = shell_exec("php -l $file 2>&1");
        if (strpos($output, 'No syntax errors') !== false) {
            echo "✅ Syntax OK: $file<br>";
        } else {
            echo "❌ Syntax error in $file: $output<br>";
        }
    } else {
        echo "❌ File not found: $file<br>";
    }
}

echo "<h3>4. Testing Simple Page Load</h3>";
echo "If you can see this, PHP is working properly.<br>";

echo "<br><a href='users/registrar/documents_uploaded.php'>← Try Documents Page</a>";
?>
