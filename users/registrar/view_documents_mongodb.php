<?php include 'config/session.php'; ?>
<!DOCTYPE html>
<html dir="ltr" lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Uploaded Documents (MongoDB) | SchoGMS</title>
    <link href="../../dist/css/style.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<?php schogms_loading_screen_once(); ?>

    <div class="container-fluid mt-4">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h4 class="card-title mb-0">Uploaded COR & COG Documents (MongoDB)</h4>
                        <p class="mb-0">Documents stored in MongoDB database</p>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th>ID</th>
                                        <th>Campus</th>
                                        <th>Category</th>
                                        <th>Academic Year</th>
                                        <th>Semester</th>
                                        <th>File Name</th>
                                        <th>File Size</th>
                                        <th>Uploaded By</th>
                                        <th>Upload Date</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    try {
                                        // Get MongoDB collection
                                        $documentCollection = $mongodb->collection('document_uploads');
                                        
                                        // Get all documents, sorted by upload date (newest first)
                                        $documents = $documentCollection->find([], ['sort' => ['uploaded_at' => -1]]);
                                        
                                        $totalDocuments = 0;
                                        $totalSize = 0;
                                        
                                        foreach ($documents as $doc) {
                                            $totalDocuments++;
                                            $totalSize += $doc['file_size'] ?? 0;
                                            
                                            echo "<tr>";
                                            echo "<td><small>" . htmlspecialchars($doc['id'] ?? 'N/A') . "</small></td>";
                                            echo "<td>" . htmlspecialchars($doc['campus'] ?? 'N/A') . "</td>";
                                            echo "<td><span class='badge badge-" . ($doc['category'] == 'COR' ? 'success' : 'primary') . "'>" . htmlspecialchars($doc['category'] ?? 'N/A') . "</span></td>";
                                            echo "<td>" . htmlspecialchars($doc['academic_year'] ?? 'N/A') . "</td>";
                                            echo "<td>" . htmlspecialchars($doc['semester'] ?? 'N/A') . "</td>";
                                            echo "<td>" . htmlspecialchars($doc['original_name'] ?? 'N/A') . "</td>";
                                            echo "<td>" . number_format(($doc['file_size'] ?? 0) / 1024, 2) . " KB</td>";
                                            echo "<td>" . htmlspecialchars($doc['uploaded_by'] ?? 'N/A') . "</td>";
                                            echo "<td>" . htmlspecialchars($doc['uploaded_at'] ?? 'N/A') . "</td>";
                                            echo "<td><span class='badge badge-" . ($doc['status'] == 'active' ? 'success' : 'secondary') . "'>" . htmlspecialchars($doc['status'] ?? 'N/A') . "</span></td>";
                                            echo "<td>";
                                            if (isset($doc['file_path']) && file_exists($doc['file_path'])) {
                                                echo "<a href='" . htmlspecialchars($doc['file_path']) . "' target='_blank' class='btn btn-sm btn-primary'>View</a>";
                                            } else {
                                                echo "<span class='text-muted'>File not found</span>";
                                            }
                                            echo "</td>";
                                            echo "</tr>";
                                        }
                                        
                                        if ($totalDocuments == 0) {
                                            echo "<tr><td colspan='11' class='text-center text-muted'>No documents found in MongoDB</td></tr>";
                                        }
                                        
                                    } catch (Exception $e) {
                                        echo "<tr><td colspan='11' class='text-center text-danger'>Error loading documents: " . htmlspecialchars($e->getMessage()) . "</td></tr>";
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                        
                        <?php if ($totalDocuments > 0): ?>
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <h6>MongoDB Statistics:</h6>
                                        <p><strong>Total Documents:</strong> <?= $totalDocuments ?></p>
                                        <p><strong>Total Size:</strong> <?= number_format($totalSize / 1024 / 1024, 2) ?> MB</p>
                                        <p><strong>Database:</strong> schogms</p>
                                        <p><strong>Collection:</strong> document_uploads</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <h6>Quick Actions:</h6>
                                        <a href="cor-cog.php" class="btn btn-primary btn-sm mb-2">Upload More Documents</a><br>
                                        <a href="batch_upload.php" class="btn btn-warning btn-sm mb-2">Batch Upload</a><br>
                                        <a href="view_documents.php" class="btn btn-info btn-sm mb-2">View JSON Version</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
