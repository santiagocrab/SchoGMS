<?php
$files = glob(__DIR__ . '/../users/chairman/*.php');
foreach ($files as $path) {
    if (str_contains($path, '/backup/')) {
        continue;
    }
    $html = file_get_contents($path);
    if (strpos($html, 'schogms_chairman_shell_close') === false) {
        continue;
    }
    // Remove duplicate legacy wrapper/footer closes before shell_close
    $html = preg_replace(
        '#\s*<!-- ============================================================== -->\s*<!-- End Page wrapper.*?<!-- End Wrapper -->.*?<!-- End Wrapper -->.*?<!-- =+ -->\s*#s',
        "\n",
        $html
    );
    $html = preg_replace(
        '#\s*<footer class="footer[^>]*>.*?</footer>\s*#s',
        "\n",
        $html
    );
    $html = preg_replace(
        '#\s*</div>\s*<!-- ============================================================== -->\s*<!-- End Page wrapper[^>]*-->\s*</div>\s*<!--[^>]*End Wrapper[^>]*-->\s*</div>\s*<!--[^>]*End Wrapper[^>]*-->\s*#s',
        "\n                </div>\n",
        $html
    );
    file_put_contents($path, $html);
    echo basename($path) . "\n";
}
echo "Layout cleanup done.\n";
