<?php
/**
 * TES validation table body (bulk, filters, exports) — same pattern as TDP validate.php
 */
require_once __DIR__ . '/validation_filters.php';
require_once __DIR__ . '/validation_edit_guide.php';

set_time_limit(120);

$tesCampus = trim((string) ($sheet_name ?? ''));
$tesRows = [];
$vfOptions = [];
$bulkStats = null;
$runBulk = isset($_GET['bulk']) && $_GET['bulk'] !== '0';

if ($tesCampus === '') {
    echo '<div class="alert alert-warning">No campus assigned to your account.</div>';
} elseif ($conn instanceof mysqli) {
    $tesRows = schogms_validation_fetch_rows($conn, 'tes', $tesCampus, $_GET, true);
    $vfOptions = schogms_validation_filter_options($conn, 'tes', $tesCampus);
}

$totalRecords = count($tesRows);
$countPassed = 0;
$countFailed = 0;
$countNoCor = 0;
foreach ($tesRows as $r) {
    $c = $r['_check'] ?? schogms_validation_row_check($r, [], 'tes');
    if ($c['passed']) {
        $countPassed++;
    } else {
        $countFailed++;
    }
    if (!$c['has_cor']) {
        $countNoCor++;
    }
}
if ($runBulk && $tesCampus !== '') {
    $bulkStats = [
        'total' => $totalRecords,
        'passed' => $countPassed,
        'failed' => $countFailed,
        'updated' => 0,
    ];
}
$viewBase = '../../view_document.php?path=';
?>
<div class="table-responsive">
    <h5 class="mb-2">TES validation — campus:
        <strong><?php echo htmlspecialchars($tesCampus); ?></strong>
        <span class="badge badge-info ml-2"><?php echo (int) $totalRecords; ?> scholars</span>
    </h5>
    <p class="text-muted small mb-3">Same filters and bulk validation as TDP. Hold Ctrl/Cmd to select multiple values.</p>

    <?php if ($bulkStats !== null): ?>
    <div class="alert alert-success">
        Bulk validation complete:
        <strong><?php echo (int) $bulkStats['passed']; ?> passed</strong>,
        <strong><?php echo (int) $bulkStats['failed']; ?> failed</strong>
        (<?php echo (int) $bulkStats['total']; ?> total).
    </div>
    <?php endif; ?>

    <div class="row mb-3">
        <div class="col-md-3"><div class="card bg-light"><div class="card-body py-2 text-center"><div class="text-muted small">Total</div><div class="h4 mb-0"><?php echo (int) $totalRecords; ?></div></div></div></div>
        <div class="col-md-3"><div class="card border-success"><div class="card-body py-2 text-center"><div class="text-muted small">Passed</div><div class="h4 mb-0 text-success"><?php echo (int) $countPassed; ?></div></div></div></div>
        <div class="col-md-3"><div class="card border-danger"><div class="card-body py-2 text-center"><div class="text-muted small">Failed</div><div class="h4 mb-0 text-danger"><?php echo (int) $countFailed; ?></div></div></div></div>
        <div class="col-md-3"><div class="card border-warning"><div class="card-body py-2 text-center"><div class="text-muted small">No COR</div><div class="h4 mb-0 text-warning"><?php echo (int) $countNoCor; ?></div></div></div></div>
    </div>

    <button type="button" id="bulkValidateTesBtn" class="btn btn-primary btn-rounded mb-3"
        data-sheet-name="<?php echo htmlspecialchars($tesCampus); ?>">Re-validate all scholars</button>

    <?php
    $vfProgram = 'tes';
    $vfCampus = $tesCampus;
    $vfGet = $_GET;
    $vfPage = 'validate_tes.php';
    require __DIR__ . '/validation_filters_ui.php';
    ?>

    <table id="zero_config" class="table table-striped table-bordered no-wrap">
        <thead>
            <tr>
                <th>SEQ</th><th>APP NO</th><th>LASTNAME</th><th>FIRSTNAME</th><th>EXT</th><th>MIDDLENAME</th>
                <th>SEX</th><th>COURSE</th><th>YEAR LEVEL</th><th>STREET</th><th>TOWN/CITY</th><th>CONTACT</th>
                <th>BATCH NO</th><th>COR/COG</th><th>STATUS</th><th>REMARKS</th><th>VALIDATION</th><th>Edit</th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($tesRows) === 0): ?>
            <tr><td colspan="18" class="text-center text-muted">No records match the current filters.</td></tr>
            <?php else: foreach ($tesRows as $row):
                $check = $row['_check'];
                $cor_path = $check['cor_path'];
                $cog_path = $check['cog_path'];
            ?>
            <tr>
                <td><?php echo htmlspecialchars((string) ($row['seq'] ?? '')); ?></td>
                <td><?php echo htmlspecialchars((string) ($row['app_no'] ?? '')); ?></td>
                <td><?php echo htmlspecialchars((string) ($row['lastname'] ?? '')); ?></td>
                <td><?php echo htmlspecialchars((string) ($row['firstname'] ?? '')); ?></td>
                <td><?php echo htmlspecialchars((string) ($row['ext'] ?? '')); ?></td>
                <td><?php echo htmlspecialchars((string) ($row['middlename'] ?? '')); ?></td>
                <td><?php echo htmlspecialchars((string) ($row['sex'] ?? '')); ?></td>
                <td><?php echo htmlspecialchars((string) ($row['course_program_enrolled'] ?? '')); ?></td>
                <td><?php echo htmlspecialchars((string) ($row['year_level'] ?? '')); ?></td>
                <td><?php echo htmlspecialchars((string) ($row['street'] ?? '')); ?></td>
                <td><?php echo htmlspecialchars((string) ($row['town_city'] ?? '')); ?></td>
                <td><?php echo htmlspecialchars((string) ($row['contact'] ?? '')); ?></td>
                <td><?php echo htmlspecialchars((string) ($row['batch_no'] ?? '')); ?></td>
                <td>
                    <div class="btn-group">
                        <?php if ($cor_path !== ''): ?>
                        <a href="<?php echo htmlspecialchars($viewBase . urlencode(base64_encode($cor_path))); ?>" target="_blank" class="btn btn-sm btn-success">COR</a>
                        <?php else: ?><span class="badge badge-secondary">No COR</span><?php endif; ?>
                        <?php if ($cog_path !== ''): ?>
                        <a href="<?php echo htmlspecialchars($viewBase . urlencode(base64_encode($cog_path))); ?>" target="_blank" class="btn btn-sm btn-primary">COG</a>
                        <?php else: ?><span class="badge badge-secondary">No COG</span><?php endif; ?>
                    </div>
                </td>
                <td>
                    <?php if ($check['has_cor']): ?>
                    <span class="badge badge-success">Enrolled</span>
                    <?php else: ?>
                    <span class="badge badge-warning">Not Enrolled</span>
                    <?php endif; ?>
                </td>
                <td></td>
                <td>
                    <?php if ($check['passed']): ?>
                    <span class="badge badge-success">Validated</span>
                    <?php else: ?>
                    <span class="badge badge-danger"><?php echo htmlspecialchars($check['validation_label']); ?></span>
                    <?php endif; ?>
                    <div class="small text-muted mt-1">
                        <?php echo $check['course_match'] ? '✓ Course' : '✗ Course'; ?> ·
                        <?php echo $check['year_level_match'] ? '✓ Year' : '✗ Year'; ?>
                    </div>
                    <?php if (!$check['passed']): ?>
                    <div class="small mt-1">
                        <?php if (!$check['course_match'] && trim((string) ($row['reg_course'] ?? '')) !== ''): ?>
                            <div>Reg. course: <em><?= htmlspecialchars((string) $row['reg_course']) ?></em></div>
                        <?php endif; ?>
                        <?php if (!$check['year_level_match'] && trim((string) ($row['reg_year_level'] ?? '')) !== ''): ?>
                            <div>Reg. year: <em><?= htmlspecialchars((string) $row['reg_year_level']) ?></em></div>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>
                </td>
                <td>
                    <button type="button" class="btn btn-sm <?= $check['passed'] ? 'btn-outline-secondary' : 'btn-warning' ?> btn-edit-student"
                        data-id="<?= (int) ($row['id'] ?? 0) ?>"
                        data-guide="<?= schogms_validation_edit_guide_attr($row, $check) ?>">
                        <?= $check['passed'] ? 'Edit' : 'Fix' ?>
                    </button>
                </td>
            </tr>
            <?php endforeach; endif; ?>
        </tbody>
    </table>
</div>
