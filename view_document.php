<?php
// Document viewer - serves COR/COG files securely
session_start();

// Check if user is logged in (basic security)
if (!isset($_SESSION['user_id'])) {
    http_response_code(403);
    die("Access denied. Please log in.");
}

// Get document ID or path from query string
$docId = $_GET['id'] ?? null;
$filePath = $_GET['path'] ?? null;

if (!$docId && !$filePath) {
    http_response_code(400);
    die("Missing document identifier");
}

require_once 'conn_mongodb.php';
$documentCollection = $mongodb->collection('document_uploads');

// Find document
$doc = null;
if ($docId) {
    $docs = $documentCollection->find(['id' => $docId], ['limit' => 1]);
    foreach ($docs as $d) {
        $doc = $d;
        break;
    }
} else if ($filePath) {
    // Decode the path
    $filePath = base64_decode($filePath);
    $docs = $documentCollection->find(['file_path' => $filePath], ['limit' => 1]);
    foreach ($docs as $d) {
        $doc = $d;
        break;
    }
}

if (!$doc || !isset($doc['file_path'])) {
    http_response_code(404);
    die("Document not found");
}

// Get the actual file path
$storedPath = $doc['file_path'];
$actualPath = null;

// Try multiple path variations to find the file
$pathVariations = [];

// If path is absolute (starts with /), use it as-is
if (strpos($storedPath, '/') === 0) {
    $pathVariations[] = $storedPath;
}

// Try relative to SchoGMS root (most common case)
$pathVariations[] = __DIR__ . '/' . ltrim($storedPath, '/');

// IMPORTANT: Files are actually stored in users/registrar/uploads/
// So if path starts with "uploads/", also try "users/registrar/uploads/"
if (strpos($storedPath, 'uploads/') === 0) {
    // Replace "uploads/" with "users/registrar/uploads/"
    $registrarPath = str_replace('uploads/', 'users/registrar/uploads/', $storedPath);
    $pathVariations[] = __DIR__ . '/' . $registrarPath;
    $pathVariations[] = __DIR__ . '/' . ltrim($registrarPath, '/');
}

// Try other variations
$pathVariations[] = dirname(__DIR__) . '/' . ltrim($storedPath, '/');

// If path contains "uploads/", try different base paths
if (strpos($storedPath, 'uploads/') !== false) {
    $pathVariations[] = __DIR__ . '/' . $storedPath;
    $pathVariations[] = $_SERVER['DOCUMENT_ROOT'] . '/SchoGMS/' . ltrim($storedPath, '/');
    $pathVariations[] = $_SERVER['DOCUMENT_ROOT'] . '/SchoGMS/users/registrar/' . ltrim($storedPath, '/');
    $pathVariations[] = $_SERVER['DOCUMENT_ROOT'] . '/' . ltrim($storedPath, '/');
}

// Also try with file_name if different
if (isset($doc['file_name']) && $doc['file_name'] !== basename($storedPath)) {
    $dirPath = dirname($storedPath);
    $pathVariations[] = __DIR__ . '/' . $dirPath . '/' . $doc['file_name'];
}

// Try each variation until we find an existing file
foreach ($pathVariations as $testPath) {
    // Normalize the path
    $testPath = str_replace('\\', '/', $testPath);
    $testPath = preg_replace('#/+#', '/', $testPath);
    
    if (file_exists($testPath) && is_file($testPath)) {
        $actualPath = $testPath;
        break;
    }
}

// If still not found, try to find by filename in uploads directory
if (!$actualPath && isset($doc['file_name'])) {
    $fileName = $doc['file_name'];
    $searchDirs = [
        __DIR__ . '/uploads',
        __DIR__ . '/uploads/documents',
        __DIR__ . '/users/registrar/uploads',
        __DIR__ . '/users/registrar/uploads/documents',
        $_SERVER['DOCUMENT_ROOT'] . '/SchoGMS/uploads',
        $_SERVER['DOCUMENT_ROOT'] . '/SchoGMS/users/registrar/uploads',
    ];
    
    foreach ($searchDirs as $searchDir) {
        if (is_dir($searchDir)) {
            try {
                $iterator = new RecursiveIteratorIterator(
                    new RecursiveDirectoryIterator($searchDir, RecursiveDirectoryIterator::SKIP_DOTS),
                    RecursiveIteratorIterator::SELF_FIRST
                );
                
                foreach ($iterator as $file) {
                    if ($file->isFile() && $file->getFilename() === $fileName) {
                        $actualPath = $file->getRealPath();
                        break 2;
                    }
                }
            } catch (Exception $e) {
                // Skip directories that can't be read
                continue;
            }
        }
    }
}

// If still not found, return error with debugging info
if (!$actualPath || !file_exists($actualPath)) {
    http_response_code(404);
    echo "File not found.<br><br>";
    echo "<strong>Stored path:</strong> " . htmlspecialchars($storedPath) . "<br>";
    echo "<strong>File name:</strong> " . htmlspecialchars($doc['file_name'] ?? 'N/A') . "<br>";
    echo "<strong>Original name:</strong> " . htmlspecialchars($doc['original_name'] ?? 'N/A') . "<br>";
    echo "<strong>__DIR__:</strong> " . htmlspecialchars(__DIR__) . "<br>";
    echo "<strong>DOCUMENT_ROOT:</strong> " . htmlspecialchars($_SERVER['DOCUMENT_ROOT'] ?? 'N/A') . "<br><br>";
    echo "<strong>Tried paths:</strong><br>";
    foreach ($pathVariations as $tried) {
        echo "- " . htmlspecialchars($tried) . " (" . (file_exists($tried) ? "EXISTS" : "NOT FOUND") . ")<br>";
    }
    exit;
}

// Check if it's a PDF
$fileExtension = strtolower(pathinfo($actualPath, PATHINFO_EXTENSION));
if ($fileExtension !== 'pdf') {
    http_response_code(400);
    die("Invalid file type");
}

// Set headers for PDF
header('Content-Type: application/pdf');
header('Content-Disposition: inline; filename="' . basename($actualPath) . '"');
header('Content-Length: ' . filesize($actualPath));
header('Cache-Control: private, max-age=0, must-revalidate');
header('Pragma: public');

// Output the file
readfile($actualPath);
exit;
?>

