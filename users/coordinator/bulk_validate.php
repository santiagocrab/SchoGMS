<?php
/**
 * AJAX bulk validation (TDP updates DB; TES returns stats only).
 */
require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/inc/tdp_bulk_validate.php';
require_once __DIR__ . '/inc/validation_filters.php';

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Invalid request method']);
    exit;
}

if (!($conn instanceof mysqli)) {
    echo json_encode(['success' => false, 'error' => 'Database connection unavailable.']);
    exit;
}

$program = strtolower(trim((string) ($_POST['program'] ?? 'tdp')));
$sheetName = trim((string) ($_POST['sheet_name'] ?? ($sheet_name ?? '')));
if ($sheetName === '') {
    echo json_encode(['success' => false, 'error' => 'No campus assigned.']);
    exit;
}

set_time_limit(120);

if ($program === 'tes') {
    $rows = schogms_validation_fetch_rows($conn, 'tes', $sheetName, $_GET, true);
    $passed = 0;
    $failed = 0;
    foreach ($rows as $row) {
        $c = $row['_check'] ?? schogms_validation_row_check($row, [], 'tes');
        if ($c['passed']) {
            $passed++;
        } else {
            $failed++;
        }
    }
    $stats = ['total' => count($rows), 'passed' => $passed, 'failed' => $failed, 'updated' => 0];
} else {
    $stats = schogms_tdp_bulk_validate_campus($conn, $sheetName, $_GET);
}

echo json_encode([
    'success' => true,
    'message' => sprintf(
        'Validated %d scholar(s): %d passed, %d failed.',
        $stats['total'],
        $stats['passed'],
        $stats['failed']
    ),
    'stats' => $stats,
]);
