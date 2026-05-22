<?php include 'config/session.php'; ?>
<!DOCTYPE html>
<html dir="ltr" lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Registrar Masterlist - SchoGMS</title>
    <link href="../../assets/libs/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
        .card { box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075); }
        .table-responsive { max-height: 600px; overflow-y: auto; }
        .badge { font-size: 0.75em; }
        .btn-sm { padding: 0.25rem 0.5rem; font-size: 0.875rem; }
    </style>
</head>
<body>
<?php schogms_loading_screen_once(); ?>

    <div class="container-fluid mt-4">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0">📋 Registrar Masterlist - ISULAN Campus</h4>
                        <small>Total Records: <span id="totalRecords">Loading...</span></small>
                    </div>
                    <div class="card-body">
                        <!-- Filter Section -->
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <form method="GET" action="" class="row">
                                    <div class="col-md-3">
                                        <label for="categoryFilter">Category:</label>
                                        <select id="categoryFilter" name="category" class="form-control form-control-sm">
                                            <option value="">All Categories</option>
                                            <?php
                                            try {
                                                require '../../conn_mongodb.php';
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
                                        <label for="academicYearFilter">Academic Year:</label>
                                        <select id="academicYearFilter" name="academic_year" class="form-control form-control-sm">
                                            <option value="">All Years</option>
                                            <option value="2026-2027" <?= (($_GET['academic_year'] ?? '') == '2026-2027') ? 'selected' : '' ?>>2026-2027</option>
                                            <option value="2025-2026" <?= (($_GET['academic_year'] ?? '') == '2025-2026') ? 'selected' : '' ?>>2025-2026</option>
                                            <option value="2024-2025" <?= (($_GET['academic_year'] ?? '') == '2024-2025') ? 'selected' : '' ?>>2024-2025</option>
                                            <option value="2023-2024" <?= (($_GET['academic_year'] ?? '') == '2023-2024') ? 'selected' : '' ?>>2023-2024</option>
                                            <option value="2022-2023" <?= (($_GET['academic_year'] ?? '') == '2022-2023') ? 'selected' : '' ?>>2022-2023</option>
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <label for="semesterFilter">Semester:</label>
                                        <select id="semesterFilter" name="semester" class="form-control form-control-sm">
                                            <option value="">All Semesters</option>
                                            <option value="1st Semester" <?= (($_GET['semester'] ?? '') == '1st Semester') ? 'selected' : '' ?>>1st Semester</option>
                                            <option value="2nd Semester" <?= (($_GET['semester'] ?? '') == '2nd Semester') ? 'selected' : '' ?>>2nd Semester</option>
                                            <option value="Summer" <?= (($_GET['semester'] ?? '') == 'Summer') ? 'selected' : '' ?>>Summer</option>
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <label for="limitFilter">Records per page:</label>
                                        <select id="limitFilter" name="limit" class="form-control form-control-sm">
                                            <option value="10" <?= (($_GET['limit'] ?? '50') == '10') ? 'selected' : '' ?>>10</option>
                                            <option value="25" <?= (($_GET['limit'] ?? '50') == '25') ? 'selected' : '' ?>>25</option>
                                            <option value="50" <?= (($_GET['limit'] ?? '50') == '50') ? 'selected' : '' ?>>50</option>
                                            <option value="100" <?= (($_GET['limit'] ?? '50') == '100') ? 'selected' : '' ?>>100</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label>&nbsp;</label><br>
                                        <button type="submit" class="btn btn-primary btn-sm">🔍 Apply Filter</button>
                                        <a href="simple_masterlist.php" class="btn btn-secondary btn-sm">🔄 Clear</a>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <!-- Results Table -->
                        <div class="table-responsive">
                            <table class="table table-striped table-hover table-sm">
                                <thead class="table-dark">
                                    <tr>
                                        <th>COR/COG</th>
                                        <th>Last Name</th>
                                        <th>First Name</th>
                                        <th>Middle Name</th>
                                        <th>ID Number</th>
                                        <th>Course</th>
                                        <th>Year Level</th>
                                        <th>Gender</th>
                                        <th>Student Type</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    try {
                                        // Build filter
                                        $filter = [];
                                        if (!empty($_GET['category'])) $filter['filename'] = $_GET['category'];
                                        if (!empty($_GET['academic_year'])) $filter['academic_year'] = $_GET['academic_year'];
                                        if (!empty($_GET['semester'])) $filter['semester'] = $_GET['semester'];
                                        
                                        // Pagination
                                        $page = (int)($_GET['page'] ?? 1);
                                        $limit = (int)($_GET['limit'] ?? 50);
                                        
                                        // Get data
                                        $result = $dbHelper->getRegistrarMasterlistPaginated($filter, $page, $limit);
                                        $registrarData = $result['data'];
                                        $totalRecords = $result['total'];
                                        $totalPages = $result['pages'];
                                        
                                        echo "<script>document.getElementById('totalRecords').textContent = '{$totalRecords}';</script>";
                                        
                                        if (empty($registrarData)) {
                                            echo "<tr><td colspan='10' class='text-center text-muted'>No records found matching your criteria</td></tr>";
                                        } else {
                                            foreach ($registrarData as $row) {
                                                echo "<tr>";
                                                
                                                // COR/COG Status
                                                echo "<td>";
                                                $documentCollection = $mongodb->collection('document_uploads');
                                                $studentName = trim($row['last_name'] . ', ' . $row['first_name'] . ' ' . ($row['middle_name'] ?? ''));
                                                $documents = $documentCollection->find(['file_name' => ['$regex' => preg_quote($studentName), '$options' => 'i']]);
                                                
                                                $hasCOR = false;
                                                $hasCOG = false;
                                                foreach ($documents as $doc) {
                                                    if (isset($doc['category'])) {
                                                        if (strpos($doc['category'], 'COR') !== false) $hasCOR = true;
                                                        if (strpos($doc['category'], 'COG') !== false) $hasCOG = true;
                                                    }
                                                }
                                                
                                                if ($hasCOR && $hasCOG) {
                                                    echo '<span class="badge badge-success">COR</span> <span class="badge badge-primary">COG</span>';
                                                } elseif ($hasCOR) {
                                                    echo '<span class="badge badge-success">COR</span>';
                                                } elseif ($hasCOG) {
                                                    echo '<span class="badge badge-primary">COG</span>';
                                                } else {
                                                    echo '<span class="badge badge-secondary">None</span>';
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
                                                echo "<td>";
                                                echo "<button class='btn btn-sm btn-info' onclick='viewStudent(\"" . htmlspecialchars($row['_id']) . "\")'>👁️ View</button>";
                                                echo "</td>";
                                                echo "</tr>";
                                            }
                                        }
                                    } catch (Exception $e) {
                                        echo "<tr><td colspan='10' class='text-center text-danger'>Error: " . htmlspecialchars($e->getMessage()) . "</td></tr>";
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <?php if ($totalPages > 1): ?>
                        <nav aria-label="Page navigation">
                            <ul class="pagination pagination-sm justify-content-center">
                                <?php if ($page > 1): ?>
                                    <li class="page-item">
                                        <a class="page-link" href="?<?= http_build_query(array_merge($_GET, ['page' => $page - 1])) ?>">Previous</a>
                                    </li>
                                <?php endif; ?>
                                
                                <?php for ($i = max(1, $page - 2); $i <= min($totalPages, $page + 2); $i++): ?>
                                    <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
                                        <a class="page-link" href="?<?= http_build_query(array_merge($_GET, ['page' => $i])) ?>"><?= $i ?></a>
                                    </li>
                                <?php endfor; ?>
                                
                                <?php if ($page < $totalPages): ?>
                                    <li class="page-item">
                                        <a class="page-link" href="?<?= http_build_query(array_merge($_GET, ['page' => $page + 1])) ?>">Next</a>
                                    </li>
                                <?php endif; ?>
                            </ul>
                        </nav>
                        <?php endif; ?>

                        <!-- Quick Actions -->
                        <div class="row mt-3">
                            <div class="col-12">
                                <div class="btn-group" role="group">
                                    <a href="cor-cog.php" class="btn btn-success">📤 COR & COG Upload</a>
                                    <a href="documents_uploaded.php" class="btn btn-info">📄 View Documents</a>
                                    <a href="masterlist.php" class="btn btn-secondary">📋 Full Masterlist</a>
                                    <a href="logout.php" class="btn btn-danger">🚪 Logout</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="../../assets/libs/jquery/dist/jquery.min.js"></script>
    <script src="../../assets/libs/bootstrap/dist/js/bootstrap.min.js"></script>
    <script>
        function viewStudent(id) {
            alert('Student ID: ' + id + '\n\nThis would show detailed student information.');
        }
        
        // Auto-submit form when limit changes
        $('#limitFilter').on('change', function() {
            var form = $(this).closest('form');
            var pageInput = $('<input>').attr({
                type: 'hidden',
                name: 'page',
                value: '1'
            });
            form.append(pageInput);
            form.submit();
        });
    </script>
</body>
</html>
