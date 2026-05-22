<?php
/**
 * MySQL `users` table authentication (admin-created accounts, email verification).
 */
require_once __DIR__ . '/schogms_helpers.php';

if (!function_exists('schogms_mysql_users_login')) {
    /**
     * Authenticate against MySQL users table. Sets session and redirects on success.
     */
    function schogms_mysql_users_login(string $username, string $password, string $locationPrefix): bool
    {
        if ($username === '' || $password === '') {
            return false;
        }

        $c = require __DIR__ . '/schogms_mysql.php';
        $dbc = new mysqli($c['host'], $c['username'], $c['password'], $c['database']);
        if ($dbc->connect_error) {
            schogms_log_error('MySQL login connection failed: ' . $dbc->connect_error);
            return false;
        }

        $stmt = $dbc->prepare(
            'SELECT user_id, name, email, role, campus, password, status, email_verified
             FROM users
             WHERE LOWER(TRIM(email)) = LOWER(TRIM(?))
                OR LOWER(TRIM(name)) = LOWER(TRIM(?))
             LIMIT 1'
        );
        if (!$stmt) {
            $dbc->close();
            return false;
        }

        $stmt->bind_param('ss', $username, $username);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result ? $result->fetch_assoc() : null;
        $stmt->close();

        if (!$row) {
            $dbc->close();
            return false;
        }

        $hash = (string) ($row['password'] ?? '');
        $ok = $hash !== '' && password_verify($password, $hash);
        if (!$ok && $hash !== '' && strlen($hash) < 60) {
            $ok = hash_equals($hash, $password);
        }
        if (!$ok) {
            $dbc->close();
            return false;
        }

        $status = (string) ($row['status'] ?? '');
        $role = (string) ($row['role'] ?? '');
        $emailVerified = (int) ($row['email_verified'] ?? 0);

        if ($status === 'restricted') {
            header('Location: ' . $locationPrefix . 'index.php?ERROR=restricted');
            $dbc->close();
            exit;
        }
        if ($status === 'pending') {
            header('Location: ' . $locationPrefix . 'index.php?ERROR=pending');
            $dbc->close();
            exit;
        }
        if ($status !== 'active') {
            header('Location: ' . $locationPrefix . 'index.php?ERROR=inactive');
            $dbc->close();
            exit;
        }
        if ($emailVerified !== 1 && $role !== 'coordinator') {
            header('Location: ' . $locationPrefix . 'verify.php?email=' . rawurlencode((string) $row['email']));
            $dbc->close();
            exit;
        }

        $_SESSION['auth_type'] = 'mysql';
        $_SESSION['user_id'] = (int) $row['user_id'];
        $_SESSION['username'] = (string) $row['name'];
        $_SESSION['role'] = $role;
        unset($_SESSION['apc_id']);

        $dbc->close();

        $home = schogms_role_home($role);
        header('Location: ' . $locationPrefix . $home);
        return true;
    }
}

if (!function_exists('schogms_load_mysql_session_user')) {
    /**
     * Load $fullname, $email, $sheet_name, $role from MySQL for protected pages.
     */
    function schogms_load_mysql_session_user(int $userId): ?array
    {
        $c = require __DIR__ . '/schogms_mysql.php';
        $dbc = new mysqli($c['host'], $c['username'], $c['password'], $c['database']);
        if ($dbc->connect_error) {
            return null;
        }
        $stmt = $dbc->prepare(
            'SELECT user_id, name, email, role, campus, status FROM users WHERE user_id = ? LIMIT 1'
        );
        $stmt->bind_param('i', $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result ? $result->fetch_assoc() : null;
        $stmt->close();
        $dbc->close();

        if (!$row || ($row['status'] ?? '') !== 'active') {
            return null;
        }

        return [
            'user_id' => (int) $row['user_id'],
            'fullname' => (string) $row['name'],
            'email' => (string) $row['email'],
            'role' => (string) $row['role'],
            'sheet_name' => (string) ($row['campus'] ?? ''),
        ];
    }
}

if (!function_exists('schogms_dean_mysql_login')) {
    /**
     * Authenticate against MySQL `assigned_dean` (director-assigned deans).
     */
    function schogms_dean_mysql_login(string $username, string $password, string $locationPrefix): bool
    {
        if ($username === '' || $password === '') {
            return false;
        }

        $c = require __DIR__ . '/schogms_mysql.php';
        $dbc = new mysqli($c['host'], $c['username'], $c['password'], $c['database']);
        if ($dbc->connect_error) {
            schogms_log_error('Dean login connection failed: ' . $dbc->connect_error);
            return false;
        }

        $stmt = $dbc->prepare(
            'SELECT id, dean, email, password, status, college_name, course_program, campus
             FROM assigned_dean
             WHERE LOWER(TRIM(email)) = LOWER(TRIM(?))
                OR LOWER(TRIM(dean)) = LOWER(TRIM(?))
             LIMIT 1'
        );
        if (!$stmt) {
            $dbc->close();
            return false;
        }

        $stmt->bind_param('ss', $username, $username);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result ? $result->fetch_assoc() : null;
        $stmt->close();

        if (!$row) {
            $dbc->close();
            return false;
        }

        $hash = (string) ($row['password'] ?? '');
        $ok = $hash !== '' && password_verify($password, $hash);
        if (!$ok && $hash !== '' && strlen($hash) < 60) {
            $ok = hash_equals($hash, $password);
        }
        if (!$ok) {
            $dbc->close();
            return false;
        }

        $status = (string) ($row['status'] ?? '');
        if ($status === 'pending') {
            header('Location: ' . $locationPrefix . 'index.php?ERROR=pending');
            $dbc->close();
            exit;
        }
        if ($status === 'restricted') {
            header('Location: ' . $locationPrefix . 'index.php?ERROR=restricted');
            $dbc->close();
            exit;
        }
        if ($status !== 'active') {
            header('Location: ' . $locationPrefix . 'index.php?ERROR=inactive');
            $dbc->close();
            exit;
        }

        $_SESSION['auth_type'] = 'mysql_ad';
        $_SESSION['user_id'] = (int) $row['id'];
        $_SESSION['username'] = (string) $row['dean'];
        $_SESSION['user_email'] = (string) $row['email'];
        $college = trim((string) ($row['college_name'] ?? ''));
        if ($college === '') {
            $college = trim((string) ($row['course_program'] ?? ''));
        }
        $_SESSION['college_name'] = $college;
        $_SESSION['course_program'] = $college;
        $_SESSION['campus'] = (string) $row['campus'];
        $_SESSION['role'] = 'dean';
        unset($_SESSION['apc_id']);

        $dbc->close();
        header('Location: ' . $locationPrefix . 'users/dean/');
        return true;
    }
}
