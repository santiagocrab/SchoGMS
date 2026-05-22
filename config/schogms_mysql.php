<?php
/**
 * Single place for MySQL connection settings (schogms database).
 */
$cfg = [
    'host'     => 'localhost',
    'username' => 'root',
    'password' => '',
    'database' => 'schogms',
];

$local = __DIR__ . '/schogms_mysql.local.php';
if (is_readable($local)) {
    $override = require $local;
    if (is_array($override)) {
        $cfg = array_merge($cfg, $override);
    }
}

return $cfg;
