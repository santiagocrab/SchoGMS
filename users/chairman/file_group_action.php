<?php
/**
 * Chairman: approve / deny / rename / delete file group batches.
 */
include 'config/session.php';
require_once __DIR__ . '/../../inc/schogms_file_group_meta.php';

if (($role ?? '') !== 'chairman') {
    header('Location: file_groups.php?error=access_denied');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: file_groups.php');
    exit;
}

$token = (string) ($_POST['csrf'] ?? '');
if ($token === '' || !hash_equals((string) ($_SESSION['fg_csrf'] ?? ''), $token)) {
    header('Location: file_groups.php?error=invalid_token');
    exit;
}

$action = strtolower(trim((string) ($_POST['action'] ?? '')));
$program = strtolower(trim((string) ($_POST['program'] ?? 'tdp'))) === 'tes' ? 'tes' : 'tdp';
$campus = trim((string) ($_POST['campus'] ?? ''));
$fileGroup = trim((string) ($_POST['file_group'] ?? ''));
$notes = trim((string) ($_POST['notes'] ?? ''));
$returnProgram = $program;
$returnStatus = trim((string) ($_POST['return_status'] ?? 'all'));
if (!in_array($returnStatus, ['all', 'pending', 'approved', 'denied'], true)) {
    $returnStatus = 'all';
}

$redirect = 'file_groups.php?program=' . rawurlencode($returnProgram) . '&status=' . rawurlencode($returnStatus);

if ($campus === '' || $fileGroup === '') {
    header('Location: ' . $redirect . '&error=missing_fields');
    exit;
}

$reviewer = trim((string) ($fullname ?? $_SESSION['username'] ?? 'Chairman'));

if (!isset($conn) || !($conn instanceof mysqli)) {
    require 'config/conn.php';
}

$ok = false;
$msg = '';

switch ($action) {
    case 'approve':
        $ok = schogms_file_group_meta_set_status($conn, $program, $campus, $fileGroup, 'approved', $reviewer, $notes);
        $msg = $ok ? 'File group approved.' : 'Could not approve file group.';
        break;

    case 'deny':
        $ok = schogms_file_group_meta_set_status($conn, $program, $campus, $fileGroup, 'denied', $reviewer, $notes);
        $msg = $ok ? 'File group denied.' : 'Could not deny file group.';
        break;

    case 'pending':
        $ok = schogms_file_group_meta_set_status($conn, $program, $campus, $fileGroup, 'pending', $reviewer, $notes);
        $msg = $ok ? 'File group marked as pending.' : 'Could not update status.';
        break;

    case 'rename':
        $newName = trim((string) ($_POST['new_file_group'] ?? ''));
        if ($newName === '') {
            header('Location: ' . $redirect . '&error=missing_new_name');
            exit;
        }
        $ok = schogms_file_group_meta_rename($conn, $program, $campus, $fileGroup, $newName);
        $msg = $ok ? 'File group renamed.' : 'Could not rename file group.';
        break;

    case 'delete':
        $deleted = schogms_file_group_meta_delete($conn, $program, $campus, $fileGroup);
        $ok = $deleted > 0;
        $msg = $ok
            ? "Deleted file group and {$deleted} scholar record(s)."
            : 'No records deleted (file group may already be empty).';
        break;

    default:
        header('Location: ' . $redirect . '&error=unknown_action');
        exit;
}

header('Location: ' . $redirect . ($ok ? '&success=' . rawurlencode($msg) : '&error=' . rawurlencode($msg)));
exit;
