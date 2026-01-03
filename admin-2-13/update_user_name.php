<?php
include 'config/conn.php';
header('Content-Type: application/json');

if (isset($_POST['userId']) && isset($_POST['newUserName']) && isset($_POST['newUserEmail']) && isset($_POST['newUserRole']) && isset($_POST['newUserCampus'])) {
    $userId = intval($_POST['userId']);
    $newUserName = trim($_POST['newUserName']);
    $newUserEmail = trim($_POST['newUserEmail']);
    $newUserRole = trim($_POST['newUserRole']);
    $newUserCampus = trim($_POST['newUserCampus']);

    if (!empty($newUserName)  && !empty($newUserEmail)  && !empty($newUserRole) && !empty($newUserCampus)) {
        $sql = "UPDATE users SET name = ?, email= ?, role = ? , campus = ? WHERE user_id = ?";
        
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("ssssi", $newUserName, $newUserEmail, $newUserRole,$newUserCampus, $userId);
            
            if ($stmt->execute()) {
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Update failed']);
            }
            $stmt->close();
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid username']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
}
?>
