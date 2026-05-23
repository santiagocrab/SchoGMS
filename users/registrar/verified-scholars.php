<?php
/**
 * Verified scholars — CHED masterlist + billing for registrar campus (same pattern as coordinator/chairman).
 */
include 'config/session.php';
require_once __DIR__ . '/inc/registrar_data.php';
require_once __DIR__ . '/inc/assets.php';
require_once __DIR__ . '/inc/registrar_nav.php';
require_once __DIR__ . '/../coordinator/inc/coordinator_data.php';
require_once __DIR__ . '/../chairman/inc/chairman_verified_data.php';
require_once __DIR__ . '/../coordinator/inc/verified_scholars_upload_guide.php';

if (($role ?? '') !== 'registrar') {
    header('Location: ../../index.php?ERROR=restricted');
    exit;
}

$campusFilter = trim((string) ($sheet_name ?? ''));
$scholarData = schogms_coordinator_ched_scholars($conn, $campusFilter);
$scholarRows = $scholarData['rows'];
$scholarError = $scholarData['error'];

$billingData = schogms_chairman_billing_rows($conn, $campusFilter, 1500);
$billingRows = $billingData['rows'];
$billingError = $billingData['error'];
$billingTotal = $billingData['total'];
?>
<!DOCTYPE html>
<html dir="ltr" lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/png" href="../../assets/images/logo.png">
    <title>Verified scholars — SchoGMS</title>
    <?php schogms_registrar_head(true); ?>
    <style>.preloader{display:none!important}</style>
</head>
<body>
<?php schogms_loading_screen_once(); ?>

