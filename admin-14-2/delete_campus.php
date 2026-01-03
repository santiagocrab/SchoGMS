<?php
include 'config/conn.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $campusId = $_POST['campusId'] ?? '';

    if (empty($campusId)) {
        echo json_encode(["success" => false, "message" => "No campus selected for deletion."]);
        exit;
    }

    $query = "DELETE FROM campuses WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $campusId);

    if ($stmt->execute()) {
        echo json_encode(["success" => true, "message" => "Campus deleted successfully!"]);
    } else {
        echo json_encode(["success" => false, "message" => "Error deleting campus."]);
    }

    $stmt->close();
}

$conn->close();
?>
