<?php
require __DIR__ . '/config/session.php';
require_once __DIR__ . '/../../inc/campus_access.php';
require_once __DIR__ . '/../../inc/schogms_upload_format.php';

$campuses = schogms_campus_catalog_names();
$recentUploads = [];
$res = $conn->query(
    "SELECT file_group, sheet_name, filename,
            MAX(upload_time) AS upload_date,
            COUNT(*) AS record_count
     FROM ched_masterlist
     GROUP BY file_group, sheet_name, filename
     ORDER BY upload_date DESC
     LIMIT 15"
);
if ($res) {
    while ($row = $res->fetch_assoc()) {
        $recentUploads[] = $row;
    }
}
?>
<!DOCTYPE html>
<html dir="ltr" lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/png" href="../../assets/images/logo.png">
    <title>Upload TDP masterlist — SchoGMS</title>
    <?php require_once __DIR__ . '/inc/assets.php'; schogms_chairman_head(false); ?>
    <style>.preloader{display:none!important}</style>
</head>
<body>
<?php schogms_loading_screen_once(); ?>

<?php require_once __DIR__ . '/inc/chairman_nav.php'; schogms_chairman_shell_open('Upload TDP'); ?>
        <div class="container-fluid">
            <div class="page-breadcrumb">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb m-0 p-0">
                        <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                        <li class="breadcrumb-item active">Upload TDP masterlist</li>
                    </ol>
                </nav>
            </div>

            <?php if (isset($_GET['success'])): ?>
                <div class="alert alert-success alert-dismissible fade show">
                    <?= htmlspecialchars((string) $_GET['success'], ENT_QUOTES, 'UTF-8') ?>
                    <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                </div>
            <?php endif; ?>

            <?php if (isset($_GET['error'])):
                $errors = [
                    'missing_fields' => 'Please fill in all required fields.',
                    'file_upload_failed' => 'File upload failed. Please try again.',
                    'invalid_file_type' => 'Invalid file type. Use .xlsx, .xls, or .csv.',
                    'file_too_large' => 'File is too large (max 10MB).',
                    'file_move_failed' => 'Could not save the uploaded file.',
                    'processing_failed' => 'Could not process the file.',
                    'access_denied' => 'Access denied.',
                ];
                $err = $errors[$_GET['error']] ?? 'An error occurred.';
                if (!empty($_GET['details'])) {
                    $err .= ' ' . htmlspecialchars((string) $_GET['details'], ENT_QUOTES, 'UTF-8');
                }
            ?>
                <div class="alert alert-danger alert-dismissible fade show">
                    <?= $err ?>
                    <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                </div>
            <?php endif; ?>

            <?php schogms_upload_format_render_guide('ched_tdp', '../../'); ?>

            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">Upload CHED TDP masterlist</h4>

                            <form action="submit_ched_tdp_upload.php" method="post" enctype="multipart/form-data" id="uploadForm">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="campus">Campus (sheet name)</label>
                                            <select class="form-control" id="campus" name="campus" required>
                                                <option value="">Select campus</option>
                                                <?php foreach ($campuses as $c): ?>
                                                    <option value="<?= htmlspecialchars($c, ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($c, ENT_QUOTES, 'UTF-8') ?></option>
                                                <?php endforeach;
                                                $campusRes = $conn->query('SELECT DISTINCT sheet_name FROM ched_masterlist ORDER BY sheet_name');
                                                if ($campusRes) {
                                                    while ($cr = $campusRes->fetch_assoc()) {
                                                        $sn = trim((string) ($cr['sheet_name'] ?? ''));
                                                        if ($sn !== '' && !in_array($sn, $campuses, true)) {
                                                            echo '<option value="' . htmlspecialchars($sn, ENT_QUOTES, 'UTF-8') . '">' . htmlspecialchars($sn, ENT_QUOTES, 'UTF-8') . '</option>';
                                                        }
                                                    }
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="academic_year">Academic year</label>
                                            <select class="form-control" id="academic_year" name="academic_year" required>
                                                <option value="">Select year</option>
                                                <option value="2026-2027">2026-2027</option>
                                                <option value="2025-2026">2025-2026</option>
                                                <option value="2024-2025" selected>2024-2025</option>
                                                <option value="2023-2024">2023-2024</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="semester">Semester</label>
                                            <select class="form-control" id="semester" name="semester" required>
                                                <option value="">Select semester</option>
                                                <option value="1st Semester" selected>1st Semester</option>
                                                <option value="2nd Semester">2nd Semester</option>
                                                <option value="Summer">Summer</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="file_group">File group / batch name</label>
                                            <input type="text" class="form-control" id="file_group" name="file_group"
                                                placeholder="e.g. CHED TDP 2024-2025 1st Sem" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="excel_file">Excel file</label>
                                    <input type="file" class="form-control-file" id="excel_file" name="excel_file"
                                        accept=".xlsx,.xls,.csv" required>
                                    <small class="form-text text-muted">.xlsx, .xls, or .csv — max 10MB</small>
                                </div>
                                <button type="submit" class="btn btn-primary">
                                    <i data-feather="upload" class="feather-icon"></i> Upload masterlist
                                </button>
                                <button type="reset" class="btn btn-secondary ml-2">Reset</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Recent uploads (MySQL)</h5>
                    <div class="table-responsive">
                        <table class="table table-striped table-sm">
                            <thead>
                                <tr>
                                    <th>File group</th>
                                    <th>Campus</th>
                                    <th>File</th>
                                    <th>Uploaded</th>
                                    <th>Records</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($recentUploads === []): ?>
                                    <tr><td colspan="5" class="text-muted text-center">No uploads yet.</td></tr>
                                <?php else: ?>
                                    <?php foreach ($recentUploads as $u): ?>
                                        <tr>
                                            <td><?= htmlspecialchars((string) $u['file_group'], ENT_QUOTES, 'UTF-8') ?></td>
                                            <td><?= htmlspecialchars((string) $u['sheet_name'], ENT_QUOTES, 'UTF-8') ?></td>
                                            <td><?= htmlspecialchars((string) $u['filename'], ENT_QUOTES, 'UTF-8') ?></td>
                                            <td><?= htmlspecialchars((string) $u['upload_date'], ENT_QUOTES, 'UTF-8') ?></td>
                                            <td><?= (int) $u['record_count'] ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                    <a href="ched_masterlist.php" class="btn btn-outline-primary btn-sm">View TDP masterlist</a>
                </div>
            </div>
        </div>
<?php
schogms_chairman_shell_close();
require_once __DIR__ . '/inc/assets.php';
schogms_chairman_footer_scripts([]);
?>
</body>
</html>
