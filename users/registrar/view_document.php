<?php
/**
 * Legacy COR/COG viewer — redirect to shared project viewer with correct path resolution.
 */
require_once __DIR__ . '/config/session.php';
require_once __DIR__ . '/../coordinator/inc/cor_cog_upload_helpers.php';

$file = trim((string) ($_GET['file'] ?? ''));
$type = strtoupper(trim((string) ($_GET['type'] ?? '')));

if ($file === '') {
    http_response_code(400);
    die('Missing file parameter.');
}

if (!in_array($type, ['COR', 'COG'], true)) {
    http_response_code(400);
    die('Invalid document type.');
}

$url = schogms_cor_cog_view_document_url($file, 'registrar');
header('Location: ' . $url, true, 302);
exit;
