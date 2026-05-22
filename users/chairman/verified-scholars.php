<?php
require __DIR__ . '/config/session.php';
require_once __DIR__ . '/inc/chairman_verified_data.php';
require_once __DIR__ . '/../coordinator/inc/verified_scholars_upload_guide.php';

$campusFilter = trim($_GET['campus'] ?? '');
$scholarData = schogms_coordinator_ched_scholars($conn, '', 1000);
$scholarRows = $scholarData['rows'];
$scholarError = $scholarData['error'];

$billingData = schogms_chairman_billing_rows($conn, $campusFilter, 1500);
$billingRows = $billingData['rows'];
$billingError = $billingData['error'];
$billingTotal = $billingData['total'];
$billingCampuses = schogms_chairman_billing_campuses($conn);
?>
<!DOCTYPE html>
<html dir="ltr" lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/png" href="../../assets/images/logo.png">
    <title>Verified scholars — SchoGMS</title>
    <?php require_once __DIR__ . '/inc/assets.php'; schogms_chairman_head(true); ?>
    <style>.preloader{display:none!important}</style>
</head>
<body>
<?php require_once __DIR__ . '/inc/chairman_nav.php'; schogms_chairman_shell_open('Verified scholars'); ?>
        <div class="container-fluid">
            <div class="page-breadcrumb">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb m-0 p-0">
                        <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                        <li class="breadcrumb-item active">Verified scholars</li>
                    </ol>
                </nav>
            </div>

            <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap">
                <p class="text-muted small mb-2 mb-md-0">System-wide view: CHED masterlist scholars and billing/payment imports.</p>
                <button type="button" class="btn btn-success btn-sm" data-toggle="modal" data-target="#uploadModal">
                    <i data-feather="upload" class="feather-icon"></i> Upload billing Excel
                </button>
            </div>

            <?php schogms_render_verified_scholars_upload_guide(false, 'All campuses'); ?>

            <div class="modal fade" id="uploadModal" tabindex="-1" role="dialog">
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Upload verified scholars (billing Excel)</h5>
                            <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                        </div>
                        <div class="modal-body">
                            <?php schogms_render_verified_scholars_upload_guide(true, 'All campuses'); ?>
                            <form id="uploadForm">
                                <div class="mb-3">
                                    <label for="excelFile">Excel file (.xlsx or .xls)</label>
                                    <input type="file" class="form-control" id="excelFile" name="excelFile"
                                        accept=".xlsx,.xls" required>
                                </div>
                                <button type="submit" class="btn btn-primary">Upload &amp; import</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <ul class="nav nav-tabs" id="verifiedTabs" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" id="masterlist-tab" data-toggle="tab" href="#masterlistPane" role="tab">CHED masterlist</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="billing-tab" data-toggle="tab" href="#billingPane" role="tab">Billing / payments</a>
                </li>
            </ul>

            <div class="tab-content border-left border-right border-bottom p-3 bg-white">
                <div class="tab-pane fade show active" id="masterlistPane" role="tabpanel">
                    <h5 class="mb-2">TDP masterlist scholars (all campuses)</h5>
                    <?php if ($scholarError !== ''): ?>
                        <div class="alert alert-warning"><?= htmlspecialchars($scholarError, ENT_QUOTES, 'UTF-8') ?></div>
                    <?php endif; ?>
                    <div class="table-responsive">
                        <table id="masterlistTable" class="table table-striped table-bordered table-sm w-100">
                            <thead>
                                <tr>
                                    <th>Last name</th>
                                    <th>First name</th>
                                    <th>Course</th>
                                    <th>Year</th>
                                    <th>Units</th>
                                    <th>Campus</th>
                                    <th>File group</th>
                                    <th>Enrollment</th>
                                    <th>Uploaded</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($scholarRows === []): ?>
                                    <tr><td colspan="9" class="text-center text-muted">No masterlist records yet.</td></tr>
                                <?php else: ?>
                                    <?php foreach ($scholarRows as $row): ?>
                                        <tr>
                                            <td><?= htmlspecialchars((string) ($row['lastname'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                                            <td><?= htmlspecialchars((string) ($row['firstname'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                                            <td><?= htmlspecialchars((string) ($row['course_program_enrolled'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                                            <td><?= htmlspecialchars((string) ($row['year_level'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                                            <td><?= htmlspecialchars((string) ($row['total_units_enrolled'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                                            <td><?= htmlspecialchars((string) ($row['sheet_name'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                                            <td><?= htmlspecialchars((string) ($row['file_group'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                                            <td><?= htmlspecialchars((string) ($row['status_of_enrollment'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                                            <td><?= htmlspecialchars((string) ($row['upload_time'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="tab-pane fade" id="billingPane" role="tabpanel">
                    <div class="d-flex justify-content-between align-items-center mb-2 flex-wrap">
                        <h5 class="mb-0">Billing records</h5>
                        <form method="get" class="form-inline">
                            <label class="mr-2 small">Campus</label>
                            <select name="campus" class="form-control form-control-sm mr-2" onchange="this.form.submit()">
                                <option value="">All campuses</option>
                                <?php foreach ($billingCampuses as $c): ?>
                                    <option value="<?= htmlspecialchars($c, ENT_QUOTES, 'UTF-8') ?>"
                                        <?= strcasecmp($c, $campusFilter) === 0 ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($c, ENT_QUOTES, 'UTF-8') ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </form>
                    </div>
                    <?php if ($billingError !== ''): ?>
                        <div class="alert alert-warning"><?= htmlspecialchars($billingError, ENT_QUOTES, 'UTF-8') ?></div>
                    <?php else: ?>
                        <p class="text-muted small">Showing <?= count($billingRows) ?> of <?= (int) $billingTotal ?> record(s)<?= $campusFilter !== '' ? ' for ' . htmlspecialchars($campusFilter, ENT_QUOTES, 'UTF-8') : '' ?>.</p>
                    <?php endif; ?>
                    <div class="table-responsive">
                        <table id="billingTable" class="table table-striped table-bordered table-sm w-100">
                            <thead>
                                <tr>
                                    <th>Last name</th>
                                    <th>First name</th>
                                    <th>Scholarship</th>
                                    <th>Course</th>
                                    <th>Campus</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th>1st sem</th>
                                    <th>2nd sem</th>
                                    <th>Payment OR</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($billingRows === []): ?>
                                    <tr><td colspan="10" class="text-center text-muted">No billing rows<?= $campusFilter !== '' ? ' for this campus' : '' ?>. Use Upload billing Excel.</td></tr>
                                <?php else: ?>
                                    <?php foreach ($billingRows as $row): ?>
                                        <tr>
                                            <td><?= htmlspecialchars((string) ($row['last_name'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                                            <td><?= htmlspecialchars((string) ($row['first_name'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                                            <td><?= htmlspecialchars((string) ($row['scholarship_type'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                                            <td><?= htmlspecialchars((string) ($row['course'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                                            <td><?= htmlspecialchars((string) ($row['campus'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                                            <td><?= htmlspecialchars((string) ($row['amount'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                                            <td><?= htmlspecialchars((string) ($row['status'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                                            <td><?= htmlspecialchars((string) ($row['first_semester'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                                            <td><?= htmlspecialchars((string) ($row['second_semester'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                                            <td><?= htmlspecialchars((string) ($row['payment_or_number'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
<?php
schogms_chairman_shell_close();
require_once __DIR__ . '/inc/assets.php';
schogms_chairman_footer_scripts(['datatables' => true, 'sweetalert' => true]);
?>
<script>
document.getElementById('uploadForm').addEventListener('submit', function (event) {
    event.preventDefault();
    const fileInput = document.getElementById('excelFile');
    const file = fileInput.files[0];
    if (!file) {
        Swal.fire('No file', 'Please choose an Excel file.', 'warning');
        return;
    }
    const ext = file.name.split('.').pop().toLowerCase();
    if (ext !== 'xls' && ext !== 'xlsx') {
        Swal.fire('Invalid file', 'Use .xls or .xlsx only.', 'error');
        return;
    }
    const formData = new FormData();
    formData.append('excelFile', file);
    Swal.fire({ title: 'Uploading…', allowOutsideClick: false, didOpen: () => Swal.showLoading() });
    fetch('process_excel.php', { method: 'POST', body: formData })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                Swal.fire({ icon: 'success', title: 'Imported', text: data.message || 'Done' })
                    .then(() => { window.location.href = 'verified-scholars.php?campus=' + encodeURIComponent(''); });
            } else {
                Swal.fire('Error', data.error || 'Upload failed', 'error');
            }
        })
        .catch(err => Swal.fire('Error', err.message || 'Upload failed', 'error'));
});
$(function () {
    if (!$.fn.DataTable) return;
    const opts = { pageLength: 25, lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, 'All']], order: [] };
    if ($('#masterlistTable').length && !$.fn.dataTable.isDataTable('#masterlistTable')) {
        $('#masterlistTable').DataTable(opts);
    }
    if ($('#billingTable').length && !$.fn.dataTable.isDataTable('#billingTable')) {
        $('#billingTable').DataTable(opts);
    }
});
</script>
</body>
</html>
