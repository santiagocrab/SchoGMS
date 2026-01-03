<?php
include 'config/session.php';

echo "<h3>🔧 FIX SEARCH ISSUE</h3>";

echo "<h4>🔍 Diagnosing Search Problem:</h4>";

// Check if the search is working properly
$testName = "ARGAÑOZA, ANGELINE SACAY";
$testVariations = [
    "ARGAÑOZA, ANGELINE SACAY",
    "ARGA?OZA, ANGELINE SACAY",
    "ARGAÑOZA",
    "ARGA?OZA",
    "ANGELINE",
    "SACAY"
];

echo "<h5>🧪 Testing Search Variations:</h5>";

try {
    $documentCollection = $mongodb->collection('document_uploads');
    $allDocs = $documentCollection->find([]);
    
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr><th>Search Term</th><th>Database Match</th><th>Filesystem Match</th><th>Status</th></tr>";
    
    foreach ($testVariations as $searchTerm) {
        $dbMatch = false;
        $fsMatch = false;
        $matchedDocs = [];
        $matchedFiles = [];
        
        // Test database
        foreach ($allDocs as $doc) {
            $originalName = $doc['original_name'] ?? '';
            $searchableText = strtolower($originalName . ' ' . str_replace('?', 'Ñ', $originalName));
            
            if (stripos($searchableText, strtolower($searchTerm)) !== false) {
                $dbMatch = true;
                $matchedDocs[] = $originalName;
            }
        }
        
        // Test filesystem
        $directories = ['uploads/COR/', 'uploads/COG/'];
        foreach ($directories as $dir) {
            if (is_dir($dir)) {
                $files = scandir($dir);
                foreach ($files as $file) {
                    if ($file != '.' && $file != '..' && is_file($dir . $file)) {
                        $fileName = pathinfo($file, PATHINFO_FILENAME);
                        $searchableText = strtolower($fileName . ' ' . str_replace('?', 'Ñ', $fileName));
                        
                        if (stripos($searchableText, strtolower($searchTerm)) !== false) {
                            $fsMatch = true;
                            $matchedFiles[] = $file;
                        }
                    }
                }
            }
        }
        
        $status = '';
        if ($dbMatch && $fsMatch) {
            $status = '✅ BOTH';
        } elseif ($dbMatch) {
            $status = '✅ DB ONLY';
        } elseif ($fsMatch) {
            $status = '✅ FS ONLY';
        } else {
            $status = '❌ NONE';
        }
        
        $color = ($dbMatch || $fsMatch) ? 'green' : 'red';
        
        echo "<tr>";
        echo "<td style='font-family: monospace;'>" . htmlspecialchars($searchTerm) . "</td>";
        echo "<td style='color: $color;'>" . ($dbMatch ? 'YES (' . count($matchedDocs) . ')' : 'NO') . "</td>";
        echo "<td style='color: $color;'>" . ($fsMatch ? 'YES (' . count($matchedFiles) . ')' : 'NO') . "</td>";
        echo "<td style='color: $color;'>$status</td>";
        echo "</tr>";
    }
    
    echo "</table>";
    
    // Show matched documents
    if (!empty($matchedDocs)) {
        echo "<h5>📄 Matched Documents:</h5>";
        echo "<ul>";
        foreach ($matchedDocs as $doc) {
            echo "<li>" . htmlspecialchars($doc) . "</li>";
        }
        echo "</ul>";
    }
    
    if (!empty($matchedFiles)) {
        echo "<h5>📁 Matched Files:</h5>";
        echo "<ul>";
        foreach ($matchedFiles as $file) {
            echo "<li>" . htmlspecialchars($file) . "</li>";
        }
        echo "</ul>";
    }
    
} catch (Exception $e) {
    echo "<div class='alert alert-danger'>Error: " . htmlspecialchars($e->getMessage()) . "</div>";
}

