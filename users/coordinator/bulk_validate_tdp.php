<?php
/**
 * AJAX: run bulk TDP validation for the coordinator campus.
 */
require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/inc/tdp_bulk_validate.php';

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Invalid request method']);
    exit;
}

if (!($conn instanceof mysqli)) {
    echo json_encode(['success' => false, 'error' => 'Database connection unavailable.']);
    exit;
}

$sheetName = trim((string) ($_POST['sheet_name'] ?? ($sheet_name ?? '')));
if ($sheetName === '') {
    echo json_encode(['success' => false, 'error' => 'No campus assigned.']);
    exit;
}

set_time_limit(120);
$stats = schogms_tdp_bulk_validate_campus($conn, $sheetName, $_GET);

echo json_encode([
    'success' => true,
    'message' => sprintf(
        'Validated %d of %d scholars (%d passed, %d failed).',
        $stats['updated'],
        $stats['total'],
        $stats['passed'],
        $stats['failed']
    ),
    'stats' => $stats,
]);
