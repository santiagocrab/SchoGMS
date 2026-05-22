<?php
/**
 * One-off: inject schogms_loading_screen_once() after <body> on app pages missing the loader.
 * Run: php tools/patch_loading_screen_all.php
 */
$root = dirname(__DIR__);
$insert = "<?php schogms_loading_screen_once(); ?>\n";
$dirs = [
    $root . '/users/coordinator',
    $root . '/users/registrar',
    $root . '/users/chairman',
    $root . '/users/dean',
    $root . '/users/director',
    $root . '/users/program-chair',
    $root . '/admin',
];
$skipParts = ['/backup/', '/vendor/', '/tools/', 'loading-screen.php', 'patch_loading_screen'];
$patched = 0;
$skipped = 0;

foreach ($dirs as $dir) {
    if (!is_dir($dir)) {
        continue;
    }
    $it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));
    foreach ($it as $file) {
        if (!$file->isFile() || strtolower($file->getExtension()) !== 'php') {
            continue;
        }
        $path = $file->getPathname();
        foreach ($skipParts as $part) {
            if (str_contains($path, $part)) {
                continue 2;
            }
        }
        $content = file_get_contents($path);
        if ($content === false
            || !preg_match('/<body\b/i', $content)
            || str_contains($content, 'schogms_loading_screen_once')
            || str_contains($content, 'schogms-page-loader')
            || str_contains($content, 'schogms_render_page_loader')) {
            $skipped++;
            continue;
        }
        $new = preg_replace('/(<body[^>]*>)/i', "$1\n{$insert}", $content, 1, $count);
        if ($count < 1 || $new === null) {
            $skipped++;
            continue;
        }
        file_put_contents($path, $new);
        $patched++;
        echo 'Patched: ' . str_replace($root . '/', '', $path) . PHP_EOL;
    }
}

// Site login page
$login = $root . '/index.php';
if (is_file($login)) {
    $content = file_get_contents($login);
    if ($content !== false
        && preg_match('/<body\b/i', $content)
        && !str_contains($content, 'schogms_loading_screen_once')) {
        if (!str_contains($content, 'schogms_helpers.php')) {
            $content = preg_replace(
                '/(<\?php\s+session_start\(\);)?/i',
                "<?php require_once __DIR__ . '/config/schogms_helpers.php'; ?>\n",
                $content,
                1
            );
        }
        $new = preg_replace('/(<body[^>]*>)/i', "$1\n{$insert}", $content, 1, $count);
        if ($count > 0 && $new !== null) {
            file_put_contents($login, $new);
            $patched++;
            echo "Patched: index.php\n";
        }
    }
}

echo "\nDone. Patched {$patched} file(s), skipped {$skipped}.\n";
