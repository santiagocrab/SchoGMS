<?php
/**
 * Database Migration Script for SchoGMS
 * Adds required fields to registrar_masterlist and tdp_masterlist collections
 * 
 * Run this script once to update the database schema
 */

require_once 'conn_mongodb.php';

echo "<h2>Database Migration for SchoGMS</h2>";
echo "<p>Adding required fields to collections...</p>";

try {
    // Update registrar_master_list collection documents
    $registrarCollection = $mongodb->collection('registrar_master_list');
    
    // Get all documents that don't have the new fields
    $registrarDocs = $registrarCollection->find([]);
    $updatedCount = 0;
    
    foreach ($registrarDocs as $doc) {
        $updateData = [];
        $needsUpdate = false;
        
        // Add academic_year if missing
        if (!isset($doc['academic_year']) || empty($doc['academic_year'])) {
            $updateData['academic_year'] = $doc['academic_year'] ?? '';
            $needsUpdate = true;
        }
        
        // Add semester if missing
        if (!isset($doc['semester']) || empty($doc['semester'])) {
            $updateData['semester'] = $doc['semester'] ?? '';
            $needsUpdate = true;
        }
        
        // Add email_address if missing (should already exist but ensure it's there)
        if (!isset($doc['email_address'])) {
            $updateData['email_address'] = $doc['email_address'] ?? '';
            $needsUpdate = true;
        }
        
        // Add pdf_cor_path if missing
        if (!isset($doc['pdf_cor_path'])) {
            $updateData['pdf_cor_path'] = '';
            $needsUpdate = true;
        }
        
        // Add pdf_cog_path if missing
        if (!isset($doc['pdf_cog_path'])) {
            $updateData['pdf_cog_path'] = '';
            $needsUpdate = true;
        }
        
        if ($needsUpdate) {
            $registrarCollection->updateOne(
                ['_id' => $doc['_id']],
                ['$set' => $updateData]
            );
            $updatedCount++;
        }
    }
    
    echo "<p style='color: green;'>✅ Updated $updatedCount registrar_master_list documents</p>";
    
    // Update ched_masterlist (TDP masterlist) collection documents
    $chedCollection = $mongodb->collection('ched_masterlist');
    
    $chedDocs = $chedCollection->find([]);
    $chedUpdatedCount = 0;
    
    foreach ($chedDocs as $doc) {
        $updateData = [];
        $needsUpdate = false;
        
        // Add batch if missing (use file_group as batch)
        if (!isset($doc['batch']) && isset($doc['file_group'])) {
            $updateData['batch'] = $doc['file_group'];
            $needsUpdate = true;
        }
        
        // Add semester if missing
        if (!isset($doc['semester']) || empty($doc['semester'])) {
            $updateData['semester'] = $doc['semester'] ?? '';
            $needsUpdate = true;
        }
        
        // Add academic_year if missing
        if (!isset($doc['academic_year']) || empty($doc['academic_year'])) {
            $updateData['academic_year'] = $doc['academic_year'] ?? '';
            $needsUpdate = true;
        }
        
        // Add validated_by if missing
        if (!isset($doc['validated_by'])) {
            $updateData['validated_by'] = '';
            $needsUpdate = true;
        }
        
        // Add validation_status if missing
        if (!isset($doc['validation_status'])) {
            $updateData['validation_status'] = 'Pending';
            $needsUpdate = true;
        }
        
        if ($needsUpdate) {
            $chedCollection->updateOne(
                ['_id' => $doc['_id']],
                ['$set' => $updateData]
            );
            $chedUpdatedCount++;
        }
    }
    
    echo "<p style='color: green;'>✅ Updated $chedUpdatedCount ched_masterlist documents</p>";
    
    // Update document_uploads collection to ensure campus field exists
    $docCollection = $mongodb->collection('document_uploads');
    $docDocs = $docCollection->find(['campus' => ['$exists' => false]]);
    $docUpdatedCount = 0;
    
    foreach ($docDocs as $doc) {
        // Try to infer campus from file path or set default
        $campus = 'ISULAN'; // Default
        if (isset($doc['file_path'])) {
            if (strpos($doc['file_path'], 'ISULAN') !== false) {
                $campus = 'ISULAN';
            } elseif (strpos($doc['file_path'], 'TACURONG') !== false) {
                $campus = 'TACURONG';
            } elseif (strpos($doc['file_path'], 'PALIMBANG') !== false) {
                $campus = 'PALIMBANG';
            }
        }
        
        $docCollection->updateOne(
            ['_id' => $doc['_id']],
            ['$set' => ['campus' => $campus]]
        );
        $docUpdatedCount++;
    }
    
    if ($docUpdatedCount > 0) {
        echo "<p style='color: green;'>✅ Updated $docUpdatedCount document_uploads documents with campus field</p>";
    }
    
    echo "<br><div style='background: #d4edda; padding: 15px; border-radius: 5px; margin: 20px 0;'>";
    echo "<h3 style='color: #155724;'>✅ Migration Completed Successfully!</h3>";
    echo "<p><strong>Summary:</strong></p>";
    echo "<ul>";
    echo "<li>Registrar Masterlist: $updatedCount documents updated</li>";
    echo "<li>CHED Masterlist (TDP): $chedUpdatedCount documents updated</li>";
    if ($docUpdatedCount > 0) {
        echo "<li>Document Uploads: $docUpdatedCount documents updated</li>";
    }
    echo "</ul>";
    echo "<p style='color: #856404;'><strong>Note:</strong> The database schema has been updated. All new fields are now available.</p>";
    echo "</div>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error during migration: " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<p>Please check the error and try again.</p>";
}

echo "<br><p><a href='index.php' style='background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>← Back to Home</a></p>";
?>



