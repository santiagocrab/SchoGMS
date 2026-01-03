<?php
require 'config/conn.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['file_group']) && isset($_POST['filename'])) {
    $fileGroup = $conn->real_escape_string($_POST['file_group']);
    $filename = $conn->real_escape_string($_POST['filename']);

    // Update query to change all records that match the filename
    $updateQuery = "UPDATE ched_masterlist 
                    SET file_group = '$fileGroup', filename = '$filename' 
                    WHERE filename = '$filename'";  // This updates all records that have the same filename

    if ($conn->query($updateQuery)) {
        echo json_encode(["success" => true, "message" => "All records with the filename updated successfully."]);
    } else {
        echo json_encode(["success" => false, "message" => "Failed to update records."]);
    }
} else {
    echo json_encode(["success" => false, "message" => "Invalid request."]);
}

$conn->close();
?>
