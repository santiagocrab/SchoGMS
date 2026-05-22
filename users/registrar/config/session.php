<?php
require_once __DIR__ . '/../../../config/schogms_helpers.php';
require_once __DIR__ . '/../../../config/mysql_auth.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    header('Location: ../../index.php?ERROR=session');
    exit;
}

$user_id = (int) $_SESSION['user_id'];
$fullname = 'User';
$email = '';
$sheet_name = '';
$role = $_SESSION['role'] ?? '';
$authType = $_SESSION['auth_type'] ?? 'mongodb';

if ($authType === 'mysql') {
    $row = schogms_load_mysql_session_user($user_id);
    if (!$row || $row['role'] !== 'registrar') {
        header('Location: ../../index.php?ERROR=restricted');
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
    require_once __DIR__ . '/../../../conn_mongodb.php';

    try {
        $usersCollection = $mongodb->collection('users');
        $user = $usersCollection->findOne(['user_id' => $user_id]);

        if (!$user) {
            header('Location: ../../index.php?ERROR=session');
            exit;
        }

        if (($user['status'] ?? '') === 'restricted') {
            header('Location: ../../index.php?ERROR=restricted');
            exit;
        }

        $role = (string) ($user['role'] ?? $role);
        if ($role !== 'registrar') {
            header('Location: ../../index.php?ERROR=restricted');
            exit;
        }

        $user_id = (int) ($user['user_id'] ?? $user_id);
        $fullname = (string) ($user['name'] ?? 'User');
        $email = (string) ($user['email'] ?? '');
        $sheet_name = (string) ($user['campus'] ?? '');
        $_SESSION['username'] = $fullname;
        $_SESSION['role'] = $role;
    } catch (Throwable $e) {
        schogms_log_error('Registrar session failed: ' . $e->getMessage());
        header('Location: ../../index.php?ERROR=session');
        exit;
    }
}
