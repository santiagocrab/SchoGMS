<?php
/**
 * FIX NOW - Simple direct fix
 */
$jsonFile = __DIR__ . '/mongodb_data/schogms/users.json';

// Create backup
$backup = $jsonFile . '.backup.' . date('YmdHis');
if (file_exists($jsonFile)) {
    copy($jsonFile, $backup);
}

// Read
$users = json_decode(file_get_contents($jsonFile), true);
$newHash = password_hash('password123', PASSWORD_DEFAULT);

// Verify the hash works
if (!password_verify('password123', $newHash)) {
    die("Error: Generated hash doesn't work!");
}

$processed = [];
$fixed = [];

foreach ($users as $user) {
    $id = $user['user_id'] ?? null;
    if ($id === null || isset($processed[$id])) continue;
    
    $processed[$id] = true;
    $user['password'] = $newHash;
    $user['status'] = 'active';
    $user['updated_at'] = date('Y-m-d H:i:s');
    $fixed[] = $user;
}

// Write
$result = file_put_contents($jsonFile, json_encode($fixed, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
touch($jsonFile);
clearstatcache();

// Verify it was written
if ($result === false) {
    die("Error: Failed to write JSON file. Check permissions.");
}

// Verify the fix
$verify = json_decode(file_get_contents($jsonFile), true);
$testUser = null;
foreach ($verify as $u) {
    if (isset($u['name']) && strcasecmp($u['name'], 'access') === 0) {
        $testUser = $u;
        break;
    }
}

if ($testUser && !password_verify('password123', $testUser['password'])) {
    die("Error: Password verification failed after fix!");
}

header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html>
<head>
    <title>Fixed!</title>
    <style>
        body { font-family: Arial; max-width: 600px; margin: 50px auto; padding: 20px; text-align: center; }
        .success { color: #28a745; font-size: 24px; font-weight: bold; }
        .btn { background: #007bff; color: white; padding: 15px 30px; text-decoration: none; border-radius: 5px; display: inline-block; margin: 20px; font-size: 18px; }
    </style>
</head>
<body>
    <h1 class="success">✅ FIXED!</h1>
    <p>All passwords have been reset to <strong>password123</strong></p>
    <p>Total users fixed: <?php echo count($fixed); ?></p>
    <p><a href="index.php" class="btn">Login Now →</a></p>
    <p><small>Username: <code>access</code><br>Password: <code>password123</code></small></p>
</body>
</html>

