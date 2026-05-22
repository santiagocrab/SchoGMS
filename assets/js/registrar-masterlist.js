(function () {
    'use strict';

    var form = document.getElementById('registrarMasterlistFilterForm');
    var limitEl = document.getElementById('limitFilter');
    var toggleBtn = document.getElementById('rmlToggleAdvanced');

    if (limitEl && form) {
        limitEl.addEventListener('change', function () {
            var pageInput = form.querySelector('input[name="page"]');
            if (!pageInput) {
                pageInput = document.createElement('input');
                pageInput.type = 'hidden';
                pageInput.name = 'page';
                form.appendChild(pageInput);
            }
            pageInput.value = '1';
            form.submit();
        });
    }

    if (toggleBtn) {
        var panel = document.getElementById('rmlAdvancedFilters');
        if (panel) {
            toggleBtn.addEventListener('click', function () {
                var open = panel.classList.toggle('show');
                toggleBtn.setAttribute('aria-expanded', open ? 'true' : 'false');
            });
        }
    }

    var searchInput = document.getElementById('searchInput');
    if (searchInput && form) {
        searchInput.addEventListener('keydown', function (e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                var pageInput = form.querySelector('input[name="page"]');
                if (pageInput) {
                    pageInput.value = '1';
                }
                form.submit();
            }
        });
    }
})();
