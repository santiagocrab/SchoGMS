<?php
/**
 * Chairman: review file groups (TDP/TES) — tabs, approve/deny, edit, delete.
 */
include 'config/session.php';
require_once __DIR__ . '/../../inc/schogms_file_group_meta.php';
require_once __DIR__ . '/../../inc/schogms_file_group_view.php';
require_once __DIR__ . '/inc/chairman_nav.php';

if (!isset($_SESSION['fg_csrf'])) {
    $_SESSION['fg_csrf'] = bin2hex(random_bytes(16));
}
$csrf = (string) $_SESSION['fg_csrf'];

$program = strtolower(trim((string) ($_GET['program'] ?? 'tdp'))) === 'tes' ? 'tes' : 'tdp';
$statusFilter = trim((string) ($_GET['status'] ?? 'all'));
if (!in_array($statusFilter, ['all', 'pending', 'approved', 'denied'], true)) {
    $statusFilter = 'all';
}

$flashSuccess = trim((string) ($_GET['success'] ?? ''));
$flashError = trim((string) ($_GET['error'] ?? ''));

if (!isset($conn) || !($conn instanceof mysqli)) {
    require 'config/conn.php';
}

$list = schogms_file_group_meta_list(
    $conn,
    $program,
    $statusFilter === 'all' ? null : $statusFilter
);
$rows = $list['rows'];
$counts = $list['counts'];

