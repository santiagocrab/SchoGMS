<?php
include 'config/session.php';

// Get file and type parameters
$file = $_GET['file'] ?? '';
$type = $_GET['type'] ?? '';

if (empty($file) || empty($type)) {
    die('Invalid file or type parameter');
}

// Security: Only allow COR and COG types
if (!in_array($type, ['COR', 'COG'])) {
    die('Invalid document type');
}

// Security: Check if file path is within allowed directories
$allowedDirs = [
    'uploads/COR/',
    'uploads/COG/',
    'uploads/documents/ISULAN/2024-2025/1st Semester/COR/',
    'uploads/documents/ISULAN/2024-2025/1st Semester/COG/',
    'uploads/documents/ISULAN/2024-2025/2nd Semester/COR/',
    'uploads/documents/ISULAN/2024-2025/2nd Semester/COG/',
    'uploads/documents/ISULAN/2023-2024/1st Semester/COR/',
    'uploads/documents/ISULAN/2023-2024/1st Semester/COG/',
    'uploads/documents/ISULAN/2023-2024/2nd Semester/COR/',
    'uploads/documents/ISULAN/2023-2024/2nd Semester/COG/'
];

$filePath = $file;
$isValidPath = false;

foreach ($allowedDirs as $allowedDir) {
    if (strpos($filePath, $allowedDir) === 0) {
        $isValidPath = true;
        break;
    }
}

if (!$isValidPath) {
    die('Invalid file path');
}

// Check if file exists
if (!file_exists($filePath)) {
    die('File not found: ' . htmlspecialchars(basename($filePath)));
}

// Check if it's a supported file type
$fileExtension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
$allowedExtensions = ['pdf', 'rar', 'zip'];
if (!in_array($fileExtension, $allowedExtensions)) {
    die('Only PDF, RAR, and ZIP files can be viewed');
}

        // Handle RAR files specially
        if ($fileExtension === 'rar') {
            // For RAR files, show download option
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="' . basename($filePath) . '"');
            header('Content-Length: ' . filesize($filePath));
            readfile($filePath);
            exit;
        }
        
        // Check if file is empty or corrupted
        $fileSize = filesize($filePath);
        if ($fileSize === 0) {
            // Create a minimal PDF content for 0-byte files
            $fileName = basename($filePath);
            $studentName = pathinfo($fileName, PATHINFO_FILENAME);
            
            // Generate a simple PDF content
            $pdfContent = "%PDF-1.4
1 0 obj
<<
/Type /Catalog
/Pages 2 0 R
>>
endobj

2 0 obj
<<
/Type /Pages
/Kids [3 0 R]
/Count 1
>>
endobj

3 0 obj
<<
/Type /Page
/Parent 2 0 R
/MediaBox [0 0 612 792]
/Contents 4 0 R
/Resources <<
/Font <<
/F1 5 0 R
>>
>>
>>
endobj

4 0 obj
<<
/Length 200
>>
stream
BT
/F1 12 Tf
100 700 Td
(COR Document - Empty File) Tj
0 -30 Td
(Student: " . $studentName . ") Tj
0 -30 Td
(File Size: 0 bytes) Tj
0 -30 Td
(This file is empty but viewable) Tj
0 -30 Td
(Please re-upload with actual content) Tj
ET
endstream
endobj

5 0 obj
<<
/Type /Font
/Subtype /Type1
/BaseFont /Helvetica
>>
endobj

xref
0 6
0000000000 65535 f 
0000000009 00000 n 
0000000058 00000 n 
0000000115 00000 n 
0000000274 00000 n 
0000000525 00000 n 
trailer
<<
/Size 6
/Root 1 0 R
>>
startxref
625
%%EOF";
            
            // Set headers for PDF viewing
            header('Content-Type: application/pdf');
            header('Content-Disposition: inline; filename="' . $fileName . '"');
            header('Content-Length: ' . strlen($pdfContent));
            header('Cache-Control: private, max-age=0, must-revalidate');
            header('Pragma: public');
            
            // Output the generated PDF content
            echo $pdfContent;
            exit;
        }

// Check if it's a valid PDF by reading the first few bytes
$handle = fopen($filePath, 'rb');
if (!$handle) {
    die('Cannot open PDF file');
}

$header = fread($handle, 4);
fclose($handle);

if ($header !== '%PDF') {
    die('File is not a valid PDF document');
}

// Get just the filename for display
$fileName = basename($filePath);

// Set headers for PDF viewing
header('Content-Type: application/pdf');
header('Content-Disposition: inline; filename="' . $fileName . '"');
header('Content-Length: ' . $fileSize);
header('Cache-Control: private, max-age=0, must-revalidate');
header('Pragma: public');

// Output the file
readfile($filePath);
exit;
?>
