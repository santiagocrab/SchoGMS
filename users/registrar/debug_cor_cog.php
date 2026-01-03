<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo "<h1>❌ Not logged in</h1>";
    echo "<p><a href='../login.php'>Go to Login</a></p>";
    exit;
}

echo "<h1>🔍 COR/COG Debug Page</h1>";

try {
    require '../../conn_mongodb.php';
    
    echo "<h3>1. Checking Document Uploads Collection</h3>";
    $documentCollection = $mongodb->collection('document_uploads');
    $totalDocs = $documentCollection->count();
    echo "<p>📄 Total documents uploaded: <strong>{$totalDocs}</strong></p>";
    
    if ($totalDocs > 0) {
        echo "<h4>Sample Documents (First 10):</h4>";
        $sampleDocs = $documentCollection->find([], ['limit' => 10]);
        echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr style='background-color: #f0f0f0;'>";
        echo "<th>Category</th><th>Original Name</th><th>File Name</th><th>Academic Year</th><th>Semester</th>";
        echo "</tr>";
        
        foreach ($sampleDocs as $doc) {
            echo "<tr>";
            echo "<td>" . ($doc['category'] ?? 'N/A') . "</td>";
            echo "<td>" . ($doc['original_name'] ?? 'N/A') . "</td>";
            echo "<td>" . ($doc['file_name'] ?? 'N/A') . "</td>";
            echo "<td>" . ($doc['academic_year'] ?? 'N/A') . "</td>";
            echo "<td>" . ($doc['semester'] ?? 'N/A') . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
    
    echo "<h3>2. Checking Student Names from Masterlist</h3>";
    $registrarCollection = $mongodb->collection('registrar_master_list');
    $sampleStudents = $registrarCollection->find([], ['limit' => 5]);
    
    echo "<h4>Sample Student Names:</h4>";
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr style='background-color: #f0f0f0;'>";
    echo "<th>Last Name</th><th>First Name</th><th>Middle Name</th><th>Full Name (as searched)</th>";
    echo "</tr>";
    
    foreach ($sampleStudents as $student) {
        $fullName = trim($student['last_name'] . ', ' . $student['first_name'] . ' ' . ($student['middle_name'] ?? ''));
        echo "<tr>";
        echo "<td>" . ($student['last_name'] ?? 'N/A') . "</td>";
        echo "<td>" . ($student['first_name'] ?? 'N/A') . "</td>";
        echo "<td>" . ($student['middle_name'] ?? 'N/A') . "</td>";
        echo "<td><strong>" . $fullName . "</strong></td>";
        echo "</tr>";
    }
    echo "</table>";
    
    echo "<h3>3. Testing Document Matching</h3>";
    foreach ($sampleStudents as $student) {
        $studentName = trim($student['last_name'] . ', ' . $student['first_name'] . ' ' . ($student['middle_name'] ?? ''));
        echo "<h4>Testing: {$studentName}</h4>";
        
        // Test different search patterns
        $patterns = [
            'exact' => preg_quote($studentName),
            'lastname_only' => preg_quote($student['last_name']),
            'firstname_only' => preg_quote($student['first_name']),
            'partial' => preg_quote(substr($student['last_name'], 0, 5))
        ];
        
        foreach ($patterns as $patternName => $pattern) {
            $documents = $documentCollection->find(['file_name' => ['$regex' => $pattern, '$options' => 'i']]);
            $count = 0;
            $hasCOR = false;
            $hasCOG = false;
            
            foreach ($documents as $doc) {
                $count++;
                if (isset($doc['category'])) {
                    if (strpos($doc['category'], 'COR') !== false) $hasCOR = true;
                    if (strpos($doc['category'], 'COG') !== false) $hasCOG = true;
                }
            }
            
            echo "<p><strong>{$patternName}:</strong> Found {$count} documents";
            if ($hasCOR) echo " ✅ COR";
            if ($hasCOG) echo " ✅ COG";
            if (!$hasCOR && !$hasCOG && $count > 0) echo " ❌ No COR/COG";
            echo "</p>";
        }
        echo "<hr>";
    }
    
    echo "<h3>4. COR/COG Statistics</h3>";
    $corDocs = $documentCollection->count(['category' => 'COR']);
    $cogDocs = $documentCollection->count(['category' => 'COG']);
    $otherDocs = $documentCollection->count(['category' => ['$nin' => ['COR', 'COG']]]);
    
    echo "<p>📊 COR documents: <strong>{$corDocs}</strong></p>";
    echo "<p>📊 COG documents: <strong>{$cogDocs}</strong></p>";
    echo "<p>📊 Other documents: <strong>{$otherDocs}</strong></p>";
    
} catch (Exception $e) {
    echo "<p>❌ Error: " . $e->getMessage() . "</p>";
}

echo "<hr>";
echo "<h3>🔗 Quick Links:</h3>";
echo "<p><a href='final_masterlist.php' target='_blank'>📋 Go to Final Masterlist</a></p>";
echo "<p><a href='cor-cog.php' target='_blank'>📤 Go to COR & COG Upload</a></p>";
echo "<p><a href='documents_uploaded.php' target='_blank'>📄 Go to Documents Uploaded</a></p>";
?>
