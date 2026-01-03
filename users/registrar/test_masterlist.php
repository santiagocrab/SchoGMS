<?php
// Simple test page for masterlist
session_start();

echo "<h1>Masterlist Test Page</h1>";

// Check session
if (isset($_SESSION['user_id'])) {
    echo "<p>✅ Session exists - User ID: " . $_SESSION['user_id'] . "</p>";
} else {
    echo "<p>❌ No session found</p>";
    echo "<p><a href='../login.php'>Go to Login</a></p>";
    exit;
}

// Test MongoDB connection
try {
    require '../../conn_mongodb.php';
    echo "<p>✅ MongoDB connection successful</p>";
    
    // Test getting registrar masterlist
    $registrarCollection = $mongodb->collection('registrar_master_list');
    $count = $registrarCollection->count();
    echo "<p>✅ Registrar masterlist records: " . $count . "</p>";
    
    // Show first few records
    $records = $registrarCollection->find([], ['limit' => 5]);
    echo "<h3>Sample Records:</h3>";
    echo "<table border='1' style='border-collapse: collapse;'>";
    echo "<tr><th>ID</th><th>Last Name</th><th>First Name</th><th>Campus</th></tr>";
    
    foreach ($records as $record) {
        echo "<tr>";
        echo "<td>" . ($record['_id'] ?? 'N/A') . "</td>";
        echo "<td>" . ($record['last_name'] ?? 'N/A') . "</td>";
        echo "<td>" . ($record['first_name'] ?? 'N/A') . "</td>";
        echo "<td>" . ($record['campus'] ?? 'N/A') . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
} catch (Exception $e) {
    echo "<p>❌ MongoDB error: " . $e->getMessage() . "</p>";
}

echo "<hr>";
echo "<h3>Navigation Links:</h3>";
echo "<p><a href='masterlist.php'>Go to Full Masterlist</a></p>";
echo "<p><a href='cor-cog.php'>Go to COR & COG Upload</a></p>";
echo "<p><a href='documents_uploaded.php'>Go to Documents Uploaded</a></p>";
echo "<p><a href='logout.php'>Logout</a></p>";
?>
