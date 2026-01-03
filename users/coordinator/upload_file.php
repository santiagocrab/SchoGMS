<?php
require '../config/conn.php'; // Include your database connection file

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $uploadDir = 'forms/';
    $user_id = $_POST['user_id'] ?? '';
    $email = $_POST['email'] ?? '';
    $campus = $_POST['campus'] ?? '';

    if (!isset($_FILES['excelFile']) || empty($user_id) || empty($email) || empty($campus) ) {
        echo json_encode(["success" => false, "error" => "Missing file or file group."]);
        exit;
    }

    $file = $_FILES['excelFile'];
    $fileName = basename($file['name']); // Keep original filename
    $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
    $allowedExtensions = ['xls', 'xlsx', 'csv'];

    if (!in_array($fileExt, $allowedExtensions)) {
        echo json_encode(["success" => false, "error" => "Invalid file type."]);
        exit;
    }

    $filePath = $uploadDir . $fileName; // Keep original filename

    // Check if filename already exists in database
    $stmt = $conn->prepare("SELECT COUNT(*) FROM file_submissions WHERE file_name = ?");
    $stmt->bind_param("s", $fileName);
    $stmt->execute();
    $stmt->bind_result($fileCount);
    $stmt->fetch();
    $stmt->close();

    if ($fileCount > 0) {
        echo json_encode(["success" => false, "error" => "File already exists in the database."]);
        exit;
    }

    // Move the file to the upload directory
    if (move_uploaded_file($file['tmp_name'], $filePath)) {
        $stmt = $conn->prepare("INSERT INTO file_submissions (user_id, user_email, campus, file_name, file_path, uploaded_at, status) VALUES (?, ?, ?, ?, ?, NOW(), 'Pending')");
        $stmt->bind_param("sssss",$user_id, $email, $campus, $fileName, $filePath);

        if ($stmt->execute()) {
            echo json_encode(["success" => true, "message" => "File uploaded successfully!"]);
        } else {
            echo json_encode(["success" => false, "error" => "Database error: " . $stmt->error]);
        }
        $stmt->close();
    } else {
        echo json_encode(["success" => false, "error" => "Failed to upload file."]);
    }

    $conn->close();
}
?>
