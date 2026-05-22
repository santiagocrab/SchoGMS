<?php
/**
 * Activate a pending chairman (or by email) when verification email was not received.
 *
 *   php tools/activate_chairman.php
 *   php tools/activate_chairman.php james.remegio@wvsu.edu.ph
 */
$c = require __DIR__ . '/../config/schogms_mysql.php';
$conn = new mysqli($c['host'], $c['username'], $c['password'], $c['database']);
if ($conn->connect_error) {
    fwrite(STDERR, $conn->connect_error . "\n");
    exit(1);
}

$email = $argv[1] ?? null;
if ($email) {
    $stmt = $conn->prepare(
        "UPDATE users SET status = 'active', email_verified = 1, verification_code = NULL, verification_expires = NULL
         WHERE role = 'chairman' AND LOWER(TRIM(email)) = LOWER(TRIM(?))"
    );
    $stmt->bind_param('s', $email);
} else {
    $stmt = $conn->prepare(
        "UPDATE users SET status = 'active', email_verified = 1, verification_code = NULL, verification_expires = NULL
         WHERE role = 'chairman' ORDER BY user_id DESC LIMIT 1"
    );
}

$stmt->execute();
echo $stmt->affected_rows > 0
    ? "Chairman activated. They can log in at /index.php with their email and default password.\n"
    : "No chairman row updated.\n";
$stmt->close();
$conn->close();
