<?php
/**
 * Download CSV upload templates (TDP, TES, registrar masterlist).
 */
require_once __DIR__ . '/schogms_upload_format.php';

$type = strtolower(trim((string) ($_GET['type'] ?? '')));
$pack = schogms_upload_format_template_csv($type);

if ($pack === null) {
    http_response_code(404);
    header('Content-Type: text/plain; charset=utf-8');
    echo 'Unknown template type.';
    exit;
}

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="' . str_replace('"', '', $pack['filename']) . '"');
header('Cache-Control: no-store');
echo $pack['content'];
