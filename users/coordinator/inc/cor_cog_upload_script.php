<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
<script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
<script>
(function () {
    var form = document.getElementById('corCogUploadForm');
    if (!form) return;

    form.addEventListener('submit', function (e) {
        e.preventDefault();
        var input = document.getElementById('fileUpload');
        if (!input || !input.files || input.files.length === 0) {
            showCorCogToast('Select at least one file.', 'error');
            return;
        }
        var fd = new FormData(form);
        showCorCogToast('Uploading…', 'loading');
        fetch('submit_document_cor_cog.php', { method: 'POST', body: fd })
            .then(function (r) { return r.json(); })
            .then(function (data) {
                if (data.success) {
                    showCorCogToast(data.message || 'Upload complete.', 'success');
                    setTimeout(function () { window.location.reload(); }, 1200);
                } else {
                    showCorCogToast(data.message || 'Upload failed.', 'error');
                }
            })
            .catch(function () {
                showCorCogToast('Upload request failed.', 'error');
            });
    });

    function showCorCogToast(message, type) {
        var style = 'linear-gradient(to right, #555, #888)';
        if (type === 'success') style = 'linear-gradient(to right, #00b09b, #96c93d)';
        if (type === 'error') style = 'linear-gradient(to right, #ff5f6d, #ffc371)';
        if (type === 'loading') style = 'linear-gradient(to right, #0078D7, #00b4d8)';
        Toastify({ text: message, duration: type === 'loading' ? 5000 : 3500, gravity: 'top', position: 'center', style: { background: style } }).showToast();
    }
})();
</script>
