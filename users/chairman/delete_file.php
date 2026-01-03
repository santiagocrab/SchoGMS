<?php
require 'config/conn.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['filename'])) {
    $filename = $conn->real_escape_string($_POST['filename']);

    // Delete query
    $deleteQuery = "DELETE FROM ched_masterlist WHERE filename = '$filename'";
    if ($conn->query($deleteQuery)) {
        echo json_encode(["success" => true]);
    } else {
        echo json_encode(["success" => false, "message" => "Failed to delete record."]);
    }
} else {
    echo json_encode(["success" => false, "message" => "Invalid request."]);
}

$conn->close();
?>
