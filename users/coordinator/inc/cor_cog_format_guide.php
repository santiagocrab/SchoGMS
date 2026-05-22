<?php
/**
 * "Here is the format" block for coordinator COR / COG uploads.
 *
 * @param string $category COR or COG
 * @param string $campus   Coordinator campus (sheet_name)
 */
function schogms_coordinator_render_cor_cog_format(string $category, string $campus = ''): void
{
    $category = strtoupper(trim($category));
    if (!in_array($category, ['COR', 'COG'], true)) {
        $category = 'COR';
    }
    $label = $category === 'COR' ? 'Certificate of Registration (COR)' : 'Certificate of Grades (COG)';
    $campus = trim($campus);
    $fileGroupExample = $category . ($campus !== '' ? ' ' . ucfirst(strtolower($campus)) : ' Campus');
    ?>
    <div class="row mt-3" id="upload-format">
        <div class="col-12">
            <div class="card border-primary">
                <div class="card-body">
                    <h4 class="card-title text-primary mb-3">Here is the format</h4>
                    <p class="text-muted mb-3">
                        Upload one <?= htmlspecialchars($label) ?> file per scholar.
                        Accepted types: <strong>.pdf</strong>, <strong>.jpg</strong>, <strong>.jpeg</strong>, <strong>.png</strong>.
                        Each filename must match the scholar name on the CHED masterlist so documents link automatically on validation pages.
                    </p>
                    <div class="mb-3">
                        <a href="download_cor_cog_naming_guide.php?category=<?= urlencode($category) ?>"
                           class="btn btn-outline-primary btn-sm">
                            <i data-feather="download" class="feather-icon"></i>
                            Download naming guide (text)
                        </a>
                    </div>
                    <h6 class="font-weight-medium">File naming (required)</h6>
                    <p class="small text-muted mb-2">
                        Use uppercase letters and this pattern — same order as the masterlist
                        (<em>Last name, First name Middle name</em>):
                    </p>
                    <div class="alert alert-light border font-monospace mb-3">
                        LASTNAME, FIRSTNAME MIDDLENAME.pdf
                    </div>
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm format-sample-table mb-3">
                            <thead>
                                <tr class="table-active">
                                    <th>Correct filename</th>
                                    <th>Masterlist match</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><code>ABACARO, ROSE ANN PIQUE.pdf</code></td>
                                    <td>Abacaro / Rose Ann / Pique</td>
                                </tr>
                                <tr>
                                    <td><code>DELA CRUZ, JUAN CARLOS.pdf</code></td>
                                    <td>Dela Cruz / Juan / Carlos</td>
                                </tr>
                                <tr class="text-muted">
                                    <td colspan="2"><em>Avoid:</em> <code>juan_delacruz.pdf</code>, <code>COR-Juan.pdf</code>, or IDs-only names — these will not match the masterlist.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <h6 class="font-weight-medium">Upload fields</h6>
                    <ul class="small text-muted mb-3">
                        <li><strong>Campus</strong> — your assigned campus<?= $campus !== '' ? ' (<strong>' . htmlspecialchars($campus) . '</strong>)' : '' ?>.</li>
                        <li><strong>Category</strong> — <?= htmlspecialchars($category) ?> only on this page.</li>
                        <li><strong>File group</strong> — batch label stored with the file (e.g. <code><?= htmlspecialchars($fileGroupExample) ?></code>). Use one group per upload batch.</li>
                        <li><strong>Files</strong> — select multiple PDF or image files; each file is one scholar.</li>
                    </ul>
                    <p class="small text-muted mb-0">
                        After upload, files appear in the table above. Scholars on the masterlist show COR/COG status when filenames match.
                    </p>
                </div>
            </div>
        </div>
    </div>
    <?php
}
