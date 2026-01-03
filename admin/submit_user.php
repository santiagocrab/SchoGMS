<?php
// Start session
session_start();
require 'config/conn.php'; 
require '../users/vendor/autoload.php'; 

date_default_timezone_set("Asia/Manila");

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$smtp_host = 'smtp.hostinger.com'; 
$smtp_username = 'server-email@cloudhost.host'; 
$smtp_password = 'Schogms_2025'; 
$smtp_port = 465; 
$smtp_secure = PHPMailer::ENCRYPTION_SMTPS; 

$response = ["success" => false];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve form data
    $userName = trim($_POST['userName']);
    $userEmail = trim($_POST['userEmail']);
    $userRole = trim($_POST['userRole']);
    $userCampus = trim($_POST['userCampus']);

    // Default password
    $defaultPassword = "schogms123";
    $hashedPassword = password_hash($defaultPassword, PASSWORD_DEFAULT);

    // Check if email already exists
    $checkEmailQuery = "SELECT user_id FROM users WHERE email = ?";
    $stmt = $conn->prepare($checkEmailQuery);
    $stmt->bind_param("s", $userEmail);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        // Email already exists, send notification email
        sendEmailNotification($userEmail, $userName, $smtp_host, $smtp_username, $smtp_password, $smtp_port, $smtp_secure);
        echo json_encode(["success" => false, "message" => "Email already registered! Notification sent."]);
        exit;
    }

    $stmt->close();

    // Check if a chairman already exists
    if ($userRole === 'chairman') {
        $checkChairmanQuery = "SELECT user_id FROM users WHERE role = 'chairman' LIMIT 1";
        $checkChairmanResult = $conn->query($checkChairmanQuery);

        if ($checkChairmanResult->num_rows > 0) {
            echo json_encode(["success" => false, "message" => "Only one Chairman account is allowed."]);
            exit;
        }

        // If the user is a Chairman, set campus to NULL
        $userCampus = null;
    }

    // Generate a 6-digit verification code
    $verificationCode = rand(100000, 999999);
    $expiresAt = date("Y-m-d H:i:s", strtotime("+60 minutes")); // Code expires in 10 minutes

    // Insert user into the database
    $sql = "INSERT INTO users (name, email, role, campus, password, verification_code, verification_expires, email_verified) 
            VALUES (?, ?, ?, ?, ?, ?, ?, 0)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssss", $userName, $userEmail, $userRole, $userCampus, $hashedPassword, $verificationCode, $expiresAt);

    if ($stmt->execute()) {
        // Fetch the newly inserted user ID
        $userId = $stmt->insert_id;

        // Send verification email
        $mail = new PHPMailer(true);
        try {
            // SMTP Configuration
            $mail->isSMTP();
            $mail->Host = $smtp_host;
            $mail->SMTPAuth = true;
            $mail->Username = $smtp_username;
            $mail->Password = $smtp_password;
            $mail->SMTPSecure = $smtp_secure;
            $mail->Port = $smtp_port;

            // Recipients
            $mail->setFrom($smtp_username, 'SchoGMS 2FA Verification');
            $mail->addAddress($userEmail);
            $mail->addReplyTo($smtp_username, 'SchoGMS Support');

            // Email Content
            $mail->isHTML(true);
            $mail->Subject = 'Your Verification Code for SchoGMS';
            $mail->Body = "
                <html>
                    <head>
                        <style>
                            body { font-family: Arial, sans-serif; background-color: #f4f4f4; padding: 20px; }
                            .email-container { max-width: 600px; margin: 0 auto; background-color: #ffffff; padding: 20px; border-radius: 8px; box-shadow: 0 0 10px rgba(0, 0, 0, 0.1); }
                            .header { text-align: center; padding: 10px; background: #5f76e8; color: white; border-radius: 5px; }
                            .message { padding: 15px; font-size: 16px; line-height: 1.6; color: #333333; text-align: center; }
                            .code { font-size: 24px; font-weight: bold; background: #f8f8f8; padding: 10px; border-radius: 5px; display: inline-block; }
                            .footer { text-align: center; font-size: 14px; color: #777777; margin-top: 20px; }
                        </style>
                    </head>
                    <body>
                        <div class='email-container'>
                            <div class='header'>
                                <h2>Verification Code</h2>
                            </div>
                            <div class='message'>
                                <p>Dear $userName,</p>
                                <p>Your verification code is:</p>
                                <p class='code'>$verificationCode</p>
                                <p>This code will expire in 10 minutes. If you did not request this, please ignore this email.</p>
                            </div>
                            <div class='footer'>
                                <p>Best regards,</p>
                                <p>SchoGMS Support Team</p>
                            </div>
                        </div>
                    </body>
                </html>
            ";

            $mail->send();
            $response['email_sent'] = true;
        } catch (Exception $e) {
            $response['email_sent'] = false;
            $response['error'] = "Mailer Error: " . $mail->ErrorInfo;
        }

        $response["success"] = true;
        $response["message"] = "User created successfully! Verification email sent.";
    } else {
        $response["success"] = false;
        $response["message"] = "Error creating user: " . $conn->error;
    }

    $stmt->close();
    $conn->close();
}

echo json_encode($response);

// Function to send email notification if email already exists
function sendEmailNotification($userEmail, $userName, $smtp_host, $smtp_username, $smtp_password, $smtp_port, $smtp_secure) {
    $mail = new PHPMailer(true);
    try {
        // SMTP Configuration
        $mail->isSMTP();
        $mail->Host = $smtp_host;
        $mail->SMTPAuth = true;
        $mail->Username = $smtp_username;
        $mail->Password = $smtp_password;
        $mail->SMTPSecure = $smtp_secure;
        $mail->Port = $smtp_port;

        // Recipients
        $mail->setFrom($smtp_username, 'SchoGMS Notification');
        $mail->addAddress($userEmail);

        // Email Content
        $mail->isHTML(true);
        $mail->Subject = 'Duplicate Email Notification';
        $mail->Body = "Hello $userName,<br><br>Your email is already registered in SchoGMS. If you forgot your password, please request at administrator for reset password.<br><br>Regards,<br>SchoGMS Team";

        $mail->send();
    } catch (Exception $e) {
        error_log("Mailer Error: " . $mail->ErrorInfo);
    }
}
?>
