<?php
/**
 * CLI: add one row to assigned_program_chairs (used by main index.php + login.php MySQL bridge).
 * Edit the variables below, then:
 *   php tools/add_one_program_chair.php
 */
$c = require __DIR__ . '/../config/schogms_mysql.php';
$conn = new mysqli($c['host'], $c['username'], $c['password'], $c['database']);
if ($conn->connect_error) {
    die('Connection failed: ' . $conn->connect_error);
}

$campus = 'ISULAN';
$courseProgram = 'Bachelor of Science in Information Technology (demo)';
$programChair = 'ProgramChairDemo';
$email = 'programchair.schogms.demo@local';
$userPassword = 'schogms123';
$status = 'active';

$check = $conn->prepare('SELECT id FROM assigned_program_chairs WHERE email = ? OR program_chair = ?');
$check->bind_param('ss', $email, $programChair);
$check->execute();
$check->store_result();
if ($check->num_rows > 0) {
    $check->close();
    $conn->close();
    die("Email or program_chair name already exists. Change \$email and \$programChair in tools/add_one_program_chair.php\n");
}
$check->close();

$hash = password_hash($userPassword, PASSWORD_DEFAULT);
$st = $conn->prepare(
    'INSERT INTO assigned_program_chairs (campus, course_program, program_chair, email, password, status) VALUES (?, ?, ?, ?, ?, ?)'
);
$st->bind_param('ssssss', $campus, $courseProgram, $programChair, $email, $hash, $status);
$st->execute();
echo "Inserted id={$conn->insert_id}\n";
echo "Login: index.php  (or http://localhost/SchoGMS/index.php)\n";
echo "Username: type the program chair name OR this email\n";
echo "  program_chair: {$programChair}\n";
echo "  email: {$email}\n";
echo "Password: {$userPassword}\n";
$st->close();
$conn->close();
