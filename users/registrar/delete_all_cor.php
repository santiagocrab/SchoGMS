<?php
include 'config/session.php';

echo "<h3>🗑️ DELETING ALL COR DOCUMENTS</h3>";

try {
    $documentCollection = $mongodb->collection('document_uploads');
    
    // Get all COR documents
    $corFilter = ['category' => 'COR'];
    $allCorDocs = $documentCollection->find($corFilter);
    
    $deletedCount = 0;
    $fileDeletedCount = 0;
    $errors = [];
    
    echo "<p><strong>Scanning for COR documents...</strong></p>";
    
    foreach ($allCorDocs as $doc) {
        $docId = $doc['id'] ?? '';
        $fileName = $doc['original_name'] ?? 'Unknown';
        $filePath = $doc['file_path'] ?? '';
        
        echo "<p>🔍 Processing: " . htmlspecialchars($fileName) . "</p>";
        
        // Delete the database record
        $deleteResult = $documentCollection->deleteOne(['id' => $docId]);
        
        if ($deleteResult) {
            $deletedCount++;
            echo "<p>✅ Database record deleted: " . htmlspecialchars($fileName) . "</p>";
        } else {
            $errors[] = "Failed to delete database record for: " . $fileName;
            echo "<p>❌ Failed to delete database record: " . htmlspecialchars($fileName) . "</p>";
        }
        
        // Delete the physical file if it exists
        if (!empty($filePath) && file_exists($filePath)) {
            if (unlink($filePath)) {
                $fileDeletedCount++;
                echo "<p>✅ Physical file deleted: " . htmlspecialchars($filePath) . "</p>";
            } else {
                $errors[] = "Failed to delete physical file: " . $filePath;
                echo "<p>❌ Failed to delete physical file: " . htmlspecialchars($filePath) . "</p>";
            }
        } else {
            echo "<p>⚠️ Physical file not found: " . htmlspecialchars($filePath) . "</p>";
        }
    }
    
    // Also clean up any remaining COR files in upload directories
    $corDirectories = [
        'uploads/COR/',
        'uploads/documents/ISULAN/2024-2025/1st Semester/COR/',
        'uploads/documents/ISULAN/2024-2025/2nd Semester/COR/',
        'uploads/documents/ISULAN/2023-2024/1st Semester/COR/',
        'uploads/documents/ISULAN/2023-2024/2nd Semester/COR/'
    ];
    
    echo "<p><strong>Cleaning up remaining COR files in directories...</strong></p>";
    
    foreach ($corDirectories as $dir) {
        if (is_dir($dir)) {
            $files = scandir($dir);
            foreach ($files as $file) {
                if ($file != '.' && $file != '..' && is_file($dir . $file)) {
                    if (unlink($dir . $file)) {
                        $fileDeletedCount++;
                        echo "<p>✅ Directory file deleted: " . htmlspecialchars($dir . $file) . "</p>";
                    }
                }
            }
        }
    }
    
    echo "<h4>📊 DELETION RESULTS:</h4>";
    echo "<p><strong>Database records deleted:</strong> " . $deletedCount . "</p>";
    echo "<p><strong>Physical files deleted:</strong> " . $fileDeletedCount . "</p>";
    
    if (count($errors) > 0) {
        echo "<h4>⚠️ ERRORS ENCOUNTERED:</h4>";
        foreach ($errors as $error) {
            echo "<p>❌ " . htmlspecialchars($error) . "</p>";
        }
    }
    
    if ($deletedCount == 0) {
        echo "<p>✅ <strong>No COR documents found in database!</strong></p>";
    } else {
        echo "<p>✅ <strong>All COR documents deleted successfully!</strong></p>";
    }
    
} catch (Exception $e) {
    echo "<p>❌ Error: " . $e->getMessage() . "</p>";
}

echo "<p><a href='cor-cog.php' class='btn btn-primary'>← Back to COR Interface</a></p>";
echo "<p><a href='masterlist.php' class='btn btn-secondary'>← Back to Masterlist</a></p>";
?>
