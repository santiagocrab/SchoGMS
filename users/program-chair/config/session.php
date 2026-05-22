<?php
/**
 * Program chair: MySQL `assigned_program_chairs` (auth_type mysql_apc) or legacy Mongo `users`.
 */
require_once __DIR__ . '/../../../config/schogms_helpers.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$authType = $_SESSION['auth_type'] ?? '';

if ($authType === 'mysql_apc') {
    if (empty($_SESSION['apc_id'])) {
        header('Location: logout.php');
        exit;
    }
    include __DIR__ . '/conn.php';
    $aid = (int) $_SESSION['apc_id'];
    $st = 'active';
    $stmt = $conn->prepare(
        'SELECT id, campus, college_name, course_program, program_chair, email, password, status, assigned_at
         FROM assigned_program_chairs WHERE id = ? AND status = ? LIMIT 1'
    );
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
    $stmt->close();
    $user_id = (int) $id;
    $campus = (string) $campusCol;
    $college_name = trim((string) $collegeCol);
    $course_program = trim((string) $cp);
    if ($course_program === '' && $college_name !== '') {
        $course_program = $college_name;
    }
    if ($college_name === '' && $course_program !== '') {
        $college_name = $course_program;
    }
    $program_chair = (string) $pch;
    $fullname = $program_chair;
    $email = (string) $em;
    $status = (string) $stat;
    $assigned_at = $at;
    $_SESSION['auth_type'] = 'mysql_apc';
    $_SESSION['username'] = $program_chair;
    $_SESSION['user_email'] = $email;
    $_SESSION['campus'] = $campus;
    $_SESSION['college_name'] = $college_name;
    $_SESSION['course_program'] = $course_program;
    $_SESSION['role'] = 'program-chair';
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

$email = (string) ($user['email'] ?? '');
$program_chair = (string) ($user['name'] ?? 'User');
$fullname = $program_chair;
$campus = (string) ($user['campus'] ?? '');
$college_name = trim((string) ($user['college_name'] ?? ''));
$course_program = trim((string) ($user['course_program'] ?? $user['file_group'] ?? ''));
$user_id = 0;

include __DIR__ . '/conn.php';

if ($email !== '') {
    $st = 'active';
    $stmt = $conn->prepare(
        'SELECT id, campus, college_name, course_program, program_chair
         FROM assigned_program_chairs WHERE email = ? AND status = ? LIMIT 1'
    );
    $stmt->bind_param('ss', $email, $st);
    $stmt->execute();
    $apc = $stmt->get_result();
    if ($apc && $apc->num_rows > 0) {
        $r2 = $apc->fetch_assoc();
        $user_id = (int) $r2['id'];
        if ($campus === '') {
            $campus = (string) ($r2['campus'] ?? '');
        }
        if ($college_name === '') {
            $college_name = trim((string) ($r2['college_name'] ?? ''));
        }
        if ($course_program === '') {
            $course_program = trim((string) ($r2['course_program'] ?? ''));
        }
        if (!empty($r2['program_chair'])) {
            $program_chair = (string) $r2['program_chair'];
            $fullname = $program_chair;
        }
    }
    $stmt->close();
}

if ($course_program === '' && $college_name !== '') {
    $course_program = $college_name;
}
if ($college_name === '' && $course_program !== '') {
    $college_name = $course_program;
}

$_SESSION['auth_type'] = 'mongodb';
$_SESSION['username'] = $program_chair;
$_SESSION['user_email'] = $email;
$_SESSION['campus'] = $campus;
$_SESSION['college_name'] = $college_name;
$_SESSION['course_program'] = $course_program;
$_SESSION['role'] = $role;
