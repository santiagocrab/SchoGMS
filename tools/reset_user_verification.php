<?php
/**
 * Regenerate verification code for a pending / stuck user (CLI or local web).
 *
 * CLI:  php tools/reset_user_verification.php user@example.com
 * Web:  http://localhost/SchoGMS/tools/reset_user_verification.php?key=schogms_demo&email=user@example.com
 */
declare(strict_types=1);

$isCli = PHP_SAPI === 'cli';
if (!$isCli) {
    if (($_GET['key'] ?? '') !== 'schogms_demo') {
        http_response_code(403);
        echo 'Forbidden';
        exit;
    }
    header('Content-Type: text/plain; charset=utf-8');
}

$email = $isCli ? ($argv[1] ?? '') : trim((string) ($_GET['email'] ?? ''));
if ($email === '') {
    echo "Usage: php tools/reset_user_verification.php <email>\n";
    exit(1);
}

require_once dirname(__DIR__) . '/inc/verify_account.php';

$c = require dirname(__DIR__) . '/config/schogms_mysql.php';
$conn = new mysqli($c['host'], $c['username'], $c['password'], $c['database']);
$conn->set_charset('utf8mb4');

$result = schogms_regenerate_verification_code($conn, $email, 1440);
$conn->close();

if (!$result['success']) {
    echo ($result['message'] ?? 'Failed') . "\n";
    exit(1);
}

echo "New verification code for {$email}\n";
echo "Code:    {$result['code']}\n";
echo "Expires: {$result['expires']}\n";
echo "Verify:  " . schogms_app_base_url() . "/verify.php?email=" . rawurlencode($email) . "\n";
