<?php
/**
 * View all scholars in a CHED file group (registrar).
 */
include 'config/session.php';
require_once __DIR__ . '/inc/registrar_data.php';
require_once __DIR__ . '/inc/registrar_nav.php';
require_once __DIR__ . '/../../inc/schogms_file_group_view.php';

$p = schogms_file_group_view_params();
$program = $p['program'] === 'tes' ? 'tes' : 'tdp';
$fileGroup = $p['file_group'];
$filename = $p['filename'];
$campus = $p['campus'] !== '' ? $p['campus'] : trim((string) ($sheet_name ?? ''));

$data = ['meta' => [], 'summary' => [], 'rows' => [], 'error' => ''];
try {
    $db = schogms_registrar_db();
    $data = schogms_file_group_view_fetch($program, $fileGroup, $campus, $filename !== '' ? $filename : null, $db);
} catch (Throwable $e) {
    $data['error'] = 'Database unavailable.';
    schogms_log_error('file_group_view: ' . $e->getMessage());
}

$label = (string) ($data['meta']['label'] ?? strtoupper($program));
$pageTitle = $label . ' file group';
?>
<!DOCTYPE html>
<html dir="ltr" lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/png" href="../../assets/images/logo.png">
    <title><?= schogms_e($pageTitle) ?> — SchoGMS</title>
    <?php require_once __DIR__ . '/inc/assets.php'; schogms_registrar_head(true); ?>
</head>
<body>
<?php schogms_loading_screen_once(); ?>
<?php schogms_registrar_shell_open($pageTitle); ?>

<div class="page-breadcrumb">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb m-0 p-0">
            <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="program_list.php">Program list</a></li>
            <li class="breadcrumb-item active"><?= schogms_e($fileGroup) ?></li>
        </ol>
    </nav>
</div>

<div class="container-fluid">
    <?php if ($data['error'] !== ''): ?>
        <div class="alert alert-warning"><?= schogms_e($data['error']) ?></div>
    <?php else: ?>
        <?php $sum = $data['summary']; ?>
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-body">
                <h4 class="card-title mb-1"><?= schogms_e($label) ?> — <?= schogms_e($fileGroup) ?></h4>
                <p class="text-muted mb-2 small">
                    Campus: <strong><?= schogms_e((string) ($sum['campus'] ?? '')) ?></strong>
                    <?php if ($filename !== ''): ?>
                        · File: <strong><?= schogms_e($filename) ?></strong>
                    <?php endif; ?>
                </p>
                <p class="mb-0"><?= schogms_e(schogms_file_group_summary_text($sum)) ?></p>
                <?php if (!empty($sum['programs_summary'])): ?>
                    <p class="small text-muted mt-2 mb-0"><strong>Programs:</strong> <?= schogms_e((string) $sum['programs_summary']) ?></p>
                <?php endif; ?>
            </div>
        </div>
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table id="fgScholarsTable" class="table table-striped table-bordered table-sm w-100">
                        <thead>
                            <tr>
                                <th>Last name</th>
                                <th>First name</th>
                                <th>Middle</th>
                                <th>Course / program</th>
                                <th>Year</th>
                                <th>App no.</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($data['rows'] as $row): ?>
                            <tr>
                                <td><?= schogms_e((string) ($row['lastname'] ?? '')) ?></td>
                                <td><?= schogms_e((string) ($row['firstname'] ?? '')) ?></td>
                                <td><?= schogms_e((string) ($row['middlename'] ?? '')) ?></td>
                                <td><?= schogms_e((string) ($row['course_program_enrolled'] ?? '')) ?></td>
                                <td><?= schogms_e((string) ($row['year_level'] ?? '')) ?></td>
                                <td><?= schogms_e((string) ($row['app_no'] ?? '')) ?></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    <?php endif; ?>
    <a href="program_list.php" class="btn btn-secondary mt-2">Back to program list</a>
</div>

<footer class="footer text-center text-muted">SchoGMS</footer>
<?php
schogms_registrar_shell_close();
schogms_registrar_footer_scripts(['datatables' => true]);
?>
<script>
$(function () {
    if ($.fn.DataTable && $('#fgScholarsTable tbody tr').length) {
        $('#fgScholarsTable').DataTable({ pageLength: 25, order: [[0, 'asc']] });
    }
});
</script>
</body>
</html>
