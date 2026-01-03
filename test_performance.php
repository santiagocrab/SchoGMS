<?php
/**
 * Performance Test for Fast MongoDB
 */

require_once 'conn_mongodb.php';

echo "<h2>MongoDB Performance Test</h2>";

// Test 1: Count all records
echo "<h3>1. Record Counts</h3>";
$start = microtime(true);
$userCount = $dbHelper->countRecords('users');
$adminCount = $dbHelper->countRecords('admin');
$registrarCount = $dbHelper->countRecords('registrar_master_list');
$end = microtime(true);
$time = round(($end - $start) * 1000, 2);

echo "Users: {$userCount}<br>";
echo "Admins: {$adminCount}<br>";
echo "Registrar Masterlist: {$registrarCount}<br>";
echo "Time taken: {$time}ms<br><br>";

// Test 2: Paginated query
echo "<h3>2. Paginated Query Test</h3>";
$start = microtime(true);
$paginatedResults = $dbHelper->getRegistrarMasterlistPaginated([], 1, 20);
$end = microtime(true);
$time = round(($end - $start) * 1000, 2);

echo "Page 1 (20 records): {$time}ms<br>";
echo "Total records: {$paginatedResults['total']}<br>";
echo "Total pages: {$paginatedResults['pages']}<br>";
echo "Records returned: " . count($paginatedResults['data']) . "<br><br>";

// Test 3: Search test
echo "<h3>3. Search Test</h3>";
$start = microtime(true);
$searchResults = $dbHelper->searchRegistrarMasterlist('ISULAN', 1, 10);
$end = microtime(true);
$time = round(($end - $start) * 1000, 2);

echo "Search for 'ISULAN' (10 records): {$time}ms<br>";
echo "Total matches: {$searchResults['total']}<br>";
echo "Records returned: " . count($searchResults['data']) . "<br><br>";

// Test 4: Filter test
echo "<h3>4. Filter Test</h3>";
$start = microtime(true);
$filteredResults = $dbHelper->getRegistrarMasterlistPaginated(['campus' => 'ISULAN'], 1, 15);
$end = microtime(true);
$time = round(($end - $start) * 1000, 2);

echo "Filter by campus='ISULAN' (15 records): {$time}ms<br>";
echo "Total matches: {$filteredResults['total']}<br>";
echo "Records returned: " . count($filteredResults['data']) . "<br><br>";

// Test 5: Multiple page test
echo "<h3>5. Multiple Page Test</h3>";
$start = microtime(true);
for ($i = 1; $i <= 3; $i++) {
    $pageResults = $dbHelper->getRegistrarMasterlistPaginated([], $i, 25);
}
$end = microtime(true);
$time = round(($end - $start) * 1000, 2);

echo "3 pages of 25 records each: {$time}ms<br>";
echo "Average per page: " . round($time / 3, 2) . "ms<br><br>";

// Test 6: Cache test
echo "<h3>6. Cache Performance Test</h3>";
$start = microtime(true);
$cacheResults1 = $dbHelper->getRegistrarMasterlistPaginated([], 1, 30);
$end = microtime(true);
$time1 = round(($end - $start) * 1000, 2);

$start = microtime(true);
$cacheResults2 = $dbHelper->getRegistrarMasterlistPaginated([], 1, 30);
$end = microtime(true);
$time2 = round(($end - $start) * 1000, 2);

echo "First query: {$time1}ms<br>";
echo "Cached query: {$time2}ms<br>";
echo "Cache improvement: " . round(($time1 - $time2) / $time1 * 100, 1) . "%<br><br>";

echo "<h3>Performance Test Complete!</h3>";
echo "<p><strong>Key Improvements:</strong></p>";
echo "<ul>";
echo "<li>✅ Caching system reduces repeated queries</li>";
echo "<li>✅ Pagination prevents loading all records at once</li>";
echo "<li>✅ Optimized filtering and sorting</li>";
echo "<li>✅ Memory-efficient data handling</li>";
echo "</ul>";

echo "<p><a href='index.php'>Go to Main Application</a></p>";
?>
