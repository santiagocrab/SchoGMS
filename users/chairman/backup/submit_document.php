<?php
// Database connection settings
include 'config/conn.php';

// Handle the file upload and form data
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the form inputs
    $fileGroup = $_POST['fileGroup'] ?? '';
    $category = $_POST['category'] ?? '';
    $files = $_FILES['fileUpload'] ?? null;

    // Validate inputs
    if (empty($fileGroup) || empty($category) || !$files || count($files['name']) == 0) {
        echo json_encode(["success" => false, "message" => "Please provide all required fields and upload at least one document."]);
        exit;
    }

    // Validate file types
    $allowedTypes = ['application/pdf', 'image/jpeg', 'image/png']; // Add allowed mime types
    $baseUploadDir = 'uploads/';

    // Set specific upload directory based on the category
    $uploadDir = '';
    if ($category === 'COR') {
        $uploadDir = $baseUploadDir . 'COR/';
    } elseif ($category === 'COG') {
        $uploadDir = $baseUploadDir . 'COG/';
    }

    // Create the directory if it doesn't exist
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true); // Create the directory if it doesn't exist
    }

    $successFiles = [];
    $errorFiles = [];

    // Loop through each uploaded file
    foreach ($files['name'] as $index => $fileName) {
        // Check file type
        if (!in_array($files['type'][$index], $allowedTypes)) {
            $errorFiles[] = "File {$fileName} has an invalid file type.";
            continue;
        }

        // Check file size
        if ($files['size'][$index] > 0) {
            // Generate a unique file name
            $newFileName = time() . '_' . basename($fileName);  // You can use a unique naming strategy
            $filePath = $uploadDir . $newFileName;

            // Move the uploaded file to the server directory
            if (move_uploaded_file($files['tmp_name'][$index], $filePath)) {
                // Insert file data into the database (Note: 'unique_id' is auto-incremented or set as UUID)
                $stmt = $conn->prepare("INSERT INTO document_uploads (file_group, category, file_name, file_path) VALUES (?, ?, ?, ?)");
                if ($stmt) {
                    $stmt->bind_param("ssss", $fileGroup, $category, $fileName, $filePath);
                    if ($stmt->execute()) {
                        $successFiles[] = $fileName;
                    } else {
                        $errorFiles[] = "Failed to insert file {$fileName} into the database.";
                    }
                    $stmt->close();
                } else {
                    $errorFiles[] = "Failed to prepare SQL statement for {$fileName}.";
                }
            } else {
                $errorFiles[] = "Failed to upload file {$fileName}.";
            }
        } else {
            $errorFiles[] = "File {$fileName} is empty.";
        }
    }

    // Check if any files were successfully uploaded and processed
    if (count($successFiles) > 0) {
        $message = "Successfully uploaded files.";
        if (count($errorFiles) > 0) {
            $message .= ". However, there were errors with: ";
        }
        echo json_encode(["success" => true, "message" => $message]);
    } else {
        echo json_encode(["success" => false, "message" => "No files were uploaded. "]);
    }
} else {
    echo json_encode(["success" => false, "message" => "Invalid request method. Only POST is allowed."]);
}
?>
