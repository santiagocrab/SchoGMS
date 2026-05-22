<?php
/**
 * Shared admin user creation (admin/submit_user.php and users/submit_user.php).
 */
require_once __DIR__ . '/../config/mail.php';

if (!function_exists('schogms_admin_create_user')) {
    /**
     * @return array{success: bool, message?: string, email_sent?: bool, error?: string, login_url?: string, default_password?: string, verify_url?: string, manual_code?: string}
     */
    function schogms_admin_create_user(mysqli $conn, string $userName, string $userEmail, string $userRole, ?string $userCampus): array
    {
        $response = ['success' => false];
        $userName = trim($userName);
        $userEmail = trim($userEmail);
        $userRole = trim($userRole);
        $userCampus = $userCampus !== null ? trim($userCampus) : '';

        if ($userName === '' || $userEmail === '' || $userRole === '') {
            $response['message'] = 'Name, email, and role are required.';

            return $response;
        }

        $defaultPassword = schogms_default_user_password();
        $hashedPassword = password_hash($defaultPassword, PASSWORD_DEFAULT);

        $stmt = $conn->prepare('SELECT user_id FROM users WHERE email = ?');
        $stmt->bind_param('s', $userEmail);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $stmt->close();
            $html = schogms_email_duplicate_registration(['name' => $userName]);
            schogms_send_mail($userEmail, 'Email Already Registered — SchoGMS', $html, $userName, 'SchoGMS');

            return ['success' => false, 'message' => 'Email already registered! Notification sent.'];
        }
        $stmt->close();

        if ($userRole === 'chairman') {
            $checkChairmanResult = $conn->query("SELECT user_id FROM users WHERE role = 'chairman' LIMIT 1");
            if ($checkChairmanResult && $checkChairmanResult->num_rows > 0) {
                return ['success' => false, 'message' => 'Only one Chairman account is allowed.'];
            }
            $userCampus = '';
        }

        $isChairman = $userRole === 'chairman';
        $status = $isChairman ? 'active' : 'pending';
        $emailVerified = $isChairman ? 1 : 0;
        $verificationCode = $isChairman ? null : (string) random_int(100000, 999999);
        $expiresAt = $isChairman ? null : date('Y-m-d H:i:s', strtotime('+24 hours'));

        if ($isChairman) {
            $sql = 'INSERT INTO users (name, email, role, campus, password, verification_code, verification_expires, email_verified, status)
                    VALUES (?, ?, ?, ?, ?, NULL, NULL, 1, ?)';
            $stmt = $conn->prepare($sql);
            $campusVal = '';
            $stmt->bind_param('ssssss', $userName, $userEmail, $userRole, $campusVal, $hashedPassword, $status);
        } else {
            $sql = 'INSERT INTO users (name, email, role, campus, password, verification_code, verification_expires, email_verified, status)
                    VALUES (?, ?, ?, ?, ?, ?, ?, 0, ?)';
            $stmt = $conn->prepare($sql);
            $campusVal = $userCampus !== '' ? $userCampus : '';
            $stmt->bind_param('ssssssss', $userName, $userEmail, $userRole, $campusVal, $hashedPassword, $verificationCode, $expiresAt, $status);
        }

        if (!$stmt->execute()) {
            $stmt->close();

            return ['success' => false, 'message' => 'Error creating user: ' . $conn->error];
        }
        $stmt->close();

        $baseUrl = schogms_app_base_url();
        $loginUrl = $baseUrl . '/index.php';

        if ($isChairman) {
            $html = schogms_email_role_assignment([
                'role_title' => 'Chairman — System-wide oversight',
                'name' => $userName,
                'email' => $userEmail,
                'course' => '',
                'campus' => 'All campuses',
                'password' => $defaultPassword,
                'confirm_url' => $loginUrl,
            ]);
            $subject = 'SchoGMS Chairman Account — Login Details';
        } else {
            $html = schogms_email_welcome_verification([
                'name' => $userName,
                'email' => $userEmail,
                'role' => $userRole,
                'campus' => $userCampus !== '' ? $userCampus : 'N/A',
                'verification_code' => $verificationCode,
                'password' => $defaultPassword,
                'expires_minutes' => '1440',
            ]);
            $subject = 'Welcome to SchoGMS - Verify Your Account and Login Details';
        }

        $sent = schogms_send_mail($userEmail, $subject, $html, $userName, 'SchoGMS Account Setup');

        $response['success'] = true;
        $response['email_sent'] = $sent['ok'];
        $response['login_url'] = $loginUrl;
        $response['default_password'] = $defaultPassword;
        $response['verify_url'] = $baseUrl . '/verify.php';

        if ($isChairman) {
            $response['message'] = $sent['ok']
                ? 'Chairman created and activated. Login email sent (no verification step required).'
                : 'Chairman created and activated, but email could not be sent. Share login details manually (see below).';
        } else {
            $response['manual_code'] = $verificationCode;
            $response['message'] = $sent['ok']
                ? 'User created successfully! Verification email sent with login instructions.'
                : 'User created but email could not be sent. Share the verification code and login details manually.';
        }

        if (!$sent['ok']) {
            $response['error'] = $sent['error'] ?? 'Mail send failed';
        }

        return $response;
    }
}
