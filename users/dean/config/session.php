<?php
/**
 * Dean: MySQL `assigned_dean` (auth_type mysql_ad) or legacy session user_id.
 */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$authType = $_SESSION['auth_type'] ?? '';

if ($authType === 'mysql_ad' || isset($_SESSION['user_id'])) {
    if (empty($_SESSION['user_id'])) {
        header('Location: logout.php');
        exit;
    }
    include __DIR__ . '/conn.php';
    $aid = (int) $_SESSION['user_id'];
    $st = 'active';
    $stmt = $conn->prepare(
        'SELECT id, campus, college_name, course_program, dean, email, password, status, assigned_at
         FROM assigned_dean WHERE id = ? AND status = ? LIMIT 1'
    );
    $stmt->bind_param('is', $aid, $st);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows < 1) {
        $stmt->close();
        header('Location: logout.php');
        exit;
    }
    $id = $campusCol = $collegeCol = $cp = $deanName = $em = $pw = $stat = $at = null;
    $stmt->bind_result($id, $campusCol, $collegeCol, $cp, $deanName, $em, $pw, $stat, $at);
    if (!$stmt->fetch()) {
        $stmt->close();
        header('Location: logout.php');
        exit;
    }
    $stmt->close();
    $user_id = (int) $id;
    $campus = (string) $campusCol;
    $college_name = trim((string) $collegeCol);
    if ($college_name === '') {
        $college_name = trim((string) $cp);
    }
    $course_program = $college_name;
    $program_chair = (string) $deanName;
    $email = (string) $em;
    $status = (string) $stat;
    $assigned_at = $at;
    $_SESSION['auth_type'] = 'mysql_ad';
    $_SESSION['username'] = $program_chair;
    $_SESSION['user_email'] = $email;
    $_SESSION['campus'] = $campus;
    $_SESSION['college_name'] = $college_name;
    $_SESSION['course_program'] = $college_name;
    $_SESSION['role'] = 'dean';
    return;
}

header('Location: logout.php');
exit;
