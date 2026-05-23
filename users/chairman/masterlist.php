<?php
/**
 * Chairman: registrar masterlist (paginated — no slow document_uploads LIKE join).
 */
include 'config/session.php';
require_once __DIR__ . '/../registrar/inc/registrar_data.php';
require_once __DIR__ . '/../coordinator/inc/masterlist_rows.php';
require_once __DIR__ . '/../registrar/inc/registrar_masterlist_ui.php';
require_once __DIR__ . '/../../inc/schogms_upload_format.php';
require_once __DIR__ . '/inc/chairman_nav.php';

$campusFilter = trim((string) ($_GET['campus'] ?? ''));
$category = trim((string) ($_GET['category'] ?? ''));
$fileGroupFilter = trim((string) ($_GET['file_group'] ?? ''));
$academicYearFilter = trim((string) ($_GET['academic_year'] ?? ''));
$semesterFilter = trim((string) ($_GET['semester'] ?? ''));
$searchTerm = trim((string) ($_GET['search'] ?? ''));
$loadError = '';
$registrarData = [];
$totalRecords = 0;
$totalPages = 1;
$page = 1;
$limit = (int) ($_GET['limit'] ?? 100);
$categories = [];
$fileGroups = [];
$campusNames = [];
$academicYears = [];
$docIndex = [];
$pageCor = 0;
$pageCog = 0;
$pageBoth = 0;
$dashCounts = ['masterlist' => 0, 'cor' => 0, 'cog' => 0, 'file_groups' => 0, 'courses' => 0];
$startTime = microtime(true);

if (!isset($conn) || !($conn instanceof mysqli)) {
    require 'config/conn.php';
}

try {
    if (!($conn instanceof mysqli)) {
        throw new RuntimeException('Database unavailable.');
    }

    $fetchCampus = $campusFilter !== '' ? $campusFilter : null;
    $campusNames = schogms_registrar_masterlist_campuses($conn);
    $categories = schogms_registrar_masterlist_categories($fetchCampus, $conn);
    $fileGroups = schogms_registrar_masterlist_file_groups($fetchCampus, $conn);
    $academicYears = schogms_registrar_masterlist_academic_years($fetchCampus, $conn);
    $dashCounts = schogms_registrar_dashboard_counts($fetchCampus);

    $docIndex = schogms_coordinator_document_index(
        $conn,
        $fetchCampus ?? '',
        $fetchCampus === null
    );

    $ml = schogms_registrar_masterlist_fetch([
        'category' => $category,
        'file_group' => $fileGroupFilter,
        'academic_year' => $academicYearFilter,
        'semester' => $semesterFilter,
        'search' => $searchTerm,
        'page' => $_GET['page'] ?? 1,
        'limit' => $limit,
    ], $fetchCampus, $conn);

    $registrarData = $ml['data'];
    $totalRecords = $ml['total'];
    $totalPages = $ml['pages'];
    $page = $ml['page'];
    $limit = $ml['limit'];

    foreach ($registrarData as $row) {
        $docs = schogms_coordinator_resolve_doc($docIndex, [
            'lastname' => $row['last_name'] ?? '',
            'firstname' => $row['first_name'] ?? '',
            'middlename' => $row['middle_name'] ?? '',
        ]);
        if ($docs['has_cor']) {
            $pageCor++;
        }
        if ($docs['has_cog']) {
            $pageCog++;
        }
        if ($docs['has_cor'] && $docs['has_cog']) {
            $pageBoth++;
        }
    }
} catch (Throwable $e) {
    $loadError = 'Could not load masterlist data.';
    schogms_log_error('Chairman masterlist: ' . $e->getMessage());
}

$filterParams = [
    'campus' => $campusFilter,
    'category' => $category,
    'file_group' => $fileGroupFilter,
    'academic_year' => $academicYearFilter,
    'semester' => $semesterFilter,
    'search' => $searchTerm,
    'limit' => (string) $limit,
];
$activeChips = schogms_registrar_masterlist_active_chips($filterParams);
$loadMs = $loadError === '' ? round((microtime(true) - $startTime) * 1000, 1) : 0;
$showCampusColumn = $campusFilter === '';

