<?php
include 'config/session.php';
require_once __DIR__ . '/inc/registrar_data.php';
require_once __DIR__ . '/inc/registrar_nav.php';
require_once __DIR__ . '/inc/registrar_masterlist_ui.php';
require_once __DIR__ . '/../coordinator/inc/masterlist_rows.php';

$campus = trim((string) ($sheet_name ?? ''));
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
$academicYears = [];
$docIndex = [];
$pageCor = 0;
$pageCog = 0;
$pageBoth = 0;
$dashCounts = ['masterlist' => 0, 'cor' => 0, 'cog' => 0, 'file_groups' => 0];
$startTime = microtime(true);

try {
    $conn = schogms_registrar_db();
} catch (Throwable $e) {
    $conn = null;
    $loadError = 'Database connection unavailable.';
}

if ($loadError === '' && $campus === '') {
    $loadError = 'No campus assigned to your account.';
}

if ($loadError === '' && $conn instanceof mysqli) {
    $categories = schogms_registrar_masterlist_categories($campus, $conn);
    $fileGroups = schogms_registrar_masterlist_file_groups($campus, $conn);
    $academicYears = schogms_registrar_masterlist_academic_years($campus, $conn);
    $docIndex = schogms_coordinator_document_index($conn, $campus);
    $dashCounts = schogms_registrar_dashboard_counts($campus);

    $ml = schogms_registrar_masterlist_fetch([
        'category' => $category,
        'file_group' => $fileGroupFilter,
        'academic_year' => $academicYearFilter,
        'semester' => $semesterFilter,
        'search' => $searchTerm,
        'page' => $_GET['page'] ?? 1,
        'limit' => $limit,
    ], $campus, $conn);

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
}

$filterParams = [
    'category' => $category,
    'file_group' => $fileGroupFilter,
    'academic_year' => $academicYearFilter,
    'semester' => $semesterFilter,
    'search' => $searchTerm,
    'limit' => (string) $limit,
];
$activeChips = schogms_registrar_masterlist_active_chips($filterParams);
$loadMs = $loadError === '' ? round((microtime(true) - $startTime) * 1000, 1) : 0;

$extraColumns = [
    'ext_name' => 'Ext. Name',
    'id_number' => 'ID Number',
    'gender' => 'Gender',
    'student_type' => 'Student Type',
    'year_level' => 'Year Level',
    'attended' => 'Attended',
    'course' => 'Course',
    'curriculum' => 'Curriculum',
    'scholarship' => 'Scholarship',
    'gpa' => 'GPA',
    'cgpa' => 'CGPA',
    'pass_percentage' => '% Pass',
    'grade_remarks' => 'Grade Remarks',
    'enrolled' => 'Enrolled',
    'lec_unit' => 'Lec. Unit',
    'lab_unit' => 'Lab. Unit',
    'cor_printed' => 'COR Printed',
    'billing_profile' => 'Billing Profile',
    'misc_fee_total' => 'Misc. Fee Total',
    'misc_fee_paid' => 'Misc. Fee Paid',
    'tuition_fee_total' => 'Tuition Fee Total',
    'tuition_fee_paid' => 'Tuition Fee Paid',
    'street' => 'Street',
    'barangay' => 'Barangay',
    'municipality_city' => 'Municipality/City',
    'province' => 'Province',
    'zip_code' => 'Zip Code',
    'date_of_birth' => 'Date of Birth',
    'place_of_birth' => 'Place of Birth',
    'civil_status' => 'Civil Status',
    'tribe' => 'Tribe',
    'religion' => 'Religion',
    'year_admitted' => 'Year Admitted',
    'semester_admitted' => 'Semester Admitted',
    'school_last_attended' => 'School Last Attended',
    'year_last_attended' => 'Year Last Attended',
    'semester_last_attended' => 'Semester Last Attended',
    'high_school_graduated' => 'High School Graduated',
    'exam_date' => 'Exam Date',
    'exam_rating' => 'Exam Rating',
    'ref_number' => 'Ref. Number',
    'guardian' => 'Guardian',
    'guardian_address' => 'Address',
    'guardian_contact' => 'Contact Nos.',
    'blood_type' => 'Blood Type',
    'email_address' => 'Email Address',
    'mobile_number' => 'Mobile Number',
    'deped_number' => 'DEPED Number',
    'scholarship_grant' => 'Scholarship Grant',
    'scholarship_allowance' => 'Scholarship Allowance',
    'documents_submitted' => 'Documents Submitted',
    'lacking_documents' => 'Lacking Document(s)',
];
?>
<!DOCTYPE html>
<html dir="ltr" lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/png" href="../../assets/images/logo.png">
    <title>Registrar masterlist — SchoGMS</title>
    <?php require_once __DIR__ . '/inc/assets.php'; schogms_registrar_head(); ?>
    <link href="../../assets/css/registrar-masterlist.css" rel="stylesheet">
