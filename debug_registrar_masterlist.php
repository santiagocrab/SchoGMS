<?php
/**
 * Debug version of Registrar Masterlist
 * This will help us troubleshoot the filter issue
 */

echo "<h2>Debug Registrar Masterlist</h2>";

try {
    require_once 'conn_mongodb.php';
    
    echo "<p>✅ MongoDB connection loaded</p>";
    
    // Get the selected category and file_group from the form (if any)
    $category = isset($_GET['category']) ? $_GET['category'] : '';
    $file_group = isset($_GET['file_group']) ? $_GET['file_group'] : '';
    
    echo "<p>🔍 Current filters:</p>";
    echo "<ul>";
    echo "<li>Category: '" . htmlspecialchars($category) . "'</li>";
    echo "<li>File Group: '" . htmlspecialchars($file_group) . "'</li>";
    echo "</ul>";
    
    // Test collection access
    $registrarCollection = $mongodb->collection('registrar_master_list');
    echo "<p>✅ Collection accessed</p>";
    
    // Get total count
    $totalCount = $registrarCollection->count([]);
    echo "<p>📊 Total records: <strong>{$totalCount}</strong></p>";
    
    // Test getting categories and file groups
    echo "<h3>Available Categories and File Groups</h3>";
    $sampleRecords = $registrarCollection->find([], ['limit' => 100]);
    
    $categories = [];
    $fileGroups = [];
    foreach ($sampleRecords as $record) {
        if (!empty($record['filename'])) {
            $categories[$record['filename']] = $record['filename'];
        }
        if (!empty($record['file_group'])) {
            $fileGroups[$record['file_group']] = $record['file_group'];
        }
    }
    
    echo "<h4>Categories found:</h4>";
    echo "<ul>";
    foreach ($categories as $cat) {
        echo "<li>" . htmlspecialchars($cat) . "</li>";
    }
    echo "</ul>";
    
    echo "<h4>File Groups found:</h4>";
    echo "<ul>";
    foreach ($fileGroups as $group) {
        echo "<li>" . htmlspecialchars($group) . "</li>";
    }
    echo "</ul>";
    
    // Test filter
    echo "<h3>Filter Test</h3>";
    $filter = [];
    if ($category !== '') {
        $filter['filename'] = $category;
    }
    if ($file_group !== '') {
        $filter['file_group'] = $file_group;
    }
    
    echo "<p>🔍 Filter being applied: " . json_encode($filter) . "</p>";
    
    if (!empty($filter)) {
        $filteredCount = $registrarCollection->count($filter);
        echo "<p>📊 Filtered records count: <strong>{$filteredCount}</strong></p>";
        
        // Get some filtered records
        $filteredRecords = $registrarCollection->find($filter, ['limit' => 5]);
        echo "<h4>Sample filtered records:</h4>";
        echo "<table border='1' style='border-collapse: collapse;'>";
        echo "<tr><th>ID</th><th>Last Name</th><th>First Name</th><th>Filename</th><th>File Group</th></tr>";
        
        foreach ($filteredRecords as $record) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($record['id'] ?? 'N/A') . "</td>";
            echo "<td>" . htmlspecialchars($record['last_name'] ?? 'N/A') . "</td>";
            echo "<td>" . htmlspecialchars($record['first_name'] ?? 'N/A') . "</td>";
            echo "<td>" . htmlspecialchars($record['filename'] ?? 'N/A') . "</td>";
            echo "<td>" . htmlspecialchars($record['file_group'] ?? 'N/A') . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p>ℹ️ No filters applied - showing all records</p>";
    }
    
    // Test pagination
    echo "<h3>Pagination Test</h3>";
    if (isset($dbHelper)) {
        $pageResult = $dbHelper->getRegistrarMasterlistPaginated($filter, 1, 10);
        echo "<p>📄 Pagination test - Records returned: <strong>" . count($pageResult['data']) . "</strong></p>";
        echo "<p>📄 Total records: <strong>{$pageResult['total']}</strong></p>";
        echo "<p>📄 Total pages: <strong>{$pageResult['pages']}</strong></p>";
    } else {
        echo "<p>❌ dbHelper not available</p>";
    }
    
    // Test form
    echo "<h3>Filter Form Test</h3>";
    echo "<form method='GET' action=''>";
    echo "<p>Category: <select name='category'>";
    echo "<option value=''>All</option>";
    foreach ($categories as $cat) {
        $selected = ($category == $cat) ? 'selected' : '';
        echo "<option value='" . htmlspecialchars($cat) . "' {$selected}>" . htmlspecialchars($cat) . "</option>";
    }
    echo "</select></p>";
    
    echo "<p>File Group: <select name='file_group'>";
    echo "<option value=''>All</option>";
    foreach ($fileGroups as $group) {
        $selected = ($file_group == $group) ? 'selected' : '';
        echo "<option value='" . htmlspecialchars($group) . "' {$selected}>" . htmlspecialchars($group) . "</option>";
    }
    echo "</select></p>";
    
    echo "<p><input type='submit' value='Apply Filter'></p>";
    echo "</form>";
    
    echo "<h3>✅ Debug completed!</h3>";
    echo "<p><a href='users/registrar/masterlist.php'>Go to actual Registrar Masterlist</a></p>";
    
} catch (Exception $e) {
    echo "<p>❌ Error: " . $e->getMessage() . "</p>";
    echo "<p>Stack trace: " . $e->getTraceAsString() . "</p>";
}
?>
