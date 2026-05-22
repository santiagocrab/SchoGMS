<?php
/**
 * Program list (read-only) for registrar — TDP and TES file groups.
 */
include 'config/session.php';
require_once __DIR__ . '/../../config/schogms_helpers.php';
include 'config/conn.php';

$tdpRows = [];
$tesRows = [];
$error = '';

$campus = $conn->real_escape_string($sheet_name ?? '');

$tdpQuery = "SELECT file_group, filename, COUNT(*) AS total_entries
    FROM ched_masterlist
    WHERE sheet_name = '$campus'
    GROUP BY file_group, filename
    ORDER BY file_group ASC
    LIMIT 500";

$tesQuery = "SELECT file_group, filename, COUNT(*) AS total_entries
    FROM ched_masterlist_tes
    WHERE campus = '$campus'
    GROUP BY file_group, filename
    ORDER BY file_group ASC
    LIMIT 500";

$tdpResult = @$conn->query($tdpQuery);
if ($tdpResult) {
    while ($row = $tdpResult->fetch_assoc()) {
        $tdpRows[] = $row;
    }
} else {
    $error = 'Unable to load TDP program list.';
}

$tesResult = @$conn->query($tesQuery);
if ($tesResult) {
    while ($row = $tesResult->fetch_assoc()) {
        $tesRows[] = $row;
    }
}
?>
<!DOCTYPE html>
<html dir="ltr" lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/png" href="../../assets/images/logo.png">
    <title>Program List — SchoGMS</title>
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
            <div class="page-breadcrumb"><nav aria-label="breadcrumb"><ol class="breadcrumb"><li class="breadcrumb-item"><a href="index.php">Dashboard</a></li><li class="breadcrumb-item active">Program List</li></ol></nav></div>
            <?php if ($error): ?><div class="alert alert-warning"><?= schogms_e($error) ?></div><?php endif; ?>
            <div class="row">
                <div class="col-12 mb-4">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">CHED TDP Programs — <?= schogms_e($sheet_name) ?></h4>
                            <div class="table-responsive">
                                <table id="tdpTable" class="table table-striped table-bordered">
                                    <thead><tr><th>File Group</th><th>Filename</th><th>Total Entries</th></tr></thead>
                                    <tbody>
                                    <?php if (empty($tdpRows)): ?>
                                        <tr><td colspan="3" class="text-center text-muted">No TDP program files found.</td></tr>
                                    <?php else: foreach ($tdpRows as $row): ?>
                                        <tr>
                                            <td><?= schogms_e($row['file_group']) ?></td>
                                            <td><?= schogms_e($row['filename']) ?></td>
                                            <td><?= (int) $row['total_entries'] ?></td>
                                        </tr>
                                    <?php endforeach; endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">CHED TES Programs — <?= schogms_e($sheet_name) ?></h4>
                            <div class="table-responsive">
                                <table id="tesTable" class="table table-striped table-bordered">
                                    <thead><tr><th>File Group</th><th>Filename</th><th>Total Entries</th></tr></thead>
                                    <tbody>
                                    <?php if (empty($tesRows)): ?>
                                        <tr><td colspan="3" class="text-center text-muted">No TES program files found.</td></tr>
                                    <?php else: foreach ($tesRows as $row): ?>
                                        <tr>
                                            <td><?= schogms_e($row['file_group']) ?></td>
                                            <td><?= schogms_e($row['filename']) ?></td>
                                            <td><?= (int) $row['total_entries'] ?></td>
                                        </tr>
                                    <?php endforeach; endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
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
<script>$(function(){ $('#tdpTable,#tesTable').DataTable({pageLength:25}); feather.replace(); });</script>
</body>
</html>
