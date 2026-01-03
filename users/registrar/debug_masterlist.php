<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo "<h1>❌ Not logged in</h1>";
    echo "<p><a href='../login.php'>Go to Login</a></p>";
    exit;
}

echo "<h1>🔍 Masterlist Debug Page</h1>";
echo "<p>✅ User logged in: " . $_SESSION['user_id'] . "</p>";

// Test MongoDB connection
try {
    require '../../conn_mongodb.php';
    echo "<p>✅ MongoDB connection successful</p>";
    
    // Test registrar collection
    $registrarCollection = $mongodb->collection('registrar_master_list');
    $totalCount = $registrarCollection->count();
    echo "<p>📊 Total records in registrar_master_list: <strong>" . $totalCount . "</strong></p>";
    
    if ($totalCount > 0) {
        // Get first 5 records
        $sampleRecords = $registrarCollection->find([], ['limit' => 5]);
        echo "<h3>📋 Sample Records (First 5):</h3>";
        echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr style='background-color: #f0f0f0;'>";
        echo "<th>ID</th><th>Last Name</th><th>First Name</th><th>Middle Name</th><th>Campus</th><th>Filename</th>";
        echo "</tr>";
        
        foreach ($sampleRecords as $record) {
            echo "<tr>";
            echo "<td>" . ($record['_id'] ?? 'N/A') . "</td>";
            echo "<td>" . ($record['last_name'] ?? 'N/A') . "</td>";
            echo "<td>" . ($record['first_name'] ?? 'N/A') . "</td>";
            echo "<td>" . ($record['middle_name'] ?? 'N/A') . "</td>";
            echo "<td>" . ($record['campus'] ?? 'N/A') . "</td>";
            echo "<td>" . ($record['filename'] ?? 'N/A') . "</td>";
            echo "</tr>";
        }
        echo "</table>";
        
        // Test pagination
        echo "<h3>🔢 Testing Pagination:</h3>";
        try {
            $result = $dbHelper->getRegistrarMasterlistPaginated([], 1, 10);
            echo "<p>✅ Pagination test successful</p>";
            echo "<p>📊 Page 1 results: " . count($result['data']) . " records</p>";
            echo "<p>📊 Total pages: " . $result['pages'] . "</p>";
            echo "<p>📊 Total records: " . $result['total'] . "</p>";
        } catch (Exception $e) {
            echo "<p>❌ Pagination error: " . $e->getMessage() . "</p>";
        }
        
    } else {
        echo "<h3>⚠️ No records found in registrar_master_list</h3>";
        echo "<p>This might be why the masterlist is empty.</p>";
    }
    
    // Test document collection
    $documentCollection = $mongodb->collection('document_uploads');
    $docCount = $documentCollection->count();
    echo "<p>📄 Total documents uploaded: <strong>" . $docCount . "</strong></p>";
    
} catch (Exception $e) {
    echo "<p>❌ MongoDB error: " . $e->getMessage() . "</p>";
}

echo "<hr>";
echo "<h3>🔗 Quick Links:</h3>";
echo "<p><a href='masterlist.php' target='_blank'>📋 Go to Full Masterlist</a></p>";
echo "<p><a href='test_masterlist.php' target='_blank'>🧪 Go to Test Masterlist</a></p>";
echo "<p><a href='cor-cog.php' target='_blank'>📤 Go to COR & COG Upload</a></p>";
echo "<p><a href='logout.php'>🚪 Logout</a></p>";

echo "<hr>";
echo "<h3>🔧 If Masterlist is Empty:</h3>";
echo "<p>1. Check if there are records in the database (see above)</p>";
echo "<p>2. If no records, you need to upload student data first</p>";
echo "<p>3. Use the 'Upload Student Data' button in the masterlist page</p>";
echo "<p>4. Or check if the database connection is working properly</p>";
?>
