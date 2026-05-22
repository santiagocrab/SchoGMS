<?php
/**
 * Download filtered remarks validation as CSV (main TDP/TES column layout).
 */
include '../config/session.php';
require_once __DIR__ . '/inc/remarks_export.php';

if (($role ?? '') !== 'coordinator') {
    header('HTTP/1.1 403 Forbidden');
    exit('Access denied.');
}

$program = strtolower(trim((string) ($_GET['program'] ?? 'tdp')));
if (!in_array($program, ['tdp', 'tes'], true)) {
    $program = 'tdp';
}

$campus = trim((string) ($sheet_name ?? ''));
if ($campus === '' || !($conn instanceof mysqli)) {
    header('HTTP/1.1 400 Bad Request');
    exit('Campus not set or database unavailable.');
}

$format = schogms_remarks_csv_format($_GET);
$rows = schogms_validation_export_rows($conn, $program, $campus, $_GET);
$prepared = schogms_remarks_prepare_rows($rows, $program);
schogms_remarks_stream_csv($prepared, $program, $campus, $format);
