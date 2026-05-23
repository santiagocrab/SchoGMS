<?php
/**
 * Scholarship requirements overview for registrars (COR/COG from document_uploads).
 */
include 'config/session.php';
require_once __DIR__ . '/inc/registrar_data.php';
require_once __DIR__ . '/inc/assets.php';
require_once __DIR__ . '/inc/registrar_nav.php';

if (($role ?? '') !== 'registrar') {
    header('Location: ../../index.php?ERROR=restricted');
    exit;
}

$requirements = [];
$message = '';
$campusFilter = trim((string) ($sheet_name ?? ''));

$cor = schogms_registrar_documents($conn, $campusFilter, 'COR');
$cog = schogms_registrar_documents($conn, $campusFilter, 'COG');
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
    <?php schogms_registrar_head(true); ?>
</head>
<body>
<?php schogms_loading_screen_once(); ?>

<?php schogms_registrar_shell_open('Requirements'); ?>
        <div class="container-fluid">
            <div class="page-breadcrumb">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                        <li class="breadcrumb-item active">Requirements</li>
                    </ol>
                </nav>
            </div>
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Scholarship Requirements — <?= schogms_e($sheet_name) ?></h4>
                    <p class="text-muted small mb-3">
                        COR and COG documents uploaded for your campus. Upload more from
                        <a href="cor-cog.php">COR &amp; COG</a>.
                    </p>
                    <?php if ($message !== ''): ?>
                        <div class="alert alert-warning"><?= schogms_e($message) ?></div>
                    <?php endif; ?>
                    <?php if (empty($requirements)): ?>
                        <div class="alert alert-info mb-0">No COR/COG documents found for this campus.</div>
                    <?php else: ?>
                    <p class="text-muted small mb-2"><?= count($requirements) ?> document(s)</p>
                    <div class="table-responsive">
                        <table id="zero_config" class="table table-striped table-bordered no-wrap">
                            <thead>
                                <tr>
                                    <th>Campus</th>
                                    <th>File Group</th>
                                    <th>Category</th>
                                    <th>Document</th>
                                    <th>Uploaded</th>
                                    <th>View</th>
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
                                    <td>
                                        <?php if (!empty($doc['file_path'])): ?>
                                            <a href="<?= htmlspecialchars((string) $doc['file_path'], ENT_QUOTES, 'UTF-8') ?>" target="_blank" rel="noopener">Open</a>
                                        <?php else: ?>
                                            —
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
<?php
schogms_registrar_shell_close();
schogms_registrar_footer_scripts(['datatables' => true]);
?>
</body>
</html>