</head>
<body>
<?php schogms_loading_screen_once(); ?>

<?php include __DIR__ . '/loading-screen.php'; ?>
<?php schogms_registrar_shell_open('Registrar masterlist'); ?>

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
                <i data-feather="upload" class="feather-icon" style="width:16px;height:16px"></i> Upload masterlist
            </button>
            <a href="check_all_cor_status.php" class="btn btn-outline-info btn-rounded ml-1">Check COR status</a>
        </div>
    </div>
</div>

<div class="modal fade" id="uploadModal" tabindex="-1" role="dialog" aria-labelledby="uploadModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="uploadModalLabel">Upload student masterlist</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <form id="uploadForm">
                    <div class="form-group">
                        <label for="session_campus">Campus</label>
                        <input type="text" class="form-control" id="session_campus" name="session_campus"
                               value="<?= htmlspecialchars($campus, ENT_QUOTES, 'UTF-8') ?>" readonly>
                    </div>
                    <div class="form-group">
                        <label for="academic_year">Academic year</label>
                        <select class="form-control" id="academic_year" name="academic_year" required>
                            <?php foreach ($academicYears as $ay): ?>
                                <option value="<?= htmlspecialchars($ay, ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($ay, ENT_QUOTES, 'UTF-8') ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="semester">Semester</label>
                        <select class="form-control" id="semester" name="semester" required>
                            <option value="">Select semester</option>
                            <option value="1st Semester">1st Semester</option>
                            <option value="2nd Semester">2nd Semester</option>
                            <option value="Summer">Summer</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="file_group">File group name</label>
                        <input type="text" class="form-control" id="file_group" name="file_group"
                               placeholder="e.g. Batch 1, 2024-2025 1st Semester" required>
                        <small class="form-text text-muted">Used to group this upload with filters below.</small>
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
            Campus: <strong><?= htmlspecialchars($campus, ENT_QUOTES, 'UTF-8') ?></strong>
            · Sorted A–Z by last name · Import encoding fixes: <code>?</code> is shown as proper <strong>ñ</strong> (or <strong>Ñ</strong> in ALL CAPS names)
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
            <h5><i data-feather="filter" style="width:18px;height:18px"></i> Filters &amp; search</h5>
            <button type="button" class="btn btn-sm btn-outline-secondary" id="rmlToggleAdvanced"
                    aria-expanded="false" aria-controls="rmlAdvancedFilters">
                More options
            </button>
        </div>
        <form method="get" action="masterlist.php" id="registrarMasterlistFilterForm">
            <input type="hidden" name="page" value="1">
            <div class="rml-filter-body">
                <div class="row rml-filter-grid">
                    <div class="col-md-6 col-lg-3 mb-3">
                        <label for="academicYearFilter">Academic year</label>
                        <select id="academicYearFilter" name="academic_year" class="form-control form-control-sm">
                            <option value="">All years</option>
                            <?php foreach ($academicYears as $ay): ?>
                                <option value="<?= htmlspecialchars($ay, ENT_QUOTES, 'UTF-8') ?>"
                                    <?= $academicYearFilter === $ay ? 'selected' : '' ?>><?= htmlspecialchars($ay, ENT_QUOTES, 'UTF-8') ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-6 col-lg-3 mb-3">
                        <label for="semesterFilter">Semester</label>
                        <select id="semesterFilter" name="semester" class="form-control form-control-sm">
                            <option value="">All semesters</option>
                            <?php foreach (['1st Semester', '2nd Semester', 'Summer'] as $sem): ?>
                                <option value="<?= htmlspecialchars($sem, ENT_QUOTES, 'UTF-8') ?>"
                                    <?= $semesterFilter === $sem ? 'selected' : '' ?>><?= htmlspecialchars($sem, ENT_QUOTES, 'UTF-8') ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-6 col-lg-3 mb-3">
                        <label for="fileGroupFilter">File group</label>
                        <select id="fileGroupFilter" name="file_group" class="form-control form-control-sm">
                            <option value="">All groups</option>
                            <?php foreach ($fileGroups as $fg): ?>
                                <option value="<?= htmlspecialchars($fg, ENT_QUOTES, 'UTF-8') ?>"
                                    <?= $fileGroupFilter === $fg ? 'selected' : '' ?>><?= htmlspecialchars($fg, ENT_QUOTES, 'UTF-8') ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-6 col-lg-3 mb-3">
                        <label for="limitFilter">Rows per page</label>
                        <select id="limitFilter" name="limit" class="form-control form-control-sm">
                            <?php foreach ([25, 50, 100, 200, 500, 1000] as $lim): ?>
                                <option value="<?= $lim ?>" <?= $limit === $lim ? 'selected' : '' ?>><?= $lim ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="collapse" id="rmlAdvancedFilters">
                    <div class="row rml-filter-grid pt-1">
                        <div class="col-md-6 col-lg-6 mb-3">
                            <label for="categoryFilter">Source file (upload batch)</label>
                            <select id="categoryFilter" name="category" class="form-control form-control-sm">
                                <option value="">All source files</option>
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?= htmlspecialchars($cat, ENT_QUOTES, 'UTF-8') ?>"
                                        <?= $category === $cat ? 'selected' : '' ?>><?= htmlspecialchars($cat, ENT_QUOTES, 'UTF-8') ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <div class="rml-filter-actions">
                <div class="rml-search-wrap">
                    <i data-feather="search" class="rml-search-icon"></i>
                    <input type="search" id="searchInput" name="search" class="form-control form-control-sm"
                           placeholder="Search name, ID, course, scholarship…"
                           value="<?= htmlspecialchars($searchTerm, ENT_QUOTES, 'UTF-8') ?>"
                           autocomplete="off">
                </div>
                <button type="submit" class="btn btn-primary btn-sm btn-rounded px-4">Apply filters</button>
                <a href="masterlist.php" class="btn btn-outline-secondary btn-sm btn-rounded">Clear all</a>
            </div>
        </form>
    </div>

    <?php if ($activeChips !== []): ?>
        <div class="rml-chips" aria-label="Active filters">
            <?php foreach ($activeChips as $chip): ?>
                <a class="rml-chip" href="<?= htmlspecialchars($chip['href'], ENT_QUOTES, 'UTF-8') ?>"
                   title="Remove this filter">
                    <?= htmlspecialchars($chip['label'], ENT_QUOTES, 'UTF-8') ?>
                    <span class="rml-chip-remove" aria-hidden="true">×</span>
                </a>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <div class="rml-meta-bar">
        <div>
            <strong><?= number_format($totalRecords) ?></strong> record(s) match
            <?php if ($totalRecords > 0): ?>
                · page <strong><?= (int) $page ?></strong> of <strong><?= (int) $totalPages ?></strong>
                · showing <strong><?= count($registrarData) ?></strong> row(s)
                <?php if (count($registrarData) > 0): ?>
                    · on page: <strong><?= $pageCor ?></strong> COR,
                    <strong><?= $pageCog ?></strong> COG,
                    <strong><?= $pageBoth ?></strong> both
                <?php endif; ?>
            <?php endif; ?>
        </div>
        <small class="text-muted"><i data-feather="zap" style="width:14px;height:14px"></i> Loaded in <?= $loadMs ?>ms</small>
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
                        <?php if (empty($registrarData)): ?>
                            <tr>
                                <td colspan="<?= 2 + count($extraColumns) ?>" class="text-center text-muted py-5">
                                    No records match your filters.
                                    <a href="masterlist.php">Clear filters</a> or upload a new masterlist file.
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

