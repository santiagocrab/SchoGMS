<?php
include 'config/session.php';

echo "<h3>📋 SHOW ALL UPLOADED COR NAMES</h3>";

// Get all documents from database
try {
    $documentCollection = $mongodb->collection('document_uploads');
    $allDocs = $documentCollection->find([]);
    
    $corDocs = [];
    $cogDocs = [];
    $otherDocs = [];
    
    foreach ($allDocs as $doc) {
        $category = $doc['category'] ?? '';
        $originalName = $doc['original_name'] ?? '';
        $academicYear = $doc['academic_year'] ?? '';
        $semester = $doc['semester'] ?? '';
        $uploadedAt = $doc['uploaded_at'] ?? '';
        
        $docInfo = [
            'name' => $originalName,
            'academic_year' => $academicYear,
            'semester' => $semester,
            'uploaded_at' => $uploadedAt,
            'file_path' => $doc['file_path'] ?? ''
        ];
        
        if ($category === 'COR') {
            $corDocs[] = $docInfo;
        } elseif ($category === 'COG') {
            $cogDocs[] = $docInfo;
        } else {
            $otherDocs[] = $docInfo;
        }
    }
    
    echo "<h4>📊 Document Statistics:</h4>";
    echo "<div class='alert alert-info'>";
    echo "<ul>";
    echo "<li><strong>Total Documents:</strong> " . (count($corDocs) + count($cogDocs) + count($otherDocs)) . "</li>";
    echo "<li><strong>COR Documents:</strong> " . count($corDocs) . "</li>";
    echo "<li><strong>COG Documents:</strong> " . count($cogDocs) . "</li>";
    echo "<li><strong>Other Documents:</strong> " . count($otherDocs) . "</li>";
    echo "</ul>";
    echo "</div>";
    
    // Show COR documents
    echo "<h4>📄 All COR Documents (" . count($corDocs) . "):</h4>";
    
    if (!empty($corDocs)) {
        echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr><th>#</th><th>Document Name</th><th>Academic Year</th><th>Semester</th><th>Uploaded At</th><th>Search Test</th></tr>";
        
        $count = 0;
        foreach ($corDocs as $doc) {
            $count++;
            
            // Test if this document would be found by search
            $searchableText = strtolower(implode(' ', [
                $doc['name'],
                str_replace('?', 'Ñ', $doc['name']),
                $doc['academic_year'],
                $doc['semester']
            ]));
            
            $searchTests = ['arganoza', 'angeline', 'sacay', 'argañoza'];
            $searchFound = false;
            foreach ($searchTests as $test) {
                if (stripos($searchableText, $test) !== false) {
                    $searchFound = true;
                    break;
                }
            }
            
            $searchStatus = $searchFound ? '✅ FOUND' : '❌ NOT FOUND';
            $searchColor = $searchFound ? 'green' : 'red';
            
            echo "<tr>";
            echo "<td>$count</td>";
            echo "<td style='font-family: monospace;'>" . htmlspecialchars($doc['name']) . "</td>";
            echo "<td>" . htmlspecialchars($doc['academic_year']) . "</td>";
            echo "<td>" . htmlspecialchars($doc['semester']) . "</td>";
            echo "<td>" . htmlspecialchars($doc['uploaded_at']) . "</td>";
            echo "<td style='color: $searchColor;'>$searchStatus</td>";
            echo "</tr>";
        }
        
        echo "</table>";
    } else {
        echo "<div class='alert alert-warning'>No COR documents found in database.</div>";
    }
    
    // Show COG documents
    echo "<h4>📊 All COG Documents (" . count($cogDocs) . "):</h4>";
    
    if (!empty($cogDocs)) {
        echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr><th>#</th><th>Document Name</th><th>Academic Year</th><th>Semester</th><th>Uploaded At</th></tr>";
        
        $count = 0;
        foreach (array_slice($cogDocs, 0, 20) as $doc) {
            $count++;
            
            echo "<tr>";
            echo "<td>$count</td>";
            echo "<td style='font-family: monospace;'>" . htmlspecialchars($doc['name']) . "</td>";
            echo "<td>" . htmlspecialchars($doc['academic_year']) . "</td>";
            echo "<td>" . htmlspecialchars($doc['semester']) . "</td>";
            echo "<td>" . htmlspecialchars($doc['uploaded_at']) . "</td>";
            echo "</tr>";
        }
        
        echo "</table>";
        
        if (count($cogDocs) > 20) {
            echo "<p><em>Showing first 20 COG documents. Total: " . count($cogDocs) . "</em></p>";
        }
    } else {
        echo "<div class='alert alert-warning'>No COG documents found in database.</div>";
    }
    
} catch (Exception $e) {
    echo "<div class='alert alert-danger'>Database Error: " . htmlspecialchars($e->getMessage()) . "</div>";
}

