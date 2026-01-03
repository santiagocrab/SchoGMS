<?php
include 'config/session.php';

echo "<h3>🔧 ADD RAR FILES TO DATABASE</h3>";

// Check file system for RAR files
$corDirs = [
    'uploads/COR/',
    'uploads/documents/ISULAN/2024-2025/1st Semester/COR/',
    'uploads/documents/ISULAN/2024-2025/2nd Semester/COR/',
    'uploads/documents/ISULAN/2023-2024/1st Semester/COR/',
    'uploads/documents/ISULAN/2023-2024/2nd Semester/COR/'
];

$rarFiles = [];
$addedCount = 0;
$errorCount = 0;

echo "<h4>🔍 Scanning for RAR files...</h4>";

foreach ($corDirs as $corDir) {
    if (is_dir($corDir)) {
        $files = scandir($corDir);
        foreach ($files as $file) {
            if ($file != '.' && $file != '..' && is_file($corDir . $file)) {
                $fileExtension = strtolower(pathinfo($file, PATHINFO_EXTENSION));
                if ($fileExtension === 'rar') {
                    $rarFiles[] = [
                        'path' => $corDir . $file,
                        'name' => $file,
                        'size' => filesize($corDir . $file)
                    ];
                }
            }
        }
    }
}

if (count($rarFiles) > 0) {
    echo "<div class='alert alert-info'>";
    echo "<h5>📦 Found " . count($rarFiles) . " RAR files</h5>";
    echo "</div>";
    
    echo "<h4>📝 Adding RAR files to database...</h4>";
    
    try {
        $documentCollection = $mongodb->collection('document_uploads');
        
        echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr><th>RAR File</th><th>Size</th><th>Status</th><th>Action</th></tr>";
        
        foreach ($rarFiles as $rarFile) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($rarFile['name']) . "</td>";
            echo "<td>" . number_format($rarFile['size']) . " bytes</td>";
            
            // Check if already in database
            $existing = $documentCollection->findOne(['file_path' => $rarFile['path']]);
            
            if ($existing) {
                echo "<td style='color: green;'>✅ Already in database</td>";
                echo "<td>-</td>";
            } else {
                // Add to database
                $documentData = [
                    'id' => uniqid(),
                    'original_name' => $rarFile['name'],
                    'file_name' => $rarFile['name'],
                    'file_path' => $rarFile['path'],
                    'category' => 'COR',
                    'academic_year' => '2024-2025',
                    'semester' => '1st Semester',
                    'campus' => 'ISULAN',
                    'uploaded_by' => $_SESSION['username'] ?? 'System',
                    'uploaded_at' => date('Y-m-d H:i:s'),
                    'file_size' => $rarFile['size'],
                    'file_type' => 'application/x-rar-compressed'
                ];
                
                try {
                    $documentCollection->insertOne($documentData);
                    echo "<td style='color: green;'>✅ Added to database</td>";
                    echo "<td><a href='view_document.php?file=" . urlencode($rarFile['path']) . "&type=COR' class='btn btn-primary btn-sm'>View RAR</a></td>";
                    $addedCount++;
                } catch (Exception $e) {
                    echo "<td style='color: red;'>❌ Error: " . htmlspecialchars($e->getMessage()) . "</td>";
                    echo "<td>-</td>";
                    $errorCount++;
                }
            }
            
            echo "</tr>";
        }
        
        echo "</table>";
        
        echo "<div class='alert alert-success'>";
        echo "<h5>✅ DATABASE UPDATE COMPLETE!</h5>";
        echo "<p><strong>Files added to database:</strong> $addedCount</p>";
        echo "<p><strong>Errors:</strong> $errorCount</p>";
        echo "</div>";
        
    } catch (Exception $e) {
        echo "<div class='alert alert-danger'>";
        echo "<h5>❌ Database Error:</h5>";
        echo "<p>" . htmlspecialchars($e->getMessage()) . "</p>";
        echo "</div>";
    }
    
} else {
    echo "<div class='alert alert-warning'>";
    echo "<h5>⚠️ No RAR files found</h5>";
    echo "<p>No .rar files were found in the COR directories.</p>";
    echo "</div>";
}

// Show updated count
echo "<h4>📊 Updated Document Count:</h4>";
try {
    $documentCollection = $mongodb->collection('document_uploads');
    $totalDocs = $documentCollection->count();
    $rarDocs = $documentCollection->count(['file_type' => 'application/x-rar-compressed']);
    
    echo "<div class='alert alert-info'>";
    echo "<h5>📈 Current Database Status:</h5>";
    echo "<p><strong>Total Documents:</strong> $totalDocs</p>";
    echo "<p><strong>RAR Documents:</strong> $rarDocs</p>";
    echo "</div>";
    
} catch (Exception $e) {
    echo "<div class='alert alert-danger'>";
    echo "<p>Error getting updated count: " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "</div>";
}

?>

<div class="row mt-4">
    <div class="col-md-4">
        <a href="debug_rar_upload.php" class="btn btn-info btn-block">🔍 Debug RAR Upload</a>
    </div>
    <div class="col-md-4">
        <a href="cor-cog.php" class="btn btn-primary btn-block">📤 Upload More Files</a>
    </div>
    <div class="col-md-4">
        <a href="masterlist.php" class="btn btn-secondary btn-block">📋 View Masterlist</a>
    </div>
</div>

<p><a href="cor-cog.php" class="btn btn-success">← Back to COR Interface</a></p>












