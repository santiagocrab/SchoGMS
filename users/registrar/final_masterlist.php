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
    <title>Registrar Masterlist - SchoGMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
        .header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 20px; margin-bottom: 20px; }
        .card { box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); }
        .table-responsive { max-height: 70vh; overflow-y: auto; }
        .badge { font-size: 0.8em; }
        .filter-section { background: #e9ecef; padding: 15px; border-radius: 8px; margin-bottom: 20px; }
    </style>
</head>
<body>
<?php schogms_loading_screen_once(); ?>

    <!-- Header -->
    <div class="header">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h2 class="mb-0">📋 Registrar Masterlist</h2>
                    <p class="mb-0">Scholarship and Grants Management System - <?= $campus ?> Campus</p>
                </div>
                <div class="col-md-4 text-end">
                    <span class="badge bg-light text-dark">Hello, <?= $fullname ?></span>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid">
        <!-- Filter Section -->
        <div class="filter-section">
            <form method="GET" action="" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Category:</label>
                    <select name="category" class="form-select">
                        <option value="">All Categories</option>
                        <?php
                        try {
                            $registrarCollection = $mongodb->collection('registrar_master_list');
                            $categories = [];
                            $allRecords = $registrarCollection->find([]);
                            foreach ($allRecords as $record) {
                                if (!empty($record['filename'])) {
                                    $categories[$record['filename']] = $record['filename'];
                                }
                            }
                            ksort($categories);
                            $selectedCategory = $_GET['category'] ?? '';
                            foreach ($categories as $cat) {
                                $selected = ($selectedCategory == $cat) ? 'selected' : '';
                                echo "<option value='" . htmlspecialchars($cat) . "' {$selected}>" . htmlspecialchars($cat) . "</option>";
                            }
                        } catch (Exception $e) {
                            echo "<!-- Error: " . $e->getMessage() . " -->";
                        }
                        ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Academic Year:</label>
                    <select name="academic_year" class="form-select">
                        <option value="">All Years</option>
                        <option value="2026-2027" <?= (($_GET['academic_year'] ?? '') == '2026-2027') ? 'selected' : '' ?>>2026-2027</option>
                        <option value="2025-2026" <?= (($_GET['academic_year'] ?? '') == '2025-2026') ? 'selected' : '' ?>>2025-2026</option>
                        <option value="2024-2025" <?= (($_GET['academic_year'] ?? '') == '2024-2025') ? 'selected' : '' ?>>2024-2025</option>
                        <option value="2023-2024" <?= (($_GET['academic_year'] ?? '') == '2023-2024') ? 'selected' : '' ?>>2023-2024</option>
                        <option value="2022-2023" <?= (($_GET['academic_year'] ?? '') == '2022-2023') ? 'selected' : '' ?>>2022-2023</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Semester:</label>
                    <select name="semester" class="form-select">
                        <option value="">All Semesters</option>
                        <option value="1st Semester" <?= (($_GET['semester'] ?? '') == '1st Semester') ? 'selected' : '' ?>>1st Semester</option>
                        <option value="2nd Semester" <?= (($_GET['semester'] ?? '') == '2nd Semester') ? 'selected' : '' ?>>2nd Semester</option>
                        <option value="Summer" <?= (($_GET['semester'] ?? '') == 'Summer') ? 'selected' : '' ?>>Summer</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Records per page:</label>
                    <select name="limit" class="form-select">
                        <option value="10" <?= (($_GET['limit'] ?? '50') == '10') ? 'selected' : '' ?>>10</option>
                        <option value="25" <?= (($_GET['limit'] ?? '50') == '25') ? 'selected' : '' ?>>25</option>
                        <option value="50" <?= (($_GET['limit'] ?? '50') == '50') ? 'selected' : '' ?>>50</option>
                        <option value="100" <?= (($_GET['limit'] ?? '50') == '100') ? 'selected' : '' ?>>100</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">&nbsp;</label><br>
                    <button type="submit" class="btn btn-primary">🔍 Apply Filter</button>
                    <a href="final_masterlist.php" class="btn btn-secondary">🔄 Clear</a>
                </div>
            </form>
        </div>

        <!-- Results -->
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">📊 Student Records</h5>
            </div>
            <div class="card-body">
                <?php
                try {
                    // Build filter
                    $filter = [];
                    if (!empty($_GET['category'])) $filter['filename'] = $_GET['category'];
                    if (!empty($_GET['academic_year'])) $filter['academic_year'] = $_GET['academic_year'];
                    if (!empty($_GET['semester'])) $filter['semester'] = $_GET['semester'];
                    
                    // Get limit
                    $limit = (int)($_GET['limit'] ?? 50);
                    
                    // Get total count
                    $totalCount = $registrarCollection->count($filter);
                    
                    // Get records
                    $records = $registrarCollection->find($filter, ['limit' => $limit, 'sort' => ['last_name' => 1]]);
                    
                    echo "<div class='alert alert-info mb-3'>";
                    echo "<strong>📊 Total Records:</strong> " . number_format($totalCount) . " students found";
                    echo "</div>";
                    
                    if ($totalCount == 0) {
                        echo "<div class='alert alert-warning'>No records found matching your criteria.</div>";
                    } else {
                        echo "<div class='table-responsive'>";
                        echo "<table class='table table-striped table-hover'>";
                        echo "<thead class='table-dark'>";
                        echo "<tr>";
                        echo "<th>COR/COG</th>";
                        echo "<th>Last Name</th>";
                        echo "<th>First Name</th>";
                        echo "<th>Middle Name</th>";
                        echo "<th>ID Number</th>";
                        echo "<th>Course</th>";
                        echo "<th>Year Level</th>";
                        echo "<th>Gender</th>";
                        echo "<th>Student Type</th>";
                        echo "</tr>";
                        echo "</thead>";
                        echo "<tbody>";
                        
                        $displayCount = 0;
                        foreach ($records as $row) {
                            $displayCount++;
                            echo "<tr>";
                            
                            // COR/COG Status
                            echo "<td>";
                            $documentCollection = $mongodb->collection('document_uploads');
                            $studentName = trim($row['last_name'] . ', ' . $row['first_name'] . ' ' . ($row['middle_name'] ?? ''));
                            
                            $hasCOR = false;
                            $hasCOG = false;
                            
                            // Check if COR files exist in uploads/COR/ directory
                            $corDir = 'uploads/COR/';
                            if (is_dir($corDir)) {
                                $corFiles = scandir($corDir);
                                foreach ($corFiles as $file) {
                                    if ($file != '.' && $file != '..' && is_file($corDir . $file)) {
                                        $hasCOR = true;
                                        break;
                                    }
                                }
                            }
                            
                            // Check if COG files exist in uploads/COG/ directory
                            $cogDir = 'uploads/COG/';
                            if (is_dir($cogDir)) {
                                $cogFiles = scandir($cogDir);
                                foreach ($cogFiles as $file) {
                                    if ($file != '.' && $file != '..' && is_file($cogDir . $file)) {
                                        $hasCOG = true;
                                        break;
                                    }
                                }
                            }
                            
                            if ($hasCOR && $hasCOG) {
                                echo '<span class="badge bg-success">COR</span> <span class="badge bg-primary">COG</span>';
                            } elseif ($hasCOR) {
                                echo '<span class="badge bg-success">COR</span>';
                            } elseif ($hasCOG) {
                                echo '<span class="badge bg-primary">COG</span>';
                            } else {
                                echo '<span class="badge bg-secondary">None</span>';
                            }
                            echo "</td>";
                            
                            echo "<td>" . htmlspecialchars($row['last_name'] ?? '') . "</td>";
                            echo "<td>" . htmlspecialchars($row['first_name'] ?? '') . "</td>";
                            echo "<td>" . htmlspecialchars($row['middle_name'] ?? '') . "</td>";
                            echo "<td>" . htmlspecialchars($row['id_number'] ?? '') . "</td>";
                            echo "<td>" . htmlspecialchars($row['course'] ?? '') . "</td>";
                            echo "<td>" . htmlspecialchars($row['year_level'] ?? '') . "</td>";
                            echo "<td>" . htmlspecialchars($row['gender'] ?? '') . "</td>";
                            echo "<td>" . htmlspecialchars($row['student_type'] ?? '') . "</td>";
                            echo "</tr>";
                        }
                        
                        echo "</tbody>";
                        echo "</table>";
                        echo "</div>";
                        
                        echo "<div class='alert alert-success mt-3'>";
                        echo "<strong>✅ Success!</strong> Displaying {$displayCount} records out of " . number_format($totalCount) . " total records.";
                        echo "</div>";
                    }
                    
                } catch (Exception $e) {
                    echo "<div class='alert alert-danger'>";
                    echo "<strong>❌ Error:</strong> " . htmlspecialchars($e->getMessage());
                    echo "</div>";
                }
                ?>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h5>🚀 Quick Actions</h5>
                        <div class="btn-group" role="group">
                            <a href="cor-cog.php" class="btn btn-success">📤 COR & COG Upload</a>
                            <a href="documents_uploaded.php" class="btn btn-info">📄 View Documents</a>
                            <a href="masterlist.php" class="btn btn-secondary">📋 Original Masterlist</a>
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
