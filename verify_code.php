<?php
session_start();
require 'users/config/conn.php';

$response = ["success" => false];

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['user_email'], $_POST['verification_code'])) {
    $userEmail = trim($_POST['user_email']);
    $enteredCode = trim($_POST['verification_code']);

    // Check if the user exists and has an active verification code
    $stmt = $conn->prepare("SELECT user_id, name, role, email_verified, status FROM users WHERE email = ? AND verification_code = ? AND verification_expires > NOW()");
    $stmt->bind_param("ss", $userEmail, $enteredCode);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($userId, $userName, $userRole, $emailVerified, $status);
        $stmt->fetch();

        if ($emailVerified == 1) {
            $response["error"] = "Your email is already verified. Please log in.";
        } else {
            // Ensure the user is pending before updating status
            if ($status === 'pending') {
                // Mark email as verified and activate the account
                $updateStmt = $conn->prepare("UPDATE users SET status = 'active', email_verified = 1, verification_code = NULL, verification_expires = NULL WHERE user_id = ?");
                $updateStmt->bind_param("i", $userId);
                $updateStmt->execute();
                $updateStmt->close();

                // Set session variables
                $_SESSION['user_id'] = $userId;
                $_SESSION['user_email'] = $userEmail;
                $_SESSION['user_name'] = $userName;
                $_SESSION['user_role'] = $userRole;
                $_SESSION['email_verified'] = true;

                // Redirect based on role
                $roleRedirects = [
                    'coordinator' => "users/coordinator/",
                    'chairman' => "users/chairman/",
                    'registrar' => "users/registrar/",
                    'program-head' => "users/program-head/",
                    'dean' => "users/dean/"
                ];
                $redirectURL = isset($roleRedirects[$userRole]) ? $roleRedirects[$userRole] : "users/";

                $response["success"] = true;
                $response["redirect"] = $redirectURL;
            } else {
                $response["error"] = "Your account is not in a pending state.";
            }
        }
    } else {
        // Log invalid attempt (Optional: You can track failed verification attempts)
        $logStmt = $conn->prepare("INSERT INTO verification_attempts (email, code, attempt_time) VALUES (?, ?, NOW())");
        $logStmt->bind_param("ss", $userEmail, $enteredCode);
        $logStmt->execute();
        $logStmt->close();

        $response["error"] = "Invalid or expired verification code.";
    }

    $stmt->close();
}

$conn->close();
echo json_encode($response);
?>
