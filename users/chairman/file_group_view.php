<?php
include 'config/session.php';
require_once __DIR__ . '/../registrar/inc/registrar_data.php';
require_once __DIR__ . '/inc/chairman_nav.php';
require_once __DIR__ . '/../../inc/schogms_file_group_view.php';

$p = schogms_file_group_view_params();
$program = $p['program'] === 'tes' ? 'tes' : 'tdp';
$fileGroup = $p['file_group'];
$filename = $p['filename'];
$campus = $p['campus'] !== '' ? $p['campus'] : trim((string) ($sheet_name ?? ''));

$data = ['meta' => [], 'summary' => [], 'rows' => [], 'error' => ''];
if (!isset($conn) || !($conn instanceof mysqli)) {
    require 'config/conn.php';
}
$data = schogms_file_group_view_fetch($program, $fileGroup, $campus, $filename !== '' ? $filename : null, $conn);

$label = (string) ($data['meta']['label'] ?? strtoupper($program));
?>
<!DOCTYPE html>
<html dir="ltr" lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/png" href="../../assets/images/logo.png">
    <title><?= htmlspecialchars($label, ENT_QUOTES, 'UTF-8') ?> file group — SchoGMS</title>
    <?php require_once __DIR__ . '/inc/assets.php'; schogms_chairman_head(true); ?>
</head>
<body>
<?php schogms_chairman_shell_open($label . ' file group'); ?>

<div class="page-breadcrumb">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb m-0 p-0">
            <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="program_list.php">Program list</a></li>
            <li class="breadcrumb-item active"><?= htmlspecialchars($fileGroup, ENT_QUOTES, 'UTF-8') ?></li>
        </ol>
    </nav>
</div>

<div class="container-fluid">
    <?php if ($data['error'] !== ''): ?>
        <div class="alert alert-warning"><?= htmlspecialchars($data['error'], ENT_QUOTES, 'UTF-8') ?></div>
    <?php else: ?>
        <?php $sum = $data['summary']; ?>
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-body">
                <h4 class="card-title mb-1"><?= htmlspecialchars($label, ENT_QUOTES, 'UTF-8') ?> — <?= htmlspecialchars($fileGroup, ENT_QUOTES, 'UTF-8') ?></h4>
                <p class="text-muted small mb-2">
                    Campus: <strong><?= htmlspecialchars((string) ($sum['campus'] ?? ''), ENT_QUOTES, 'UTF-8') ?></strong>
                    <?php if ($filename !== ''): ?> · File: <strong><?= htmlspecialchars($filename, ENT_QUOTES, 'UTF-8') ?></strong><?php endif; ?>
                </p>
                <p class="mb-0"><?= htmlspecialchars(schogms_file_group_summary_text($sum), ENT_QUOTES, 'UTF-8') ?></p>
                <?php if (!empty($sum['programs_summary'])): ?>
                    <p class="small text-muted mt-2 mb-0"><strong>Programs:</strong> <?= htmlspecialchars((string) $sum['programs_summary'], ENT_QUOTES, 'UTF-8') ?></p>
                <?php endif; ?>
            </div>
        </div>
        <div class="card">
            <div class="card-body table-responsive">
                <table id="fgScholarsTable" class="table table-striped table-bordered table-sm w-100">
                    <thead>
                        <tr>
                            <th>Campus</th>
                            <th>Last name</th>
                            <th>First name</th>
                            <th>Middle</th>
                            <th>Course / program</th>
                            <th>Year</th>
                            <th>App no.</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                    $campusCol = $program === 'tes' ? 'campus' : 'sheet_name';
                    foreach ($data['rows'] as $row): ?>
                        <tr>
                            <td><?= htmlspecialchars((string) ($row[$campusCol] ?? $row['campus'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                            <td><?= htmlspecialchars((string) ($row['lastname'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                            <td><?= htmlspecialchars((string) ($row['firstname'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                            <td><?= htmlspecialchars((string) ($row['middlename'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                            <td><?= htmlspecialchars((string) ($row['course_program_enrolled'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                            <td><?= htmlspecialchars((string) ($row['year_level'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                            <td><?= htmlspecialchars((string) ($row['app_no'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php endif; ?>
    <a href="program_list.php" class="btn btn-secondary mt-2">Back to program list</a>
</div>

<?php schogms_chairman_shell_close(); schogms_chairman_footer_scripts(['datatables' => true]); ?>
<script>$(function(){ if($.fn.DataTable && $('#fgScholarsTable tbody tr').length) $('#fgScholarsTable').DataTable({pageLength:25,order:[[0,'asc']]}); });</script>
</body>
</html>