$programLabel = strtoupper($program);
$tabBase = 'file_groups.php';
?>
<!DOCTYPE html>
<html dir="ltr" lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/png" href="../../assets/images/logo.png">
    <title>File groups — SchoGMS</title>
    <?php require_once __DIR__ . '/inc/assets.php'; schogms_chairman_head(true); ?>
    <style>
        .fg-hero { background: linear-gradient(135deg, #0d47a1 0%, #5e35b1 100%); color: #fff; border-radius: 12px; padding: 1.15rem 1.35rem; margin-bottom: 1.25rem; }
        .fg-hero h4 { color: #fff; margin-bottom: 0.35rem; font-weight: 600; }
        .fg-hero p { margin: 0; opacity: 0.92; font-size: 0.9rem; }
        .fg-program-tabs .nav-link { font-weight: 600; }
        .fg-status-tabs .nav-link { font-size: 0.85rem; padding: 0.45rem 0.85rem; }
        .fg-status-tabs .badge { font-size: 0.7rem; vertical-align: middle; margin-left: 0.25rem; }
        .fg-summary { font-size: 0.82rem; color: #475569; max-width: 420px; }
        .fg-actions .btn { margin: 0.1rem 0.15rem 0.1rem 0; font-size: 0.75rem; padding: 0.2rem 0.45rem; }
        .fg-name { font-weight: 600; color: #0f172a; max-width: 220px; word-break: break-word; }
        .fg-review-meta { font-size: 0.72rem; color: #64748b; }
    </style>
</head>
<body>
<?php schogms_loading_screen_once(); ?>
<?php schogms_chairman_shell_open('Review file groups'); ?>

<div class="page-breadcrumb">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb m-0 p-0">
            <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="program_list.php">Program list</a></li>
            <li class="breadcrumb-item active">File groups</li>
        </ol>
    </nav>
</div>

<div class="container-fluid">
    <div class="fg-hero">
        <h4>File group review</h4>
        <p>
            Review CHED TDP/TES masterlist batches from all campuses.
            Coordinator <strong>Annex 7</strong> files are reviewed on <a href="annex7.php" class="text-white font-weight-bold"><u>Annex 7</u></a>.
            <strong>Approve</strong> or <strong>deny</strong> batches, <strong>rename</strong> file group labels,
            or <strong>delete</strong> a batch and its scholar records. Legacy batches without a review record are treated as <strong>approved</strong>.
        </p>
    </div>

    <?php if ($flashSuccess !== ''): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= htmlspecialchars($flashSuccess, ENT_QUOTES, 'UTF-8') ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        </div>
    <?php endif; ?>
    <?php if ($flashError !== ''): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= htmlspecialchars($flashError, ENT_QUOTES, 'UTF-8') ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        </div>
    <?php endif; ?>

    <ul class="nav nav-tabs fg-program-tabs mb-3" role="tablist">
        <li class="nav-item">
            <a class="nav-link <?= $program === 'tdp' ? 'active' : '' ?>"
               href="<?= htmlspecialchars($tabBase . '?program=tdp&status=' . rawurlencode($statusFilter), ENT_QUOTES, 'UTF-8') ?>">CHED TDP</a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?= $program === 'tes' ? 'active' : '' ?>"
               href="<?= htmlspecialchars($tabBase . '?program=tes&status=' . rawurlencode($statusFilter), ENT_QUOTES, 'UTF-8') ?>">CHED TES</a>
        </li>
    </ul>

    <ul class="nav nav-pills fg-status-tabs mb-3 flex-wrap">
        <?php
        $statusTabs = [
            'all' => ['label' => 'All', 'count' => $counts['all']],
            'pending' => ['label' => 'Pending', 'count' => $counts['pending']],
            'approved' => ['label' => 'Approved', 'count' => $counts['approved']],
            'denied' => ['label' => 'Denied', 'count' => $counts['denied']],
        ];
        foreach ($statusTabs as $key => $tab):
            $href = $tabBase . '?program=' . rawurlencode($program) . '&status=' . rawurlencode($key);
            ?>
        <li class="nav-item">
            <a class="nav-link <?= $statusFilter === $key ? 'active' : '' ?>"
               href="<?= htmlspecialchars($href, ENT_QUOTES, 'UTF-8') ?>">
                <?= htmlspecialchars($tab['label'], ENT_QUOTES, 'UTF-8') ?>
                <span class="badge badge-<?= $key === 'pending' ? 'warning' : ($key === 'denied' ? 'danger' : ($key === 'approved' ? 'success' : 'secondary')) ?>">
                    <?= (int) $tab['count'] ?>
                </span>
            </a>
        </li>
        <?php endforeach; ?>
    </ul>

    <div class="card shadow-sm">
        <div class="card-body">
            <h5 class="card-title text-<?= $program === 'tes' ? 'success' : 'primary' ?> mb-3">
                <?= htmlspecialchars($programLabel, ENT_QUOTES, 'UTF-8') ?> file groups
                <?php if ($statusFilter !== 'all'): ?>
                    <span class="text-muted font-weight-normal">— <?= htmlspecialchars(ucfirst($statusFilter), ENT_QUOTES, 'UTF-8') ?></span>
                <?php endif; ?>
            </h5>

            <?php if (count($rows) === 0): ?>
                <p class="text-muted mb-0">No file groups match this filter.</p>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover table-sm" id="fgTable">
                        <thead class="thead-light">
                        <tr>
                            <th>Campus</th>
                            <th>File group</th>
                            <th>Uploaded by</th>
                            <th>Summary</th>
                            <th>Status</th>
                            <th>Review</th>
                            <th class="text-right">Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($rows as $row):
                            $campus = (string) ($row['campus'] ?? '');
                            $fg = (string) ($row['file_group'] ?? '');
                            $st = (string) ($row['status'] ?? 'pending');
                            $badge = schogms_file_group_status_badge_class($st);
                            $viewUrl = 'file_group_view.php?program=' . rawurlencode($program)
                                . '&file_group=' . rawurlencode($fg)
                                . '&campus=' . rawurlencode($campus);
                            $summary = schogms_file_group_batch_summary_text($row);
                            $reviewedBy = trim((string) ($row['reviewed_by'] ?? ''));
                            $reviewedAt = trim((string) ($row['reviewed_at'] ?? ''));
                            $notes = trim((string) ($row['review_notes'] ?? ''));
                            $uploaderText = schogms_file_group_meta_uploader_display($row);
                            $uploadedAt = trim((string) ($row['uploaded_at'] ?? ''));
                            ?>
                        <tr>
                            <td><?= htmlspecialchars($campus, ENT_QUOTES, 'UTF-8') ?></td>
                            <td class="fg-name"><?= htmlspecialchars($fg, ENT_QUOTES, 'UTF-8') ?></td>
                            <td class="fg-uploader small">
                                <?= htmlspecialchars($uploaderText, ENT_QUOTES, 'UTF-8') ?>
                                <?php if ($uploadedAt !== ''): ?>
                                    <br><span class="text-muted"><?= htmlspecialchars($uploadedAt, ENT_QUOTES, 'UTF-8') ?></span>
                                <?php endif; ?>
                            </td>
                            <td class="fg-summary"><?= htmlspecialchars($summary, ENT_QUOTES, 'UTF-8') ?></td>
                            <td><span class="badge badge-<?= htmlspecialchars($badge, ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars(ucfirst($st), ENT_QUOTES, 'UTF-8') ?></span></td>
                            <td class="fg-review-meta">
                                <?php if ($reviewedBy !== ''): ?>
                                    <?= htmlspecialchars($reviewedBy, ENT_QUOTES, 'UTF-8') ?>
                                    <?php if ($reviewedAt !== ''): ?>
                                        <br><span><?= htmlspecialchars($reviewedAt, ENT_QUOTES, 'UTF-8') ?></span>
                                    <?php endif; ?>
                                    <?php if ($notes !== ''): ?>
                                        <br><em><?= htmlspecialchars($notes, ENT_QUOTES, 'UTF-8') ?></em>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <span class="text-muted">—</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-right fg-actions text-nowrap">
                                <a class="btn btn-outline-primary btn-sm" href="<?= htmlspecialchars($viewUrl, ENT_QUOTES, 'UTF-8') ?>">View</a>
                                <button type="button" class="btn btn-outline-secondary btn-sm js-fg-edit"
                                        data-program="<?= htmlspecialchars($program, ENT_QUOTES, 'UTF-8') ?>"
                                        data-campus="<?= htmlspecialchars($campus, ENT_QUOTES, 'UTF-8') ?>"
                                        data-file-group="<?= htmlspecialchars($fg, ENT_QUOTES, 'UTF-8') ?>">Edit</button>
                                <?php if ($st !== 'approved'): ?>
                                <button type="button" class="btn btn-outline-success btn-sm js-fg-review"
                                        data-action="approve"
                                        data-program="<?= htmlspecialchars($program, ENT_QUOTES, 'UTF-8') ?>"
                                        data-campus="<?= htmlspecialchars($campus, ENT_QUOTES, 'UTF-8') ?>"
                                        data-file-group="<?= htmlspecialchars($fg, ENT_QUOTES, 'UTF-8') ?>">Approve</button>
                                <?php endif; ?>
                                <?php if ($st !== 'denied'): ?>
                                <button type="button" class="btn btn-outline-danger btn-sm js-fg-review"
                                        data-action="deny"
                                        data-program="<?= htmlspecialchars($program, ENT_QUOTES, 'UTF-8') ?>"
                                        data-campus="<?= htmlspecialchars($campus, ENT_QUOTES, 'UTF-8') ?>"
                                        data-file-group="<?= htmlspecialchars($fg, ENT_QUOTES, 'UTF-8') ?>">Deny</button>
                                <?php endif; ?>
                                <?php if ($st !== 'pending'): ?>
                                <button type="button" class="btn btn-outline-warning btn-sm js-fg-review"
                                        data-action="pending"
                                        data-program="<?= htmlspecialchars($program, ENT_QUOTES, 'UTF-8') ?>"
                                        data-campus="<?= htmlspecialchars($campus, ENT_QUOTES, 'UTF-8') ?>"
                                        data-file-group="<?= htmlspecialchars($fg, ENT_QUOTES, 'UTF-8') ?>">Pending</button>
                                <?php endif; ?>
                                <button type="button" class="btn btn-outline-dark btn-sm js-fg-delete"
                                        data-program="<?= htmlspecialchars($program, ENT_QUOTES, 'UTF-8') ?>"
                                        data-campus="<?= htmlspecialchars($campus, ENT_QUOTES, 'UTF-8') ?>"
                                        data-file-group="<?= htmlspecialchars($fg, ENT_QUOTES, 'UTF-8') ?>"
                                        data-scholars="<?= (int) ($row['total_entries'] ?? 0) ?>">Delete</button>
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

<!-- Review modal -->
<div class="modal fade" id="fgReviewModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form class="modal-content" method="post" action="file_group_action.php" id="fgReviewForm">
            <div class="modal-header">
                <h5 class="modal-title" id="fgReviewTitle">Review file group</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="csrf" value="<?= htmlspecialchars($csrf, ENT_QUOTES, 'UTF-8') ?>">
                <input type="hidden" name="action" id="fgReviewAction" value="">
                <input type="hidden" name="program" id="fgReviewProgram" value="">
                <input type="hidden" name="campus" id="fgReviewCampus" value="">
                <input type="hidden" name="file_group" id="fgReviewFileGroup" value="">
                <input type="hidden" name="return_status" value="<?= htmlspecialchars($statusFilter, ENT_QUOTES, 'UTF-8') ?>">
                <p class="mb-2"><strong id="fgReviewLabel"></strong></p>
                <div class="form-group mb-0">
                    <label for="fgReviewNotes">Notes (optional)</label>
                    <textarea class="form-control" name="notes" id="fgReviewNotes" rows="3" placeholder="Reason for approval or denial…"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary" id="fgReviewSubmit">Confirm</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit modal -->
<div class="modal fade" id="fgEditModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form class="modal-content" method="post" action="file_group_action.php" id="fgEditForm">
            <div class="modal-header">
                <h5 class="modal-title">Rename file group</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="csrf" value="<?= htmlspecialchars($csrf, ENT_QUOTES, 'UTF-8') ?>">
                <input type="hidden" name="action" value="rename">
                <input type="hidden" name="program" id="fgEditProgram" value="">
                <input type="hidden" name="campus" id="fgEditCampus" value="">
                <input type="hidden" name="file_group" id="fgEditOldName" value="">
                <input type="hidden" name="return_status" value="<?= htmlspecialchars($statusFilter, ENT_QUOTES, 'UTF-8') ?>">
                <p class="small text-muted mb-2">Updates all scholar records for this campus and renames the batch label.</p>
                <div class="form-group">
                    <label for="fgEditNewName">New file group name</label>
                    <input type="text" class="form-control" name="new_file_group" id="fgEditNewName" required>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary">Save</button>
            </div>
        </form>
    </div>
</div>

<!-- Delete modal -->
<div class="modal fade" id="fgDeleteModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form class="modal-content" method="post" action="file_group_action.php" id="fgDeleteForm">
            <div class="modal-header">
                <h5 class="modal-title text-danger">Delete file group</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="csrf" value="<?= htmlspecialchars($csrf, ENT_QUOTES, 'UTF-8') ?>">
                <input type="hidden" name="action" value="delete">
                <input type="hidden" name="program" id="fgDeleteProgram" value="">
                <input type="hidden" name="campus" id="fgDeleteCampus" value="">
                <input type="hidden" name="file_group" id="fgDeleteFileGroup" value="">
                <input type="hidden" name="return_status" value="<?= htmlspecialchars($statusFilter, ENT_QUOTES, 'UTF-8') ?>">
                <p id="fgDeleteWarning" class="mb-0"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-danger">Delete permanently</button>
            </div>
        </form>
    </div>
</div>

<footer class="footer text-center text-muted">SchoGMS</footer>
<?php
schogms_chairman_shell_close();
schogms_chairman_footer_scripts(['datatables' => true]);
?>
<script>
(function () {
    var titles = { approve: 'Approve file group', deny: 'Deny file group', pending: 'Mark as pending' };
    var btnClass = { approve: 'btn-success', deny: 'btn-danger', pending: 'btn-warning' };

    document.querySelectorAll('.js-fg-review').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var action = btn.getAttribute('data-action') || '';
            document.getElementById('fgReviewAction').value = action;
            document.getElementById('fgReviewProgram').value = btn.getAttribute('data-program') || '';
            document.getElementById('fgReviewCampus').value = btn.getAttribute('data-campus') || '';
            document.getElementById('fgReviewFileGroup').value = btn.getAttribute('data-file-group') || '';
            document.getElementById('fgReviewLabel').textContent = btn.getAttribute('data-file-group') || '';
            document.getElementById('fgReviewTitle').textContent = titles[action] || 'Review';
            var submit = document.getElementById('fgReviewSubmit');
            submit.textContent = action === 'approve' ? 'Approve' : (action === 'deny' ? 'Deny' : 'Save');
            submit.className = 'btn ' + (btnClass[action] || 'btn-primary');
            $('#fgReviewModal').modal('show');
        });
    });

    document.querySelectorAll('.js-fg-edit').forEach(function (btn) {
        btn.addEventListener('click', function () {
            document.getElementById('fgEditProgram').value = btn.getAttribute('data-program') || '';
            document.getElementById('fgEditCampus').value = btn.getAttribute('data-campus') || '';
            document.getElementById('fgEditOldName').value = btn.getAttribute('data-file-group') || '';
            document.getElementById('fgEditNewName').value = btn.getAttribute('data-file-group') || '';
            $('#fgEditModal').modal('show');
        });
    });

    document.querySelectorAll('.js-fg-delete').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var fg = btn.getAttribute('data-file-group') || '';
            var n = parseInt(btn.getAttribute('data-scholars') || '0', 10);
            document.getElementById('fgDeleteProgram').value = btn.getAttribute('data-program') || '';
            document.getElementById('fgDeleteCampus').value = btn.getAttribute('data-campus') || '';
            document.getElementById('fgDeleteFileGroup').value = fg;
            document.getElementById('fgDeleteWarning').textContent =
                'Delete “' + fg + '” and all ' + n.toLocaleString() + ' scholar record(s) for this campus? This cannot be undone.';
            $('#fgDeleteModal').modal('show');
        });
    });

    if (typeof $ !== 'undefined' && $.fn.DataTable && document.getElementById('fgTable')) {
        $('#fgTable').DataTable({
            pageLength: 25,
            order: [[0, 'asc']],
            columnDefs: [{ orderable: false, targets: 6 }]
        });
    }
})();
</script>
</body>
</html>
