<?php
include 'config/conn.php';

if (isset($_POST['userId'])) {
    $userId = $_POST['userId'];

    // Prepare SQL query to update user status
    $sql = "DELETE FROM users WHERE user_id = ?";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("i", $userId);
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
