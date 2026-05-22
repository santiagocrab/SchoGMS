<?php
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../config/session.php';
require __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../../config/mail.php';
require_once __DIR__ . '/../../inc/campus_access.php';

$response = ['success' => false];

if (($role ?? '') !== 'director') {
    $response['message'] = 'Access denied.';
    echo json_encode($response);
    exit;
}

error_reporting(E_ALL);
ini_set('log_errors', '1');
ini_set('error_log', __DIR__ . '/error_log.txt');

function logError($message)
{
    error_log('[' . date('Y-m-d H:i:s') . '] ' . $message . PHP_EOL, 3, __DIR__ . '/error_log.txt');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userName = trim($_POST['program_chair_name'] ?? '');
    $userEmail = trim($_POST['usermail'] ?? '');
    $collegeName = trim($_POST['college_name'] ?? '');
    $campus = trim((string) ($sheet_name ?? ''));

    if ($campus === '') {
        $response['message'] = 'Your account has no campus. Contact the coordinator.';
        echo json_encode($response);
        exit;
    }

    $postedCampus = trim($_POST['session_campus'] ?? '');
    if ($postedCampus !== '' && strcasecmp($postedCampus, $campus) !== 0) {
        $response['message'] = 'Campus mismatch. Assignment must use your assigned campus only.';
        echo json_encode($response);
        exit;
    }

    if ($collegeName === '') {
        $response['message'] = 'Please select a college.';
        echo json_encode($response);
        exit;
    }

    $colleges = schogms_get_colleges_for_campus($conn, $campus);
    $collegeValid = false;
    foreach ($colleges as $col) {
        if ((string) $col['college_name'] === $collegeName) {
            $collegeValid = true;
            break;
        }
    }
    if (!$collegeValid) {
        $response['message'] = 'Invalid college for this campus.';
        echo json_encode($response);
        exit;
    }

    $defaultPassword = schogms_default_user_password();
    $hashedPassword = password_hash($defaultPassword, PASSWORD_DEFAULT);

    $stmt = $conn->prepare(
        'SELECT id FROM assigned_dean
         WHERE UPPER(TRIM(campus)) = UPPER(TRIM(?))
           AND (
             (college_name IS NOT NULL AND college_name = ?)
             OR (COALESCE(college_name, "") = "" AND course_program = ?)
           )
         LIMIT 1'
    );
    $stmt->bind_param('sss', $campus, $collegeName, $collegeName);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $response['message'] = 'A dean is already assigned for this college.';
        echo json_encode($response);
        exit;
    }
    $stmt->close();

    $legacyCourse = $collegeName;
    $query = 'INSERT INTO assigned_dean (campus, college_name, course_program, dean, email, password, status)
              VALUES (?, ?, ?, ?, ?, ?, ?)';
    $status = 'pending';
    $stmt = $conn->prepare($query);
    $stmt->bind_param('sssssss', $campus, $collegeName, $legacyCourse, $userName, $userEmail, $hashedPassword, $status);

    if ($stmt->execute()) {
        $base = schogms_app_base_url();
        $confirmUrl = $base . '/login-dean-confirm.php?username=' . urlencode($userName)
            . '&campus=' . urlencode($campus) . '&email=' . urlencode($userEmail);

        $html = schogms_email_role_assignment([
            'role_title' => 'Dean Assignment',
            'name' => $userName,
            'email' => $userEmail,
            'course' => $collegeName,
            'campus' => $campus,
            'password' => $defaultPassword,
            'confirm_url' => $confirmUrl,
        ]);
        $sent = schogms_send_mail(
            $userEmail,
            'Dean Assignment — SchoGMS Login Details',
            $html,
            $userName,
            'SchoGMS'
        );
        $response['email_sent'] = $sent['ok'];
        if (!$sent['ok']) {
            $response['error'] = $sent['error'] ?? 'Mail send failed';
        }
        $response['success'] = true;
        $response['message'] = $sent['ok']
            ? 'Dean assigned successfully! Email with login details sent.'
            : 'Dean assigned but email could not be sent.';
    } else {
        logError('Error inserting dean: ' . $conn->error);
        $response['message'] = 'Error assigning dean: ' . $conn->error;
    }

    $stmt->close();
}

echo json_encode($response);
