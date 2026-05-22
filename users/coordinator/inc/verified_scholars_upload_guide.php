<?php
/**
 * Upload format & workflow for Verified Scholars billing Excel.
 *
 * @param bool $compact  true = short version for modal
 */
function schogms_render_verified_scholars_upload_guide(bool $compact = false, string $campus = ''): void
{
    $campus = trim($campus);
    ?>
    <div class="<?= $compact ? '' : 'card border-primary mb-4' ?>">
        <div class="<?= $compact ? '' : 'card-body' ?>">
            <?php if (!$compact): ?>
            <h4 class="card-title text-primary mb-3">Upload format &amp; how the system works</h4>
            <?php else: ?>
            <h6 class="font-weight-bold text-primary mb-2">File format (required)</h6>
            <?php endif; ?>

            <p class="<?= $compact ? 'small text-muted mb-2' : 'text-muted mb-3' ?>">
                Use an <strong>Excel workbook</strong> (`.xlsx` or `.xls`). Row <strong>1</strong> and <strong>2</strong> may be titles or headers;
                <strong>data starts on row 3</strong>. Each row is one scholar billing / payment record.
                Empty rows are skipped.
            </p>

            <?php if (!$compact): ?>
            <h6 class="font-weight-medium">How this page works</h6>
            <ol class="small text-muted mb-3">
                <li><strong>Table below</strong> — shows scholars from your campus <strong>CHED TDP masterlist</strong> (uploaded under CHED TDP Masterlist). Use it to see who is on the masterlist.</li>
                <li><strong>Upload File</strong> — imports <strong>billing &amp; payment</strong> rows into <code>billing_table</code> (scholarship type, amounts, OR numbers, refunds, etc.). This is separate from the masterlist upload.</li>
                <li>After a successful upload, new billing rows are stored in the database for reporting and verification workflows.</li>
            </ol>
            <?php if ($campus !== ''): ?>
            <p class="small mb-3">Your assigned campus: <strong><?= htmlspecialchars($campus) ?></strong> — put this value in column <strong>F (Campus)</strong> for each row when applicable.</p>
            <?php endif; ?>
            <?php endif; ?>

            <h6 class="font-weight-medium">Excel columns (row 3 onward)</h6>
            <div class="table-responsive">
                <table class="table table-bordered table-sm <?= $compact ? 'small' : '' ?> mb-3">
                    <thead class="thead-light">
                        <tr><th>Column</th><th>Field</th><th>Notes</th></tr>
                    </thead>
                    <tbody>
                        <tr><td>A</td><td>Last name</td><td>Text</td></tr>
                        <tr><td>B</td><td>First name</td><td>Text</td></tr>
                        <tr><td>C</td><td>Scholarship type</td><td>e.g. TDP, TES, congressional grant label</td></tr>
                        <tr><td>D</td><td>Units enrolled</td><td>Number</td></tr>
                        <tr><td>E</td><td>Course / program</td><td>Text</td></tr>
                        <tr><td>F</td><td>Campus</td><td>Must match campus name (e.g. ACCESS, ISULAN)</td></tr>
                        <tr><td>G</td><td>Year &amp; date submitted (CHED)</td><td>Date</td></tr>
                        <tr><td>H</td><td>Amount</td><td>Number (commas allowed)</td></tr>
                        <tr><td>I</td><td>First semester</td><td>Text / status</td></tr>
                        <tr><td>J</td><td>Second semester</td><td>Text / status</td></tr>
                        <tr><td>K</td><td>Status</td><td>e.g. verified, B2 HELP</td></tr>
                        <tr><td>L</td><td>Payment scholarship type</td><td>Text</td></tr>
                        <tr><td>M</td><td>Payment amount</td><td>Number</td></tr>
                        <tr><td>N</td><td>Payment year &amp; date</td><td>Date</td></tr>
                        <tr><td>O</td><td>Payment OR number</td><td>Official receipt no.</td></tr>
                        <tr><td>P</td><td>Payment amount per OR</td><td>Number</td></tr>
                        <tr><td>Q</td><td>Refund 1st semester</td><td>Number</td></tr>
                        <tr><td>R</td><td>Refund 2nd semester</td><td>Number</td></tr>
                        <tr><td>S</td><td>Refund year &amp; date released</td><td>Date</td></tr>
                    </tbody>
                </table>
            </div>

            <p class="<?= $compact ? 'small text-muted mb-2' : 'small text-muted mb-3' ?>">
                <strong>Do not</strong> change column order. The system reads columns <strong>A–S</strong> by position, not by header name.
            </p>

            <a href="download_verified_scholars_template.php" class="btn btn-outline-primary btn-sm mb-<?= $compact ? '2' : '0' ?>">
                <i data-feather="download" class="feather-icon"></i> Download Excel template (.xlsx)
            </a>
        </div>
    </div>
    <?php
}
