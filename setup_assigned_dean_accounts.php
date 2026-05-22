<?php
/**
 * Ensure assigned_dean rows can log in at index.php (dean name or email).
 * Resets non-bcrypt passwords to the default; sets status to active so login is allowed.
 *
 *   php setup_assigned_dean_accounts.php
 * Or once in browser (then remove or protect this file):
 *   setup_assigned_dean_accounts.php?key=schogms_dean_setup_2026
 */
$allowBrowser = (php_sapi_name() !== 'cli') && isset($_GET['key']) && $_GET['key'] === 'schogms_dean_setup_2026';
if (php_sapi_name() !== 'cli' && !$allowBrowser) {
    http_response_code(403);
    header('Content-Type: text/plain; charset=utf-8');
    echo "Forbidden. Run: php setup_assigned_dean_accounts.php\n";
    exit(1);
}

require_once __DIR__ . '/users/dean/config/conn.php';

$defaultPassword = 'schogms123';
$defaultHash = password_hash($defaultPassword, PASSWORD_DEFAULT);

$sql = "SELECT id, dean, email, password, status FROM assigned_dean";
$res = $conn->query($sql);
if (!$res) {
    die('Query failed: ' . $conn->error);
}

$report = [];
while ($row = $res->fetch_assoc()) {
    $id = (int) $row['id'];
    $pw = (string) $row['password'];
    $isBcrypt = (strlen($pw) >= 60 && (strncmp($pw, '$2y$', 4) === 0 || strncmp($pw, '$2a$', 4) === 0));
    $newPw = $isBcrypt ? $pw : $defaultHash;
    $newStatus = $row['status'];
    if ($row['status'] === 'pending') {
        $newStatus = 'active';
    }
    if (!$isBcrypt || $row['status'] === 'pending') {
        $st = $conn->prepare('UPDATE assigned_dean SET password = ?, status = ? WHERE id = ?');
        $st->bind_param('ssi', $newPw, $newStatus, $id);
        $st->execute();
        $st->close();
        $report[] = "id={$id} {$row['dean']}: " . ($isBcrypt ? 'kept hash' : 'set default password') . "; status => {$newStatus}";
    }
}

$conn->close();

header('Content-Type: text/plain; charset=utf-8');
echo "If password was reset, default is: {$defaultPassword}\n";
echo "Login URL: index.php — use dean name or email and password.\n\n";
if (count($report) === 0) {
    echo "No updates (all rows already use bcrypt and non-pending status).\n";
} else {
    echo "Updates:\n" . implode("\n", $report) . "\n";
}
