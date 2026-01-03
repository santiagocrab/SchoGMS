<?php
/**
 * Test MongoDB Connection
 */

echo "<h2>MongoDB Connection Test</h2>";

// Test 1: Check if mongodb_simple.php exists
echo "<h3>1. Checking mongodb_simple.php</h3>";
if (file_exists('mongodb_simple.php')) {
    echo "✅ mongodb_simple.php exists<br>";
} else {
    echo "❌ mongodb_simple.php not found<br>";
}

// Test 2: Include mongodb_simple.php
echo "<h3>2. Including mongodb_simple.php</h3>";
try {
    require_once 'mongodb_simple.php';
    echo "✅ mongodb_simple.php included successfully<br>";
} catch (Exception $e) {
    echo "❌ Error including mongodb_simple.php: " . $e->getMessage() . "<br>";
}

// Test 3: Create MongoDB instance
echo "<h3>3. Creating MongoDB instance</h3>";
try {
    $mongodb = new SimpleMongoDB('schogms');
    echo "✅ MongoDB instance created successfully<br>";
} catch (Exception $e) {
    echo "❌ Error creating MongoDB instance: " . $e->getMessage() . "<br>";
}

// Test 4: Test connection
echo "<h3>4. Testing connection</h3>";
try {
    $connectionTest = $mongodb->testConnection();
    if ($connectionTest) {
        echo "✅ MongoDB connection test passed<br>";
    } else {
        echo "❌ MongoDB connection test failed<br>";
    }
} catch (Exception $e) {
    echo "❌ Error testing connection: " . $e->getMessage() . "<br>";
}

// Test 5: Check data directory
echo "<h3>5. Checking data directory</h3>";
$dataDir = __DIR__ . '/mongodb_data/schogms';
echo "Data directory: {$dataDir}<br>";
if (is_dir($dataDir)) {
    echo "✅ Data directory exists<br>";
    if (is_writable($dataDir)) {
        echo "✅ Data directory is writable<br>";
    } else {
        echo "❌ Data directory is not writable<br>";
    }
} else {
    echo "❌ Data directory does not exist<br>";
}

// Test 6: Try to access a collection
echo "<h3>6. Testing collection access</h3>";
try {
    $users = $mongodb->collection('users');
    $userCount = $users->count();
    echo "✅ Users collection accessible, count: {$userCount}<br>";
} catch (Exception $e) {
    echo "❌ Error accessing users collection: " . $e->getMessage() . "<br>";
}

echo "<h3>Test Complete!</h3>";
?>
