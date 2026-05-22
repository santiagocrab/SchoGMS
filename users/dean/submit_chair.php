<?php
session_start();
require 'config/conn.php';
require __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../../config/mail.php';
require_once __DIR__ . '/../../inc/campus_access.php';

$response = ['success' => false];

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
    $course = trim($_POST['course_program_enrolled'] ?? '');
    $campus = trim($_POST['session_campus'] ?? '');
    $collegeName = trim($_POST['college_name'] ?? $_SESSION['college_name'] ?? $_SESSION['course_program'] ?? '');

    if ($campus === '' || $collegeName === '') {
        $response['message'] = 'Campus or college not set for your dean account.';
        echo json_encode($response);
        exit;
    }

    $catalogCourses = schogms_get_course_names_for_college($conn, $campus, $collegeName);
    $courseValid = false;
    foreach ($catalogCourses as $catalogCourse) {
        if ($catalogCourse === $course || schogms_course_enrolled_matches($course, $catalogCourse)) {
            $course = $catalogCourse;
            $courseValid = true;
            break;
        }
    }
    if (!$courseValid) {
        $response['message'] = 'Invalid course for this college.';
        echo json_encode($response);
        exit;
    }

    $defaultPassword = schogms_default_user_password();
    $hashedPassword = password_hash($defaultPassword, PASSWORD_DEFAULT);

    $stmt = $conn->prepare(
        'SELECT id FROM assigned_program_chairs
         WHERE UPPER(TRIM(campus)) = UPPER(TRIM(?))
           AND course_program = ?
           AND (
             (college_name IS NOT NULL AND college_name = ?)
             OR (COALESCE(college_name, "") = "")
           )
         LIMIT 1'
    );
    $stmt->bind_param('sss', $campus, $course, $collegeName);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $response['message'] = 'A program chair is already assigned for this course.';
        echo json_encode($response);
        exit;
    }
    $stmt->close();

    $query = 'INSERT INTO assigned_program_chairs (campus, college_name, course_program, program_chair, email, password, status)
              VALUES (?, ?, ?, ?, ?, ?, ?)';
    $status = 'pending';
    $stmt = $conn->prepare($query);
    $stmt->bind_param('sssssss', $campus, $collegeName, $course, $userName, $userEmail, $hashedPassword, $status);

    if ($stmt->execute()) {
        $base = schogms_app_base_url();
        $confirmUrl = $base . '/login-chairman-confirm.php?username=' . urlencode($userName)
            . '&campus=' . urlencode($campus) . '&email=' . urlencode($userEmail);

        $html = schogms_email_role_assignment([
            'role_title' => 'Program Chair Assignment',
            'name' => $userName,
            'email' => $userEmail,
            'course' => $course,
            'campus' => $campus,
            'password' => $defaultPassword,
            'confirm_url' => $confirmUrl,
        ]);
        $sent = schogms_send_mail(
            $userEmail,
            'Program Chair Assignment — SchoGMS Login Details',
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
            ? 'Program chair assigned successfully! Email sent.'
            : 'Program chair assigned but email could not be sent.';
    } else {
        logError('Error inserting program chair: ' . $conn->error);
        $response['message'] = 'Error assigning program chair: ' . $conn->error;
    }

    $stmt->close();
}

echo json_encode($response);
