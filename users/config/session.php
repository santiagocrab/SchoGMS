<?php
/**
 * Shared session guard: MongoDB users (legacy) and MySQL users (admin + email verify).
 */
require_once __DIR__ . '/../../config/schogms_helpers.php';
require_once __DIR__ . '/../../config/mysql_auth.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    if (!headers_sent()) {
        header('Location: ../../index.php?ERROR=session');
    } else {
        echo '<script>window.location.href="../../index.php?ERROR=session";</script>';
    }
    exit;
}

$authType = $_SESSION['auth_type'] ?? 'mongodb';
$user_id = (int) $_SESSION['user_id'];
$fullname = 'User';
$email = '';
$sheet_name = '';
$role = $_SESSION['role'] ?? '';

if ($authType === 'mysql') {
    $row = schogms_load_mysql_session_user($user_id);
    if (!$row) {
        if (!headers_sent()) {
            header('Location: ../../index.php?ERROR=session');
        }
        exit;
    }
    $user_id = $row['user_id'];
    $fullname = $row['fullname'];
    $email = $row['email'];
    $sheet_name = $row['sheet_name'];
    $role = $row['role'];
    $_SESSION['username'] = $fullname;
    $_SESSION['role'] = $role;
} else {
    require_once __DIR__ . '/../../conn_mongodb.php';

    try {
        $user = $users->findOne(['user_id' => $user_id]);

        if (!$user) {
            schogms_log_error('Session user not found in MongoDB', ['user_id' => $user_id]);
            if (!headers_sent()) {
                header('Location: ../../index.php?ERROR=session');
            }
            exit;
        }

        if (($user['status'] ?? '') === 'restricted') {
            header('Location: ../../index.php?ERROR=restricted');
            exit;
        }

        $user_id = (int) ($user['user_id'] ?? $user_id);
        $fullname = (string) ($user['name'] ?? $user['fullname'] ?? 'User');
        $email = (string) ($user['email'] ?? '');
        $sheet_name = (string) ($user['campus'] ?? '');
        $role = (string) ($user['role'] ?? $role);

        $_SESSION['username'] = $fullname;
        $_SESSION['role'] = $role;
    } catch (Throwable $e) {
        schogms_log_error('Session load failed: ' . $e->getMessage());
        if (!headers_sent()) {
            header('Location: ../../index.php?ERROR=session');
        }
        exit;
    }
}

$script = str_replace('\\', '/', $_SERVER['SCRIPT_FILENAME'] ?? '');
if (preg_match('#/users/([^/]+)/#', $script, $m)) {
    $folder = $m[1];
    $allowed = schogms_role_folder($role);
    if ($allowed !== null && $folder !== $allowed && $folder !== 'config') {
        header('Location: ../../index.php?ERROR=restricted');
        exit;
    }
}

// Coordinator/registrar pages use MySQL mysqli — conn_mongodb.php sets $conn to MongoDBi, so restore mysqli.
if (!isset($conn) || !($conn instanceof mysqli)) {
    require_once __DIR__ . '/conn.php';
}
