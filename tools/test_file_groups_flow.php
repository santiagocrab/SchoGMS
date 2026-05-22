<?php
/**
 * CLI test: coordinator-style upload → chairman file groups (pending → approve).
 *
 *   /Applications/XAMPP/xamppfiles/bin/php tools/test_file_groups_flow.php
 *   /Applications/XAMPP/xamppfiles/bin/php tools/test_file_groups_flow.php --keep
 */
declare(strict_types=1);

$keep = in_array('--keep', $argv ?? [], true);
$cfg = require dirname(__DIR__) . '/config/schogms_mysql.php';
require_once dirname(__DIR__) . '/inc/schogms_file_group_meta.php';

$conn = new mysqli($cfg['host'], $cfg['username'], $cfg['password'], $cfg['database']);
if ($conn->connect_error) {
    fwrite(STDERR, "DB connect failed: {$conn->connect_error}\n");
    exit(1);
}
$conn->set_charset('utf8mb4');

$campus = 'ACCESS';
$fileGroup = 'FG-TEST ' . date('Y-m-d H:i:s');
$filename = 'fg_test_' . time() . '.xlsx';
$coordinatorName = 'Coordinator (test script)';
$coordinatorRole = 'coordinator';
$chairmanName = 'Chairman (test script)';

function ok(string $msg): void
{
    echo "[OK] {$msg}\n";
}

function fail(string $msg): int
{
    fwrite(STDERR, "[FAIL] {$msg}\n");
    return 1;
}

echo "=== File groups workflow test ===\n";
echo "Campus: {$campus}\n";
echo "File group: {$fileGroup}\n\n";

schogms_file_group_meta_ensure_table($conn);

$stmt = $conn->prepare(
    'INSERT INTO ched_masterlist (
        sheet_name, seq, app_no, award_no, lastname, firstname, extname, middlename,
        sex, birthdate, course_program_enrolled, year_level,
        total_units_enrolled, status_of_enrollment, remarks,
        filename, file_group
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'
);
if (!$stmt) {
    exit(fail('Prepare insert: ' . $conn->error));
}

$rows = [
    ['1', 'APP-TEST-1', 'AWD-1', 'DELA CRUZ', 'JUAN', '', 'SANTOS', 'M', '2000-01-01', 'BSIT', '1', '18', 'ENROLLED', '', $filename, $fileGroup],
    ['2', 'APP-TEST-2', 'AWD-2', 'REYES', 'MARIA', '', 'GARCIA', 'F', '2001-02-02', 'BSED', '2', '21', 'ENROLLED', '', $filename, $fileGroup],
];

$conn->begin_transaction();
try {
    foreach ($rows as $r) {
        $stmt->bind_param(
            'sssssssssssssssss',
            $campus,
            $r[0],
            $r[1],
            $r[2],
            $r[3],
            $r[4],
            $r[5],
            $r[6],
            $r[7],
            $r[8],
            $r[9],
            $r[10],
            $r[11],
            $r[12],
            $r[13],
            $r[14],
            $r[15]
        );
        if (!$stmt->execute()) {
            throw new RuntimeException($stmt->error);
        }
    }
    $conn->commit();
    ok('Inserted ' . count($rows) . ' TDP masterlist row(s)');
} catch (Throwable $e) {
    $conn->rollback();
    exit(fail('Insert: ' . $e->getMessage()));
}
$stmt->close();

schogms_file_group_meta_register($conn, 'tdp', $campus, $fileGroup, 'pending', [
    'role' => $coordinatorRole,
    'name' => $coordinatorName,
    'id' => 10,
]);
ok('Registered file group as pending with uploader');

$list = schogms_file_group_meta_list($conn, 'tdp', 'pending');
$found = null;
foreach ($list['rows'] as $row) {
    if (($row['file_group'] ?? '') === $fileGroup && strcasecmp((string) ($row['campus'] ?? ''), $campus) === 0) {
        $found = $row;
        break;
    }
}
if ($found === null) {
    exit(fail('Batch not found on Pending tab list'));
}
ok('Visible on chairman Pending list');
if (($found['uploaded_by_name'] ?? '') !== $coordinatorName) {
    exit(fail('Uploader name mismatch: ' . ($found['uploaded_by_name'] ?? '(empty)')));
}
ok('Uploader name: ' . schogms_file_group_meta_uploader_display($found));

$approved = schogms_file_group_meta_set_status($conn, 'tdp', $campus, $fileGroup, 'approved', $chairmanName, 'Approved by automated test');
if (!$approved) {
    exit(fail('Approve failed'));
}
ok('Chairman approved batch');

$listApproved = schogms_file_group_meta_list($conn, 'tdp', 'approved');
$onApproved = false;
foreach ($listApproved['rows'] as $row) {
    if (($row['file_group'] ?? '') === $fileGroup) {
        $onApproved = true;
        break;
    }
}
if (!$onApproved) {
    exit(fail('Not found on Approved tab after approve'));
}
ok('Visible on Approved tab');

if (!$keep) {
    $deleted = schogms_file_group_meta_delete($conn, 'tdp', $campus, $fileGroup);
    ok("Cleanup: deleted {$deleted} masterlist row(s) + meta");
} else {
    echo "\n--keep: left data in DB for manual UI check.\n";
    echo "Chairman: file_groups.php?program=tdp&status=approved\n";
    echo "Search file group: {$fileGroup}\n";
}

echo "\n=== All automated checks passed ===\n";
echo "Manual UI test:\n";
echo "  1. Log in as coordinator (e.g. test@mail / password123)\n";
echo "  2. Upload TDP masterlist with a new file group name\n";
echo "  3. Log in as chairman → Review → File groups → Pending\n";
echo "  4. Approve and confirm uploader column shows coordinator name\n";
exit(0);
