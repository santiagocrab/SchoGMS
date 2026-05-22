<?php
/**
 * CLI: add one row to assigned_dean. Edit the variables below, then:
 *   php tools/add_one_assigned_dean.php
 */
$c = require __DIR__ . '/../config/schogms_mysql.php';
$conn = new mysqli($c['host'], $c['username'], $c['password'], $c['database']);
if ($conn->connect_error) {
    die('Connection failed: ' . $conn->connect_error);
}

$campus = 'ISULAN';
$courseProgram = 'Masterlist - General (demo)';
$dean = 'DeanAccount';
$email = 'dean.schogms.demo@local';
$userPassword = 'schogms123';
$status = 'active';

$check = $conn->prepare('SELECT id FROM assigned_dean WHERE email = ? OR dean = ?');
$check->bind_param('ss', $email, $dean);
$check->execute();
$check->store_result();
if ($check->num_rows > 0) {
    $check->close();
    $conn->close();
    die("Email or dean name already exists. Change \$email and \$dean in tools/add_one_assigned_dean.php\n");
}
$check->close();

$hash = password_hash($userPassword, PASSWORD_DEFAULT);
$st = $conn->prepare('INSERT INTO assigned_dean (campus, course_program, dean, email, password, status) VALUES (?, ?, ?, ?, ?, ?)');
$st->bind_param('ssssss', $campus, $courseProgram, $dean, $email, $hash, $status);
$st->execute();
echo "Inserted id={$conn->insert_id}\n";
echo "Login: index.php (email or dean name + password)\n";
echo "Username (dean column): {$dean}\n";
echo "Password: {$userPassword}\n";
$st->close();
$conn->close();
