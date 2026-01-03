<?php
require 'config/conn.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['category'])) {
    $category = $conn->real_escape_string($_GET['category']);

    // Ensure category is not empty
    if (empty($category)) {
        echo json_encode(["success" => false, "message" => "Invalid category name"]);
        exit;
    }

    // Directories to track COR & COG files
    $corDir = __DIR__ . "/";
    $cogDir = __DIR__ . "/";

    // Fetch files from the database linked to this category
    $fetchFilesQuery = "SELECT file_path FROM document_uploads WHERE category = '$category'";
    $result = $conn->query($fetchFilesQuery);

    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $filePath = __DIR__ . "/" . $row['file_path']; // Absolute path to file

            // Check if file exists in COR or COG folders and delete
            if (strpos($filePath, $corDir) !== false || strpos($filePath, $cogDir) !== false) {
                if (file_exists($filePath)) {
                    unlink($filePath); // Delete file
                }
            }
        }
    }

    // Delete only COR & COG database records
    $deleteQuery = "DELETE FROM document_uploads WHERE category = '$category'";

    if ($conn->query($deleteQuery)) {
        echo json_encode(["success" => true, "message" => "Files and records deleted successfully for $category"]);
    } else {
        echo json_encode(["success" => false, "message" => "Error deleting records: " . $conn->error]);
    }
} else {
    echo json_encode(["success" => false, "message" => "Invalid request"]);
}

$conn->close();
?>
