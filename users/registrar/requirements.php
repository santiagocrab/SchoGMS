<?php
/**
 * Scholarship requirements overview for registrars.
 */
include 'config/session.php';
require_once __DIR__ . '/../../conn_mongodb.php';
require_once __DIR__ . '/../../config/schogms_helpers.php';

if (($role ?? '') !== 'registrar') {
    header('Location: ../../index.php?ERROR=restricted');
    exit;
}

$requirements = [];
$message = '';

try {
    $col = $mongodb->collection('document_uploads');
    $filter = ['campus' => $sheet_name];
    $docs = $col->find($filter, ['limit' => 200, 'sort' => ['uploaded_at' => -1]]);
    foreach ($docs as $doc) {
        $requirements[] = $doc;
    }
} catch (Throwable $e) {
    schogms_log_error('Requirements load failed: ' . $e->getMessage());
    $message = 'Unable to load requirements at this time. Please try again later.';
}
?>
<!DOCTYPE html>
<html dir="ltr" lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/png" href="../../assets/images/logo.png">
    <title>Requirements — SchoGMS</title>
    <link href="../../dist/css/style.min.css" rel="stylesheet">
    <link href="../../assets/extra-libs/datatables.net-bs4/css/dataTables.bootstrap4.css" rel="stylesheet">
</head>
<body>
<div id="main-wrapper" data-theme="light" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full"
    data-sidebar-position="fixed" data-header-position="fixed" data-boxed-layout="full">
    <header class="topbar" data-navbarbg="skin6">
        <nav class="navbar top-navbar navbar-expand-md">
            <div class="navbar-header" data-logobg="skin6">
                <div class="navbar-brand"><a href="index.php"><img src="../../assets/images/logo.png" style="width:200px" alt="SchoGMS"></a></div>
            </div>
            <div class="navbar-collapse collapse">
                <ul class="navbar-nav float-right">
                    <li class="nav-item"><span class="nav-link text-dark"><?= schogms_e($fullname) ?></span></li>
                    <li class="nav-item"><a class="nav-link" href="logout.php">Logout</a></li>
                </ul>
            </div>
        </nav>
    </header>
    <aside class="left-sidebar" data-sidebarbg="skin6">
        <div class="scroll-sidebar">
            <nav class="sidebar-nav">
                <ul id="sidebarnav">
                    <?php require __DIR__ . '/inc/registrar_sidebar_menu.php'; ?>
                </ul>
            </nav>
        </div>
    </aside>
    <div class="page-wrapper">
        <div class="container-fluid">
            <div class="page-breadcrumb"><nav aria-label="breadcrumb"><ol class="breadcrumb"><li class="breadcrumb-item"><a href="index.php">Dashboard</a></li><li class="breadcrumb-item active">Requirements</li></ol></nav></div>
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Scholarship Requirements — <?= schogms_e($sheet_name) ?></h4>
                    <?php if ($message): ?>
                        <div class="alert alert-warning"><?= schogms_e($message) ?></div>
                    <?php endif; ?>
                    <div class="table-responsive">
                        <table id="reqTable" class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th>Scholar</th>
                                    <th>Category</th>
                                    <th>Document</th>
                                    <th>Status</th>
                                    <th>Uploaded</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php if (empty($requirements)): ?>
                                <tr><td colspan="5" class="text-center text-muted">No scholarship requirements found for this campus.</td></tr>
                            <?php else: ?>
                                <?php foreach ($requirements as $doc): ?>
                                <tr>
                                    <td><?= schogms_e($doc['student_name'] ?? $doc['scholar_name'] ?? '—') ?></td>
                                    <td><?= schogms_e($doc['category'] ?? '—') ?></td>
                                    <td><?= schogms_e($doc['original_name'] ?? $doc['file_name'] ?? '—') ?></td>
                                    <td><?= schogms_status_badge($doc['status'] ?? 'pending') ?></td>
                                    <td><?= schogms_e($doc['uploaded_at'] ?? $doc['created_at'] ?? '—') ?></td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="../../assets/libs/jquery/dist/jquery.min.js"></script>
<script src="../../assets/extra-libs/datatables.net/js/jquery.dataTables.min.js"></script>
<script src="../../assets/extra-libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js"></script>
<script src="../../assets/libs/feather-icons/dist/feather.min.js"></script>
<script>
$(function(){ $('#reqTable').DataTable({ pageLength: 25, order: [[4,'desc']] }); feather.replace(); });
</script>
</body>
</html>