<?php schogms_registrar_shell_open('Verified scholars'); ?>
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
                <p class="text-muted small mb-2 mb-md-0">
                    Campus: <strong><?= schogms_e($campusFilter !== '' ? $campusFilter : '—') ?></strong>
                    — CHED TDP scholars and billing/payment records for your campus.
                </p>
                <button type="button" class="btn btn-success btn-sm" data-toggle="modal" data-target="#uploadModal">
                    <i data-feather="upload" class="feather-icon"></i> Upload billing Excel
                </button>
            </div>

            <?php schogms_render_verified_scholars_upload_guide(false, $campusFilter); ?>

            <div class="modal fade" id="uploadModal" tabindex="-1" role="dialog">
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Upload verified scholars (billing Excel)</h5>
                            <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                        </div>
                        <div class="modal-body">
                            <?php schogms_render_verified_scholars_upload_guide(true, $campusFilter); ?>
                            <p class="small text-muted">
                                Template download:
                                <a href="../coordinator/download_verified_scholars_template.php">Billing Excel template</a>
                            </p>
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

            <div class="tab-content border-left border-right border-bottom p-3 bg-white mb-4">
                <div class="tab-pane fade show active" id="masterlistPane" role="tabpanel">
                    <h5 class="mb-2">TDP masterlist scholars</h5>
                    <p class="text-muted small mb-2">
                        To add or update rows, use <a href="ched_masterlist.php">CHED TDP masterlist</a> (coordinator workflow)
                        or ensure your campus data was imported by the scholarship coordinator.
                    </p>
                    <?php if ($scholarError !== ''): ?>
                        <div class="alert alert-warning"><?= schogms_e($scholarError) ?></div>
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
                                    <th>Remarks</th>
                                    <th>Uploaded</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($scholarRows === []): ?>
                                    <tr><td colspan="10" class="text-center text-muted">No masterlist records<?= $campusFilter !== '' ? ' for ' . schogms_e($campusFilter) : '' ?>.</td></tr>
                                <?php else: ?>
                                    <?php foreach ($scholarRows as $row): ?>
                                        <tr>
                                            <td><?= schogms_e((string) ($row['lastname'] ?? '')) ?></td>
                                            <td><?= schogms_e((string) ($row['firstname'] ?? '')) ?></td>
                                            <td><?= schogms_e((string) ($row['course_program_enrolled'] ?? '')) ?></td>
                                            <td><?= schogms_e((string) ($row['year_level'] ?? '')) ?></td>
                                            <td><?= schogms_e((string) ($row['total_units_enrolled'] ?? '')) ?></td>
                                            <td><?= schogms_e((string) ($row['sheet_name'] ?? '')) ?></td>
                                            <td><?= schogms_e((string) ($row['file_group'] ?? '')) ?></td>
                                            <td><?= schogms_e((string) ($row['status_of_enrollment'] ?? '')) ?></td>
                                            <td><?= schogms_e((string) ($row['remarks'] ?? '')) ?></td>
                                            <td><?= schogms_e((string) ($row['upload_time'] ?? '')) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="tab-pane fade" id="billingPane" role="tabpanel">
                    <h5 class="mb-2">Billing records</h5>
                    <?php if ($billingError !== ''): ?>
                        <div class="alert alert-warning"><?= schogms_e($billingError) ?></div>
                    <?php else: ?>
                        <p class="text-muted small">Showing <?= count($billingRows) ?> of <?= (int) $billingTotal ?> record(s)<?= $campusFilter !== '' ? ' for ' . schogms_e($campusFilter) : '' ?>.</p>
                    <?php endif; ?>
                    <div class="table-responsive">
                        <table id="billingTable" class="table table-striped table-bordered table-sm w-100">
                            <thead>
                                <tr>
                                    <th>Last name</th>
                                    <th>First name</th>
                                    <th>Scholarship</th>
                                    <th>Units</th>
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
                                    <tr><td colspan="11" class="text-center text-muted">No billing rows<?= $campusFilter !== '' ? ' for this campus' : '' ?>. Use <strong>Upload billing Excel</strong> or ask the chairman to import billing data.</td></tr>
                                <?php else: ?>
                                    <?php foreach ($billingRows as $row): ?>
                                        <tr>
                                            <td><?= schogms_e((string) ($row['last_name'] ?? '')) ?></td>
                                            <td><?= schogms_e((string) ($row['first_name'] ?? '')) ?></td>
                                            <td><?= schogms_e((string) ($row['scholarship_type'] ?? '')) ?></td>
                                            <td><?= schogms_e((string) ($row['units_enrolled'] ?? '')) ?></td>
                                            <td><?= schogms_e((string) ($row['course'] ?? '')) ?></td>
                                            <td><?= schogms_e((string) ($row['campus'] ?? '')) ?></td>
                                            <td><?= schogms_e((string) ($row['amount'] ?? '')) ?></td>
                                            <td><?= schogms_e((string) ($row['status'] ?? '')) ?></td>
                                            <td><?= schogms_e((string) ($row['first_semester'] ?? '')) ?></td>
                                            <td><?= schogms_e((string) ($row['second_semester'] ?? '')) ?></td>
                                            <td><?= schogms_e((string) ($row['payment_or_number'] ?? '')) ?></td>
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
schogms_registrar_shell_close();
schogms_registrar_footer_scripts(['datatables' => true, 'sweetalert' => true]);
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
    Swal.fire({ title: 'Uploading…', allowOutsideClick: false, didOpen: function () { Swal.showLoading(); } });
    fetch('process_excel.php', { method: 'POST', body: formData })
        .then(function (r) { return r.json(); })
        .then(function (data) {
            if (data.success) {
                Swal.fire({ icon: 'success', title: 'Imported', text: data.message || 'Done' })
                    .then(function () { window.location.reload(); });
            } else {
                Swal.fire('Error', data.error || 'Upload failed', 'error');
            }
        })
        .catch(function (err) {
            Swal.fire('Error', err.message || 'Upload failed', 'error');
        });
});
$(function () {
    if (!$.fn.DataTable) return;
    var opts = { pageLength: 25, lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, 'All']], order: [] };
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
