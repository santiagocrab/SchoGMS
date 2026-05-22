<?php
/**
 * MongoDB scholarship statistics for admin dashboard.
 */
require_once __DIR__ . '/../../config/schogms_helpers.php';

$total_scholars_tdp = 0;
$total_scholars_tes = 0;
$total_documents = 0;
$pending_documents = 0;

try {
    require_once __DIR__ . '/../../conn_mongodb.php';

    $tdpCol = $mongodb->collection('ched_masterlist');
    $tesCol = $mongodb->collection('ched_masterlist_tes');
    $docCol = $mongodb->collection('document_uploads');

    $total_scholars_tdp = $tdpCol->count([]);
    $total_scholars_tes = $tesCol->count([]);
    $total_documents = $docCol->count([]);
    $pending_documents = $docCol->count(['status' => ['$in' => ['pending', 'incomplete', 'under review']]]);
} catch (Throwable $e) {
    schogms_log_error('MongoDB stats failed: ' . $e->getMessage());
}
