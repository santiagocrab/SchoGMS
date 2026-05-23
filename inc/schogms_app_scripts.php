<?php
/**
 * Shared footer scripts: jQuery/Bootstrap (once), nav dropdowns, notifications.
 */

if (!function_exists('schogms_app_assets_base')) {
    /** Relative path from current app page to /assets (e.g. ../../assets). */
    function schogms_app_assets_base(): string
    {
        if (function_exists('schogms_assets_rel_from_request')) {
            return schogms_assets_rel_from_request();
        }

        return '../../assets';
    }
}

if (!function_exists('schogms_notifications_api_url')) {
    /** URL to users/notifications_api.php from the current script directory. */
    function schogms_notifications_api_url(): string
    {
        $script = $_SERVER['SCRIPT_NAME'] ?? '';
        $dir = str_replace('\\', '/', dirname($script));
        $parts = array_values(array_filter(explode('/', trim($dir, '/')), static fn($p) => $p !== ''));
        $usersIdx = array_search('users', $parts, true);
        if ($usersIdx === false) {
            return '../notifications_api.php';
        }
        $depth = count($parts) - $usersIdx - 1;

        return ($depth > 0 ? str_repeat('../', $depth) : '') . 'notifications_api.php';
    }
}

if (!function_exists('schogms_app_emit_core_scripts')) {
    /**
     * jQuery, Bootstrap, sidebar, feather, dropdown init (emitted once per request).
     */
    function schogms_app_emit_core_scripts(): void
    {
        static $emitted = false;
        if ($emitted) {
            return;
        }
        $emitted = true;

        $base = rtrim(schogms_app_assets_base(), '/');
        $dist = preg_replace('#/assets$#', '/dist', $base) ?: ($base . '/../dist');
        $e = static fn(string $p): string => htmlspecialchars($p, ENT_QUOTES, 'UTF-8');

        echo '<script src="' . $e($base . '/libs/jquery/dist/jquery.min.js') . '"></script>' . "\n";
        echo '<script src="' . $e($base . '/libs/popper.js/dist/umd/popper.min.js') . '"></script>' . "\n";
        echo '<script src="' . $e($base . '/libs/bootstrap/dist/js/bootstrap.min.js') . '"></script>' . "\n";
        echo '<script src="' . $e($dist . '/js/app-style-switcher.js') . '"></script>' . "\n";
        echo '<script src="' . $e($dist . '/js/feather.min.js') . '"></script>' . "\n";
        echo '<script src="' . $e($base . '/libs/perfect-scrollbar/dist/perfect-scrollbar.jquery.min.js') . '"></script>' . "\n";
        echo '<script src="' . $e($dist . '/js/sidebarmenu.js') . '"></script>' . "\n";
        echo '<script src="' . $e($dist . '/js/custom.min.js') . '"></script>' . "\n";
        schogms_app_emit_nav_init_script();
    }
}

if (!function_exists('schogms_app_emit_nav_init_script')) {
    function schogms_app_emit_nav_init_script(): void
    {
        static $emitted = false;
        if ($emitted) {
            return;
        }
        $emitted = true;
        ?>
        <script>
        (function () {
            function initTopbarUi() {
                if (typeof jQuery === 'undefined') {
                    return;
                }
                var $ = jQuery;
                if (typeof $.fn.dropdown !== 'undefined') {
                    $('.topbar .dropdown-toggle[data-toggle="dropdown"]').dropdown();
                    $('.topbar [data-toggle="dropdown"]').not('.dropdown-toggle').addClass('dropdown-toggle');
                    $('.topbar .schogms-notif-toggle[data-toggle="dropdown"]').dropdown();
                }
                $('.schogms-notif-menu').on('click', function (e) {
                    e.stopPropagation();
                });
                if (typeof feather !== 'undefined') {
                    feather.replace();
                }
            }
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', initTopbarUi);
            } else {
                initTopbarUi();
            }
            window.schogmsInitTopbarUi = initTopbarUi;
        })();
        </script>
        <?php
    }
}

if (!function_exists('schogms_app_footer_already_emitted')) {
    function schogms_app_footer_already_emitted(): bool
    {
        return !empty($GLOBALS['schogms_app_footer_emitted']);
    }
}

if (!function_exists('schogms_app_mark_footer_emitted')) {
    function schogms_app_mark_footer_emitted(): void
    {
        $GLOBALS['schogms_app_footer_emitted'] = true;
    }
}
