<?php
/**
 * Shared coordinator layout helpers (call after ../config/session.php).
 */
if (!function_exists('schogms_coordinator_preloader_off')) {
    function schogms_coordinator_preloader_off(): void
    {
        echo '<style>.preloader{display:none!important}</style>';
    }
}
