<?php
/**
 * Shared email verification (2FA) for MySQL users table accounts.
 */
require_once __DIR__ . '/../config/schogms_helpers.php';
require_once __DIR__ . '/../config/email_templates.php';

if (!function_exists('schogms_normalize_verification_code')) {
    function schogms_normalize_verification_code(string $code): string
    {
        return preg_replace('/\D+/', '', trim($code)) ?? '';
    }
}

if (!function_exists('schogms_verification_redirect_url')) {
    function schogms_verification_redirect_url(string $role): string
    {
        $base = schogms_app_base_url();
        $home = schogms_role_home($role);

        return $base . '/' . ltrim($home, '/');
    }
}

if (!function_exists('schogms_verify_user_account')) {
    /**
     * @return array{success: bool, error?: string, redirect?: string, already_verified?: bool}
     */
    function schogms_verify_user_account(mysqli $conn, string $email, string $code): array
    {
        $email = trim($email);
        $code = schogms_normalize_verification_code($code);

        if ($email === '' || $code === '') {
            return ['success' => false, 'error' => 'Email and verification code are required.'];
        }

        if (strlen($code) < 4 || strlen($code) > 8) {
            return ['success' => false, 'error' => 'Verification code must be 6 digits.'];
        }

        $stmt = $conn->prepare(
            'SELECT user_id, name, role, email_verified, status, verification_code, verification_expires
             FROM users
             WHERE LOWER(TRIM(email)) = LOWER(TRIM(?))
             LIMIT 1'
        );
        if (!$stmt) {
            return ['success' => false, 'error' => 'Database error. Please try again.'];
        }

        $stmt->bind_param('s', $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result ? $result->fetch_assoc() : null;
        $stmt->close();

        if (!$row) {
            schogms_log_verification_attempt($conn, $email, $code);

            return ['success' => false, 'error' => 'No account found for this email.'];
        }

        $userId = (int) $row['user_id'];
        $userName = (string) $row['name'];
        $userRole = (string) $row['role'];
        $emailVerified = (int) ($row['email_verified'] ?? 0);
        $status = (string) ($row['status'] ?? '');
        $storedCode = schogms_normalize_verification_code((string) ($row['verification_code'] ?? ''));
        $expires = (string) ($row['verification_expires'] ?? '');

        if ($emailVerified === 1 && $status === 'active') {
            return [
                'success' => false,
                'already_verified' => true,
                'error' => 'Your email is already verified. Please log in.',
                'redirect' => schogms_verification_redirect_url($userRole),
            ];
        }

        $canVerify = in_array($status, ['pending', 'inactive'], true)
            || ($status === 'active' && $emailVerified !== 1);

        if (!$canVerify) {
            return [
                'success' => false,
                'error' => 'This account cannot be verified (status: ' . ($status !== '' ? $status : 'unknown') . '). Contact the administrator.',
            ];
        }

        if ($storedCode === '' || !hash_equals($storedCode, $code)) {
            schogms_log_verification_attempt($conn, $email, $code);

            return ['success' => false, 'error' => 'Invalid verification code.'];
        }

        if ($expires !== '') {
            $expTs = strtotime($expires);
            if ($expTs !== false && $expTs < time()) {
                return [
                    'success' => false,
                    'error' => 'Verification code has expired. Ask your administrator to create a new account or reset your code.',
                ];
            }
        }

        $update = $conn->prepare(
            "UPDATE users
             SET status = 'active',
                 email_verified = 1,
                 verification_code = NULL,
                 verification_expires = NULL,
                 updated_at = NOW()
             WHERE user_id = ?"
        );
        if (!$update) {
            return ['success' => false, 'error' => 'Could not activate account.'];
        }
        $update->bind_param('i', $userId);
        if (!$update->execute()) {
            $update->close();

            return ['success' => false, 'error' => 'Could not activate account: ' . $conn->error];
        }
        $update->close();

        return [
            'success' => true,
            'redirect' => schogms_verification_redirect_url($userRole),
            'user_id' => $userId,
            'user_name' => $userName,
            'user_role' => $userRole,
            'user_email' => $email,
        ];
    }
}

if (!function_exists('schogms_log_verification_attempt')) {
    function schogms_log_verification_attempt(mysqli $conn, string $email, string $code): void
    {
        try {
            $check = $conn->query("SHOW TABLES LIKE 'verification_attempts'");
            if (!$check || $check->num_rows === 0) {
                return;
            }
            $stmt = $conn->prepare(
                'INSERT INTO verification_attempts (email, code, attempt_time) VALUES (?, ?, NOW())'
            );
            if ($stmt) {
                $stmt->bind_param('ss', $email, $code);
                $stmt->execute();
                $stmt->close();
            }
        } catch (Throwable $e) {
            schogms_log_error('verification_attempts log failed: ' . $e->getMessage());
        }
    }
}

if (!function_exists('schogms_regenerate_verification_code')) {
    /**
     * Admin/tooling: new code for pending or stuck accounts.
     *
     * @return array{success: bool, code?: string, expires?: string, message?: string}
     */
    function schogms_regenerate_verification_code(mysqli $conn, string $email, int $validMinutes = 1440): array
    {
        $email = trim($email);
        $stmt = $conn->prepare(
            'SELECT user_id, status, email_verified FROM users WHERE LOWER(TRIM(email)) = LOWER(TRIM(?)) LIMIT 1'
        );
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if (!$row) {
            return ['success' => false, 'message' => 'User not found.'];
        }

        if ((int) ($row['email_verified'] ?? 0) === 1 && ($row['status'] ?? '') === 'active') {
            return ['success' => false, 'message' => 'Account is already verified.'];
        }

        $code = (string) random_int(100000, 999999);
        $expires = date('Y-m-d H:i:s', strtotime('+' . max(15, $validMinutes) . ' minutes'));

        $up = $conn->prepare(
            "UPDATE users
             SET status = 'pending',
                 email_verified = 0,
                 verification_code = ?,
                 verification_expires = ?
             WHERE user_id = ?"
        );
        $uid = (int) $row['user_id'];
        $up->bind_param('ssi', $code, $expires, $uid);
        $up->execute();
        $up->close();

        return ['success' => true, 'code' => $code, 'expires' => $expires];
    }
}