<footer class="footer text-center text-muted">
    All Rights Reserved 2026. Scholarship and Grants Management System (SchoGMS).
</footer>

<?php
schogms_registrar_shell_close();
schogms_registrar_footer_scripts(['sweetalert' => true]);
?>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
<script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
<script src="../../assets/js/registrar-masterlist.js"></script>
<script>
(function () {
    function showToast(message, type) {
        if (typeof Toastify === 'undefined') return;
        Toastify({
            text: message,
            duration: 3200,
            gravity: 'top',
            position: 'right',
            backgroundColor: type === 'error' ? '#dc3545' : type === 'success' ? '#28a745' : '#17a2b8'
        }).showToast();
    }

    var uploadForm = document.getElementById('uploadForm');
    if (!uploadForm) return;

    uploadForm.addEventListener('submit', function (e) {
        e.preventDefault();
        var file = document.getElementById('excelFile').files[0];
        if (!file) { showToast('Please select a file.', 'error'); return; }
        if (!document.getElementById('file_group').value.trim()) { showToast('Enter a file group name.', 'error'); return; }
        if (!document.getElementById('semester').value.trim()) { showToast('Select a semester.', 'error'); return; }

        Swal.fire({
            title: 'Upload masterlist?',
            text: 'This will import student records for your campus.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Yes, upload',
            cancelButtonText: 'Cancel'
        }).then(function (result) {
            if (!result.isConfirmed) return;
            var fd = new FormData(uploadForm);
            Swal.fire({ title: 'Uploading…', allowOutsideClick: false, didOpen: function () { Swal.showLoading(); } });
            fetch('submit_master_list.php', { method: 'POST', body: fd })
                .then(function (r) { return r.json(); })
                .then(function (data) {
                    if (data.success) {
                        Swal.fire({ title: 'Success', text: data.message || 'Import complete.', icon: 'success', timer: 3500 });
                        setTimeout(function () { location.reload(); }, 1800);
                    } else {
                        Swal.fire({ title: 'Error', text: data.error || 'Upload failed.', icon: 'error' });
                    }
                })
                .catch(function () {
                    Swal.fire({ title: 'Error', text: 'Network or server error.', icon: 'error' });
                });
        });
    });
})();
</script>
</body>
</html>
