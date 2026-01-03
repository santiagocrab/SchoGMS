<?php
// Database connection
include 'config/conn.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userName = $_POST['userName'];
    $userEmail = $_POST['userEmail'];
    $userRole = $_POST['userRole'];
    $userCampus = $_POST['userCampus'];

    // Default password
    $defaultPassword = "schogms123";
    
    // Hash the password for security
    $hashedPassword = password_hash($defaultPassword, PASSWORD_DEFAULT);

    // Check if a chairman already exists
    if ($userRole === 'chairman') {
        $checkChairmanQuery = "SELECT user_id FROM users WHERE role = 'chairman' LIMIT 1";
        $checkChairmanResult = $conn->query($checkChairmanQuery);

        if ($checkChairmanResult->num_rows > 0) {
            echo json_encode(["success" => false, "message" => "Only one Chairman account is allowed."]);
            exit; // Stop further execution
        }

        // If the user is a Chairman, set campus to NULL
        $userCampus = null;
    }

    // Insert user into the database (allowing NULL for campus)
    $sql = "INSERT INTO users (name, email, role, campus, password) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssss", $userName, $userEmail, $userRole, $userCampus, $hashedPassword);

    if ($stmt->execute()) {
        echo json_encode(["success" => true, "message" => "User created successfully!"]);
    } else {
        echo json_encode(["success" => false, "message" => "Error creating user: " . $conn->error]);
    }

    $stmt->close();
    $conn->close();
}
?>
