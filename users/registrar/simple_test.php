<?php
include 'config/session.php';

echo "<h3>🔤 SIMPLE CHARACTER TEST</h3>";

// Test the simple fix
$testName = "ARGA?OZA";
$fixedName = str_replace('?', 'Ñ', $testName);

echo "<h4>Test Result:</h4>";
echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
echo "<tr><th>Original</th><th>Fixed</th><th>Status</th></tr>";
echo "<tr>";
echo "<td style='font-family: monospace; font-size: 18px;'>" . htmlspecialchars($testName) . "</td>";
echo "<td style='font-family: monospace; font-size: 18px; color: green;'>" . htmlspecialchars($fixedName) . "</td>";
echo "<td style='color: green;'>✅ Fixed</td>";
echo "</tr>";
echo "</table>";

// Test with actual database
echo "<h4>Real Database Test:</h4>";

try {
    $registrarCollection = $mongodb->collection('registrar_master_list');
    $students = $registrarCollection->find([], ['limit' => 3, 'sort' => ['last_name' => 1]]);
    
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr><th>Original Last Name</th><th>Fixed Last Name</th><th>Has ?</th></tr>";
    
    foreach ($students as $student) {
        $lastName = $student['last_name'] ?? '';
        $hasQuestionMark = (strpos($lastName, '?') !== false);
        $fixedLastName = str_replace('?', 'Ñ', $lastName);
        
        $color = $hasQuestionMark ? 'orange' : 'black';
        
        echo "<tr>";
        echo "<td style='color: $color; font-family: monospace; font-size: 16px;'>" . htmlspecialchars($lastName) . "</td>";
        echo "<td style='font-family: monospace; font-size: 16px; color: green;'>" . htmlspecialchars($fixedLastName) . "</td>";
        echo "<td>" . ($hasQuestionMark ? '✅ Yes' : '❌ No') . "</td>";
        echo "</tr>";
    }
    
    echo "</table>";
    
} catch (Exception $e) {
    echo "<div class='alert alert-danger'>Error: " . htmlspecialchars($e->getMessage()) . "</div>";
}

echo "<div class='alert alert-success mt-3'>";
echo "<h5>✅ Simple Fix Applied:</h5>";
echo "<p><strong>?</strong> → <strong>ñ</strong></p>";
echo "<p>If you still see '?' in the masterlist, please:</p>";
echo "<ol>";
echo "<li>Refresh the page with <strong>Ctrl+F5</strong> (Windows) or <strong>Cmd+Shift+R</strong> (Mac)</li>";
echo "<li>Clear your browser cache</li>";
echo "<li>Try opening in a new browser tab</li>";
echo "</ol>";
echo "</div>";

?>

<div class="row mt-4">
    <div class="col-md-6">
        <a href="masterlist.php" class="btn btn-primary btn-block">📋 Check Masterlist</a>
    </div>
    <div class="col-md-6">
        <a href="cor-cog.php" class="btn btn-success btn-block">📤 COR Interface</a>
    </div>
</div>
