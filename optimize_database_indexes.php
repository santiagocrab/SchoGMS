<?php
/**
 * Database Index Optimization Script
 * Creates indexes to improve query performance
 */

require_once 'conn.php';

echo "<h2>Database Index Optimization for SchoGMS</h2>";
echo "<p>Creating indexes to improve query performance...</p>";

try {
    // Indexes for ched_masterlist table
    $indexes = [
        "CREATE INDEX IF NOT EXISTS idx_sheet_name ON ched_masterlist(sheet_name)",
        "CREATE INDEX IF NOT EXISTS idx_file_group ON ched_masterlist(file_group)",
        "CREATE INDEX IF NOT EXISTS idx_semester ON ched_masterlist(semester)",
        "CREATE INDEX IF NOT EXISTS idx_academic_year ON ched_masterlist(academic_year)",
        "CREATE INDEX IF NOT EXISTS idx_sheet_semester_ay ON ched_masterlist(sheet_name, semester, academic_year)",
        
        // Indexes for registrar_master_list table
        "CREATE INDEX IF NOT EXISTS idx_registrar_campus ON registrar_master_list(campus)",
        "CREATE INDEX IF NOT EXISTS idx_registrar_semester ON registrar_master_list(semester)",
        "CREATE INDEX IF NOT EXISTS idx_registrar_ay ON registrar_master_list(academic_year)",
        "CREATE INDEX IF NOT EXISTS idx_registrar_name ON registrar_master_list(last_name, first_name, middle_name)",
        
        // Indexes for document_uploads table
        "CREATE INDEX IF NOT EXISTS idx_doc_campus ON document_uploads(campus)",
        "CREATE INDEX IF NOT EXISTS idx_doc_category ON document_uploads(category)",
        "CREATE INDEX IF NOT EXISTS idx_doc_file_name ON document_uploads(file_name(100))",
        "CREATE INDEX IF NOT EXISTS idx_doc_campus_category ON document_uploads(campus, category)"
    ];
    
    $successCount = 0;
    $errorCount = 0;
    
    foreach ($indexes as $indexQuery) {
        if ($conn->query($indexQuery)) {
            $successCount++;
            echo "<p style='color: green;'>✅ Index created successfully</p>";
        } else {
            $errorCount++;
            // Check if error is because index already exists (not a real error)
            if (strpos($conn->error, 'Duplicate key name') === false && strpos($conn->error, 'already exists') === false) {
                echo "<p style='color: orange;'>⚠️ " . htmlspecialchars($conn->error) . "</p>";
            } else {
                echo "<p style='color: blue;'>ℹ️ Index already exists (skipped)</p>";
                $successCount++; // Count as success since index exists
                $errorCount--;
            }
        }
    }
    
    echo "<br><div style='background: #d4edda; padding: 15px; border-radius: 5px; margin: 20px 0;'>";
    echo "<h3 style='color: #155724;'>✅ Index Optimization Completed!</h3>";
    echo "<p><strong>Summary:</strong></p>";
    echo "<ul>";
    echo "<li>Successfully created/verified: $successCount indexes</li>";
    if ($errorCount > 0) {
        echo "<li>Errors: $errorCount</li>";
    }
    echo "</ul>";
    echo "<p style='color: #856404;'><strong>Note:</strong> Database queries should now run significantly faster.</p>";
    echo "</div>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error during optimization: " . htmlspecialchars($e->getMessage()) . "</p>";
}

$conn->close();

echo "<br><p><a href='index.php' style='background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>← Back to Home</a></p>";
?>



