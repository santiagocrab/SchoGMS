<?php
/**
 * Upload modal for coordinator COR / COG documents.
 *
 * Expects: $corCogCategory (COR|COG), $corCogCampus (sheet_name)
 */
$corCogCategory = strtoupper(trim((string) ($corCogCategory ?? 'COR')));
if (!in_array($corCogCategory, ['COR', 'COG'], true)) {
    $corCogCategory = 'COR';
}
$corCogCampus = trim((string) ($corCogCampus ?? ''));
$corCogDefaultGroup = $corCogCategory . ($corCogCampus !== '' ? ' ' . ucfirst(strtolower($corCogCampus)) : '');
$corCogDocLabel = $corCogCategory === 'COR' ? 'Certificate of Registration' : 'Certificate of Grades';
?>
<div class="modal fade" id="uploadModal" tabindex="-1" role="dialog" aria-labelledby="uploadModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="uploadModalLabel">Bulk upload <?= htmlspecialchars($corCogCategory) ?> documents</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p class="text-muted small mb-3">
                    <?= htmlspecialchars($corCogDocLabel) ?> — one file per scholar.
                    Name each file <strong>LASTNAME, FIRSTNAME MIDDLENAME.pdf</strong> (must match the CHED masterlist).
                    Files that do not match any scholar on the masterlist are <strong>not saved</strong> and will be listed as removed.
                </p>
                <form id="corCogUploadForm" enctype="multipart/form-data">
                    <input type="hidden" name="category" value="<?= htmlspecialchars($corCogCategory) ?>">
                    <div class="form-group">
                        <label>Campus</label>
                        <input type="text" class="form-control" name="campus" readonly
                               value="<?= htmlspecialchars($corCogCampus) ?>" style="background:#f8f9fa">
                    </div>
                    <div class="form-group">
                        <label for="masterlist_scope">Match scholars from</label>
                        <select class="form-control" id="masterlist_scope" name="masterlist_scope">
                            <option value="all" selected>TDP + TES masterlist</option>
                            <option value="tdp">TDP masterlist only</option>
                            <option value="tes">TES masterlist only</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="fileGroup">File group <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="fileGroup" name="fileGroup" required
                               value="<?= htmlspecialchars($corCogDefaultGroup) ?>"
                               placeholder="e.g. COR Isulan">
                        <small class="form-text text-muted">Batch label for this upload (stored with each file).</small>
                    </div>
                    <div class="form-group">
                        <label for="fileUpload"><?= htmlspecialchars($corCogCategory) ?> files <span class="text-danger">*</span></label>
                        <input type="file" class="form-control" id="fileUpload" name="fileUpload[]" multiple
                               accept=".pdf,.jpg,.jpeg,.png" required>
                        <small class="form-text text-muted">PDF, JPG, or PNG — select many files at once (bulk upload).</small>
                    </div>
                    <button type="submit" class="btn btn-primary">
                        <i data-feather="upload" class="feather-icon"></i> Upload
                    </button>
                </form>
                <div id="corCogUploadMessage" class="mt-3"></div>
            </div>
        </div>
    </div>
</div>
