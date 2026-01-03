<?php
include 'config/conn.php';

if (isset($_POST['userId']) && isset($_POST['status'])) {
    $userId = $_POST['userId'];
    $status = $_POST['status']; // 'restricted' or 'active'

    // Prepare SQL query to update user status
    $sql = "UPDATE users SET status = ? WHERE user_id = ?";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("si", $status, $userId);
        if ($stmt->execute()) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false]);
        }
        $stmt->close();
    } else {
        echo json_encode(['success' => false]);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid request']);
}
?>
