<?php
require __DIR__ . '/config/session.php';
require_once __DIR__ . '/../../inc/schogms_file_group_meta.php';

$totalTdp = 0;
$totalTes = 0;
$pendingFileGroups = 0;

$r = $conn->query('SELECT COUNT(*) AS n FROM ched_masterlist');
if ($r) {
    $totalTdp = (int) ($r->fetch_assoc()['n'] ?? 0);
}
$r = $conn->query('SELECT COUNT(*) AS n FROM ched_masterlist_tes');
if ($r) {
    $totalTes = (int) ($r->fetch_assoc()['n'] ?? 0);
}
schogms_file_group_meta_ensure_table($conn);
$r = $conn->query("SELECT COUNT(*) AS n FROM schogms_file_group_batches WHERE status = 'pending'");
if ($r) {
    $pendingFileGroups = (int) ($r->fetch_assoc()['n'] ?? 0);
}
?>
<!DOCTYPE html>
<html dir="ltr" lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/png" href="../../assets/images/logo.png">
    <title>Dashboard — SchoGMS Chairman</title>
    <?php require_once __DIR__ . '/inc/assets.php'; schogms_chairman_head(false); ?>
</head>
<body>
<?php schogms_loading_screen_once(); ?>

<?php require_once __DIR__ . '/inc/chairman_nav.php'; schogms_chairman_shell_open('Chairman dashboard'); ?>
        <div class="container-fluid">
            <div class="page-breadcrumb">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb m-0 p-0">
                        <li class="breadcrumb-item active">Dashboard</li>
                    </ol>
                </nav>
            </div>

            <div class="card-group">
                <div class="card border-right">
                    <div class="card-body">
                        <h2 class="text-dark mb-1 font-weight-medium"><?= number_format($totalTdp) ?></h2>
                        <h6 class="text-muted font-weight-normal mb-0">TDP scholars (all campuses)</h6>
                    </div>
                </div>
                <div class="card border-right">
                    <div class="card-body">
                        <h2 class="text-dark mb-1 font-weight-medium"><?= number_format($totalTes) ?></h2>
                        <h6 class="text-muted font-weight-normal mb-0">TES scholars (all campuses)</h6>
                    </div>
                </div>
                <div class="card border-right">
                    <div class="card-body">
                        <h2 class="text-dark mb-1 font-weight-medium"><?= number_format($pendingFileGroups) ?></h2>
                        <h6 class="text-muted font-weight-normal mb-0">File groups pending review</h6>
                        <?php if ($pendingFileGroups > 0): ?>
                            <a href="file_groups.php?status=pending" class="btn btn-sm btn-primary mt-2">Review now</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-body">
                    <h4 class="card-title">Quick links</h4>
                    <p class="text-muted small mb-3">System-wide oversight: approve coordinator file group uploads and browse masterlists.</p>
                    <a href="file_groups.php?status=pending" class="btn btn-primary mr-2 mb-2">Review file groups</a>
                    <a href="ched_masterlist.php" class="btn btn-outline-primary mr-2 mb-2">TDP masterlist</a>
                    <a href="ched_masterlist_tes.php" class="btn btn-outline-primary mb-2">TES masterlist</a>
                </div>
            </div>
        </div>
<?php
schogms_chairman_shell_close();
require_once __DIR__ . '/inc/assets.php';
schogms_chairman_footer_scripts([]);
?>
</body>
</html>
