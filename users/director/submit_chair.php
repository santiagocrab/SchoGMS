<?php
// Start session
session_start();
require '../config/conn.php';
require '../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// SMTP Configuration
$smtp_host = 'smtp.hostinger.com';
$smtp_username = 'server-email@cloudhost.host';
$smtp_password = 'Schogms_2025';
$smtp_port = 465;
$smtp_secure = PHPMailer::ENCRYPTION_SMTPS;

$response = ["success" => false];

// Enable error logging
error_reporting(E_ALL);
ini_set('log_errors', '1');
ini_set('error_log', __DIR__ . '/error_log.txt');

$logFile = __DIR__ . '/error_log.txt'; // Path to error log file

function logError($message)
{
    global $logFile;
    $timestamp = date('Y-m-d H:i:s');
    error_log("[$timestamp] $message" . PHP_EOL, 3, $logFile);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve form data
    $userName = trim($_POST['program_chair_name']);
    $userEmail = trim($_POST['usermail']);
    $course = trim($_POST['course_program_enrolled']);
    $campus = trim($_POST['session_campus']);

    // Default password
    $defaultPassword = "schogms123";
    $hashedPassword = password_hash($defaultPassword, PASSWORD_DEFAULT);

    // Check if a chair is already assigned for this course at this campus
    $checkQuery = "SELECT id FROM assigned_dean WHERE course_program = ? AND campus = ?";
    $stmt = $conn->prepare($checkQuery);
    $stmt->bind_param("ss", $course, $campus);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        // Auto-assign only if no one is assigned yet
        $response["message"] = "A chair is already assigned for this course.";
        echo json_encode($response);
        exit;
    }
    $stmt->close();

    // Insert the new Dean assignment
    $query = "INSERT INTO assigned_dean (campus, course_program, dean, email, password, status) 
              VALUES (?, ?, ?, ?, ?, 'pending')";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("sssss", $campus, $course, $userName, $userEmail, $hashedPassword);

    if ($stmt->execute()) {
        // Send email notification
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
            $mail->addReplyTo($smtp_username, 'SchoGMS Support');

            // Email Content
            $mail->isHTML(true);
            $mail->Subject = 'You Have Been Assigned as Dean';
            $mail->Body = "
                <html>
                    <head>
                        <style>
                            body { font-family: Arial, sans-serif; background-color: #f4f4f4; padding: 20px; }
                            .email-container { max-width: 600px; margin: 0 auto; background-color: #ffffff; padding: 20px; border-radius: 8px; box-shadow: 0 0 10px rgba(0, 0, 0, 0.1); }
                            .header { text-align: center; padding: 10px; background: #5f76e8; color: white; border-radius: 5px; }
                            .message { padding: 15px; font-size: 16px; line-height: 1.6; color: #333333; text-align: center; }
                            .footer { text-align: center; font-size: 14px; color: #777777; margin-top: 20px; }
                        </style>
                    </head>
                    <body>
                        <div class='email-container'>
                            <div class='header'>
                                <h2>Dean Assignment</h2>
                            </div>
                            <div class='message'>
                                <p>Dear $userEmail,</p>
                                <p>You have been assigned as the Dean for:</p>
                                <p><strong>Course:</strong> $course</p>
                                <p><strong>Campus:</strong> $campus</p>
                                <p>Your default login credentials are:</p>
                                <p><strong>Username:</strong> $userName</p>
                                <p><strong>Password:</strong> schogms123</p>
                                <p>
                                    <a href='https://schogms.cloudhost.host/SchoGMS/login-dean-confirm.php?username=" . urlencode($userName) . "&campus=". urlencode($campus)."&email=". urlencode($userEmail). "' 
                                    style='color: #5f76e8; text-decoration: none; font-weight: bold;'>Click here to confirm & log in</a>
                                </p>
                                <p>This Message Re-direct on your dashboard account if you email found.</p>
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
        $response["message"] = "Dean assigned successfully! Email notification sent.";
    } else {
        logError("Error inserting Dean: " . $conn->error);
        $response["message"] = "Error assigning Dean: " . $conn->error;
    }

    $stmt->close();
    $conn->close();
}

// Return JSON response
header('Content-Type: application/json');
echo json_encode($response);
?>