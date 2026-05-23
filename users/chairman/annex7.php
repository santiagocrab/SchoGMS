<?php
/**
 * Chairman: review coordinator Annex 7 submissions (file_submissions).
 */
include 'config/session.php';
require_once __DIR__ . '/../../inc/schogms_annex7.php';
require_once __DIR__ . '/inc/chairman_nav.php';

if (!isset($conn) || !($conn instanceof mysqli)) {
    require 'config/conn.php';
}

$statusFilter = trim((string) ($_GET['status'] ?? 'pending'));
if (!in_array($statusFilter, ['all', 'pending', 'approved', 'rejected'], true)) {
    $statusFilter = 'pending';
}

$counts = schogms_annex7_counts($conn);
$rows = schogms_annex7_list($conn, $statusFilter);
$tabBase = 'annex7.php';
?>
<!DOCTYPE html>
<html dir="ltr" lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/png" href="../../assets/images/logo.png">
    <title>Annex 7 review — SchoGMS</title>
    <?php require_once __DIR__ . '/inc/assets.php'; schogms_chairman_head(true); ?>
    <style>
        .annex-hero { background: linear-gradient(135deg, #1565c0 0%, #00838f 100%); color: #fff; border-radius: 12px; padding: 1.15rem 1.35rem; margin-bottom: 1.25rem; }
        .annex-hero h4 { color: #fff; margin-bottom: 0.35rem; font-weight: 600; }
        .annex-hero p { margin: 0; opacity: 0.92; font-size: 0.9rem; }
    </style>
</head>
<body>
<?php schogms_loading_screen_once(); ?>
<?php schogms_chairman_shell_open('Annex 7 review'); ?>

<div class="page-breadcrumb">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb m-0 p-0">
            <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="file_groups.php">File groups</a></li>
            <li class="breadcrumb-item active">Annex 7</li>
        </ol>
    </nav>
</div>

<div class="container-fluid">
    <div class="annex-hero">
        <h4>Annex 7 — Scholarship Grant Utilization</h4>
        <p>
            Coordinators upload Annex 7 on <strong>Submit Form</strong>. Review each file here, preview in the browser,
            then <strong>Approve</strong> or <strong>Decline</strong>. CHED masterlist file groups are reviewed separately on
            <a href="file_groups.php" class="text-white font-weight-bold"><u>File groups</u></a>.
        </p>
    </div>

    <ul class="nav nav-pills mb-3 flex-wrap">
        <?php
        $tabs = [
            'all' => ['label' => 'All', 'count' => $counts['all']],
            'pending' => ['label' => 'Pending', 'count' => $counts['pending']],
            'approved' => ['label' => 'Approved', 'count' => $counts['approved']],
            'rejected' => ['label' => 'Declined', 'count' => $counts['rejected']],
        ];
        foreach ($tabs as $key => $tab):
            $href = $tabBase . '?status=' . rawurlencode($key);
            ?>
        <li class="nav-item">
            <a class="nav-link <?= $statusFilter === $key ? 'active' : '' ?>"
               href="<?= htmlspecialchars($href, ENT_QUOTES, 'UTF-8') ?>">
                <?= htmlspecialchars($tab['label'], ENT_QUOTES, 'UTF-8') ?>
                <span class="badge badge-<?= $key === 'pending' ? 'warning' : ($key === 'rejected' ? 'danger' : ($key === 'approved' ? 'success' : 'secondary')) ?>">
                    <?= (int) $tab['count'] ?>
                </span>
            </a>
        </li>
        <?php endforeach; ?>
    </ul>

    <div class="card shadow-sm">
        <div class="card-body">
            <?php if (count($rows) === 0): ?>
                <p class="text-muted mb-0">No Annex 7 submissions match this filter.</p>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover table-sm" id="annex7Table">
                        <thead class="thead-light">
                        <tr>
                            <th>Campus</th>
                            <th>Coordinator email</th>
                            <th>File name</th>
                            <th>Uploaded</th>
                            <th>Status</th>
                            <th class="text-right">Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($rows as $row):
                            $id = (int) ($row['id'] ?? 0);
                            $st = (string) ($row['status'] ?? 'Pending');
                            $stLower = strtolower($st);
                            $badge = $stLower === 'approved' ? 'success' : ($stLower === 'rejected' || $stLower === 'denied' ? 'danger' : 'warning');
                            $previewUrl = 'view_annex_file.php?id=' . $id;
                            ?>
                        <tr>
                            <td><?= htmlspecialchars((string) ($row['campus'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                            <td><?= htmlspecialchars((string) ($row['user_email'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                            <td><?= htmlspecialchars((string) ($row['file_name'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                            <td class="small text-muted"><?= htmlspecialchars((string) ($row['uploaded_at'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                            <td><span class="badge badge-<?= $badge ?>"><?= htmlspecialchars($st, ENT_QUOTES, 'UTF-8') ?></span></td>
                            <td class="text-right text-nowrap">
                                <a class="btn btn-outline-primary btn-sm" href="<?= htmlspecialchars($previewUrl, ENT_QUOTES, 'UTF-8') ?>" target="_blank" rel="noopener">Preview</a>
                                <?php if ($stLower === 'pending' || $st === 'Pending'): ?>
                                    <button type="button" class="btn btn-success btn-sm js-annex-status" data-id="<?= $id ?>" data-status="Approved">Approve</button>
                                    <button type="button" class="btn btn-danger btn-sm js-annex-status" data-id="<?= $id ?>" data-status="Rejected">Decline</button>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php
schogms_chairman_shell_close();
schogms_chairman_footer_scripts(['datatables' => true, 'sweetalert' => true]);
?>
<script>
$(function () {
    if ($.fn.DataTable && !$.fn.dataTable.isDataTable('#annex7Table')) {
        $('#annex7Table').DataTable({ pageLength: 25, order: [] });
    }
});
document.querySelectorAll('.js-annex-status').forEach(function (btn) {
    btn.addEventListener('click', function () {
        var id = this.getAttribute('data-id');
        var status = this.getAttribute('data-status');
        Swal.fire({
            title: 'Are you sure?',
            text: 'Mark this Annex 7 as ' + status + '?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes',
            cancelButtonText: 'Cancel'
        }).then(function (result) {
            if (!result.isConfirmed) {
                return;
            }
            fetch('update_status.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'file_id=' + encodeURIComponent(id) + '&status=' + encodeURIComponent(status)
            })
                .then(function (r) { return r.json(); })
                .then(function (data) {
                    if (data.success) {
                        Swal.fire('Updated!', 'Annex 7 status was updated.', 'success').then(function () {
                            location.reload();
                        });
                    } else {
                        Swal.fire('Error', data.error || 'Update failed.', 'error');
                    }
                })
                .catch(function () {
                    Swal.fire('Error', 'Could not reach the server.', 'error');
                });
        });
    });
});
</script>
</body>
</html>
