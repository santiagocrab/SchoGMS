<?php
/**
 * SchoGMS branded page loader (logo-centered, cyan → purple).
 *
 * @param string $logoUrl  Relative URL to logo from the including page
 * @param string $cssUrl   Relative URL to schogms-loader.css
 * @param string $jsUrl    Relative URL to schogms-loader.js
 */
if (!function_exists('schogms_render_page_loader')) {
    function schogms_render_page_loader(
        string $logoUrl = '../../assets/images/logo.png',
        string $cssUrl = '../../assets/css/schogms-loader.css',
        string $jsUrl = '../../assets/js/schogms-loader.js'
    ): void {
        $logoEsc = htmlspecialchars($logoUrl, ENT_QUOTES, 'UTF-8');
        $cssEsc = htmlspecialchars($cssUrl, ENT_QUOTES, 'UTF-8');
        $jsEsc = htmlspecialchars($jsUrl, ENT_QUOTES, 'UTF-8');
        ?>
<link rel="preload" href="<?= $logoEsc ?>" as="image">
<link rel="stylesheet" href="<?= $cssEsc ?>">
<div
    id="schogms-page-loader"
    class="schogms-page-loader"
    role="status"
    aria-live="polite"
    aria-busy="true"
    aria-label="Loading SchoGMS"
>
    <div class="schogms-loader-ambient" aria-hidden="true">
        <div class="schogms-loader-orb schogms-loader-orb--cyan"></div>
        <div class="schogms-loader-orb schogms-loader-orb--purple"></div>
        <div class="schogms-loader-grid"></div>
    </div>
    <div class="schogms-loader-curtain" aria-hidden="true"></div>
    <div class="schogms-loader-panel">
        <div class="schogms-loader-emblem">
            <img
                class="schogms-loader-logo"
                src="<?= $logoEsc ?>"
                width="280"
                height="112"
                alt="SchoGMS Scholarship System"
                decoding="async"
                fetchpriority="high"
            >
        </div>
        <p class="schogms-loader-eyebrow">Scholarship System</p>
        <h1 class="schogms-loader-title">SchoGMS</h1>
        <p class="schogms-loader-status" id="schogms-loader-status">Preparing your workspace</p>
        <div class="schogms-loader-track" aria-hidden="true">
            <div class="schogms-loader-bar"></div>
        </div>
    </div>
</div>
<script src="<?= $jsEsc ?>" defer></script>
        <?php
    }
}
