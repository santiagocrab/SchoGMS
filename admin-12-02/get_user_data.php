<?php
include 'config/conn.php';

if (isset($_GET['userId'])) {
    $userId = $_GET['userId'];
    $sql = "SELECT user_id, name, email, role, campus FROM users WHERE user_id = ?";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $stmt->bind_result($user_id, $name, $email, $role, $campus);
            $stmt->fetch();
            $response = array(
                'success' => true,
                'user_id' => $user_id,
                'name' => $name,
                'email' => $email,
                'role' => $role,
                'campus' => $campus
            );
            echo json_encode($response);
        } else {
            echo json_encode(array('success' => false));
        }
        $stmt->close();
    }
} else {
    echo json_encode(array('success' => false));
}
?>
