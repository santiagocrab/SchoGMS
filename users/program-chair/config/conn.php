<?php
$c = require __DIR__ . '/../../../config/schogms_mysql.php';
$conn = new mysqli(
    $c['host'],
    $c['username'],
    $c['password'],
    $c['database']
);

if ($conn->connect_error) {
    die('Connection failed: ' . $conn->connect_error);
}
