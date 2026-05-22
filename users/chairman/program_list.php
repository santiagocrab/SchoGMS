<?php
/**
 * Program list — CHED TDP and TES summaries (chairman: all campuses, filterable).
 */
include 'config/session.php';
require_once __DIR__ . '/../registrar/inc/registrar_data.php';
require_once __DIR__ . '/../../inc/schogms_file_group_meta.php';
require_once __DIR__ . '/inc/chairman_nav.php';
require_once __DIR__ . '/../registrar/inc/registrar_program_list_ui.php';

$activeProgram = strtolower(trim((string) ($_GET['program'] ?? 'tdp'))) === 'tes' ? 'tes' : 'tdp';
$campusFilter = trim((string) ($_GET['campus'] ?? ''));

if (!isset($conn) || !($conn instanceof mysqli)) {
    require 'config/conn.php';
}

$campusNames = schogms_registrar_program_list_campus_names($conn);
$tdpCounts = schogms_registrar_program_list_campus_counts($conn, 'tdp');
$tesCounts = schogms_registrar_program_list_campus_counts($conn, 'tes');
$activeCounts = $activeProgram === 'tes' ? $tesCounts : $tdpCounts;

if ($campusFilter !== '' && !isset($activeCounts['campuses'][$campusFilter])) {
    foreach ($campusNames as $name) {
        if (strcasecmp($name, $campusFilter) === 0) {
            $campusFilter = $name;
            break;
        }
    }
}

$fetchCampus = $campusFilter !== '' ? $campusFilter : null;
$tdp = schogms_registrar_program_list_fetch('tdp', $fetchCampus, $conn, true);
$tes = schogms_registrar_program_list_fetch('tes', $fetchCampus, $conn, true);
$tdp['file_groups'] = schogms_file_group_meta_attach_uploaders($conn, 'tdp', $tdp['file_groups']);
$tes['file_groups'] = schogms_file_group_meta_attach_uploaders($conn, 'tes', $tes['file_groups']);
$data = $activeProgram === 'tes' ? $tes : $tdp;
$showCampusColumn = (bool) ($data['show_campus_column'] ?? ($campusFilter === ''));

