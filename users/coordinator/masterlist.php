<?php
include '../config/session.php';
require_once __DIR__ . '/../../inc/schogms_upload_format.php';
?>
<!DOCTYPE html>
<html dir="ltr" lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <!-- Tell the browser to be responsive to screen width -->
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <!-- Favicon icon -->
    <link rel="icon" type="image/png" sizes="16x16" href="../../assets/images/logo.png">
    <title> Scholarship and Grants Management System | SchoGMS </title>
    <!-- Custom CSS -->    <?php require_once __DIR__ . '/inc/assets.php'; schogms_coordinator_head(true); ?>
    <style>.preloader{display:none!important}</style>
    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
<![endif]-->
</head>

<body>
<?php schogms_loading_screen_once(); ?>

    <?php include 'loading-screen.php'; ?>
    <!-- ============================================================== -->
    <!-- Preloader - style you can find in spinners.css -->
    <!-- ============================================================== -->
    <!-- <div class="preloader">
        <div class="lds-ripple">
            <div class="lds-pos"></div>
            <div class="lds-pos"></div>
        </div>
    </div> -->
    <!-- ============================================================== -->
    <!-- Main wrapper - style you can find in pages.scss -->
    <!-- ============================================================== -->
    <?php
    require_once __DIR__ . '/inc/coordinator_nav.php';
    require_once __DIR__ . '/../registrar/inc/registrar_data.php';
    require_once __DIR__ . '/inc/masterlist_rows.php';

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
    $docIndex = [];

    if ($campus === '' || !($conn instanceof mysqli)) {
        $loadError = 'No campus assigned to your account or database unavailable.';
    } else {
        $categories = schogms_registrar_masterlist_categories($campus, $conn);
        $fileGroups = schogms_registrar_masterlist_file_groups($campus, $conn);
        $docIndex = schogms_coordinator_document_index($conn, $campus);
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
    }

    schogms_coordinator_shell_open('Registrar masterlist');
    ?>

            <!-- ============================================================== -->
            <!-- Bread crumb and right sidebar toggle -->
            <!-- ============================================================== -->
            <div class="page-breadcrumb">
                <div class="row">
                    <div class="col-7 align-self-center">
                        <!-- <h3 class="page-title text-truncate text-dark font-weight-medium mb-1">Good Coordinator!</h3> -->
                        <div class="d-flex align-items-center">
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb m-0 p-0">
                                    <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                                    <li class="breadcrumb-item active">Registrar masterlist</li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                    <div class="col-5 align-self-center">
                        <div class="customize-input float-right">
                            <button type="button" class="btn waves-effect waves-light btn-rounded btn-success"
                                data-toggle="modal" data-target="#uploadModal">
                                Upload File
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal fade" id="uploadModal" tabindex="-1" role="dialog" aria-labelledby="uploadModalLabel"
                aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="uploadModalLabel">Upload Student Data</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <?php schogms_upload_format_modal_hint('registrar_masterlist', '../../'); ?>
                            <form id="uploadForm">
                                <div class="mb-3">
                                    <label for="file-group" class="form-label">File Group</label>
                                    <input type="text" class="form-control" id="file_group" name="file_group"
                                        placeholder="Input file group name">
                                </div>
                                <div class="mb-3">
                                    <label for="excelFile" class="form-label">Choose Excel File</label>
                                    <input type="file" class="form-control" id="excelFile" name="excelFile"
                                        accept=".xls,.xlsx, .csv">
                                </div>
                                <button type="submit" class="btn btn-primary">Upload</button>
                            </form>
                            <div id="message"></div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Modal -->
            <!-- ============================================================== -->
            <!-- End Bread crumb and right sidebar toggle -->
            <!-- ============================================================== -->
            <!-- ============================================================== -->
            <!-- Container fluid  -->
            <!-- ============================================================== -->
            <!-- Container fluid  -->
            <!-- ============================================================== -->
            <div class="container-fluid">
                <!-- ============================================================== -->
                <!-- Start Page Content -->
                <!-- ============================================================== -->
                <!-- basic table -->
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <?php if ($loadError !== ''): ?>
                                    <div class="alert alert-warning"><?= htmlspecialchars($loadError, ENT_QUOTES, 'UTF-8') ?></div>
                                <?php else: ?>
                                    <h4 class="card-title">Registrar masterlist</h4>
                                    <p class="text-muted mb-3">
                                        Campus: <strong><?= htmlspecialchars($campus, ENT_QUOTES, 'UTF-8') ?></strong>.
                                        Data uploaded by the registrar for your campus. Use filters and search, then click <strong>Apply filters</strong>.
                                    </p>

                                    <div class="card border-secondary mb-3">
                                        <div class="card-body py-3">
                                            <form method="get" action="masterlist.php" id="registrarFilterForm">
                                                <div class="row">
                                                    <div class="col-md-6 col-lg-3 mb-2">
                                                        <label class="small font-weight-bold" for="categoryFilter">Source file</label>
                                                        <select id="categoryFilter" name="category" class="form-control form-control-sm">
                                                            <option value="">All files</option>
                                                            <?php foreach ($categories as $cat): ?>
                                                                <option value="<?= htmlspecialchars($cat, ENT_QUOTES, 'UTF-8') ?>"
                                                                    <?= $category === $cat ? 'selected' : '' ?>><?= htmlspecialchars($cat, ENT_QUOTES, 'UTF-8') ?></option>
                                                            <?php endforeach; ?>
                                                        </select>
                                                    </div>
                                                    <div class="col-md-6 col-lg-3 mb-2">
                                                        <label class="small font-weight-bold" for="fileGroupFilter">File group</label>
                                                        <select id="fileGroupFilter" name="file_group" class="form-control form-control-sm">
                                                            <option value="">All groups</option>
                                                            <?php foreach ($fileGroups as $fg): ?>
                                                                <option value="<?= htmlspecialchars($fg, ENT_QUOTES, 'UTF-8') ?>"
                                                                    <?= $fileGroupFilter === $fg ? 'selected' : '' ?>><?= htmlspecialchars($fg, ENT_QUOTES, 'UTF-8') ?></option>
                                                            <?php endforeach; ?>
                                                        </select>
                                                    </div>
                                                    <div class="col-md-6 col-lg-2 mb-2">
                                                        <label class="small font-weight-bold" for="academicYearFilter">Academic year</label>
                                                        <select id="academicYearFilter" name="academic_year" class="form-control form-control-sm">
                                                            <option value="">All</option>
                                                            <?php foreach (['2026-2027', '2025-2026', '2024-2025', '2023-2024', '2022-2023'] as $ay): ?>
                                                                <option value="<?= $ay ?>" <?= $academicYearFilter === $ay ? 'selected' : '' ?>><?= $ay ?></option>
                                                            <?php endforeach; ?>
                                                        </select>
                                                    </div>
                                                    <div class="col-md-6 col-lg-2 mb-2">
                                                        <label class="small font-weight-bold" for="semesterFilter">Semester</label>
                                                        <select id="semesterFilter" name="semester" class="form-control form-control-sm">
                                                            <option value="">All</option>
                                                            <?php foreach (['1st Semester', '2nd Semester', 'Summer'] as $sem): ?>
                                                                <option value="<?= htmlspecialchars($sem, ENT_QUOTES, 'UTF-8') ?>"
                                                                    <?= $semesterFilter === $sem ? 'selected' : '' ?>><?= htmlspecialchars($sem, ENT_QUOTES, 'UTF-8') ?></option>
                                                            <?php endforeach; ?>
                                                        </select>
                                                    </div>
                                                    <div class="col-md-6 col-lg-2 mb-2">
                                                        <label class="small font-weight-bold" for="limitFilter">Per page</label>
                                                        <select id="limitFilter" name="limit" class="form-control form-control-sm">
                                                            <?php foreach ([25, 50, 100, 200, 500, 1000] as $lim): ?>
                                                                <option value="<?= $lim ?>" <?= $limit === $lim ? 'selected' : '' ?>><?= $lim ?></option>
                                                            <?php endforeach; ?>
                                                        </select>
                                                    </div>
                                                    <div class="col-md-8 col-lg-6 mb-2">
                                                        <label class="small font-weight-bold" for="searchInput">Search</label>
                                                        <input type="text" id="searchInput" name="search" class="form-control form-control-sm"
                                                               placeholder="Name, ID, course, scholarship…"
                                                               value="<?= htmlspecialchars($searchTerm, ENT_QUOTES, 'UTF-8') ?>">
                                                    </div>
                                                    <div class="col-md-4 col-lg-6 mb-2 d-flex align-items-end flex-wrap">
                                                        <button type="submit" class="btn btn-success btn-sm btn-rounded mr-2 mb-1">Apply filters</button>
                                                        <a href="masterlist.php" class="btn btn-outline-secondary btn-sm btn-rounded mb-1">Clear</a>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>

                                    <div class="alert alert-info py-2 mb-3">
                                        <strong>Records:</strong>
                                        <span id="recordCount"><?= number_format($totalRecords) ?> total
                                            <?php if ($totalRecords > 0): ?>
                                                · page <?= (int) $page ?> of <?= (int) $totalPages ?>
                                                · showing <?= count($registrarData) ?> row(s)
                                            <?php endif; ?>
                                        </span>
                                    </div>

                                    <?php if ($totalPages > 1): ?>
                                        <nav class="mb-2" aria-label="Masterlist pages">
                                            <ul class="pagination pagination-sm mb-0">
                                                <?php
                                                $baseQs = $_GET;
                                                unset($baseQs['page']);
                                                $qsBase = http_build_query($baseQs);
                                                $mkPage = static function (int $p) use ($qsBase): string {
                                                    return 'masterlist.php?' . ($qsBase !== '' ? $qsBase . '&' : '') . 'page=' . $p;
                                                };
                                                if ($page > 1): ?>
                                                    <li class="page-item"><a class="page-link" href="<?= htmlspecialchars($mkPage($page - 1), ENT_QUOTES, 'UTF-8') ?>">Prev</a></li>
                                                <?php endif;
                                                for ($p = max(1, $page - 2); $p <= min($totalPages, $page + 2); $p++): ?>
                                                    <li class="page-item <?= $p === $page ? 'active' : '' ?>">
                                                        <a class="page-link" href="<?= htmlspecialchars($mkPage($p), ENT_QUOTES, 'UTF-8') ?>"><?= $p ?></a>
                                                    </li>
                                                <?php endfor;
                                                if ($page < $totalPages): ?>
                                                    <li class="page-item"><a class="page-link" href="<?= htmlspecialchars($mkPage($page + 1), ENT_QUOTES, 'UTF-8') ?>">Next</a></li>
                                                <?php endif; ?>
                                            </ul>
                                        </nav>
                                    <?php endif; ?>

                                    <div class="table-responsive">
                                            <table id="masterlist_table" class="table table-striped table-bordered no-wrap">
                                                <thead>
                                                    <tr>
                                                        <th hidden>File Name</th>
                                                        <th>COR & COG</th>
                                                        <th>Full Name</th>
                                                        <th>Ext. Name</th>
                                                        <th>ID Number</th>
                                                        <th>Gender</th>
                                                        <th>Student Type</th>
                                                        <th>Year Level</th>
                                                        <th>Attended</th>
                                                        <th>Course</th>
                                                        <th>Curriculum</th>
                                                        <th>Scholarship</th>
                                                        <th>GPA</th>
                                                        <th>CGPA</th>
                                                        <th>% Pass</th>
                                                        <th>Grade Remarks</th>
                                                        <th>Enrolled</th>
                                                        <th>Lec. Unit</th>
                                                        <th>Lab. Unit</th>
                                                        <th>COR Printed</th>
                                                        <th>Billing Profile</th>
                                                        <th>Misc. Fee Total</th>
                                                        <th>Misc. Fee Paid</th>
                                                        <th>Tuition Fee Total</th>
                                                        <th>Tuition Fee Paid</th>
                                                        <th>Street</th>
                                                        <th>Barangay</th>
                                                        <th>Municipality/City</th>
                                                        <th>Province</th>
                                                        <th>Zip Code</th>
                                                        <th>Date of Birth</th>
                                                        <th>Place of Birth</th>
                                                        <th>Civil Status</th>
                                                        <th>Tribe</th>
                                                        <th>Religion</th>
                                                        <th>Year Admitted</th>
                                                        <th>Semester Admitted</th>
                                                        <th>School Last Attended</th>
                                                        <th>Year Last Attended</th>
                                                        <th>Semester Last Attended</th>
                                                        <th>High School Graduated</th>
                                                        <th>Exam Date</th>
                                                        <th>Exam Rating</th>
                                                        <th>Ref. Number</th>
                                                        <th>Guardian</th>
                                                        <th>Address</th>
                                                        <th>Contact Nos.</th>
                                                        <th>Blood Type</th>
                                                        <th>Email Address</th>
                                                        <th>Mobile Number</th>
                                                        <th>DEPED Number</th>
                                                        <th>Scholarship Grant</th>
                                                        <th>Scholarship Allowance</th>
                                                        <th>Documents Submitted</th>
                                                        <th>Lacking Document(s)</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php if (empty($registrarData)): ?>
                                                        <tr>
                                                            <td colspan="44" class="text-center text-muted py-4">
                                                                No records match your filters. Try <a href="masterlist.php">clearing filters</a>
                                                                or ask the registrar to upload masterlist data for this campus.
                                                            </td>
                                                        </tr>
                                                    <?php else:
                                                        foreach ($registrarData as $row):
                                                            $docRow = [
                                                                'lastname' => $row['last_name'] ?? '',
                                                                'firstname' => $row['first_name'] ?? '',
                                                                'middlename' => $row['middle_name'] ?? '',
                                                            ];
                                                            $docs = schogms_coordinator_resolve_doc($docIndex, $docRow);
                                                            $hasCor = $docs['has_cor'];
                                                            $hasCog = $docs['has_cog'];
                                                    ?>
                                                        <tr>
                                                            <td hidden><?= htmlspecialchars((string) ($row['filename'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                                                            <td>
                                                                <?php if ($hasCor && $hasCog): ?>
                                                                    <span class="badge badge-success">COR</span>
                                                                    <span class="badge badge-primary">COG</span>
                                                                <?php elseif ($hasCor): ?>
                                                                    <span class="badge badge-success">COR</span>
                                                                <?php elseif ($hasCog): ?>
                                                                    <span class="badge badge-primary">COG</span>
                                                                <?php else: ?>
                                                                    <span class="badge badge-secondary">No COR/COG</span>
                                                                <?php endif; ?>
                                                            </td>
                                                            <td><?= htmlspecialchars(trim((string) ($row['last_name'] ?? '') . ', ' . (string) ($row['first_name'] ?? '') . ' ' . (string) ($row['middle_name'] ?? '')), ENT_QUOTES, 'UTF-8') ?></td>
                                                            <td><?= htmlspecialchars((string) ($row['ext_name'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                                                            <td><?= htmlspecialchars((string) ($row['id_number'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                                                            <td><?= htmlspecialchars((string) ($row['gender'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                                                            <td><?= htmlspecialchars((string) ($row['student_type'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                                                            <td><?= htmlspecialchars((string) ($row['year_level'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                                                            <td><?= htmlspecialchars((string) ($row['attended'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                                                            <td><?= htmlspecialchars((string) ($row['course'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                                                            <td><?= htmlspecialchars((string) ($row['curriculum'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                                                            <td><?= htmlspecialchars((string) ($row['scholarship'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                                                            <td><?= htmlspecialchars((string) ($row['gpa'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                                                            <td><?= htmlspecialchars((string) ($row['cgpa'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                                                            <td><?= htmlspecialchars((string) ($row['pass_percentage'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                                                            <td><?= htmlspecialchars((string) ($row['grade_remarks'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                                                            <td><?= htmlspecialchars((string) ($row['enrolled'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                                                            <td><?= htmlspecialchars((string) ($row['lec_unit'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                                                            <td><?= htmlspecialchars((string) ($row['lab_unit'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                                                            <td><?= htmlspecialchars((string) ($row['cor_printed'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                                                            <td><?= htmlspecialchars((string) ($row['billing_profile'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                                                            <td><?= htmlspecialchars((string) ($row['misc_fee_total'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                                                            <td><?= htmlspecialchars((string) ($row['misc_fee_paid'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                                                            <td><?= htmlspecialchars((string) ($row['tuition_fee_total'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                                                            <td><?= htmlspecialchars((string) ($row['tuition_fee_paid'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                                                            <td><?= htmlspecialchars((string) ($row['street'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                                                            <td><?= htmlspecialchars((string) ($row['barangay'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                                                            <td><?= htmlspecialchars((string) ($row['municipality_city'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                                                            <td><?= htmlspecialchars((string) ($row['province'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                                                            <td><?= htmlspecialchars((string) ($row['zip_code'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                                                            <td><?= htmlspecialchars((string) ($row['date_of_birth'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                                                            <td><?= htmlspecialchars((string) ($row['place_of_birth'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                                                            <td><?= htmlspecialchars((string) ($row['civil_status'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                                                            <td><?= htmlspecialchars((string) ($row['tribe'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                                                            <td><?= htmlspecialchars((string) ($row['religion'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                                                            <td><?= htmlspecialchars((string) ($row['year_admitted'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                                                            <td><?= htmlspecialchars((string) ($row['semester_admitted'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                                                            <td><?= htmlspecialchars((string) ($row['school_last_attended'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                                                            <td><?= htmlspecialchars((string) ($row['year_last_attended'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                                                            <td><?= htmlspecialchars((string) ($row['semester_last_attended'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                                                            <td><?= htmlspecialchars((string) ($row['high_school_graduated'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                                                            <td><?= htmlspecialchars((string) ($row['exam_date'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                                                            <td><?= htmlspecialchars((string) ($row['exam_rating'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                                                            <td><?= htmlspecialchars((string) ($row['ref_number'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                                                            <td><?= htmlspecialchars((string) ($row['guardian'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                                                            <td><?= htmlspecialchars((string) ($row['guardian_address'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                                                            <td><?= htmlspecialchars((string) ($row['guardian_contact'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                                                            <td><?= htmlspecialchars((string) ($row['blood_type'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                                                            <td><?= htmlspecialchars((string) ($row['email_address'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                                                            <td><?= htmlspecialchars((string) ($row['mobile_number'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                                                            <td><?= htmlspecialchars((string) ($row['deped_number'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                                                            <td><?= htmlspecialchars((string) ($row['scholarship_grant'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                                                            <td><?= htmlspecialchars((string) ($row['scholarship_allowance'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                                                            <td><?= htmlspecialchars((string) ($row['documents_submitted'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                                                            <td><?= htmlspecialchars((string) ($row['lacking_documents'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                                                        </tr>
                                                    <?php endforeach; endif; ?>
                                                </tbody>
                                            </table>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <footer class="footer text-center text-muted">
                All Rights Reserved 2026. Scholarship and Grants Management System <a href="">(SchoGMS)</a>.
            </footer>
    <?php
    schogms_coordinator_shell_close();
    schogms_coordinator_footer_scripts(['sweetalert' => true]);
    ?>
            <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
            <script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
            <script>
                (function () {
                    var limitEl = document.getElementById('limitFilter');
                    if (limitEl) {
                        limitEl.addEventListener('change', function () {
                            var form = document.getElementById('registrarFilterForm');
                            if (!form) return;
                            var pageInput = document.createElement('input');
                            pageInput.type = 'hidden';
                            pageInput.name = 'page';
                            pageInput.value = '1';
                            form.appendChild(pageInput);
                            form.submit();
                        });
                    }

                    var uploadForm = document.getElementById('uploadForm');
                    if (!uploadForm) return;
                    uploadForm.addEventListener('submit', function (event) {
                    event.preventDefault();

                    const fileGroupInput = document.getElementById('file_group');
                    const fileInput = document.getElementById('excelFile');
                    const file = fileInput.files[0];

                    if (!file) {
                        showToast("Please select a file!", "error");
                        return;
                    }

                    const fileGroup = fileGroupInput.value.trim();
                    if (!fileGroup) {
                        showToast("Please enter a file group name!", "error");
                        return;
                    }

                    // Validate file type (Accepts CSV, XLS, XLSX)
                    const allowedTypes = [
                        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', // .xlsx
                        'application/vnd.ms-excel', // .xls
                        'text/csv' // .csv
                    ];

                    const fileExtension = file.name.split('.').pop().toLowerCase();
                    const allowedExtensions = ['xls', 'xlsx', 'csv'];

                    if (!allowedTypes.includes(file.type) && !allowedExtensions.includes(fileExtension)) {
                        showToast("Please upload a valid Excel or CSV file.", "error");
                        console.error("Invalid file type:", file.type);
                        return;
                    }

                    // Show SweetAlert confirmation before proceeding
                    Swal.fire({
                        title: "Are you sure?",
                        text: "Do you want to upload and process this file?",
                        icon: "question",
                        showCancelButton: true,
                        confirmButtonText: "Yes, upload it!",
                        cancelButtonText: "Cancel",
                        reverseButtons: true
                    }).then((result) => {
                        if (result.isConfirmed) {
                            const formData = new FormData();
                            formData.append('file_group', fileGroup);
                            formData.append('excelFile', file);

                            // Display loading message
                            Swal.fire({
                                title: "Uploading...",
                                text: "Please wait while the file is being uploaded and processed.",
                                icon: "info",
                                allowOutsideClick: false,
                                showConfirmButton: false,
                                willOpen: () => {
                                    Swal.showLoading();
                                }
                            });

                            // Send file via Fetch API
                            fetch('submit_master_list.php', {
                                method: 'POST',
                                body: formData
                            })
                                .then(response => response.json())
                                .then(data => {
                                    if (data.success) {
                                        Swal.fire({
                                            title: "Success!",
                                            text: data.message || "File processed successfully!",
                                            icon: "success",
                                            timer: 3000
                                        });
                                        setTimeout(() => {
                                            location.reload();
                                        }, 1500);
                                    } else {
                                        Swal.fire({
                                            title: "Error!",
                                            text: data.error || "An error occurred during file processing.",
                                            icon: "error"
                                        });
                                        console.error("Server error:", data.error);
                                    }
                                })
                                .catch(error => {
                                    Swal.fire({
                                        title: "Upload Failed!",
                                        text: "An error occurred while uploading the file.",
                                        icon: "error"
                                    });
                                    console.error("Fetch error:", error);
                                });
                        }
                    });
                });
                })();
            </script>

</body>

</html>