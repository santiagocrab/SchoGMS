<?php 
// Simple version of documents uploaded page
include 'config/session.php'; 
?>
<!DOCTYPE html>
<html>
<head>
    <title>Documents Uploaded - Simple</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<?php schogms_loading_screen_once(); ?>

    <div class="container mt-5">
        <h2>📄 Documents Uploaded - Simple Version</h2>
        
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h5>Displaying records for: <strong><?php echo $sheet_name; ?></strong></h5>
                        
                        <?php
                        try {
                            require '../../conn_mongodb.php';
                            
                            echo "<h6>1. Testing MongoDB Connection</h6>";
                            $registrarCollection = $mongodb->collection('registrar_master_list');
                            $result = $registrarCollection->find(['campus' => $sheet_name]);
                            
                            echo "<h6>2. Getting Registrar Data</h6>";
                            $groupedData = [];
                            $count = 0;
                            foreach ($result as $record) {
                                $count++;
                                if ($count > 10) break; // Limit to prevent infinite loop
                                
                                $campus = $record['campus'] ?? '';
                                $fileGroup = $record['file_group'] ?? '';
                                $createdAt = $record['created_at'] ?? '';
                                
                                if (!isset($groupedData[$fileGroup])) {
                                    $groupedData[$fileGroup] = [
                                        'campus' => $campus,
                                        'file_group' => $fileGroup,
                                        'created_at' => $createdAt
                                    ];
                                }
                            }
                            
                            echo "<p>Found " . count($groupedData) . " unique file groups</p>";
                            
                            echo "<h6>3. Registrar Masterlist & COR/COG</h6>";
                            echo "<table class='table table-striped'>";
                            echo "<thead><tr><th>Campus</th><th>File Group</th><th>Uploaded At</th><th>Action</th></tr></thead>";
                            echo "<tbody>";
                            
                            foreach ($groupedData as $row) {
                                echo "<tr>";
                                echo "<td>" . htmlspecialchars($row['campus']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['file_group']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['created_at']) . "</td>";
                                echo "<td><button class='btn btn-danger btn-sm'>Delete</button></td>";
                                echo "</tr>";
                            }
                            
                            echo "</tbody></table>";
                            
                            echo "<h6>4. Testing Document Uploads</h6>";
                            $documentCollection = $mongodb->collection('document_uploads');
                            $docResult = $documentCollection->find(['campus' => $sheet_name]);
                            
                            $groupedDocData = [];
                            $docCount = 0;
                            foreach ($docResult as $record) {
                                $docCount++;
                                if ($docCount > 10) break; // Limit to prevent infinite loop
                                
                                $campus = $record['campus'] ?? '';
                                $category = $record['category'] ?? '';
                                $uploadedAt = $record['uploaded_at'] ?? '';
                                
                                if (!isset($groupedDocData[$category])) {
                                    $groupedDocData[$category] = [
                                        'campus' => $campus,
                                        'category' => $category,
                                        'uploaded_at' => $uploadedAt
                                    ];
                                }
                            }
                            
                            echo "<p>Found " . count($groupedDocData) . " unique document categories</p>";
                            
                            echo "<h6>5. Document Uploads</h6>";
                            echo "<table class='table table-striped'>";
                            echo "<thead><tr><th>Campus</th><th>Category</th><th>Uploaded At</th><th>Action</th></tr></thead>";
                            echo "<tbody>";
                            
                            foreach ($groupedDocData as $row) {
                                echo "<tr>";
                                echo "<td>" . htmlspecialchars($row['campus']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['category']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['uploaded_at']) . "</td>";
                                echo "<td><button class='btn btn-danger btn-sm'>Delete</button></td>";
                                echo "</tr>";
                            }
                            
                            echo "</tbody></table>";
                            
                        } catch (Exception $e) {
                            echo "<div class='alert alert-danger'>Error: " . $e->getMessage() . "</div>";
                        }
                        ?>
                        
                    </div>
                </div>
            </div>
        </div>
        
        <br>
        <a href="documents_uploaded.php" class="btn btn-primary">← Back to Full Version</a>
    </div>
</body>
</html>
