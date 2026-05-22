<?php 
include 'config/session.php'; 
?>
<!DOCTYPE html>
<html dir="ltr" lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Documents Uploaded - SchoGMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .sidebar { background: #2c3e50; min-height: 100vh; }
        .sidebar .nav-link { color: #ecf0f1; }
        .sidebar .nav-link:hover { background: #34495e; color: #fff; }
        .sidebar .nav-link.active { background: #3498db; color: #fff; }
        .main-content { margin-left: 250px; padding: 20px; }
        .card { border: none; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .table th { background: #f8f9fa; border-top: none; }
    </style>
</head>
<body>
<?php schogms_loading_screen_once(); ?>

    <div class="d-flex">
        <!-- Sidebar -->
        <div class="sidebar" style="width: 250px; position: fixed; top: 0; left: 0;">
            <div class="p-3">
                <h5 class="text-white">SchoGMS</h5>
                <p class="text-light small">Scholarship System</p>
            </div>
            <nav class="nav flex-column">
                <a href="masterlist.php" class="nav-link">
                    <i class="fas fa-list"></i> Registrar Masterlist
                </a>
                <a href="documents_uploaded_clean.php" class="nav-link active">
                    <i class="fas fa-upload"></i> Document Uploaded
                </a>
                <a href="logout.php" class="nav-link">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </nav>
        </div>

        <!-- Main Content -->
        <div class="main-content" style="flex: 1;">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2><i class="fas fa-upload"></i> Documents Uploaded</h2>
                <div class="text-muted">
                    Hello, <strong><?php echo $fullname ?? 'User'; ?></strong>
                </div>
            </div>

            <?php
            try {
                require '../../conn_mongodb.php';
                
                // Get Registrar Masterlist Data
                $registrarCollection = $mongodb->collection('registrar_master_list');
                $result = $registrarCollection->find(['campus' => $sheet_name]);
                
                $groupedData = [];
                $count = 0;
                foreach ($result as $record) {
                    $count++;
                    if ($count > 1000) break;
                    
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
                
                // Get Document Uploads Data
                $documentCollection = $mongodb->collection('document_uploads');
                $docResult = $documentCollection->find(['campus' => $sheet_name]);
                
                $groupedDocData = [];
                $docCount = 0;
                foreach ($docResult as $record) {
                    $docCount++;
                    if ($docCount > 1000) break;
                    
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
                
            } catch (Exception $e) {
                echo "<div class='alert alert-danger'>Database Error: " . $e->getMessage() . "</div>";
                $groupedData = [];
                $groupedDocData = [];
            }
            ?>

            <!-- Registrar Masterlist & COR/COG -->
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-list"></i> Registrar Masterlist & COR/COG</h5>
                </div>
                <div class="card-body">
                    <p class="text-muted">Displaying records for: <strong><?php echo $sheet_name; ?></strong></p>
                    
                    <?php if (empty($groupedData)): ?>
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i> No registrar masterlist records found.
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>Campus</th>
                                        <th>File Group</th>
                                        <th>Uploaded At</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($groupedData as $row): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($row['campus']); ?></td>
                                            <td>
                                                <a href="#" onclick="viewFileGroup('<?php echo htmlspecialchars($row['file_group']); ?>')" class="text-decoration-none">
                                                    <?php echo htmlspecialchars($row['file_group']); ?>
                                                </a>
                                            </td>
                                            <td><?php echo htmlspecialchars($row['created_at']); ?></td>
                                            <td>
                                                <button class="btn btn-danger btn-sm" onclick="confirmDelete('<?php echo htmlspecialchars($row['file_group']); ?>')">
                                                    <i class="fas fa-trash"></i> Delete
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Document Uploads -->
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fas fa-file-upload"></i> Document Uploads</h5>
                </div>
                <div class="card-body">
                    <p class="text-muted">Displaying records for: <strong><?php echo $sheet_name; ?></strong></p>
                    
                    <?php if (empty($groupedDocData)): ?>
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i> No document uploads found.
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>Campus</th>
                                        <th>Category</th>
                                        <th>Uploaded At</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($groupedDocData as $row): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($row['campus']); ?></td>
                                            <td><?php echo htmlspecialchars($row['category']); ?></td>
                                            <td><?php echo htmlspecialchars($row['uploaded_at']); ?></td>
                                            <td>
                                                <button class="btn btn-danger btn-sm" onclick="confirmDeleteCORCOG('<?php echo htmlspecialchars($row['category']); ?>')">
                                                    <i class="fas fa-trash"></i> Delete
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <script>
        function viewFileGroup(fileGroup) {
            Swal.fire({
                title: 'File Group Details',
                text: 'File Group: ' + fileGroup,
                icon: 'info',
                confirmButtonText: 'OK'
            });
        }

        function confirmDelete(fileGroup) {
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire(
                        'Deleted!',
                        'The record has been deleted.',
                        'success'
                    );
                    // Here you would add the actual delete functionality
                }
            });
        }

        function confirmDeleteCORCOG(category) {
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire(
                        'Deleted!',
                        'The document has been deleted.',
                        'success'
                    );
                    // Here you would add the actual delete functionality
                }
            });
        }
    </script>
</body>
</html>
