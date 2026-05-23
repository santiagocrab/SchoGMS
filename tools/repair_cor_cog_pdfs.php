<?php
/**
 * Replace invalid demo COR/COG stub PDFs with browser-viewable minimal PDFs.
 *
 * CLI: php tools/repair_cor_cog_pdfs.php
 */
declare(strict_types=1);

require_once dirname(__DIR__) . '/config/schogms_helpers.php';

$dirs = [
    dirname(__DIR__) . '/users/coordinator/uploads/COR',
    dirname(__DIR__) . '/users/coordinator/uploads/COG',
];

$fixed = 0;
$skipped = 0;

foreach ($dirs as $dir) {
    if (!is_dir($dir)) {
        continue;
    }
    foreach (glob($dir . '/*') ?: [] as $path) {
        if (!is_file($path) || strtolower(pathinfo($path, PATHINFO_EXTENSION)) !== 'pdf') {
            continue;
        }
        if (schogms_pdf_is_viewable($path)) {
            $skipped++;
            continue;
        }
        $label = preg_replace('/\.pdf$/i', '', basename($path));
        if (schogms_write_viewable_pdf($path, $label, 'Repaired demo PDF')) {
            $fixed++;
        }
    }
}

echo "Repaired {$fixed} PDF(s), skipped {$skipped} already viewable.\n";
