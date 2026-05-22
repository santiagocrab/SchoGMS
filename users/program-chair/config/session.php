<?php
/**
 * Program chair: supports Mongo `users` (auth_type mongodb) and MySQL `assigned_program_chairs` (auth_type mysql_apc).
 * Login for MySQL-only chairs is handled in /login.php after Mongo lookup fails.
 */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$authType = $_SESSION['auth_type'] ?? 'mongodb';

if ($authType === 'mysql_apc') {
    if (empty($_SESSION['apc_id'])) {
        header('Location: logout.php');
        exit;
    }
    include __DIR__ . '/conn.php';
    $aid = (int) $_SESSION['apc_id'];
    $st = 'active';
    $stmt = $conn->prepare('SELECT id, campus, college_name, course_program, program_chair, email, password, status, assigned_at FROM assigned_program_chairs WHERE id = ? AND status = ? LIMIT 1');
    $stmt->bind_param('is', $aid, $st);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows < 1) {
        $stmt->close();
        header('Location: logout.php');
        exit;
    }
    $id = $campusCol = $collegeCol = $cp = $pch = $em = $pw = $stat = $at = null;
    $stmt->bind_result($id, $campusCol, $collegeCol, $cp, $pch, $em, $pw, $stat, $at);
    if (!$stmt->fetch()) {
        $stmt->close();
        header('Location: logout.php');
        exit;
    }
    $row = [
        'id' => $id,
        'campus' => $campusCol,
        'course_program' => $cp,
        'program_chair' => $pch,
        'email' => $em,
        'password' => $pw,
        'status' => $stat,
        'assigned_at' => $at,
    ];
    $stmt->close();
    $user_id = (int) $row['id'];
    $email = (string) $row['email'];
    $program_chair = (string) $row['program_chair'];
    $campus = (string) $row['campus'];
    $course_program = (string) $row['course_program'];
    $_SESSION['campus'] = $campus;
    $_SESSION['college_name'] = (string) ($row['college_name'] ?? '');
    $_SESSION['course_program'] = $course_program;
    return;
}

if (!isset($_SESSION['user_id'])) {
    header('Location: logout.php');
    exit;
}

require_once __DIR__ . '/../../../conn_mongodb.php';

$userId = (int) $_SESSION['user_id'];
$user = $users->findOne(['user_id' => $userId]);

if (!$user) {
    header('Location: logout.php');
    exit;
}

$role = $user['role'] ?? '';
if (!in_array($role, ['program-chair', 'program-head'], true)) {
    header('Location: ../../index.php?ERROR=restricted');
    exit;
}

$email = $user['email'] ?? '';
$program_chair = $user['name'] ?? 'User';
$campus = $user['campus'] ?? '';
$course_program = $user['course_program'] ?? $user['file_group'] ?? '';
$user_id = 0;

if ($email !== '') {
    include __DIR__ . '/conn.php';
    $st = 'active';
    $stmt = $conn->prepare(
        'SELECT id, campus, course_program, program_chair FROM assigned_program_chairs WHERE email = ? AND status = ? LIMIT 1'
    );
    $stmt->bind_param('ss', $email, $st);
    $stmt->execute();
    $apc = $stmt->get_result();
    if ($apc && $apc->num_rows > 0) {
        $r2 = $apc->fetch_assoc();
        $user_id = (int) $r2['id'];
        if ($campus === '') {
            $campus = (string) $r2['campus'];
        }
        if ($course_program === '') {
            $course_program = (string) $r2['course_program'];
        }
        if (!empty($r2['program_chair'])) {
            $program_chair = (string) $r2['program_chair'];
        }
    }
    $stmt->close();
}

$_SESSION['campus'] = $campus;
$_SESSION['course_program'] = $course_program;