$extraColumns = [
    'campus' => 'Campus',
    'ext_name' => 'Ext. Name',
    'id_number' => 'ID Number',
    'gender' => 'Gender',
    'student_type' => 'Student Type',
    'year_level' => 'Year Level',
    'course' => 'Course',
    'scholarship' => 'Scholarship',
    'enrolled' => 'Enrolled',
    'email_address' => 'Email',
    'mobile_number' => 'Mobile',
];
if (!$showCampusColumn) {
    unset($extraColumns['campus']);
}
?>
<!DOCTYPE html>
<html dir="ltr" lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/png" href="../../assets/images/logo.png">
    <title>Registrar masterlist — SchoGMS</title>
    <?php require_once __DIR__ . '/inc/assets.php'; schogms_chairman_head(); ?>
    <link href="../../assets/css/registrar-masterlist.css" rel="stylesheet">
</head>
<body>
<?php schogms_loading_screen_once(); ?>
<?php schogms_chairman_shell_open('Registrar masterlist'); ?>

<div class="page-breadcrumb">
    <div class="row align-items-center">
        <div class="col-md-7">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb m-0 p-0">
                    <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                    <li class="breadcrumb-item active">Registrar masterlist</li>
                </ol>
            </nav>
        </div>
        <div class="col-md-5 text-md-right mt-2 mt-md-0">
            <button type="button" class="btn btn-success btn-rounded" data-toggle="modal" data-target="#uploadModal">
                Upload masterlist
            </button>
        </div>
    </div>
</div>

