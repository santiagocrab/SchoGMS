<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo "<h1>❌ Not logged in</h1>";
    echo "<p><a href='../login.php'>Go to Login</a></p>";
    exit;
}

echo "<h1>🧪 Test Data Display</h1>";

try {
    require '../../conn_mongodb.php';
    
    echo "<h3>1. Testing Direct MongoDB Query</h3>";
    $registrarCollection = $mongodb->collection('registrar_master_list');
    
    // Test 1: Direct find
    $directRecords = $registrarCollection->find([], ['limit' => 5]);
    $directCount = 0;
    echo "<h4>Direct MongoDB Query (First 5 records):</h4>";
    echo "<table border='1' style='border-collapse: collapse;'>";
    echo "<tr><th>ID</th><th>Last Name</th><th>First Name</th><th>Middle Name</th></tr>";
    
    foreach ($directRecords as $record) {
        $directCount++;
        echo "<tr>";
        echo "<td>" . ($record['_id'] ?? 'N/A') . "</td>";
        echo "<td>" . ($record['last_name'] ?? 'N/A') . "</td>";
        echo "<td>" . ($record['first_name'] ?? 'N/A') . "</td>";
        echo "<td>" . ($record['middle_name'] ?? 'N/A') . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    echo "<p>✅ Direct query returned: {$directCount} records</p>";
    
    echo "<h3>2. Testing dbHelper Function</h3>";
    
    // Test 2: Using dbHelper
    try {
        $result = $dbHelper->getRegistrarMasterlistPaginated([], 1, 10);
        echo "<p>✅ dbHelper function worked</p>";
        echo "<p>📊 Total records: " . $result['total'] . "</p>";
        echo "<p>📊 Data count: " . count($result['data']) . "</p>";
        echo "<p>📊 Total pages: " . $result['pages'] . "</p>";
        
        if (!empty($result['data'])) {
            echo "<h4>First record from dbHelper:</h4>";
            $firstRecord = $result['data'][0];
            echo "<p>ID: " . ($firstRecord['_id'] ?? 'N/A') . "</p>";
            echo "<p>Name: " . ($firstRecord['last_name'] ?? 'N/A') . ", " . ($firstRecord['first_name'] ?? 'N/A') . "</p>";
        }
        
    } catch (Exception $e) {
        echo "<p>❌ dbHelper error: " . $e->getMessage() . "</p>";
    }
    
    echo "<h3>3. Testing with Filter</h3>";
    
    // Test 3: With a simple filter
    try {
        $filter = ['campus' => 'ISULAN'];
        $result = $dbHelper->getRegistrarMasterlistPaginated($filter, 1, 5);
        echo "<p>✅ Filtered query worked</p>";
        echo "<p>📊 Filtered records: " . $result['total'] . "</p>";
        echo "<p>📊 Data count: " . count($result['data']) . "</p>";
        
    } catch (Exception $e) {
        echo "<p>❌ Filtered query error: " . $e->getMessage() . "</p>";
    }
    
} catch (Exception $e) {
    echo "<p>❌ General error: " . $e->getMessage() . "</p>";
}

echo "<hr>";
echo "<h3>🔗 Quick Links:</h3>";
echo "<p><a href='masterlist.php' target='_blank'>📋 Go to Masterlist</a></p>";
echo "<p><a href='simple_masterlist.php' target='_blank'>📋 Go to Simple Masterlist</a></p>";
echo "<p><a href='debug_masterlist.php' target='_blank'>🔍 Go to Debug Masterlist</a></p>";
?>
