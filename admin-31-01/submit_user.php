<?php
// Database connection
include 'config/conn.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userName = $_POST['userName'];
    $userEmail = $_POST['userEmail'];
    $userRole = $_POST['userRole'];
    
    // Default password
    $defaultPassword = "schogms123";
    
    // Hash the password for security
    $hashedPassword = password_hash($defaultPassword, PASSWORD_DEFAULT);

    // Insert user into the database
    $sql = "INSERT INTO users (name, email, role, password) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssss", $userName, $userEmail, $userRole, $hashedPassword);

    if ($stmt->execute()) {
        echo json_encode(["success" => true, "message" => "User created successfully!"]);
    } else {
        echo json_encode(["success" => false, "message" => "Error creating user: " . $conn->error]);
    }

    $stmt->close();
    $conn->close();
}
?>
