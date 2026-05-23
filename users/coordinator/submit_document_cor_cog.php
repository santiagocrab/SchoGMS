<?php
/**
 * Coordinator COR / COG document upload (single category or bulk COR+COG).
 */
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/inc/cor_cog_upload_helpers.php';
require_once __DIR__ . '/../../inc/schogms_document_uploads.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
    exit;
}

if (!($conn instanceof mysqli)) {
    echo json_encode(['success' => false, 'message' => 'Database connection unavailable.']);
    exit;
}

$fileGroup = trim((string) ($_POST['fileGroup'] ?? ''));
$campus = trim((string) ($_POST['campus'] ?? ($sheet_name ?? '')));
$scope = strtolower(trim((string) ($_POST['masterlist_scope'] ?? 'all')));
if (!in_array($scope, ['all', 'tdp', 'tes'], true)) {
    $scope = 'all';
}

$fgNorm = schogms_document_uploads_normalize_file_group($conn, $fileGroup);
if (!$fgNorm['ok']) {
    echo json_encode(['success' => false, 'message' => $fgNorm['error']]);
    exit;
}
$fileGroup = $fgNorm['value'];
if ($campus === '') {
    echo json_encode(['success' => false, 'message' => 'Campus is required.']);
    exit;
}

$masterlistIndex = schogms_cor_cog_masterlist_index($conn, $campus, $scope);
if ($masterlistIndex === []) {
    echo json_encode([
        'success' => false,
        'message' => 'No scholars on the masterlist for this campus. Upload CHED TDP/TES masterlist first.',
    ]);
    exit;
}

$bulkDual = !empty($_POST['bulk_dual']);
$batch = [
    'accepted' => [],
    'rejected' => [],
    'errors' => [],
    'by_student' => [],
];

if ($bulkDual) {
    $corFiles = $_FILES['corUpload'] ?? null;
    $cogFiles = $_FILES['cogUpload'] ?? null;
    $hasCor = $corFiles && is_array($corFiles['name'] ?? null) && count(array_filter($corFiles['name'])) > 0;
    $hasCog = $cogFiles && is_array($cogFiles['name'] ?? null) && count(array_filter($cogFiles['name'])) > 0;
    if (!$hasCor && !$hasCog) {
        echo json_encode(['success' => false, 'message' => 'Select at least one COR or COG file.']);
        exit;
    }
    if ($hasCor) {
        $batch = schogms_cor_cog_merge_batch_results(
            $batch,
            schogms_cor_cog_process_upload_batch($conn, $campus, $fileGroup, 'COR', $corFiles, $masterlistIndex)
        );
    }
    if ($hasCog) {
        $batch = schogms_cor_cog_merge_batch_results(
            $batch,
            schogms_cor_cog_process_upload_batch($conn, $campus, $fileGroup, 'COG', $cogFiles, $masterlistIndex)
        );
    }
} else {
    $category = strtoupper(trim((string) ($_POST['category'] ?? '')));
    if (!in_array($category, ['COR', 'COG'], true)) {
        echo json_encode(['success' => false, 'message' => 'Invalid document category.']);
        exit;
    }
    $files = $_FILES['fileUpload'] ?? null;
    if (!$files || !is_array($files['name']) || count(array_filter($files['name'])) === 0) {
        echo json_encode(['success' => false, 'message' => 'Select at least one file to upload.']);
        exit;
    }
    $batch = schogms_cor_cog_process_upload_batch($conn, $campus, $fileGroup, $category, $files, $masterlistIndex);
}

$accepted = count($batch['accepted']);
if ($accepted === 0 && count($batch['rejected']) === 0 && count($batch['errors']) === 0) {
    echo json_encode(['success' => false, 'message' => 'No files were processed.']);
    exit;
}

try {
    $payload = [
    'success' => $accepted > 0,
    'message' => schogms_cor_cog_build_upload_message($batch),
    'uploaded' => $accepted,
    'rejected' => $batch['rejected'],
    'errors' => $batch['errors'],
    'by_student' => (static function (array $byStudent): array {
        $list = [];
        foreach ($byStudent as $name => $data) {
            $list[] = [
                'student' => $name,
                'program' => $data['program'] ?? '',
                'cor' => $data['cor'] ?? [],
                'cog' => $data['cog'] ?? [],
            ];
        }

        return $list;
    })($batch['by_student']),
    'stats' => [
        'accepted' => $accepted,
        'rejected' => count($batch['rejected']),
        'errors' => count($batch['errors']),
        'scholars' => count($batch['by_student']),
    ],
    ];
    echo json_encode($payload);
} catch (mysqli_sql_exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Upload failed: ' . $e->getMessage(),
    ]);
}
