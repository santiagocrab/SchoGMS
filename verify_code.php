<?php
/**
 * Email verification API (2FA) — activates pending MySQL users (coordinator, registrar, director, etc.).
 */
declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/inc/verify_account.php';

$response = ['success' => false];

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $response['error'] = 'Invalid request method.';
    echo json_encode($response);
    exit;
}

$email = trim((string) ($_POST['user_email'] ?? ''));
$code = trim((string) ($_POST['verification_code'] ?? ''));

try {
    $c = require __DIR__ . '/config/schogms_mysql.php';
    $conn = new mysqli($c['host'], $c['username'], $c['password'], $c['database']);
    $conn->set_charset('utf8mb4');

    $result = schogms_verify_user_account($conn, $email, $code);
    $conn->close();

    if (!empty($result['success'])) {
        $_SESSION['auth_type'] = 'mysql';
        $_SESSION['user_id'] = (int) ($result['user_id'] ?? 0);
        $_SESSION['username'] = (string) ($result['user_name'] ?? '');
        $_SESSION['role'] = (string) ($result['user_role'] ?? '');
        $_SESSION['user_email'] = $email;
        $_SESSION['user_name'] = (string) ($result['user_name'] ?? '');
        $_SESSION['user_role'] = (string) ($result['user_role'] ?? '');
        $_SESSION['email_verified'] = true;
        unset($_SESSION['apc_id']);

        $response['success'] = true;
        $response['redirect'] = (string) ($result['redirect'] ?? schogms_verification_redirect_url((string) ($result['user_role'] ?? '')));
    } else {
        $response['error'] = (string) ($result['error'] ?? 'Verification failed.');
        if (!empty($result['already_verified']) && !empty($result['redirect'])) {
            $response['redirect'] = $result['redirect'];
        }
    }
} catch (Throwable $e) {
    schogms_log_error('verify_code.php: ' . $e->getMessage());
    $response['error'] = 'Server error during verification. Please try again or contact support.';
}

echo json_encode($response);
