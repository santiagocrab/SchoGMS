<?php
/**
 * Shared COR/COG document viewer — resolves uploads/COR|COG paths on disk (MySQL-era storage).
 */
declare(strict_types=1);

session_start();

if (!isset($_SESSION['user_id'])) {
    http_response_code(403);
    header('Content-Type: text/plain; charset=utf-8');
    echo 'Access denied. Please log in.';
    exit;
}

require_once __DIR__ . '/config/schogms_helpers.php';
require_once __DIR__ . '/users/coordinator/inc/cor_cog_upload_helpers.php';

$storedPath = '';
$fileName = null;

if (!empty($_GET['path'])) {
    $decoded = base64_decode((string) $_GET['path'], true);
    if ($decoded !== false && $decoded !== '') {
        $storedPath = $decoded;
    }
}

// Legacy registrar links: ?file=uploads/COR/x.pdf&type=COR
if ($storedPath === '' && !empty($_GET['file'])) {
    $storedPath = (string) $_GET['file'];
}

if ($storedPath === '') {
    http_response_code(400);
    header('Content-Type: text/plain; charset=utf-8');
    echo 'Missing document path.';
    exit;
}

$storedPath = ltrim(str_replace('\\', '/', $storedPath), '/');

// Only allow COR/COG upload trees
if (!preg_match('#^uploads/(COR|COG)/#i', $storedPath)
    && !preg_match('#^users/(coordinator|registrar)/uploads/(COR|COG)/#i', $storedPath)) {
    http_response_code(403);
    header('Content-Type: text/plain; charset=utf-8');
    echo 'Invalid document path.';
    exit;
}

$actualPath = schogms_cor_cog_resolve_disk_path($storedPath, $fileName);

if ($actualPath === null) {
    http_response_code(404);
    header('Content-Type: text/html; charset=utf-8');
    echo '<p>File not found.</p>';
    echo '<p><strong>Stored path:</strong> ' . htmlspecialchars($storedPath, ENT_QUOTES, 'UTF-8') . '</p>';
    echo '<p>Expected under <code>users/coordinator/uploads/COR</code> or <code>users/coordinator/uploads/COG</code>.</p>';
    exit;
}

$ext = strtolower(pathinfo($actualPath, PATHINFO_EXTENSION));
$allowed = ['pdf', 'jpg', 'jpeg', 'png', 'gif', 'webp'];
if (!in_array($ext, $allowed, true)) {
    http_response_code(400);
    header('Content-Type: text/plain; charset=utf-8');
    echo 'Unsupported file type.';
    exit;
}

$mime = match ($ext) {
    'pdf' => 'application/pdf',
    'jpg', 'jpeg' => 'image/jpeg',
    'png' => 'image/png',
    'gif' => 'image/gif',
    'webp' => 'image/webp',
    default => 'application/octet-stream',
};

$downloadName = basename($actualPath);
$servePath = $actualPath;
$serveBytes = null;

if ($ext === 'pdf' && !schogms_pdf_is_viewable($actualPath)) {
    $label = preg_replace('/\.(pdf|jpe?g|png)$/i', '', $downloadName);
    $label = preg_replace('/_(COR|COG)$/i', '', $label);
    $label = str_replace('_', ' ', $label);
    $serveBytes = schogms_generate_minimal_pdf(
        $label !== '' ? $label : 'Scholar document',
        'Original file on server is a demo placeholder or invalid PDF. Upload a real COR/COG PDF to replace it.'
    );
    if (is_writable(dirname($actualPath))) {
        @file_put_contents($actualPath, $serveBytes);
    }
}

if ($serveBytes !== null) {
    header('Content-Type: application/pdf');
    header('Content-Disposition: inline; filename="' . str_replace('"', '', $downloadName) . '"');
    header('Content-Length: ' . (string) strlen($serveBytes));
    header('Cache-Control: private, max-age=0, must-revalidate');
    header('Pragma: public');
    echo $serveBytes;
    exit;
}

$size = filesize($servePath);
if ($size === false) {
    http_response_code(500);
    echo 'Could not read file.';
    exit;
}

if ($ext === 'pdf') {
    $handle = fopen($servePath, 'rb');
    if ($handle) {
        $header = fread($handle, 4);
        fclose($handle);
        if ($header !== '%PDF') {
            http_response_code(400);
            echo 'File is not a valid PDF document.';
            exit;
        }
    }
}

header('Content-Type: ' . $mime);
header('Content-Disposition: inline; filename="' . str_replace('"', '', $downloadName) . '"');
header('Content-Length: ' . (string) $size);
header('Cache-Control: private, max-age=0, must-revalidate');
header('Pragma: public');
readfile($servePath);
exit;
