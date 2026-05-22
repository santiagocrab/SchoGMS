<?php
/**
 * Scholarship requirements overview for coordinators.
 */
require __DIR__ . '/../config/session.php';
require_once __DIR__ . '/inc/coordinator_data.php';

$requirements = [];
$message = '';
$campusFilter = trim((string) ($sheet_name ?? ''));

$cor = schogms_coordinator_documents($conn, $campusFilter, 'COR');
$cog = schogms_coordinator_documents($conn, $campusFilter, 'COG');
if ($cor['error'] !== '' || $cog['error'] !== '') {
    $message = $cor['error'] !== '' ? $cor['error'] : $cog['error'];
}
foreach (array_merge($cor['rows'], $cog['rows']) as $row) {
    $requirements[] = $row;
}
usort($requirements, static function ($a, $b) {
    return strcmp((string) ($b['uploaded_at'] ?? ''), (string) ($a['uploaded_at'] ?? ''));
});
?>
<!DOCTYPE html>
<html dir="ltr" lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/png" href="../../assets/images/logo.png">
    <title>Requirements — SchoGMS</title>
    <?php require_once __DIR__ . '/inc/assets.php'; schogms_coordinator_head(true); ?>
</head>
<body>
<?php schogms_loading_screen_once(); ?>

<?php require_once __DIR__ . '/inc/coordinator_nav.php'; schogms_coordinator_shell_open('Requirements'); ?>
        <div class="container-fluid">
            <div class="page-breadcrumb"><nav aria-label="breadcrumb"><ol class="breadcrumb"><li class="breadcrumb-item"><a href="index.php">Dashboard</a></li><li class="breadcrumb-item active">Requirements</li></ol></nav></div>
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Scholarship Requirements — <?= schogms_e($sheet_name) ?></h4>
                    <?php if ($message): ?>
                        <div class="alert alert-warning"><?= schogms_e($message) ?></div>
                    <?php endif; ?>
                    <?php if (empty($requirements)): ?>
                        <div class="alert alert-info mb-0">No COR/COG documents found for this campus.</div>
                    <?php else: ?>
                    <p class="text-muted small mb-2"><?= count($requirements) ?> document(s) — use the table footer to change page size or search.</p>
                    <div class="table-responsive">
                        <table id="zero_config" class="table table-striped table-bordered no-wrap">
                            <thead>
                                <tr>
                                    <th>Campus</th>
                                    <th>File Group</th>
                                    <th>Category</th>
                                    <th>Document</th>
                                    <th>Uploaded</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($requirements as $doc): ?>
                                <tr>
                                    <td><?= schogms_e($doc['campus'] ?? '—') ?></td>
                                    <td><?= schogms_e($doc['file_group'] ?? '—') ?></td>
                                    <td><?= schogms_e($doc['category'] ?? '—') ?></td>
                                    <td><?= schogms_e($doc['file_name'] ?? '—') ?></td>
                                    <td><?= schogms_e($doc['uploaded_at'] ?? '—') ?></td>
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
</div>
<?php require_once __DIR__ . '/inc/assets.php'; schogms_coordinator_footer_scripts(['datatables' => true]); ?>
</body>
</html>
