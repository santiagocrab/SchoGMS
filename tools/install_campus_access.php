<?php
/**
 * Install campus/college/course tables and seed the access catalog.
 *
 *   php tools/install_campus_access.php
 */
$c = require __DIR__ . '/../config/schogms_mysql.php';
$conn = new mysqli($c['host'], $c['username'], $c['password'], $c['database']);
if ($conn->connect_error) {
    fwrite(STDERR, "Connection failed: {$conn->connect_error}\n");
    exit(1);
}

require_once __DIR__ . '/../inc/campus_access.php';

schogms_ensure_campus_access_tables($conn);
$inserted = schogms_seed_campus_access_catalog($conn);

$colleges = $conn->query('SELECT COUNT(*) AS n FROM schogms_colleges')->fetch_assoc()['n'] ?? 0;
$courses = $conn->query('SELECT COUNT(*) AS n FROM schogms_courses')->fetch_assoc()['n'] ?? 0;

echo "Campus access installed.\n";
echo "New rows this run: {$inserted}\n";
echo "Colleges in catalog: {$colleges}\n";
echo "Courses in catalog: {$courses}\n";

$conn->close();
