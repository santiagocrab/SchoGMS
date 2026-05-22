<?php
/**
 * Download sample template for coordinator Submit Form upload.
 */
require __DIR__ . '/../config/session.php';

$samplePath = __DIR__ . '/samples/coordinator_submit_form_template.csv';
if (!is_readable($samplePath)) {
    http_response_code(404);
    echo 'Sample file not found.';
    exit;
}

header('Content-Type: text/csv; charset=UTF-8');
header('Content-Disposition: attachment; filename="SchoGMS_Annex7_Submit_Form_Sample.csv"');
header('Content-Length: ' . (string) filesize($samplePath));
readfile($samplePath);
exit;
