/**
 * SchoGMS page loader — min display time, smooth exit, accessible status updates.
 */
(function () {
    'use strict';

    var MIN_SHOW_MS = 850;
    var MAX_SHOW_MS = 9000;
    var EXIT_MS = 650;

    var loader = document.getElementById('schogms-page-loader');
    if (!loader) {
        return;
    }

    var hidden = false;
    var shownAt = Date.now();
    var statusEl = document.getElementById('schogms-loader-status');

    var messages = [
        'Preparing your workspace',
        'Loading scholarship data',
        'Almost ready'
    ];
    var msgIndex = 0;

    function setStatus(text) {
        if (!statusEl) {
            return;
        }
        statusEl.classList.add('is-changing');
        window.setTimeout(function () {
            statusEl.textContent = text;
            statusEl.classList.remove('is-changing');
        }, 180);
    }

    function cycleStatus() {
        if (hidden) {
            return;
        }
        msgIndex = (msgIndex + 1) % messages.length;
        setStatus(messages[msgIndex]);
    }

    var statusTimer = window.setInterval(cycleStatus, 2200);
    window.setTimeout(function () {
        setStatus(messages[1]);
    }, 1100);

    function hideLoader() {
        if (hidden) {
            return;
        }
        var elapsed = Date.now() - shownAt;
        var wait = Math.max(0, MIN_SHOW_MS - elapsed);

        window.setTimeout(function () {
            if (hidden) {
                return;
            }
            hidden = true;
            window.clearInterval(statusTimer);

            loader.classList.add('is-exiting');
            loader.setAttribute('aria-busy', 'false');
            loader.setAttribute('aria-hidden', 'true');

            window.setTimeout(function () {
                loader.remove();
            }, EXIT_MS + 80);
        }, wait);
    }

    if (document.readyState === 'complete') {
        hideLoader();
    } else {
        window.addEventListener('load', hideLoader, { once: true });
    }

    window.setTimeout(hideLoader, MAX_SHOW_MS);

    window.schogmsHidePageLoader = hideLoader;
})();
