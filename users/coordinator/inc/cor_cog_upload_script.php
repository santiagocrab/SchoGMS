<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
<script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
<script>
(function () {
    function escapeHtml(s) {
        var d = document.createElement('div');
        d.textContent = s;
        return d.innerHTML;
    }

    function buildUploadReportHtml(data) {
        var html = '<div class="text-left" style="max-height:420px;overflow-y:auto;font-size:14px;">';
        if (data.stats) {
            html += '<p><strong>Accepted:</strong> ' + (data.stats.accepted || 0) +
                ' · <strong>Removed (not on masterlist):</strong> ' + (data.stats.rejected || 0) +
                ' · <strong>Errors:</strong> ' + (data.stats.errors || 0) + '</p>';
        }
        if (data.by_student && data.by_student.length) {
            html += '<h6 class="mt-2">Saved by scholar</h6><ul class="pl-3 mb-2">';
            data.by_student.forEach(function (row) {
                var parts = [];
                if (row.cor && row.cor.length) parts.push('<strong>COR:</strong> ' + row.cor.map(escapeHtml).join(', '));
                if (row.cog && row.cog.length) parts.push('<strong>COG:</strong> ' + row.cog.map(escapeHtml).join(', '));
                html += '<li class="mb-1"><strong>' + escapeHtml(row.student) + '</strong>';
                if (row.program) html += ' <span class="text-muted">(' + escapeHtml(row.program) + ')</span>';
                html += '<br>' + parts.join(' · ') + '</li>';
            });
            html += '</ul>';
        }
        if (data.rejected && data.rejected.length) {
            html += '<h6 class="mt-2 text-warning">Removed (not on masterlist)</h6><ul class="pl-3 mb-2">';
            data.rejected.forEach(function (r) {
                html += '<li class="mb-1"><code>' + escapeHtml(r.file) + '</code> (' + escapeHtml(r.category || '') + ')<br>' +
                    '<span class="text-muted small">' + escapeHtml(r.reason || '') + '</span></li>';
            });
            html += '</ul>';
        }
        if (data.errors && data.errors.length) {
            html += '<h6 class="mt-2 text-danger">Errors</h6><ul class="pl-3">';
            data.errors.forEach(function (e) {
                html += '<li class="mb-1"><code>' + escapeHtml(e.file) + '</code> — ' + escapeHtml(e.reason || '') + '</li>';
            });
            html += '</ul>';
        }
        html += '</div>';
        return html;
    }

    function showUploadResult(data) {
        var icon = data.success ? 'success' : (data.stats && data.stats.accepted > 0 ? 'warning' : 'error');
        Swal.fire({
            title: data.success ? 'Upload complete' : 'Upload finished with issues',
            html: buildUploadReportHtml(data),
            icon: icon,
            width: 640,
            confirmButtonText: 'OK'
        }).then(function () {
            if (data.stats && data.stats.accepted > 0) {
                window.location.reload();
            }
        });
    }

    function bindForm(formId) {
        var form = document.getElementById(formId);
        if (!form) return;
        form.addEventListener('submit', function (e) {
            e.preventDefault();
            var fd = new FormData(form);
            Swal.fire({
                title: 'Uploading…',
                allowOutsideClick: false,
                didOpen: function () { Swal.showLoading(); }
            });
            var submitUrl = form.getAttribute('data-submit-url') || 'submit_document_cor_cog.php';
            fetch(submitUrl, { method: 'POST', body: fd })
                .then(function (r) { return r.json(); })
                .then(function (data) { showUploadResult(data); })
                .catch(function () {
                    Swal.fire('Error', 'Upload request failed.', 'error');
                });
        });
    }

    bindForm('corCogUploadForm');
    bindForm('corCogBulkUploadForm');
})();
</script>
