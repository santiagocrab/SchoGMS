<?php
session_start();
require_once __DIR__ . '/config/mysql_auth.php';
// Include MongoDB database connection
include 'conn_mongodb.php';

// Clear MongoDB cache to ensure fresh data
if (method_exists($users, 'clearCache')) {
    $users->clearCache();
}

/**
 * Log in as program chair from MySQL `assigned_program_chairs`.
 * Uses a dedicated mysqli (never the Mongo $conn from conn_mongodb.php, which is MongoDBi and would fatal).
 * On success, sets session and returns true (caller should exit).
 */
function schogms_program_chair_mysql_login(string $username, string $password, string $locationPrefix): bool
{
    if ($username === '') {
        return false;
    }
    // Real mysqli; credentials from config/schogms_mysql.php (keep in sync with program-chair/config/conn.php).
    $c = require __DIR__ . '/config/schogms_mysql.php';
    $dbc = new mysqli(
        $c['host'],
        $c['username'],
        $c['password'],
        $c['database']
    );
    if ($dbc->connect_error) {
        error_log('schogms_program_chair_mysql_login: ' . $dbc->connect_error);
        return false;
    }
    $safe = $dbc->real_escape_string($username);
    if ($safe === '') {
        return false;
    }
    $sql = "SELECT id, program_chair, email, password 
            FROM assigned_program_chairs 
            WHERE status = 'active' 
              AND (LOWER(TRIM(email)) = LOWER('{$safe}') OR LOWER(TRIM(program_chair)) = LOWER('{$safe}')) 
            LIMIT 1";
    $res = $dbc->query($sql);
    if (!$res || $res->num_rows < 1) {
        return false;
    }
    $row = $res->fetch_assoc();
    if (!$row) {
        return false;
    }
    $hash = (string) ($row['password'] ?? '');
    $ok = $hash !== '' && password_verify($password, $hash);
    if (!$ok && $hash !== '' && strlen($hash) < 60) {
        $ok = hash_equals($hash, $password);
    }
    if (!$ok) {
        return false;
    }
    // session_regenerate_id(true) can drop the new session in some XAMPP/browser setups; avoid here
    $_SESSION['auth_type'] = 'mysql_apc';
    $_SESSION['apc_id'] = (int) $row['id'];
    unset($_SESSION['user_id']);
    $_SESSION['username'] = (string) $row['program_chair'];
    $_SESSION['role'] = 'program-chair';
    header('Location: ' . $locationPrefix . 'users/program-chair/');
    return true;
}

// Check if request method is POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    $locationPrefix = '';
    $base = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '/')), '/');
    if ($base !== '' && $base !== '.') {
        $locationPrefix = $base . '/';
    }

    // Prefer MySQL accounts (admin-created users, deans, program chairs) before legacy MongoDB JSON.
    if (schogms_mysql_users_login($username, $password, $locationPrefix)) {
        exit();
    }
    if (schogms_dean_mysql_login($username, $password, $locationPrefix)) {
        exit();
    }
    if (schogms_program_chair_mysql_login($username, $password, $locationPrefix)) {
        exit();
    }

    // Legacy MongoDB users (coordinator, chairman, etc.)
    $user = $users->findOne(['name' => $username]);

    if (!$user) {
        $allUsers = $users->find([]);
        foreach ($allUsers as $u) {
            if (isset($u['name']) && strcasecmp(trim($u['name']), $username) === 0) {
                $user = $u;
                break;
            }
        }
    }

    if (!$user) {
        $user = $users->findOne(['email' => $username]);
    }
    if (!$user) {
        $allUsers = $users->find([]);
        foreach ($allUsers as $u) {
            if (isset($u['email']) && strcasecmp(trim((string) $u['email']), $username) === 0) {
                $user = $u;
                break;
            }
        }
    }

    if ($user) {
        $user_id = $user['user_id'];
        $db_username = $user['name'];
        $db_password = $user['password'];
        $db_role = $user['role'];
        $db_status = $user['status'];

        if ($db_role !== 'coordinator') {
            if ($db_status === 'restricted') {
                header('Location: ' . $locationPrefix . 'index.php?ERROR=restricted');
                exit();
            } elseif ($db_status === 'pending') {
                header('Location: ' . $locationPrefix . 'index.php?ERROR=pending');
                exit();
            } elseif ($db_status !== 'active') {
                header('Location: ' . $locationPrefix . 'index.php?ERROR=inactive');
                exit();
            }
        }

        if (empty($db_password)) {
            if (schogms_dean_mysql_login($username, $password, $locationPrefix)) {
                exit();
            }
            if (schogms_program_chair_mysql_login($username, $password, $locationPrefix)) {
                exit();
            }
            header('Location: ' . $locationPrefix . 'index.php?ERROR=1&msg=nopassword');
            exit();
        }

        if (password_verify($password, $db_password)) {
            $_SESSION['user_id'] = $user_id;
            $_SESSION['username'] = $db_username;
            $_SESSION['role'] = $db_role;
            $_SESSION['auth_type'] = 'mongodb';
            unset($_SESSION['apc_id']);

            switch ($db_role) {
                case 'coordinator':
                    header("Location: {$locationPrefix}users/coordinator/");
                    break;
                case 'chairman':
                    header("Location: {$locationPrefix}users/chairman/");
                    break;
                case 'registrar':
                    header("Location: {$locationPrefix}users/registrar/");
                    break;
                case 'program-head':
                case 'program-chair':
                    header("Location: {$locationPrefix}users/program-chair/");
                    break;
                case 'director':
                    header("Location: {$locationPrefix}users/director/");
                    break;
                case 'dean':
                    header("Location: {$locationPrefix}users/dean/");
                    break;
                default:
                    header('Location: ' . $locationPrefix . 'index.php?ERROR=1');
                    break;
            }
            exit();
        }

        error_log("Login failed for user: $username - Password verification failed (Mongo user, wrong password)");
        header('Location: ' . $locationPrefix . 'index.php?ERROR=1&msg=wrongpassword');
        exit();
    }

    error_log("Login failed: Username '$username' not found in database");
    header('Location: ' . $locationPrefix . 'index.php?ERROR=1&msg=usernotfound');
    exit();
}
