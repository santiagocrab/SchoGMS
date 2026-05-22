<?php
/**
 * Program list (read-only) — CHED TDP and TES summaries for registrar campus.
 */
include 'config/session.php';
require_once __DIR__ . '/inc/registrar_data.php';
require_once __DIR__ . '/inc/registrar_nav.php';
require_once __DIR__ . '/inc/registrar_program_list_ui.php';

$campus = trim((string) ($sheet_name ?? ''));
$tdp = ['batches' => [], 'file_groups' => [], 'programs' => [], 'totals' => ['file_groups' => 0, 'files' => 0, 'scholars' => 0, 'programs' => 0], 'error' => ''];
$tes = $tdp;

try {
    $conn = schogms_registrar_db();
    $tdp = schogms_registrar_program_list_fetch('tdp', $campus, $conn);
    $tes = schogms_registrar_program_list_fetch('tes', $campus, $conn);
} catch (Throwable $e) {
    $msg = 'Database unavailable. Please try again later.';
    $tdp['error'] = $msg;
    $tes['error'] = $msg;
    schogms_log_error('Registrar program_list: ' . $e->getMessage());
}
?>
<!DOCTYPE html>
<html dir="ltr" lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/png" href="../../assets/images/logo.png">
    <title>Program list — SchoGMS</title>
    <?php require_once __DIR__ . '/inc/assets.php'; schogms_registrar_head(true); ?>
    <style>
        .pl-hero { background: linear-gradient(135deg, #0d47a1 0%, #00838f 100%); color: #fff; border-radius: 12px; padding: 1.15rem 1.35rem; margin-bottom: 1.25rem; }
        .pl-hero h4 { color: #fff; margin-bottom: 0.35rem; font-weight: 600; }
        .pl-hero p { margin: 0; opacity: 0.9; font-size: 0.9rem; }
        .pl-stat-grid { margin-left: -0.35rem; margin-right: -0.35rem; }
        .pl-stat-card { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; padding: 0.75rem 0.9rem; height: 100%; }
        .pl-stat-card-label { display: block; font-size: 0.68rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: #64748b; }
        .pl-stat-card-value { font-size: 1.25rem; font-weight: 700; color: #0f172a; }
        .pl-program-tag { display: inline-block; font-size: 0.7rem; padding: 0.15rem 0.45rem; margin: 0.1rem 0.15rem 0.1rem 0; background: #e3f2fd; color: #1565c0; border-radius: 4px; max-width: 100%; }
        .pl-program-more { font-size: 0.7rem; color: #64748b; margin-left: 0.25rem; }
        .pl-programs-cell { max-width: 280px; }
        .pl-summary-table { font-size: 0.82rem; }
        .pl-section-note { border-left: 3px solid #1565c0; padding-left: 0.75rem; margin-bottom: 1rem; font-size: 0.85rem; color: #475569; }
    </style>
</head>
<body>
<?php schogms_loading_screen_once(); ?>

<?php include __DIR__ . '/loading-screen.php'; ?>
<?php schogms_registrar_shell_open('Program list'); ?>

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
            Campus: <strong><?= schogms_e($campus !== '' ? $campus : '—') ?></strong>.
            <strong>Programs</strong> are courses from the CHED masterlist (<code>course_program_enrolled</code>).
            <strong>File groups</strong> are upload batch names (e.g. academic year and semester).
        </p>
    </div>

    <!-- TDP -->
    <div class="row">
        <div class="col-12 mb-4">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h4 class="card-title text-primary mb-1">CHED TDP</h4>
                    <?php if ($tdp['error'] !== ''): ?>
                        <div class="alert alert-warning"><?= schogms_e($tdp['error']) ?></div>
                    <?php else: ?>
                        <div class="pl-section-note">
                            TDP (Tulong-Dunong Program) masterlist uploads for your campus.
                            Use the summaries below to see which programs and file groups are loaded.
                        </div>
                        <?php schogms_registrar_render_program_list_stats($tdp['totals']); ?>
                        <?php schogms_registrar_render_program_list_programs_table($tdp['programs'], 'tdpProgramsTable'); ?>
                        <?php schogms_registrar_render_program_list_file_groups_table($tdp['file_groups'], 'tdpFileGroupsTable'); ?>
                        <?php schogms_registrar_render_program_list_batches_table($tdp['batches'], 'tdpBatchesTable'); ?>
                    <?php endif; ?>
                    <p class="small text-muted mb-0 mt-2">
                        Open full scholar rows on <a href="ched_masterlist.php">CHED TDP masterlist</a>.
                    </p>
                </div>
            </div>
        </div>

        <!-- TES -->
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h4 class="card-title text-success mb-1">CHED TES</h4>
                    <?php if ($tes['error'] !== ''): ?>
                        <div class="alert alert-warning"><?= schogms_e($tes['error']) ?></div>
                    <?php else: ?>
                        <div class="pl-section-note" style="border-left-color:#2e7d32">
                            TES (Tertiary Education Subsidy) masterlist for your campus.
                            Coordinators typically upload TES files; this view is read-only for registrars.
                        </div>
                        <?php schogms_registrar_render_program_list_stats($tes['totals']); ?>
                        <?php schogms_registrar_render_program_list_programs_table($tes['programs'], 'tesProgramsTable'); ?>
                        <?php schogms_registrar_render_program_list_file_groups_table($tes['file_groups'], 'tesFileGroupsTable'); ?>
                        <?php schogms_registrar_render_program_list_batches_table($tes['batches'], 'tesBatchesTable'); ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<footer class="footer text-center text-muted">
    All Rights Reserved 2026. Scholarship and Grants Management System (SchoGMS).
</footer>

<?php
schogms_registrar_shell_close();
schogms_registrar_footer_scripts(['datatables' => true]);
?>
<script>
(function () {
    if (typeof $ === 'undefined' || !$.fn.DataTable) return;
    var opts = {
        pageLength: 25,
        lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, 'All']],
        order: [],
        deferRender: true,
        autoWidth: false
    };
    [
        '#tdpProgramsTable', '#tdpFileGroupsTable', '#tdpBatchesTable',
        '#tesProgramsTable', '#tesFileGroupsTable', '#tesBatchesTable'
    ].forEach(function (sel) {
        var $t = $(sel);
        if (!$t.length || $t.find('tbody tr.pl-empty-row').length) return;
        if (!$.fn.dataTable.isDataTable($t[0])) {
            var o = $.extend({}, opts);
            if (sel.indexOf('Programs') !== -1) {
                o.order = [[0, 'asc']];
            } else if (sel.indexOf('FileGroups') !== -1) {
                o.order = [[0, 'asc']];
            } else {
                o.order = [[0, 'asc'], [1, 'asc']];
            }
            $t.DataTable(o);
        }
    });
})();
</script>
</body>
</html>
