<?php
/**
 * Fast Registrar Masterlist Page
 * Optimized with pagination and caching
 */

require_once 'conn.php';

// Get pagination parameters
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$limit = isset($_GET['limit']) ? max(10, min(100, intval($_GET['limit']))) : 50;
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$campus = isset($_GET['campus']) ? trim($_GET['campus']) : '';

// Build filter
$filter = [];
if (!empty($campus)) {
    $filter['campus'] = $campus;
}

// Get data
$start = microtime(true);
if (!empty($search)) {
    $results = $dbHelper->searchRegistrarMasterlist($search, $page, $limit);
} else {
    $results = $dbHelper->getRegistrarMasterlistPaginated($filter, $page, $limit);
}
$end = microtime(true);
$loadTime = round(($end - $start) * 1000, 2);

// Get campuses for filter
$campuses = $dbHelper->getCampuses();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Registrar Masterlist - Fast Version</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .performance-info {
            background: #e8f5e8;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .table-responsive {
            max-height: 600px;
            overflow-y: auto;
        }
    </style>
</head>
<body>
    <div class="container-fluid mt-4">
        <h2>Registrar Masterlist - Fast Version</h2>
        
        <!-- Performance Info -->
        <div class="performance-info">
            <strong>Performance:</strong> Loaded in <?php echo $loadTime; ?>ms | 
            Showing <?php echo count($results['data']); ?> of <?php echo $results['total']; ?> records | 
            Page <?php echo $results['page']; ?> of <?php echo $results['pages']; ?>
        </div>
        
        <!-- Search and Filter -->
        <div class="row mb-3">
            <div class="col-md-4">
                <form method="GET" class="d-flex">
                    <input type="text" name="search" class="form-control" placeholder="Search..." value="<?php echo htmlspecialchars($search); ?>">
                    <button type="submit" class="btn btn-primary ms-2">Search</button>
                </form>
            </div>
            <div class="col-md-3">
                <form method="GET" class="d-flex">
                    <select name="campus" class="form-select">
                        <option value="">All Campuses</option>
                        <?php foreach ($campuses as $campusOption): ?>
                            <option value="<?php echo htmlspecialchars($campusOption['campus_name']); ?>" 
                                    <?php echo $campus === $campusOption['campus_name'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($campusOption['campus_name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <button type="submit" class="btn btn-secondary ms-2">Filter</button>
                </form>
            </div>
            <div class="col-md-2">
                <form method="GET" class="d-flex">
                    <select name="limit" class="form-select" onchange="this.form.submit()">
                        <option value="25" <?php echo $limit == 25 ? 'selected' : ''; ?>>25 per page</option>
                        <option value="50" <?php echo $limit == 50 ? 'selected' : ''; ?>>50 per page</option>
                        <option value="100" <?php echo $limit == 100 ? 'selected' : ''; ?>>100 per page</option>
                    </select>
                    <input type="hidden" name="page" value="1">
                    <input type="hidden" name="search" value="<?php echo htmlspecialchars($search); ?>">
                    <input type="hidden" name="campus" value="<?php echo htmlspecialchars($campus); ?>">
                </form>
            </div>
        </div>
        
        <!-- Results Table -->
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead class="table-dark sticky-top">
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>ID Number</th>
                        <th>Course</th>
                        <th>Campus</th>
                        <th>Year Level</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($results['data'])): ?>
                        <?php foreach ($results['data'] as $record): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($record['id'] ?? ''); ?></td>
                                <td><?php echo htmlspecialchars(($record['last_name'] ?? '') . ', ' . ($record['first_name'] ?? '')); ?></td>
                                <td><?php echo htmlspecialchars($record['id_number'] ?? ''); ?></td>
                                <td><?php echo htmlspecialchars($record['course'] ?? ''); ?></td>
                                <td><?php echo htmlspecialchars($record['campus'] ?? ''); ?></td>
                                <td><?php echo htmlspecialchars($record['year_level'] ?? ''); ?></td>
                                <td><?php echo htmlspecialchars($record['enrolled'] ?? ''); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="text-center">No records found</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <?php if ($results['pages'] > 1): ?>
            <nav aria-label="Page navigation">
                <ul class="pagination justify-content-center">
                    <!-- Previous Page -->
                    <?php if ($results['page'] > 1): ?>
                        <li class="page-item">
                            <a class="page-link" href="?page=<?php echo $results['page'] - 1; ?>&limit=<?php echo $limit; ?>&search=<?php echo urlencode($search); ?>&campus=<?php echo urlencode($campus); ?>">Previous</a>
                        </li>
                    <?php endif; ?>
                    
                    <!-- Page Numbers -->
                    <?php
                    $startPage = max(1, $results['page'] - 2);
                    $endPage = min($results['pages'], $results['page'] + 2);
                    
                    for ($i = $startPage; $i <= $endPage; $i++):
                    ?>
                        <li class="page-item <?php echo $i == $results['page'] ? 'active' : ''; ?>">
                            <a class="page-link" href="?page=<?php echo $i; ?>&limit=<?php echo $limit; ?>&search=<?php echo urlencode($search); ?>&campus=<?php echo urlencode($campus); ?>"><?php echo $i; ?></a>
                        </li>
                    <?php endfor; ?>
                    
                    <!-- Next Page -->
                    <?php if ($results['page'] < $results['pages']): ?>
                        <li class="page-item">
                            <a class="page-link" href="?page=<?php echo $results['page'] + 1; ?>&limit=<?php echo $limit; ?>&search=<?php echo urlencode($search); ?>&campus=<?php echo urlencode($campus); ?>">Next</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </nav>
        <?php endif; ?>
        
        <!-- Back to Main -->
        <div class="mt-4">
            <a href="index.php" class="btn btn-secondary">Back to Main</a>
            <a href="test_performance.php" class="btn btn-info">Performance Test</a>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
