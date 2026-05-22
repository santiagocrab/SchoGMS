<?php
include '../config/session.php';
require_once __DIR__ . '/inc/validation_filters.php';
require_once __DIR__ . '/inc/validation_export.php';
require_once __DIR__ . '/inc/remarks_export.php';
require_once __DIR__ . '/inc/validation_edit_guide.php';

$program = strtolower(trim((string) ($_GET['program'] ?? 'tdp')));
if (!in_array($program, ['tdp', 'tes'], true)) {
    $program = 'tdp';
}

$campus = trim((string) ($sheet_name ?? ''));
$rows = [];
$vfOptions = [];
$loadError = '';
$prepared = [];
$stats = [
    'total' => 0,
    'enrolled' => 0,
    'passed' => 0,
    'failed' => 0,
    'pending' => 0,
    'no_cor' => 0,
    'no_cog' => 0,
];

if ($campus === '') {
    $loadError = 'No campus assigned to your account.';
} elseif ($conn instanceof mysqli) {
    $rows = schogms_validation_fetch_rows($conn, $program, $campus, $_GET, true);
    $vfOptions = schogms_validation_filter_options($conn, $program, $campus);
    $prepared = schogms_remarks_prepare_rows($rows, $program);
    $stats = schogms_remarks_stats($prepared);
}

$vfExportQs = schogms_validation_export_query($program, $campus, $_GET);
$csvQsTemplate = $vfExportQs . '&format=template';
$csvQsExtended = $vfExportQs . '&format=extended';
$exportPage = $program === 'tes' ? 'validated_remarks_tes.php' : 'validated_remarks.php';
$validatePage = $program === 'tes' ? 'validate_tes.php' : 'validate.php';
$progLabel = $program === 'tes' ? 'TES' : 'TDP';
$colspan = $program === 'tdp' ? 13 : 12;
?>
<!DOCTYPE html>
<html dir="ltr" lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/png" sizes="16x16" href="../../assets/images/logo.png">
    <title>Validate remarks — SchoGMS</title>
    <?php require_once __DIR__ . '/inc/assets.php'; schogms_coordinator_head(true); ?>
    <style>
        .remarks-hero {
            background: linear-gradient(135deg, #1e3a5f 0%, #2962ff 55%, #5c6bc0 100%);
            border-radius: 12px;
            color: #fff;
            padding: 1.25rem 1.5rem;
            margin-bottom: 1.25rem;
        }
        .remarks-hero .badge-light { color: #1e3a5f; font-weight: 600; }
        .remarks-stat {
            border: none;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0,0,0,.06);
            transition: transform .15s ease;
        }
        .remarks-stat:hover { transform: translateY(-2px); }
        .remarks-stat .stat-num { font-size: 1.75rem; font-weight: 700; line-height: 1.1; }
        .remarks-stat .stat-label { font-size: .75rem; text-transform: uppercase; letter-spacing: .04em; color: #6c757d; }
        .remarks-toolbar {
            position: sticky;
            top: 0;
            z-index: 20;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 2px 12px rgba(0,0,0,.08);
            padding: .75rem 1rem;
            margin-bottom: 1rem;
        }
        #remarksTable tbody tr.row-failed { background-color: rgba(220, 53, 69, .06); }
        #remarksTable tbody tr.row-passed { background-color: rgba(40, 167, 69, .04); }
        #remarksTable tbody tr.row-pending { background-color: rgba(255, 193, 7, .08); }
        #remarksTable .suggested-remarks {
            max-width: 220px;
            font-size: .85rem;
            color: #495057;
        }
        #remarksTable thead th {
            font-size: .72rem;
            text-transform: uppercase;
            letter-spacing: .03em;
            white-space: nowrap;
        }
        .remarks-format-hint {
            font-size: .8rem;
            color: #6c757d;
            border-left: 3px solid #2962ff;
            padding-left: .75rem;
            margin-top: .5rem;
        }
    </style>
