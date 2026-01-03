<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

// Include MongoDB connection
require '../../conn_mongodb.php';

// Get user details
$user_id = $_SESSION['user_id'];
$usersCollection = $mongodb->collection('users');
$user = $usersCollection->findOne(['user_id' => (int)$user_id]);
$fullname = $user['name'] ?? 'Registrar';
$campus = $user['campus'] ?? 'ISULAN';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>COR/COG Status - SchoGMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
        .header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 20px; margin-bottom: 20px; }
        .card { box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); }
        .table-responsive { max-height: 70vh; overflow-y: auto; }
        .badge { font-size: 0.8em; }
        .stats-card { background: linear-gradient(45deg, #28a745, #20c997); color: white; }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h2 class="mb-0">📊 COR/COG Status Report</h2>
                    <p class="mb-0">Scholarship and Grants Management System - <?= $campus ?> Campus</p>
                </div>
                <div class="col-md-4 text-end">
                    <span class="badge bg-light text-dark">Hello, <?= $fullname ?></span>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid">
        <!-- Statistics -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card stats-card">
                    <div class="card-body text-center">
                        <h3><?php
                        $corDir = 'uploads/COR/';
                        $corCount = 0;
                        if (is_dir($corDir)) {
                            $files = scandir($corDir);
                            $corCount = count($files) - 2; // Subtract . and ..
                        }
                        echo $corCount;
                        ?></h3>
                        <p class="mb-0">COR Files</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card stats-card">
                    <div class="card-body text-center">
                        <h3><?php
                        $cogDir = 'uploads/COG/';
                        $cogCount = 0;
                        if (is_dir($cogDir)) {
                            $files = scandir($cogDir);
                            $cogCount = count($files) - 2; // Subtract . and ..
                        }
                        echo $cogCount;
                        ?></h3>
                        <p class="mb-0">COG Files</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card stats-card">
                    <div class="card-body text-center">
                        <h3><?php
                        $registrarCollection = $mongodb->collection('registrar_master_list');
                        $totalStudents = $registrarCollection->count();
                        echo number_format($totalStudents);
                        ?></h3>
                        <p class="mb-0">Total Students</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card stats-card">
                    <div class="card-body text-center">
                        <h3><?php
                        $documentCollection = $mongodb->collection('document_uploads');
                        $totalDocs = $documentCollection->count();
                        echo number_format($totalDocs);
                        ?></h3>
                        <p class="mb-0">Total Documents</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- COR Files List -->
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0">📄 COR Files (<?= $corCount ?> files)</h5>
                    </div>
                    <div class="card-body">
                        <?php if ($corCount > 0): ?>
                            <div class="table-responsive" style="max-height: 300px;">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>File Name</th>
                                            <th>Size</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $corFiles = scandir($corDir);
                                        foreach ($corFiles as $file) {
                                            if ($file != '.' && $file != '..') {
                                                $filePath = $corDir . $file;
                                                $fileSize = file_exists($filePath) ? filesize($filePath) : 0;
                                                echo "<tr>";
                                                echo "<td><small>" . htmlspecialchars($file) . "</small></td>";
                                                echo "<td><small>" . number_format($fileSize) . " bytes</small></td>";
                                                echo "</tr>";
                                            }
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <p class="text-muted">No COR files found.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">📄 COG Files (<?= $cogCount ?> files)</h5>
                    </div>
                    <div class="card-body">
                        <?php if ($cogCount > 0): ?>
                            <div class="table-responsive" style="max-height: 300px;">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>File Name</th>
                                            <th>Size</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $cogFiles = scandir($cogDir);
                                        foreach ($cogFiles as $file) {
                                            if ($file != '.' && $file != '..') {
                                                $filePath = $cogDir . $file;
                                                $fileSize = file_exists($filePath) ? filesize($filePath) : 0;
                                                echo "<tr>";
                                                echo "<td><small>" . htmlspecialchars($file) . "</small></td>";
                                                echo "<td><small>" . number_format($fileSize) . " bytes</small></td>";
                                                echo "</tr>";
                                            }
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <p class="text-muted">No COG files found.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h5>🚀 Quick Actions</h5>
                        <div class="btn-group" role="group">
                            <a href="final_masterlist.php" class="btn btn-primary">📋 View Masterlist</a>
                            <a href="cor-cog.php" class="btn btn-success">📤 Upload COR/COG</a>
                            <a href="documents_uploaded.php" class="btn btn-info">📄 View All Documents</a>
                            <a href="logout.php" class="btn btn-danger">🚪 Logout</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
