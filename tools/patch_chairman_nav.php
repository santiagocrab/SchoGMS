<?php
/**
 * One-time: replace duplicated chairman nav with unified shell include.
 */
$dir = __DIR__ . '/../users/chairman';
$files = [
    'anex-form2.php',
    'ched_masterlist.php',
    'ched_masterlist_tes.php',
    'program_list.php',
    'upload_ched_tdp.php',
    'verified-scholars.php',
    'change_password.php',
    'masterlist.php',
];

$shellOpen = "<?php require_once __DIR__ . '/inc/chairman_nav.php'; schogms_chairman_shell_open(null); ?>\n";

foreach ($files as $file) {
    $path = $dir . '/' . $file;
    if (!is_readable($path)) {
        echo "Skip missing: $file\n";
        continue;
    }
    $html = file_get_contents($path);
    $orig = $html;

    // Replace from main-wrapper through page-wrapper open
    $pattern = '#<div id="main-wrapper"[^>]*>.*?<div class="page-wrapper">#s';
    if (!preg_match($pattern, $html)) {
        echo "No nav block in: $file\n";
        continue;
    }
    $html = preg_replace($pattern, $shellOpen, $html, 1);

    // Head: lean assets if still has old heavy css block
    if (strpos($html, 'schogms_chairman_head') === false && strpos($html, '<body>') !== false) {
        $headInject = "    <?php require_once __DIR__ . '/inc/assets.php'; schogms_chairman_head(true); ?>\n";
        $html = preg_replace(
            '#<link href="../../dist/css/style\.min\.css" rel="stylesheet">#',
            $headInject,
            $html,
            1
        );
        $html = preg_replace(
            '#\s*<link href="../../assets/extra-libs/datatables[^>]+>\s*#',
            '',
            $html
        );
        $html = preg_replace(
            '#\s*<link href="../../assets/extra-libs/c3[^>]+>\s*#',
            '',
            $html
        );
        $html = preg_replace(
            '#\s*<link href="../../assets/libs/chartist[^>]+>\s*#',
            '',
            $html
        );
        $html = preg_replace(
            '#\s*<link href="../../assets/extra-libs/jvector[^>]+>\s*#',
            '',
            $html
        );
    }

    // Footer scripts: before </body>
    $footer = "    <?php schogms_chairman_shell_close(); ?>\n"
        . "    <?php require_once __DIR__ . '/inc/assets.php'; schogms_chairman_footer_scripts(['datatables' => true, 'sweetalert' => true]); ?>\n";
    if (strpos($html, 'schogms_chairman_footer_scripts') === false) {
        $html = preg_replace(
            '#<script src="../../assets/libs/jquery/dist/jquery\.min\.js"></script>.*?</body>#s',
            $footer . '</body>',
            $html,
            1
        );
    }

    // Close wrapper before footer if duplicate closing divs
    if ($html !== $orig) {
        file_put_contents($path, $html);
        echo "Patched: $file\n";
    }
}

echo "Done.\n";
