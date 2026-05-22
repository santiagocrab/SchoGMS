<?php
/**
 * Bulk COR + COG upload on COR & COG hub page.
 *
 * Expects: $corCogCampus
 */
$corCogCampus = trim((string) ($corCogCampus ?? ''));
$defaultGroup = 'COR COG ' . ($corCogCampus !== '' ? ucfirst(strtolower($corCogCampus)) : '');
?>
<div class="card border-primary mb-4" id="bulk-cor-cog-upload">
    <div class="card-body">
        <h4 class="card-title text-primary">Bulk upload COR &amp; COG</h4>
        <p class="text-muted small mb-3">
            Upload many COR and COG files in one step. Each filename must match a scholar on your
            <strong>TDP and/or TES</strong> masterlist (e.g. <code>ABACARO, ROSE ANN PIQUE.pdf</code>).
            Unmatched files are <strong>removed</strong> and reported — they are not stored.
        </p>
        <form id="corCogBulkUploadForm" enctype="multipart/form-data">
            <input type="hidden" name="bulk_dual" value="1">
            <div class="row">
                <div class="col-md-6 form-group">
                    <label>Campus</label>
                    <input type="text" class="form-control" name="campus" readonly
                           value="<?= htmlspecialchars($corCogCampus) ?>" style="background:#f8f9fa">
                </div>
                <div class="col-md-6 form-group">
                    <label for="bulk_masterlist_scope">Match scholars from</label>
                    <select class="form-control" id="bulk_masterlist_scope" name="masterlist_scope">
                        <option value="all" selected>TDP + TES masterlist</option>
                        <option value="tdp">TDP masterlist only</option>
                        <option value="tes">TES masterlist only</option>
                    </select>
                </div>
                <div class="col-md-12 form-group">
                    <label for="bulkFileGroup">File group <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="bulkFileGroup" name="fileGroup" required
                           value="<?= htmlspecialchars($defaultGroup) ?>">
                </div>
                <div class="col-md-6 form-group">
                    <label for="corUpload">COR files (bulk)</label>
                    <input type="file" class="form-control" id="corUpload" name="corUpload[]" multiple
                           accept=".pdf,.jpg,.jpeg,.png">
                </div>
                <div class="col-md-6 form-group">
                    <label for="cogUpload">COG files (bulk)</label>
                    <input type="file" class="form-control" id="cogUpload" name="cogUpload[]" multiple
                           accept=".pdf,.jpg,.jpeg,.png">
                </div>
            </div>
            <button type="submit" class="btn btn-primary">
                <i data-feather="upload" class="feather-icon"></i> Upload COR &amp; COG
            </button>
        </form>
        <div id="corCogBulkUploadMessage" class="mt-3"></div>
    </div>
</div>
