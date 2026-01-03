<?php
require 'config/conn.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $filename = $conn->real_escape_string($_POST['id']);

    // Delete query
    $deleteQuery = "DELETE FROM assigned_program_chairs WHERE id = '$filename'";
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
