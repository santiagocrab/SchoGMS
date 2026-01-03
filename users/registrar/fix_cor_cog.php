<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo "<h1>❌ Not logged in</h1>";
    echo "<p><a href='../login.php'>Go to Login</a></p>";
    exit;
}

echo "<h1>🔧 Fix COR/COG Matching</h1>";

try {
    require '../../conn_mongodb.php';
    
    echo "<h3>1. Checking Document Uploads Collection Structure</h3>";
    $documentCollection = $mongodb->collection('document_uploads');
    
    // Get a sample document to see the actual structure
    $sampleDoc = $documentCollection->findOne([]);
    if ($sampleDoc) {
        echo "<h4>Sample Document Structure:</h4>";
        echo "<pre>" . print_r($sampleDoc, true) . "</pre>";
    }
    
    echo "<h3>2. Checking for COR Documents with Different Field Names</h3>";
    
    // Try different possible field names for category
    $possibleCategoryFields = ['category', 'type', 'document_type', 'doc_type', 'file_type'];
    $possibleNameFields = ['original_name', 'filename', 'file_name', 'name', 'student_name'];
    
    foreach ($possibleCategoryFields as $field) {
        $corCount = $documentCollection->count([$field => 'COR']);
        $cogCount = $documentCollection->count([$field => 'COG']);
        echo "<p><strong>{$field}:</strong> COR: {$corCount}, COG: {$cogCount}</p>";
    }
    
    echo "<h3>3. Looking for Documents with Actual Data</h3>";
    
    // Find documents that have non-empty values
    $documentsWithData = $documentCollection->find([
        '$or' => [
            ['original_name' => ['$ne' => null, '$ne' => '']],
            ['filename' => ['$ne' => null, '$ne' => '']],
            ['file_name' => ['$ne' => null, '$ne' => '']],
            ['category' => ['$ne' => null, '$ne' => '']]
        ]
    ], ['limit' => 10]);
    
    echo "<h4>Documents with Actual Data:</h4>";
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr style='background-color: #f0f0f0;'>";
    echo "<th>ID</th><th>Category</th><th>Original Name</th><th>File Name</th><th>Filename</th><th>All Fields</th>";
    echo "</tr>";
    
    foreach ($documentsWithData as $doc) {
        echo "<tr>";
        echo "<td>" . ($doc['_id'] ?? 'N/A') . "</td>";
        echo "<td>" . ($doc['category'] ?? 'N/A') . "</td>";
        echo "<td>" . ($doc['original_name'] ?? 'N/A') . "</td>";
        echo "<td>" . ($doc['file_name'] ?? 'N/A') . "</td>";
        echo "<td>" . ($doc['filename'] ?? 'N/A') . "</td>";
        echo "<td><small>" . implode(', ', array_keys($doc)) . "</small></td>";
        echo "</tr>";
    }
    echo "</table>";
    
    echo "<h3>4. Alternative: Check File System for COR/COG Files</h3>";
    
    // Check if there are actual files in the uploads directory
    $uploadDirs = [
        'uploads/',
        'uploads/documents/',
        'uploads/COR/',
        'uploads/COG/'
    ];
    
    foreach ($uploadDirs as $dir) {
        if (is_dir($dir)) {
            $files = scandir($dir);
            $fileCount = count($files) - 2; // Subtract . and ..
            echo "<p><strong>{$dir}:</strong> {$fileCount} files</p>";
            
            if ($fileCount > 0 && $fileCount < 20) {
                echo "<ul>";
                foreach ($files as $file) {
                    if ($file != '.' && $file != '..') {
                        echo "<li>{$file}</li>";
                    }
                }
                echo "</ul>";
            }
        } else {
            echo "<p><strong>{$dir}:</strong> Directory not found</p>";
        }
    }
    
    echo "<h3>5. Creating Simple COR/COG Status Based on File System</h3>";
    
    // Create a simple function to check for COR/COG files
    function checkCORCOGFiles($studentName) {
        $uploadDirs = ['uploads/COR/', 'uploads/COG/', 'uploads/documents/'];
        $hasCOR = false;
        $hasCOG = false;
        
        foreach ($uploadDirs as $dir) {
            if (is_dir($dir)) {
                $files = scandir($dir);
                foreach ($files as $file) {
                    if ($file != '.' && $file != '..') {
                        // Check if filename contains student name
                        if (stripos($file, $studentName) !== false) {
                            if (strpos($dir, 'COR') !== false) {
                                $hasCOR = true;
                            } elseif (strpos($dir, 'COG') !== false) {
                                $hasCOG = true;
                            }
                        }
                    }
                }
            }
        }
        
        return ['COR' => $hasCOR, 'COG' => $hasCOG];
    }
    
    // Test with sample students
    $registrarCollection = $mongodb->collection('registrar_master_list');
    $sampleStudents = $registrarCollection->find([], ['limit' => 5]);
    
    echo "<h4>Testing File System Check:</h4>";
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr style='background-color: #f0f0f0;'>";
    echo "<th>Student Name</th><th>COR Status</th><th>COG Status</th>";
    echo "</tr>";
    
    foreach ($sampleStudents as $student) {
        $studentName = trim($student['last_name'] . ', ' . $student['first_name'] . ' ' . ($student['middle_name'] ?? ''));
        $status = checkCORCOGFiles($studentName);
        
        echo "<tr>";
        echo "<td>{$studentName}</td>";
        echo "<td>" . ($status['COR'] ? '✅ Yes' : '❌ No') . "</td>";
        echo "<td>" . ($status['COG'] ? '✅ Yes' : '❌ No') . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
} catch (Exception $e) {
    echo "<p>❌ Error: " . $e->getMessage() . "</p>";
}

echo "<hr>";
echo "<h3>🔗 Quick Links:</h3>";
echo "<p><a href='final_masterlist.php' target='_blank'>📋 Go to Final Masterlist</a></p>";
echo "<p><a href='cor-cog.php' target='_blank'>📤 Go to COR & COG Upload</a></p>";
echo "<p><a href='debug_cor_cog.php' target='_blank'>🔍 Go to COR/COG Debug</a></p>";
?>
