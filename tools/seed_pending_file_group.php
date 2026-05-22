<?php
/**
 * Insert one pending TDP file group for UI testing (chairman File groups → Pending).
 * CLI: php tools/seed_pending_file_group.php
 */
declare(strict_types=1);

$cfg = require dirname(__DIR__) . '/config/schogms_mysql.php';
require_once dirname(__DIR__) . '/inc/schogms_file_group_meta.php';

$conn = new mysqli($cfg['host'], $cfg['username'], $cfg['password'], $cfg['database']);
$conn->set_charset('utf8mb4');

$campus = 'ACCESS';
$fileGroup = 'FG-UI-TEST ' . date('Y-m-d H:i:s');
$filename = 'ui_test_seed.xlsx';

$seq = '1';
$appNo = 'APP-UI-1';
$awardNo = 'AWD-UI-1';
$lastname = 'TESTUSER';
$firstname = 'DEMO';
$extname = '';
$middlename = 'SAMPLE';
$sex = 'M';
$birthdate = '2000-01-01';
$course = 'BSIT';
$yearLevel = '1';
$units = '18';
$statusEnroll = 'ENROLLED';
$remarks = '';

$stmt = $conn->prepare(
    'INSERT INTO ched_masterlist (
        sheet_name, seq, app_no, award_no, lastname, firstname, extname, middlename,
        sex, birthdate, course_program_enrolled, year_level,
        total_units_enrolled, status_of_enrollment, remarks,
        filename, file_group
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'
);
$stmt->bind_param(
    'sssssssssssssssss',
    $campus,
    $seq,
    $appNo,
    $awardNo,
    $lastname,
    $firstname,
    $extname,
    $middlename,
    $sex,
    $birthdate,
    $course,
    $yearLevel,
    $units,
    $statusEnroll,
    $remarks,
    $filename,
    $fileGroup
);
$stmt->execute();
$stmt->close();

schogms_file_group_meta_register($conn, 'tdp', $campus, $fileGroup, 'pending', [
    'role' => 'coordinator',
    'name' => 'Coordinator',
    'id' => 10,
]);

echo "Created pending file group:\n";
echo "  Campus: {$campus}\n";
echo "  Name:   {$fileGroup}\n";
echo "  Uploader: Coordinator · Coordinator\n\n";
echo "Chairman UI:\n";
echo "  http://localhost/SchoGMS/users/chairman/file_groups.php?program=tdp&status=pending\n";
