<?php
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/inc/coordinator_dashboard_stats.php';

if (!($conn instanceof mysqli)) {
    echo json_encode(['success' => false, 'message' => 'Database unavailable']);
    exit;
}

$campus = schogms_coordinator_dashboard_campus();
$fileGroup = trim((string) ($_GET['file_group'] ?? ''));

$stats = schogms_coordinator_dashboard_stats($conn, $campus, $fileGroup);
$charts = schogms_coordinator_dashboard_chart_data($conn, $campus, $fileGroup);

echo json_encode([
    'success' => true,
    'campus' => $campus,
    'total_records' => $stats['tdp_records'],
    'total_records_tes' => $stats['tes_records'],
    'total_courses' => $charts['tdp_courses'],
    'total_courses_tes' => $charts['tes_courses'],
    'total_file_groups' => $charts['tdp_file_groups'],
]);
