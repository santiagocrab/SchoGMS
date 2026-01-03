<?php
echo "<h3>🔍 MASTERLIST LOADING TEST</h3>";

// Test 1: Check if session file exists
echo "<h4>1. Session File Check:</h4>";
if (file_exists('config/session.php')) {
    echo "✅ Session file exists<br>";
} else {
    echo "❌ Session file missing<br>";
}

// Test 2: Check if MongoDB connection works
echo "<h4>2. MongoDB Connection Test:</h4>";
try {
    require '../../conn_mongodb.php';
    echo "✅ MongoDB connection successful<br>";
} catch (Exception $e) {
    echo "❌ MongoDB connection failed: " . $e->getMessage() . "<br>";
}

// Test 3: Check if registrar collection exists
echo "<h4>3. Registrar Collection Test:</h4>";
try {
    $registrarCollection = $mongodb->collection('registrar_master_list');
    $count = $registrarCollection->count();
    echo "✅ Registrar collection accessible - Records: $count<br>";
} catch (Exception $e) {
    echo "❌ Registrar collection error: " . $e->getMessage() . "<br>";
}

// Test 4: Check if directories exist
echo "<h4>4. Directory Check:</h4>";
$dirs = [
    'uploads/COR/',
    'uploads/documents/ISULAN/2024-2025/1st Semester/COR/',
    'uploads/documents/ISULAN/2024-2025/2nd Semester/COR/'
];

foreach ($dirs as $dir) {
    if (is_dir($dir)) {
        echo "✅ Directory exists: $dir<br>";
    } else {
        echo "❌ Directory missing: $dir<br>";
    }
}

// Test 5: Check PHP errors
echo "<h4>5. PHP Error Check:</h4>";
$error_reporting = error_reporting();
echo "Error reporting level: $error_reporting<br>";

if (function_exists('error_get_last')) {
    $last_error = error_get_last();
    if ($last_error) {
        echo "❌ Last PHP error: " . $last_error['message'] . "<br>";
    } else {
        echo "✅ No PHP errors detected<br>";
    }
}

// Test 6: Try to include session and see what happens
echo "<h4>6. Session Include Test:</h4>";
try {
    ob_start();
    include 'config/session.php';
    $output = ob_get_clean();
    if (empty($output)) {
        echo "✅ Session included successfully<br>";
    } else {
        echo "⚠️ Session output: " . htmlspecialchars($output) . "<br>";
    }
} catch (Exception $e) {
    echo "❌ Session include failed: " . $e->getMessage() . "<br>";
}

echo "<h4>7. Quick Masterlist Test:</h4>";
try {
    // Try to get some data like the masterlist does
    $registrarCollection = $mongodb->collection('registrar_master_list');
    $result = $registrarCollection->findPaginated([], [
        'page' => 1,
        'limit' => 5,
        'sort' => ['last_name' => 1, 'first_name' => 1]
    ]);
    
    $registrarData = $result['data'] ?? [];
    $totalRecords = $result['total'] ?? 0;
    
    echo "✅ Data retrieval successful<br>";
    echo "Records found: " . count($registrarData) . "<br>";
    echo "Total records: $totalRecords<br>";
    
    if (count($registrarData) > 0) {
        echo "✅ Sample record: " . htmlspecialchars($registrarData[0]['last_name'] ?? 'Unknown') . "<br>";
    }
    
} catch (Exception $e) {
    echo "❌ Data retrieval failed: " . $e->getMessage() . "<br>";
}

?>

<p><a href="masterlist.php" class="btn btn-primary">Try Masterlist Again</a></p>
<p><a href="index.php" class="btn btn-secondary">Back to Dashboard</a></p>