// Test search functionality
echo "<h4>🔍 Test Search Functionality:</h4>";

$testSearches = [
    'ARGAÑOZA',
    'ARGA?OZA', 
    'ANGELINE',
    'SACAY',
    'arganoza',
    'angeline',
    'sacay'
];

echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
echo "<tr><th>Search Term</th><th>Database Results</th><th>Search Logic Test</th></tr>";

foreach ($testSearches as $searchTerm) {
    $dbResults = 0;
    $searchLogicResults = 0;
    
    try {
        $documentCollection = $mongodb->collection('document_uploads');
        $allDocs = $documentCollection->find([]);
        
        foreach ($allDocs as $doc) {
            $originalName = $doc['original_name'] ?? '';
            $searchableText = strtolower(implode(' ', [
                $originalName,
                str_replace('?', 'Ñ', $originalName),
                $doc['academic_year'] ?? '',
                $doc['semester'] ?? ''
            ]));
            
            if (stripos($searchableText, strtolower($searchTerm)) !== false) {
                $dbResults++;
            }
            
            // Test the exact search logic used in cor-cog.php
            $searchableText2 = strtolower(implode(' ', [
                str_replace('?', 'Ñ', $originalName),
                $originalName,
                $doc['academic_year'] ?? '',
                $doc['semester'] ?? '',
                $doc['campus'] ?? '',
                $doc['uploaded_by'] ?? '',
                $doc['category'] ?? ''
            ]));
            
            if (stripos($searchableText2, strtolower($searchTerm)) !== false) {
                $searchLogicResults++;
            }
        }
        
    } catch (Exception $e) {
        $dbResults = "ERROR";
        $searchLogicResults = "ERROR";
    }
    
    $dbColor = ($dbResults > 0) ? 'green' : 'red';
    $logicColor = ($searchLogicResults > 0) ? 'green' : 'red';
    
    echo "<tr>";
    echo "<td style='font-family: monospace;'>" . htmlspecialchars($searchTerm) . "</td>";
    echo "<td style='color: $dbColor;'>$dbResults results</td>";
    echo "<td style='color: $logicColor;'>$searchLogicResults results</td>";
    echo "</tr>";
}

echo "</table>";

// Check if there's a mismatch between what we found and what the search shows
echo "<h4>🔍 Search Issue Diagnosis:</h4>";

if (count($corDocs) > 0) {
    echo "<div class='alert alert-info'>";
    echo "<h5>📊 Analysis:</h5>";
    echo "<ul>";
    echo "<li><strong>COR Documents in Database:</strong> " . count($corDocs) . "</li>";
    echo "<li><strong>ARGAÑOZA Documents Found:</strong> " . (count(array_filter($corDocs, function($doc) {
        return stripos($doc['name'], 'arganoza') !== false || stripos($doc['name'], 'angeline') !== false;
    }))) . "</li>";
    echo "<li><strong>Search Issue:</strong> The search in COR interface might not be working properly</li>";
    echo "</ul>";
    echo "</div>";
    
    echo "<div class='alert alert-warning'>";
    echo "<h5>⚠️ Possible Issues:</h5>";
    echo "<ul>";
    echo "<li><strong>Search Logic:</strong> The search logic in cor-cog.php might have bugs</li>";
    echo "<li><strong>Character Encoding:</strong> Search might not handle Ñ vs ? properly</li>";
    echo "<li><strong>Database Connection:</strong> Search might be using different database connection</li>";
    echo "<li><strong>Filter Logic:</strong> Search might be applying additional filters</li>";
    echo "</ul>";
    echo "</div>";
}

?>

<div class="row mt-4">
    <div class="col-md-3">
        <a href="cor-cog.php" class="btn btn-primary btn-block">📤 COR Interface</a>
    </div>
    <div class="col-md-3">
        <a href="fix_search_issue.php" class="btn btn-warning btn-block">🔧 Fix Search</a>
    </div>
    <div class="col-md-3">
        <a href="check_all_uploaded_cor.php" class="btn btn-info btn-block">📋 Check All COR</a>
    </div>
    <div class="col-md-3">
        <a href="masterlist.php" class="btn btn-success btn-block">📋 Masterlist</a>
    </div>
</div>