<div class="modal fade" id="uploadModal" tabindex="-1" role="dialog" aria-labelledby="uploadModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="uploadModalLabel">Upload student masterlist</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span>&times;</span></button>
            </div>
            <div class="modal-body">
                <?php schogms_upload_format_modal_hint('registrar_masterlist', '../../'); ?>
                <form id="uploadForm">
                    <div class="form-group">
                        <label for="file_group">File group name</label>
                        <input type="text" class="form-control" id="file_group" name="file_group" required>
                    </div>
                    <div class="form-group">
                        <label for="excelFile">Excel / CSV file</label>
                        <input type="file" class="form-control" id="excelFile" name="excelFile" accept=".xls,.xlsx,.csv" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Upload</button>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid">
    <?php if ($loadError !== ''): ?>
        <div class="alert alert-warning"><?= htmlspecialchars($loadError, ENT_QUOTES, 'UTF-8') ?></div>
    <?php else: ?>

    <div class="rml-hero">
        <h4>Registrar masterlist</h4>
        <p>
            <?php if ($campusFilter !== ''): ?>
                Campus: <strong><?= htmlspecialchars($campusFilter, ENT_QUOTES, 'UTF-8') ?></strong>
            <?php else: ?>
                Viewing <strong>all campuses</strong> — use filters to narrow results (recommended for faster loading).
            <?php endif; ?>
        </p>
    </div>

    <div class="rml-stat-grid">
        <div class="rml-stat">
            <div class="rml-stat-label">Total scholars</div>
            <div class="rml-stat-value rml-stat-value--accent"><?= number_format($dashCounts['masterlist']) ?></div>
        </div>
        <div class="rml-stat">
            <div class="rml-stat-label">Matching filters</div>
            <div class="rml-stat-value"><?= number_format($totalRecords) ?></div>
        </div>
        <div class="rml-stat">
            <div class="rml-stat-label">COR uploaded</div>
            <div class="rml-stat-value rml-stat-value--success"><?= number_format($dashCounts['cor']) ?></div>
        </div>
        <div class="rml-stat">
            <div class="rml-stat-label">COG uploaded</div>
            <div class="rml-stat-value rml-stat-value--success"><?= number_format($dashCounts['cog']) ?></div>
        </div>
        <div class="rml-stat">
            <div class="rml-stat-label">On this page</div>
            <div class="rml-stat-value rml-stat-value--warn"><?= count($registrarData) ?></div>
        </div>
    </div>

    <div class="rml-filter-card">
        <div class="rml-filter-head">
            <h5>Filters &amp; search</h5>
            <button type="button" class="btn btn-sm btn-outline-secondary" id="rmlToggleAdvanced"
                    aria-expanded="false" aria-controls="rmlAdvancedFilters">More options</button>
        </div>
        <form method="get" action="masterlist.php" id="registrarMasterlistFilterForm">
            <input type="hidden" name="page" value="1">
            <div class="rml-filter-body">
                <div class="form-row">
                    <div class="form-group col-md-3">
                        <label for="campusFilter">Campus</label>
                        <select id="campusFilter" name="campus" class="form-control form-control-sm">
                            <option value="">All campuses</option>
                            <?php foreach ($campusNames as $campusName): ?>
                                <option value="<?= htmlspecialchars($campusName, ENT_QUOTES, 'UTF-8') ?>"
                                    <?= $campusFilter === $campusName ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($campusName, ENT_QUOTES, 'UTF-8') ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group col-md-3">
                        <label for="categoryFilter">Source file</label>
                        <select id="categoryFilter" name="category" class="form-control form-control-sm">
                            <option value="">All</option>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?= htmlspecialchars((string) $cat, ENT_QUOTES, 'UTF-8') ?>"
                                    <?= $category === (string) $cat ? 'selected' : '' ?>>
                                    <?= htmlspecialchars((string) $cat, ENT_QUOTES, 'UTF-8') ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group col-md-3">
                        <label for="fileGroupFilter">File group</label>
                        <select id="fileGroupFilter" name="file_group" class="form-control form-control-sm">
                            <option value="">All</option>
                            <?php foreach ($fileGroups as $fg): ?>
                                <option value="<?= htmlspecialchars($fg, ENT_QUOTES, 'UTF-8') ?>"
                                    <?= $fileGroupFilter === $fg ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($fg, ENT_QUOTES, 'UTF-8') ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group col-md-3">
                        <label for="limitFilter">Rows per page</label>
                        <select id="limitFilter" name="limit" class="form-control form-control-sm">
                            <?php foreach ([50, 100, 200, 500] as $opt): ?>
                                <option value="<?= $opt ?>" <?= $limit === $opt ? 'selected' : '' ?>><?= $opt ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div id="rmlAdvancedFilters" class="collapse">
                    <div class="form-row">
                        <div class="form-group col-md-4">
                            <label for="academicYearFilter">Academic year (in file group)</label>
                            <select id="academicYearFilter" name="academic_year" class="form-control form-control-sm">
                                <option value="">Any</option>
                                <?php foreach ($academicYears as $ay): ?>
                                    <option value="<?= htmlspecialchars($ay, ENT_QUOTES, 'UTF-8') ?>"
                                        <?= $academicYearFilter === $ay ? 'selected' : '' ?>><?= htmlspecialchars($ay, ENT_QUOTES, 'UTF-8') ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group col-md-4">
                            <label for="semesterFilter">Semester (in file group)</label>
                            <input type="text" id="semesterFilter" name="semester" class="form-control form-control-sm"
                                   value="<?= htmlspecialchars($semesterFilter, ENT_QUOTES, 'UTF-8') ?>"
                                   placeholder="e.g. 1st Semester">
                        </div>
                    </div>
                </div>
            </div>
            <div class="rml-filter-actions">
                <div class="rml-search-wrap">
                    <input type="search" id="searchInput" name="search" class="form-control form-control-sm"
                           placeholder="Search name, ID, course…"
                           value="<?= htmlspecialchars($searchTerm, ENT_QUOTES, 'UTF-8') ?>">
                </div>
                <button type="submit" class="btn btn-primary btn-sm btn-rounded px-4">Apply filters</button>
                <a href="masterlist.php" class="btn btn-outline-secondary btn-sm btn-rounded">Clear all</a>
            </div>
        </form>
    </div>

    <?php if ($activeChips !== []): ?>
        <div class="rml-chips">
            <?php foreach ($activeChips as $chip): ?>
                <a class="rml-chip" href="<?= htmlspecialchars($chip['href'], ENT_QUOTES, 'UTF-8') ?>">
                    <?= htmlspecialchars($chip['label'], ENT_QUOTES, 'UTF-8') ?> ×
                </a>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <div class="rml-meta-bar">
        <div>
            <strong><?= number_format($totalRecords) ?></strong> record(s) match
            <?php if ($totalRecords > 0): ?>
                · page <strong><?= (int) $page ?></strong> of <strong><?= (int) $totalPages ?></strong>
                · COR <strong><?= $pageCor ?></strong>, COG <strong><?= $pageCog ?></strong>, both <strong><?= $pageBoth ?></strong>
            <?php endif; ?>
        </div>
        <small class="text-muted">Loaded in <?= $loadMs ?>ms</small>
    </div>

    <?php schogms_registrar_masterlist_render_pagination($page, $totalPages, $filterParams); ?>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0 rml-table-wrap">
            <div class="table-responsive" style="max-height: 70vh;">
                <table id="masterlist_table" class="table table-striped table-bordered table-sm mb-0">
                    <thead>
                        <tr>
                            <th>COR &amp; COG</th>
                            <th>Full name</th>
                            <?php foreach ($extraColumns as $label): ?>
                                <th><?= htmlspecialchars($label, ENT_QUOTES, 'UTF-8') ?></th>
                            <?php endforeach; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($registrarData === []): ?>
                            <tr>
                                <td colspan="<?= 2 + count($extraColumns) ?>" class="text-center text-muted py-5">
                                    No records match your filters.
                                </td>
                            </tr>
                        <?php else:
                            foreach ($registrarData as $row):
                                $ln = schogms_registrar_fix_enye((string) ($row['last_name'] ?? ''));
                                $fn = schogms_registrar_fix_enye((string) ($row['first_name'] ?? ''));
                                $mn = schogms_registrar_fix_enye((string) ($row['middle_name'] ?? ''));
                                $fullName = trim($ln . ', ' . $fn . ' ' . $mn);
                                $docs = schogms_coordinator_resolve_doc($docIndex, [
                                    'lastname' => $row['last_name'] ?? '',
                                    'firstname' => $row['first_name'] ?? '',
                                    'middlename' => $row['middle_name'] ?? '',
                                ]);
                                ?>
                            <tr>
                                <td class="text-nowrap"><?php schogms_registrar_masterlist_render_cor_cog($docs); ?></td>
                                <td class="text-nowrap font-weight-medium"><?= htmlspecialchars($fullName, ENT_QUOTES, 'UTF-8') ?></td>
                                <?php foreach (array_keys($extraColumns) as $colKey):
                                    $cell = (string) ($row[$colKey] ?? '');
                                    if (in_array($colKey, ['last_name', 'first_name', 'middle_name'], true)) {
                                        $cell = schogms_registrar_fix_enye($cell);
                                    }
                                    ?>
                                    <td><?= htmlspecialchars($cell, ENT_QUOTES, 'UTF-8') ?></td>
                                <?php endforeach; ?>
                            </tr>
                        <?php endforeach; endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <?php schogms_registrar_masterlist_render_pagination($page, $totalPages, $filterParams); ?>

    <?php endif; ?>
</div>

<?php
schogms_chairman_shell_close();
schogms_chairman_footer_scripts(['sweetalert' => true]);
?>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
<script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
<script src="../../assets/js/registrar-masterlist.js"></script>
<script>
document.getElementById('uploadForm')?.addEventListener('submit', function (e) {
    e.preventDefault();
    var fileGroup = document.getElementById('file_group').value.trim();
    var file = document.getElementById('excelFile').files[0];
    if (!fileGroup || !file) {
        Swal.fire('Error', 'File group and file are required.', 'error');
        return;
    }
    var fd = new FormData();
    fd.append('file_group', fileGroup);
    fd.append('excelFile', file);
    Swal.fire({ title: 'Uploading…', allowOutsideClick: false, didOpen: function () { Swal.showLoading(); } });
    fetch('submit_master_list.php', { method: 'POST', body: fd })
        .then(function (r) { return r.json(); })
        .then(function (data) {
            if (data.success) {
                Swal.fire('Success', data.message || 'Uploaded.', 'success').then(function () { location.reload(); });
            } else {
                Swal.fire('Error', data.error || data.message || 'Upload failed.', 'error');
            }
        })
        .catch(function () { Swal.fire('Error', 'Upload failed.', 'error'); });
});
</script>
</body>
</html>
