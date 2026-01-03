<?php
include 'config/session.php';

echo "<h3>🧹 CLEANING UP DUPLICATE COR DOCUMENTS</h3>";

try {
    $documentCollection = $mongodb->collection('document_uploads');
    
    // Get all documents grouped by campus, academic_year, semester, and original_name
    $allDocs = $documentCollection->find([]);
    $groupedDocs = [];
    $duplicatesFound = 0;
    $duplicatesRemoved = 0;
    
    foreach ($allDocs as $doc) {
        $key = strtolower(trim($doc['campus'] . '|' . $doc['academic_year'] . '|' . $doc['semester'] . '|' . $doc['original_name']));
        
        if (!isset($groupedDocs[$key])) {
            $groupedDocs[$key] = [];
        }
        $groupedDocs[$key][] = $doc;
    }
    
    echo "<p><strong>Scanning for duplicates...</strong></p>";
    
    foreach ($groupedDocs as $key => $docs) {
        if (count($docs) > 1) {
            $duplicatesFound++;
            echo "<p>🔍 Found " . count($docs) . " duplicates for: " . htmlspecialchars($key) . "</p>";
            
            // Keep the first one (oldest), remove the rest
            $keepDoc = array_shift($docs); // First document (oldest)
            
            foreach ($docs as $duplicateDoc) {
                // Delete the duplicate document
                $deleteResult = $documentCollection->deleteOne(['id' => $duplicateDoc['id']]);
                
                // Also delete the physical file if it exists
                if (isset($duplicateDoc['file_path']) && file_exists($duplicateDoc['file_path'])) {
                    unlink($duplicateDoc['file_path']);
                    echo "<p>🗑️ Deleted file: " . htmlspecialchars($duplicateDoc['file_path']) . "</p>";
                }
                
                if ($deleteResult) {
                    $duplicatesRemoved++;
                    echo "<p>✅ Removed duplicate: " . htmlspecialchars($duplicateDoc['original_name']) . "</p>";
                }
            }
        }
    }
    
    echo "<h4>📊 CLEANUP RESULTS:</h4>";
    echo "<p><strong>Duplicate groups found:</strong> " . $duplicatesFound . "</p>";
    echo "<p><strong>Duplicate documents removed:</strong> " . $duplicatesRemoved . "</p>";
    
    if ($duplicatesFound == 0) {
        echo "<p>✅ <strong>No duplicates found!</strong> Your database is clean.</p>";
    } else {
        echo "<p>✅ <strong>Cleanup complete!</strong> Removed " . $duplicatesRemoved . " duplicate documents.</p>";
    }
    
} catch (Exception $e) {
    echo "<p>❌ Error: " . $e->getMessage() . "</p>";
}

echo "<p><a href='cor-cog.php' class='btn btn-primary'>← Back to COR Interface</a></p>";
?>