</head>
<body>
<?php schogms_loading_screen_once(); ?>

<?php include __DIR__ . '/loading-screen.php'; ?>
<?php require_once __DIR__ . '/inc/coordinator_nav.php'; schogms_coordinator_shell_open('Validate remarks'); ?>

<div class="page-breadcrumb">
    <div class="row">
        <div class="col-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb m-0 p-0">
                    <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="<?= htmlspecialchars($validatePage, ENT_QUOTES, 'UTF-8') ?>?bulk=1">Validation</a></li>
                    <li class="breadcrumb-item active">Validate remarks</li>
                </ol>
            </nav>
        </div>
    </div>
</div>

<div class="container-fluid">
    <ul class="nav nav-pills mb-3">
        <li class="nav-item">
            <a class="nav-link <?= $program === 'tdp' ? 'active' : '' ?>"
               href="validate_remarks.php?program=tdp&amp;bulk=1&amp;sheet_name=<?= rawurlencode($campus) ?>">TDP remarks</a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?= $program === 'tes' ? 'active' : '' ?>"
               href="validate_remarks.php?program=tes&amp;bulk=1&amp;sheet_name=<?= rawurlencode($campus) ?>">TES remarks</a>
        </li>
    </ul>

    <?php if ($loadError !== ''): ?>
        <div class="alert alert-warning"><?= htmlspecialchars($loadError, ENT_QUOTES, 'UTF-8') ?></div>
    <?php else: ?>

        <div class="remarks-hero d-flex flex-wrap align-items-center justify-content-between">
            <div>
                <h4 class="mb-1 text-white font-weight-bold"><?= htmlspecialchars($progLabel, ENT_QUOTES, 'UTF-8') ?> — remarks validation</h4>
                <p class="mb-0 small opacity-90">
                    Review document status, enrollment, and remarks before exporting for the chairman.
                </p>
            </div>
            <span class="badge badge-light badge-pill mt-2 mt-md-0 px-3 py-2"><?= htmlspecialchars($campus, ENT_QUOTES, 'UTF-8') ?></span>
        </div>

        <div class="row mb-3">
            <div class="col-6 col-md-4 col-lg-2 mb-2">
                <div class="card remarks-stat h-100 mb-0">
                    <div class="card-body py-3 text-center">
                        <div class="stat-num text-primary"><?= (int) $stats['total'] ?></div>
                        <div class="stat-label">Total scholars</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-4 col-lg-2 mb-2">
                <div class="card remarks-stat h-100 mb-0">
                    <div class="card-body py-3 text-center">
                        <div class="stat-num text-success"><?= (int) $stats['enrolled'] ?></div>
                        <div class="stat-label">Enrolled</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-4 col-lg-2 mb-2">
                <div class="card remarks-stat h-100 mb-0">
                    <div class="card-body py-3 text-center">
                        <div class="stat-num text-success"><?= (int) $stats['passed'] ?></div>
                        <div class="stat-label">Validated</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-4 col-lg-2 mb-2">
                <div class="card remarks-stat h-100 mb-0">
                    <div class="card-body py-3 text-center">
                        <div class="stat-num text-danger"><?= (int) $stats['failed'] ?></div>
                        <div class="stat-label">Failed</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-4 col-lg-2 mb-2">
                <div class="card remarks-stat h-100 mb-0">
                    <div class="card-body py-3 text-center">
                        <div class="stat-num text-warning"><?= (int) $stats['pending'] ?></div>
                        <div class="stat-label">Pending</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-4 col-lg-2 mb-2">
                <div class="card remarks-stat h-100 mb-0">
                    <div class="card-body py-3 text-center">
                        <div class="stat-num text-secondary"><?= (int) $stats['no_cor'] ?></div>
                        <div class="stat-label">No COR</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm mb-3">
            <div class="card-body py-3">
                <?php
                $vfProgram = $program;
                $vfCampus = $campus;
                $vfGet = $_GET;
                $vfPage = 'validate_remarks.php';
                require __DIR__ . '/inc/validation_filters_ui.php';
                ?>
            </div>
        </div>

        <div class="remarks-toolbar d-flex flex-wrap align-items-center gap-2">
            <a class="btn btn-success btn-sm px-3"
               href="export_remarks_csv.php?<?= htmlspecialchars($csvQsTemplate, ENT_QUOTES, 'UTF-8') ?>">
                <i data-feather="download" class="feather-icon" style="width:14px;height:14px"></i>
                Export CSV (main format)
            </a>
            <a class="btn btn-outline-success btn-sm"
               href="export_remarks_csv.php?<?= htmlspecialchars($csvQsExtended, ENT_QUOTES, 'UTF-8') ?>">
                Extended CSV
            </a>
            <a class="btn btn-outline-primary btn-sm"
               href="<?= htmlspecialchars($exportPage . '?' . $vfExportQs, ENT_QUOTES, 'UTF-8') ?>"
               target="_blank" rel="noopener">
                <i data-feather="file-text" class="feather-icon" style="width:14px;height:14px"></i>
                Excel template
            </a>
            <a class="btn btn-outline-secondary btn-sm ml-md-auto"
               href="<?= htmlspecialchars($validatePage . '?bulk=1&amp;sheet_name=' . rawurlencode($campus), ENT_QUOTES, 'UTF-8') ?>">
                ← Back to validation
            </a>
        </div>
        <p class="remarks-format-hint mb-3">
            <strong>Main format</strong> matches the <?= htmlspecialchars($progLabel, ENT_QUOTES, 'UTF-8') ?> verification spreadsheet
            (<?= $program === 'tes' ? 'columns A–P' : 'columns A–O' ?>): scholar fields, registrar units, COR/COG status, enrollment status, and remarks.
            Opens in Excel with UTF-8. Use <em>Extended CSV</em> for validation status and separate suggested-remarks columns.
            Use <strong>Edit</strong> / <strong>Fix</strong> on a row to update remarks, enrollment status, course/year, or upload COR/COG.
        </p>

        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="table-responsive">
                    <table id="remarksTable" class="table table-hover table-bordered table-sm mb-0">
                        <thead class="thead-light">
                            <tr>
                                <th>SEQ</th>
                                <th>APP NO</th>
                                <?php if ($program === 'tdp'): ?><th>AWARD</th><?php endif; ?>
                                <th>Name</th>
                                <th>Course</th>
                                <th>Year</th>
                                <th>COR / COG</th>
                                <th>Enrollment</th>
                                <th>Validation</th>
                                <th>Masterlist remarks</th>
                                <th>Suggested remarks</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($prepared)): ?>
                                <tr>
                                    <td colspan="<?= $colspan ?>" class="text-center text-muted py-4">
                                        No records for this campus or filter. Adjust filters or upload the registrar masterlist.
                                    </td>
                                </tr>
                            <?php else:
                                foreach ($prepared as $item):
                                    $row = $item['row'];
                                    $check = $item['check'];
                                    $hasCor = !empty($check['has_cor']);
                                    $hasCog = !empty($check['has_cog']);
                                    $corCog = schogms_remarks_cor_cog_label($check);
                                    $enrollment = (string) ($check['enrollment'] ?? 'Not Enrolled');
                                    $validation = (string) ($check['validation_label'] ?? 'Failed');
                                    $masterRemarks = trim((string) ($row['remarks'] ?? ''));
                                    $suggested = schogms_remarks_suggested_text($row, $check);
                                    $name = trim((string) ($row['lastname'] ?? '') . ', ' . (string) ($row['firstname'] ?? '') . ' ' . (string) ($row['middlename'] ?? ''));
                                    $rowClass = 'row-failed';
                                    if (!empty($check['passed'])) {
                                        $rowClass = 'row-passed';
                                    } elseif (strtolower($validation) === 'pending') {
                                        $rowClass = 'row-pending';
                                    }
                                    $valBadge = 'danger';
                                    if (!empty($check['passed'])) {
                                        $valBadge = 'success';
                                    } elseif (strtolower($validation) === 'pending') {
                                        $valBadge = 'warning';
                                    }
                            ?>
                                <tr class="<?= htmlspecialchars($rowClass, ENT_QUOTES, 'UTF-8') ?>">
                                    <td><?= htmlspecialchars((string) ($row['seq'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                                    <td><?= htmlspecialchars((string) ($row['app_no'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                                    <?php if ($program === 'tdp'): ?>
                                        <td><?= htmlspecialchars((string) ($row['award_no'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                                    <?php endif; ?>
                                    <td class="text-nowrap"><?= htmlspecialchars($name, ENT_QUOTES, 'UTF-8') ?></td>
                                    <td><?= htmlspecialchars((string) ($row['course_program_enrolled'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                                    <td><?= htmlspecialchars((string) ($row['year_level'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                                    <td>
                                        <?php if ($hasCor && $hasCog): ?>
                                            <span class="badge badge-success"><?= htmlspecialchars($corCog, ENT_QUOTES, 'UTF-8') ?></span>
                                        <?php elseif ($hasCor || $hasCog): ?>
                                            <span class="badge badge-warning"><?= htmlspecialchars($corCog, ENT_QUOTES, 'UTF-8') ?></span>
                                        <?php else: ?>
                                            <span class="badge badge-danger"><?= htmlspecialchars($corCog, ENT_QUOTES, 'UTF-8') ?></span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <span class="badge badge-<?= $enrollment === 'Enrolled' ? 'success' : 'secondary' ?>">
                                            <?= htmlspecialchars($enrollment, ENT_QUOTES, 'UTF-8') ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge badge-<?= $valBadge ?>"><?= htmlspecialchars($validation, ENT_QUOTES, 'UTF-8') ?></span>
                                    </td>
                                    <td><?php if ($masterRemarks !== ''): ?><?= htmlspecialchars($masterRemarks, ENT_QUOTES, 'UTF-8') ?><?php else: ?><span class="text-muted">—</span><?php endif; ?></td>
                                    <td class="suggested-remarks" title="<?= htmlspecialchars($suggested, ENT_QUOTES, 'UTF-8') ?>">
                                        <?= htmlspecialchars($suggested, ENT_QUOTES, 'UTF-8') ?>
                                    </td>
                                    <td class="text-nowrap">
                                        <button type="button"
                                            class="btn btn-sm <?= !empty($check['passed']) ? 'btn-outline-primary' : 'btn-warning' ?> btn-edit-student"
                                            data-id="<?= (int) ($row['id'] ?? 0) ?>"
                                            data-guide="<?= schogms_validation_edit_guide_attr($row, $check) ?>">
                                            <?= !empty($check['passed']) ? 'Edit' : 'Fix' ?>
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<footer class="footer text-center text-muted">
    All Rights Reserved 2026. Scholarship and Grants Management System (SchoGMS).
</footer>

<?php
schogms_coordinator_shell_close();
schogms_coordinator_footer_scripts(['datatables' => true, 'sweetalert' => true]);
$mlProgram = $program;
$mlCampus = $campus;
require __DIR__ . '/inc/masterlist_edit_ui.php';
?>
<script>
(function () {
    if (typeof $ === 'undefined' || !$.fn.DataTable) return;
    var $t = $('#remarksTable');
    if (!$t.length || $t.find('tbody tr td[colspan]').length) return;
    $t.DataTable({
        pageLength: 25,
        lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, 'All']],
        order: [[0, 'asc']],
        dom: '<"row"<"col-sm-6"l><"col-sm-6"f>>rtip',
        language: { search: 'Filter table:', lengthMenu: 'Show _MENU_ rows' }
    });
})();
</script>
</body>
</html>
