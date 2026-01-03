<?php
require 'config/conn.php'; // Adjust based on your setup
require '../vendor/autoload.php'; // Make sure the path is correct

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$smtp_host = 'smtp.hostinger.com'; // Replace with your SMTP server
$smtp_username = 'server-email@cloudhost.host'; // Replace with your email
$smtp_password = 'Schogms_2025'; // Replace with your email password
$smtp_port = 465; // Adjust port (e.g., 465 for SSL, 587 for TLS)
$smtp_secure = PHPMailer::ENCRYPTION_SMTPS; // Use 'ssl' for port 465

$response = ["success" => false];

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

                // Create a new PHPMailer instance
                $mail = new PHPMailer(true);

                try {
                    // Server settings
                    $mail->isSMTP();                                      // Set mailer to use SMTP
                    $mail->Host = $smtp_host;                        // Specify main SMTP server (e.g., Gmail SMTP)
                    $mail->SMTPAuth = true;                                // Enable SMTP authentication
                    $mail->Username = $smtp_username;               // SMTP username
                    $mail->Password = $smtp_password;                      // SMTP password
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;    // Enable SSL encryption for port 465
                    $mail->Port = $smtp_port;                                     // TCP port to connect to

                    // Recipients
                    $mail->setFrom($smtp_username, 'SchoGMS Scholarship System');
                    $mail->addAddress($userEmail, $campus);                // Add recipient's email
                    $mail->addReplyTo($smtp_username, 'SchoGMS Scholarship System');

                    // Content
                    $mail->isHTML(true);                                  // Set email format to HTML
                    $mail->Subject = 'Anex 7 Form Approved by Chairman';
                    $mail->Body    = "
                        <html>
                            <head>
                                <style>
                                    body { font-family: Arial, sans-serif; background-color: #f4f4f4; padding: 20px; }
                                    .email-container { max-width: 600px; margin: 0 auto; background-color: #ffffff; padding: 20px; border-radius: 8px; box-shadow: 0 0 10px rgba(0, 0, 0, 0.1); }
                                    .header { text-align: center; padding: 10px; background: linear-gradient(to right, #8971ea, #7f72ea, #7574ea, #6a75e9, #5f76e8); color: white; border-radius: 5px; }
                                    .message { padding: 15px; font-size: 16px; line-height: 1.6; color: #333333; }
                                    .footer { text-align: center; font-size: 14px; color: #777777; margin-top: 20px; }
                                    .button { display: inline-block; background-color: #4CAF50; color: white; padding: 10px 20px; font-size: 16px; text-decoration: none; border-radius: 5px; margin-top: 20px; }
                                </style>
                            </head>
                            <body>
                                <div class='email-container'>
                                    <div class='header'>
                                        <h2>Anex 7 Form Approved</h2>
                                    </div>
                                    <div class='message'>
                                        <p>Dear Coordinator From Campus <strong>$campus</strong>,</p>
                                        <p>The Anex 7 form uploaded by the following details has been approved by the Chairman and ready for submission to CHED Region XII</p>
                                        <ul>
                                            <li><strong>User ID:</strong> $userId</li>
                                            <li><strong>Campus:</strong> $campus</li>
                                            <li><strong>File Name:</strong> $fileName</li>
                                        </ul>
                                        <p>Thank you and more power.</p>
                                    </div>
                                    <div class='footer'>
                                        <p>Best regards,</p>
                                        <p>Scholarship System (SchoGMS)</p>
                                    </div>
                                </div>
                            </body>
                        </html>
                    ";

                    // Send the email
                    $mail->send();
                    $response['email_sent'] = true;
                } catch (Exception $e) {
                    $response['email_sent'] = false;
                    $response['error'] = "Mailer Error: " . $mail->ErrorInfo;
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
