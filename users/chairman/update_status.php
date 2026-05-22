<?php
require 'config/conn.php';
require __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../../config/mail.php';

$response = ['success' => false];

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['file_id'], $_POST['status'])) {
    $fileId = $_POST['file_id'];
    $status = $_POST['status'];

    // Sanitize input to avoid SQL injection
    if (!is_numeric($fileId) || !in_array($status, ['Approved', 'Pending', 'Rejected'])) {
        $response["error"] = "Invalid data provided.";
        echo json_encode($response);
        exit;
    }

    // Prepare query to get the file details from the database
    $stmt = $conn->prepare("SELECT user_id, user_email, campus, file_name FROM file_submissions WHERE id = ?");
    $stmt->bind_param("i", $fileId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();

        // Update file status
        $updateStmt = $conn->prepare("UPDATE file_submissions SET status = ? WHERE id = ?");
        $updateStmt->bind_param("si", $status, $fileId);

        if ($updateStmt->execute()) {
            $response["success"] = true;

            // If status is 'Approved', send an email to the coordinator
            if ($status == 'Approved') {
                $userEmail = $row['user_email'];
                $userId = $row['user_id'];
                $campus = $row['campus'];
                $fileName = $row['file_name'];

                $html = schogms_email_chairman_approved([
                    'campus' => $campus,
                    'file_name' => $fileName,
                    'user_id' => (string) $userId,
                ]);
                $sent = schogms_send_mail(
                    $userEmail,
                    'Annex 7 Approved — SchoGMS',
                    $html,
                    'Coordinator',
                    'SchoGMS Scholarship System'
                );
                $response['email_sent'] = $sent['ok'];
                if (!$sent['ok']) {
                    $response['error'] = $sent['error'] ?? 'Mail send failed';
                }
            }

        } else {
            $response["error"] = "Database error: " . $updateStmt->error;
        }

        $updateStmt->close();
    } else {
        $response["error"] = "File not found.";
    }

    $stmt->close();
}

$conn->close();
echo json_encode($response);
?>
