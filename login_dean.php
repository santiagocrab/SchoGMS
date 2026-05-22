<?php
/**
 * Legacy dean login endpoint — forwards to the main login (index.php / login.php).
 */
session_start();
require_once __DIR__ . '/config/mysql_auth.php';

$locationPrefix = '';
$base = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '/')), '/');
if ($base !== '' && $base !== '.') {
    $locationPrefix = $base . '/';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');
    if (schogms_dean_mysql_login($username, $password, $locationPrefix)) {
        exit;
    }
    header('Location: ' . $locationPrefix . 'index.php?ERROR=1');
    exit;
}

header('Location: ' . $locationPrefix . 'index.php', true, 301);
exit;
