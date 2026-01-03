<?php
require 'config/conn.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['campus'])) {
    $campus = $conn->real_escape_string($_GET['campus']);

    // Ensure campus is not empty
    if (empty($campus)) {
        echo json_encode(["success" => false, "message" => "Invalid campus name"]);
        exit;
    }

    // Delete only COR & COG records
    $deleteQuery = "
        DELETE FROM registrar_master_list
        WHERE campus = '$campus';
    ";

    if ($conn->query($deleteQuery)) {
        echo json_encode(["success" => true, "message" => "Records deleted successfully for $campus"]);
    } else {
        echo json_encode(["success" => false, "message" => "Error deleting records: " . $conn->error]);
    }
} else {
    echo json_encode(["success" => false, "message" => "Invalid request"]);
}
$conn->close();
?>
