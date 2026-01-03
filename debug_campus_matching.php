<?php
// Debug script to check campus matching between registrar and coordinator
require 'conn_mongodb.php';

echo "<h2>🔍 Campus Matching Debug</h2>";

// Check coordinator session
echo "<h3>1. Coordinator Session Info</h3>";
session_start();
if (isset($_SESSION['user_id'])) {
    include 'users/coordinator/config/session.php';
    echo "<p><strong>Coordinator Campus (sheet_name):</strong> " . htmlspecialchars($sheet_name ?? 'NOT SET') . "</p>";
    echo "<p><strong>User ID:</strong> " . htmlspecialchars($_SESSION['user_id'] ?? 'NOT SET') . "</p>";
} else {
    echo "<p>Not logged in as coordinator</p>";
}

// Check registrar session
echo "<h3>2. Registrar Session Info</h3>";
if (file_exists('users/registrar/config/session.php')) {
    // Try to get registrar info
    echo "<p>Registrar session file exists</p>";
} else {
    echo "<p>Registrar session file not found</p>";
}

// Check document_uploads collection for campus values
echo "<h3>3. Campus Values in document_uploads Collection</h3>";
$documentCollection = $mongodb->collection('document_uploads');

// Get distinct campus values
$campuses = $documentCollection->distinct('campus');
echo "<p><strong>Distinct campus values found:</strong></p>";
echo "<ul>";
foreach ($campuses as $campus) {
    $count = $documentCollection->count(['campus' => $campus]);
    echo "<li><strong>" . htmlspecialchars($campus ?? 'NULL') . "</strong> - " . $count . " documents</li>";
}
echo "</ul>";

// Check sample COR documents
echo "<h3>4. Sample COR Documents</h3>";
$sampleDocs = $documentCollection->find(['category' => 'COR'], ['limit' => 5]);
echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
echo "<tr><th>Campus</th><th>Category</th><th>Original Name</th><th>File Name</th><th>File Path</th></tr>";
foreach ($sampleDocs as $doc) {
    echo "<tr>";
    echo "<td>" . htmlspecialchars($doc['campus'] ?? 'NULL') . "</td>";
    echo "<td>" . htmlspecialchars($doc['category'] ?? 'NULL') . "</td>";
    echo "<td>" . htmlspecialchars($doc['original_name'] ?? 'NULL') . "</td>";
    echo "<td>" . htmlspecialchars($doc['file_name'] ?? 'NULL') . "</td>";
    echo "<td>" . htmlspecialchars(substr($doc['file_path'] ?? 'NULL', 0, 50)) . "...</td>";
    echo "</tr>";
}
echo "</table>";

// Test matching for a specific student
echo "<h3>5. Test Matching Logic</h3>";
$testLastName = "ARGANOZA"; // Change this to a student you know has COR
$testFirstName = "JOHN"; // Change this to match

$testPattern = $testLastName . ', ' . $testFirstName;
echo "<p><strong>Testing pattern:</strong> " . htmlspecialchars($testPattern) . "</p>";

// Test with coordinator campus
$coordinatorCampus = $sheet_name ?? 'ISULAN';
echo "<p><strong>Coordinator Campus:</strong> " . htmlspecialchars($coordinatorCampus) . "</p>";

// Test query
$testQuery = [
    '$or' => [
        ['original_name' => ['$regex' => '^' . preg_quote($testPattern, '/'), '$options' => 'i']],
        ['file_name' => ['$regex' => preg_quote($testPattern, '/'), '$options' => 'i']]
    ],
    'category' => 'COR',
    'campus' => $coordinatorCampus
];

echo "<p><strong>Query with campus filter:</strong></p>";
echo "<pre>" . print_r($testQuery, true) . "</pre>";

$results = $documentCollection->find($testQuery, ['limit' => 5]);
$resultCount = 0;
foreach ($results as $result) {
    $resultCount++;
    echo "<p>✅ Found: " . htmlspecialchars($result['original_name'] ?? 'N/A') . " (Campus: " . htmlspecialchars($result['campus'] ?? 'N/A') . ")</p>";
}

if ($resultCount == 0) {
    echo "<p>❌ No documents found with campus filter</p>";
    
    // Try without campus filter
    $testQueryNoCampus = [
        '$or' => [
            ['original_name' => ['$regex' => '^' . preg_quote($testPattern, '/'), '$options' => 'i']],
            ['file_name' => ['$regex' => preg_quote($testPattern, '/'), '$options' => 'i']]
        ],
        'category' => 'COR'
    ];
    
    echo "<p><strong>Trying without campus filter:</strong></p>";
    $resultsNoCampus = $documentCollection->find($testQueryNoCampus, ['limit' => 5]);
    $resultCountNoCampus = 0;
    foreach ($resultsNoCampus as $result) {
        $resultCountNoCampus++;
        echo "<p>✅ Found: " . htmlspecialchars($result['original_name'] ?? 'N/A') . " (Campus: " . htmlspecialchars($result['campus'] ?? 'N/A') . ")</p>";
    }
    
    if ($resultCountNoCampus > 0) {
        echo "<p style='color: red;'><strong>⚠️ ISSUE FOUND:</strong> Documents exist but campus values don't match!</p>";
        echo "<p>Coordinator campus: <strong>" . htmlspecialchars($coordinatorCampus) . "</strong></p>";
        echo "<p>Document campus: <strong>" . htmlspecialchars($resultsNoCampus->current()['campus'] ?? 'N/A') . "</strong></p>";
    }
}

echo "<hr>";
echo "<p><a href='index.php'>← Back to Home</a></p>";
?>



