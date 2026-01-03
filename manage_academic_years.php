<?php
/**
 * Academic Year Management
 * Allows adding, editing, and managing academic years
 */

require_once 'conn_mongodb.php';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'add':
                $academicYear = $_POST['academic_year'];
                $description = $_POST['description'] ?? '';
                
                // Check if academic year already exists
                $existing = $mongodb->collection('academic_years')->findOne(['academic_year' => $academicYear]);
                
                if (!$existing) {
                    $result = $mongodb->collection('academic_years')->insertOne([
                        'academic_year' => $academicYear,
                        'description' => $description,
                        'is_active' => true,
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s')
                    ]);
                    
                    if ($result) {
                        $message = "Academic year '{$academicYear}' added successfully!";
                        $messageType = 'success';
                    } else {
                        $message = "Failed to add academic year.";
                        $messageType = 'error';
                    }
                } else {
                    $message = "Academic year '{$academicYear}' already exists!";
                    $messageType = 'error';
                }
                break;
                
            case 'delete':
                $academicYear = $_POST['academic_year'];
                $result = $mongodb->collection('academic_years')->deleteOne(['academic_year' => $academicYear]);
                
                if ($result && $result['deletedCount'] > 0) {
                    $message = "Academic year '{$academicYear}' deleted successfully!";
                    $messageType = 'success';
                } else {
                    $message = "Failed to delete academic year.";
                    $messageType = 'error';
                }
                break;
        }
    }
}

// Get all academic years
$academicYears = $mongodb->collection('academic_years')->find([], ['sort' => ['academic_year' => -1]]);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Academic Years - SchoGMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .academic-year-card {
            border-left: 4px solid #007bff;
        }
        .academic-year-card.inactive {
            border-left-color: #6c757d;
            opacity: 0.7;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <div class="row">
            <div class="col-md-8">
                <h2>📚 Manage Academic Years</h2>
                
                <?php if (isset($message)): ?>
                    <div class="alert alert-<?= $messageType === 'success' ? 'success' : 'danger' ?> alert-dismissible fade show">
                        <?= htmlspecialchars($message) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>
                
                <!-- Add New Academic Year Form -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5>➕ Add New Academic Year</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST">
                            <input type="hidden" name="action" value="add">
                            <div class="row">
                                <div class="col-md-6">
                                    <label for="academic_year" class="form-label">Academic Year</label>
                                    <input type="text" class="form-control" id="academic_year" name="academic_year" 
                                           placeholder="e.g., 2025-2026" pattern="\d{4}-\d{4}" required>
                                    <div class="form-text">Format: YYYY-YYYY (e.g., 2025-2026)</div>
                                </div>
                                <div class="col-md-6">
                                    <label for="description" class="form-label">Description (Optional)</label>
                                    <input type="text" class="form-control" id="description" name="description" 
                                           placeholder="e.g., Current Academic Year">
                                </div>
                            </div>
                            <div class="mt-3">
                                <button type="submit" class="btn btn-primary">Add Academic Year</button>
                            </div>
                        </form>
                    </div>
                </div>
                
                <!-- List of Academic Years -->
                <div class="card">
                    <div class="card-header">
                        <h5>📋 Current Academic Years</h5>
                    </div>
                    <div class="card-body">
                        <?php if (iterator_count($academicYears) > 0): ?>
                            <div class="row">
                                <?php foreach ($academicYears as $ay): ?>
                                    <div class="col-md-6 mb-3">
                                        <div class="card academic-year-card <?= !$ay['is_active'] ? 'inactive' : '' ?>">
                                            <div class="card-body">
                                                <h6 class="card-title"><?= htmlspecialchars($ay['academic_year']) ?></h6>
                                                <?php if (!empty($ay['description'])): ?>
                                                    <p class="card-text text-muted"><?= htmlspecialchars($ay['description']) ?></p>
                                                <?php endif; ?>
                                                <small class="text-muted">
                                                    Created: <?= date('M d, Y', strtotime($ay['created_at'])) ?>
                                                </small>
                                                <div class="mt-2">
                                                    <form method="POST" style="display: inline;" 
                                                          onsubmit="return confirm('Are you sure you want to delete this academic year?')">
                                                        <input type="hidden" name="action" value="delete">
                                                        <input type="hidden" name="academic_year" value="<?= htmlspecialchars($ay['academic_year']) ?>">
                                                        <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <div class="text-center text-muted">
                                <p>No academic years found. Add one above!</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h5>ℹ️ Information</h5>
                    </div>
                    <div class="card-body">
                        <h6>How to Add Academic Years:</h6>
                        <ol>
                            <li>Enter the academic year in format: <code>YYYY-YYYY</code></li>
                            <li>Optionally add a description</li>
                            <li>Click "Add Academic Year"</li>
                        </ol>
                        
                        <h6 class="mt-3">Examples:</h6>
                        <ul>
                            <li><code>2025-2026</code> - Next academic year</li>
                            <li><code>2024-2025</code> - Current academic year</li>
                            <li><code>2023-2024</code> - Previous academic year</li>
                        </ul>
                        
                        <div class="mt-3">
                            <a href="users/registrar/masterlist.php" class="btn btn-secondary">Back to Masterlist</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
