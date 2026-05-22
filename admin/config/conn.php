<?php
$c = require __DIR__ . '/../../config/schogms_mysql.php';

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    $conn = new mysqli($c['host'], $c['username'], $c['password'], $c['database']);
    $conn->set_charset('utf8mb4');
} catch (mysqli_sql_exception $e) {
    error_log('SchoGMS MySQL connection failed: ' . $e->getMessage());
    http_response_code(500);
    die('Database connection failed. Check config/schogms_mysql.local.php (MySQL username/password).');
}

date_default_timezone_set('Asia/Manila');