// Check if there's a mismatch between database and filesystem
echo "<h4>🔍 Check Database vs Filesystem Mismatch:</h4>";

try {
    $documentCollection = $mongodb->collection('document_uploads');
    $allDocs = $documentCollection->find([]);
    
    $dbNames = [];
    $fsNames = [];
    
    // Get all database names
    foreach ($allDocs as $doc) {
        $dbNames[] = $doc['original_name'] ?? '';
    }
    
    // Get all filesystem names
    $directories = ['uploads/COR/', 'uploads/COG/'];
    foreach ($directories as $dir) {
        if (is_dir($dir)) {
            $files = scandir($dir);
            foreach ($files as $file) {
                if ($file != '.' && $file != '..' && is_file($dir . $file)) {
                    $fsNames[] = pathinfo($file, PATHINFO_FILENAME);
                }
            }
        }
    }
    
    $dbCount = count($dbNames);
    $fsCount = count($fsNames);
    
    echo "<div class='alert alert-info'>";
    echo "<h5>📊 Count Comparison:</h5>";
    echo "<ul>";
    echo "<li><strong>Database Documents:</strong> $dbCount</li>";
    echo "<li><strong>Filesystem Files:</strong> $fsCount</li>";
    echo "<li><strong>Difference:</strong> " . abs($dbCount - $fsCount) . "</li>";
    echo "</ul>";
    echo "</div>";
    
    if ($dbCount != $fsCount) {
        echo "<div class='alert alert-warning'>";
        echo "<h5>⚠️ Mismatch Detected!</h5>";
        echo "<p>The database and filesystem have different numbers of documents. This could cause search issues.</p>";
        echo "<p><strong>Possible causes:</strong></p>";
        echo "<ul>";
        echo "<li>Documents uploaded but not saved to database</li>";
        echo "<li>Database entries without corresponding files</li>";
        echo "<li>Files uploaded but not recorded in database</li>";
        echo "</ul>";
        echo "</div>";
    }
    
} catch (Exception $e) {
    echo "<div class='alert alert-danger'>Error: " . htmlspecialchars($e->getMessage()) . "</div>";
}

// Provide solutions
echo "<h4>💡 Solutions:</h4>";

echo "<div class='alert alert-info'>";
echo "<h5>🔧 If ARGAÑOZA document exists but search doesn't work:</h5>";
echo "<ol>";
echo "<li><strong>Check exact filename:</strong> The document might have a different name format</li>";
echo "<li><strong>Check character encoding:</strong> Try searching with both 'Ñ' and '?' characters</li>";
echo "<li><strong>Check database vs filesystem:</strong> Document might be in filesystem but not database</li>";
echo "<li><strong>Re-upload if needed:</strong> If document exists but search fails, try re-uploading</li>";
echo "</ol>";
echo "</div>";

echo "<div class='alert alert-success'>";
echo "<h5>✅ If ARGAÑOZA document doesn't exist:</h5>";
echo "<ol>";
echo "<li><strong>Upload the document:</strong> Use the COR interface to upload ARGAÑOZA, ANGELINE SACAY</li>";
echo "<li><strong>Check masterlist:</strong> Make sure the student exists in the masterlist</li>";
echo "<li><strong>Verify upload:</strong> After uploading, check if it appears in the search</li>";
echo "</ol>";
echo "</div>";

?>

<div class="row mt-4">
    <div class="col-md-3">
        <a href="check_all_uploaded_cor.php" class="btn btn-primary btn-block">📋 Check All COR</a>
    </div>
    <div class="col-md-3">
        <a href="cor-cog.php" class="btn btn-info btn-block">📤 COR Interface</a>
    </div>
    <div class="col-md-3">
        <a href="find_arganoza_cor.php" class="btn btn-danger btn-block">🔍 Find ARGAÑOZA</a>
    </div>
    <div class="col-md-3">
        <a href="masterlist.php" class="btn btn-success btn-block">📋 Masterlist</a>
    </div>
</div>