$tabBase = 'program_list.php';
$programLabel = strtoupper($activeProgram);
$heroCampus = $campusFilter !== '' ? $campusFilter : 'All campuses';
?>
<!DOCTYPE html>
<html dir="ltr" lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/png" href="../../assets/images/logo.png">
    <title>Program list — SchoGMS</title>
    <?php require_once __DIR__ . '/inc/assets.php'; schogms_chairman_head(true); ?>
    <style>
        .pl-hero { background: linear-gradient(135deg, #0d47a1 0%, #00838f 100%); color: #fff; border-radius: 12px; padding: 1.15rem 1.35rem; margin-bottom: 1.25rem; }
        .pl-hero h4 { color: #fff; margin-bottom: 0.35rem; font-weight: 600; }
        .pl-hero p { margin: 0; opacity: 0.9; font-size: 0.9rem; }
        .pl-stat-grid { margin-left: -0.35rem; margin-right: -0.35rem; }
        .pl-stat-card { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; padding: 0.75rem 0.9rem; height: 100%; }
        .pl-stat-card-label { display: block; font-size: 0.68rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: #64748b; }
        .pl-stat-card-value { font-size: 1.25rem; font-weight: 700; color: #0f172a; }
        .pl-program-tag { display: inline-block; font-size: 0.7rem; padding: 0.15rem 0.45rem; margin: 0.1rem 0.15rem 0.1rem 0; background: #e3f2fd; color: #1565c0; border-radius: 4px; }
        .pl-program-more { font-size: 0.7rem; color: #64748b; margin-left: 0.25rem; }
        .pl-programs-cell { max-width: 280px; }
        .pl-summary-table { font-size: 0.82rem; }
        .pl-section-note { border-left: 3px solid #1565c0; padding-left: 0.75rem; margin-bottom: 1rem; font-size: 0.85rem; color: #475569; }
        .pl-program-tabs .nav-link { font-weight: 600; }
        .pl-campus-tabs .nav-link { font-size: 0.82rem; padding: 0.4rem 0.75rem; }
        .pl-campus-tabs .badge { font-size: 0.68rem; margin-left: 0.2rem; vertical-align: middle; }
    </style>
</head>
<body>
<?php schogms_loading_screen_once(); ?>
<?php schogms_chairman_shell_open('Program list'); ?>

<div class="page-breadcrumb">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb m-0 p-0">
            <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
            <li class="breadcrumb-item active">Program list</li>
        </ol>
    </nav>
</div>

<div class="container-fluid">
    <div class="pl-hero">
        <h4>CHED program list</h4>
        <p>
            Viewing <strong><?= htmlspecialchars($programLabel, ENT_QUOTES, 'UTF-8') ?></strong>
            for <strong><?= htmlspecialchars($heroCampus, ENT_QUOTES, 'UTF-8') ?></strong>.
            Use the tabs below to switch program and campus. <strong>View</strong> opens scholars for a file group.
        </p>
    </div>

    <ul class="nav nav-tabs pl-program-tabs mb-3" role="tablist">
        <li class="nav-item">
            <a class="nav-link <?= $activeProgram === 'tdp' ? 'active' : '' ?>"
               href="<?= htmlspecialchars($tabBase . '?program=tdp' . ($campusFilter !== '' ? '&campus=' . rawurlencode($campusFilter) : ''), ENT_QUOTES, 'UTF-8') ?>">
                CHED TDP
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?= $activeProgram === 'tes' ? 'active' : '' ?>"
               href="<?= htmlspecialchars($tabBase . '?program=tes' . ($campusFilter !== '' ? '&campus=' . rawurlencode($campusFilter) : ''), ENT_QUOTES, 'UTF-8') ?>">
                CHED TES
            </a>
        </li>
    </ul>

    <ul class="nav nav-pills pl-campus-tabs mb-3 flex-wrap">
        <?php
        $allHref = $tabBase . '?program=' . rawurlencode($activeProgram);
        $allCount = (int) ($activeCounts['all'] ?? 0);
        ?>
        <li class="nav-item">
            <a class="nav-link <?= $campusFilter === '' ? 'active' : '' ?>"
               href="<?= htmlspecialchars($allHref, ENT_QUOTES, 'UTF-8') ?>">
                All campuses
                <span class="badge badge-secondary"><?= number_format($allCount) ?></span>
            </a>
        </li>
        <?php foreach ($campusNames as $campusName):
            $n = (int) ($activeCounts['campuses'][$campusName] ?? 0);
            if ($n === 0) {
                continue;
            }
            $href = $tabBase . '?program=' . rawurlencode($activeProgram) . '&campus=' . rawurlencode($campusName);
            ?>
        <li class="nav-item">
            <a class="nav-link <?= strcasecmp($campusFilter, $campusName) === 0 ? 'active' : '' ?>"
               href="<?= htmlspecialchars($href, ENT_QUOTES, 'UTF-8') ?>">
                <?= htmlspecialchars($campusName, ENT_QUOTES, 'UTF-8') ?>
                <span class="badge badge-light text-dark border"><?= number_format($n) ?></span>
            </a>
        </li>
        <?php endforeach; ?>
    </ul>

    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h4 class="card-title text-<?= $activeProgram === 'tes' ? 'success' : 'primary' ?> mb-1">
                        CHED <?= htmlspecialchars($programLabel, ENT_QUOTES, 'UTF-8') ?>
                        <?php if ($campusFilter !== ''): ?>
                            <span class="text-muted font-weight-normal">— <?= htmlspecialchars($campusFilter, ENT_QUOTES, 'UTF-8') ?></span>
                        <?php endif; ?>
                    </h4>
                    <?php if ($data['error'] !== ''): ?>
                        <div class="alert alert-warning"><?= htmlspecialchars($data['error'], ENT_QUOTES, 'UTF-8') ?></div>
                    <?php else: ?>
                        <div class="pl-section-note" style="<?= $activeProgram === 'tes' ? 'border-left-color:#2e7d32' : '' ?>">
                            <?= $activeProgram === 'tes' ? 'TES' : 'TDP' ?> masterlist batches
                            <?= $campusFilter !== '' ? 'for this campus.' : 'across all campuses (Campus column identifies the uploader).' ?>
                        </div>
                        <?php schogms_registrar_render_program_list_stats($data['totals'], $showCampusColumn); ?>
                        <?php
                        $prefix = $activeProgram;
                        schogms_registrar_render_program_list_programs_table(
                            $data['programs'],
                            $prefix . 'ProgramsTable',
                            $showCampusColumn
                        );
                        schogms_registrar_render_program_list_file_groups_table(
                            $data['file_groups'],
                            $prefix . 'FileGroupsTable',
                            $activeProgram,
                            'file_group_view.php',
                            $showCampusColumn,
                            true
                        );
                        schogms_registrar_render_program_list_batches_table(
                            $data['batches'],
                            $prefix . 'BatchesTable',
                            $activeProgram,
                            'file_group_view.php',
                            $showCampusColumn
                        );
                        ?>
                    <?php endif; ?>
                    <p class="small text-muted mb-0 mt-2">
                        <a href="file_groups.php?program=<?= rawurlencode($activeProgram) ?><?= $campusFilter !== '' ? '&amp;campus=' . rawurlencode($campusFilter) : '' ?>">Review file groups</a> ·
                        <?php if ($activeProgram === 'tdp'): ?>
                            <a href="ched_masterlist.php">TDP masterlist</a> · <a href="upload_ched_tdp.php">Upload TDP</a>
                        <?php else: ?>
                            <a href="ched_masterlist_tes.php">TES masterlist</a> · <a href="upload_ched_tes.php">Upload TES</a>
                        <?php endif; ?>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<footer class="footer text-center text-muted">SchoGMS</footer>
<?php
schogms_chairman_shell_close();
schogms_chairman_footer_scripts(['datatables' => true]);
?>
<script>
(function () {
    if (typeof $ === 'undefined' || !$.fn.DataTable) return;
    var opts = { pageLength: 25, lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, 'All']], order: [], deferRender: true };
    ['<?= $activeProgram ?>ProgramsTable', '<?= $activeProgram ?>FileGroupsTable', '<?= $activeProgram ?>BatchesTable'].forEach(function (id) {
        var $t = $('#' + id);
        if (!$t.length || $t.find('tbody tr.pl-empty-row').length) return;
        if (!$.fn.dataTable.isDataTable($t[0])) $t.DataTable(opts);
    });
})();
</script>
</body>
</html>
