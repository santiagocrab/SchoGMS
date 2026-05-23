<?php
/**
 * Reset demo account passwords and sync MongoDB + MySQL so index.php login works.
 *
 * CLI:  /Applications/XAMPP/xamppfiles/bin/php tools/fix_demo_logins.php
 * Web:  http://localhost/SchoGMS/tools/fix_demo_logins.php?key=schogms_demo
 */
declare(strict_types=1);

$isCli = PHP_SAPI === 'cli';
if (!$isCli && (($_GET['key'] ?? '') !== 'schogms_demo')) {
    header('Content-Type: text/html; charset=utf-8');
    echo '<h1>Fix demo logins</h1><p>Add <code>?key=schogms_demo</code></p>';
    exit;
}

header('Content-Type: text/plain; charset=utf-8');

function out(string $msg): void
{
    echo $msg . PHP_EOL;
}

$pwMain = 'password123';
$pwDean = 'schogms123';
$pwAdmin = 'admin123';
$hashMain = password_hash($pwMain, PASSWORD_DEFAULT);
$hashDean = password_hash($pwDean, PASSWORD_DEFAULT);
$hashAdmin = password_hash($pwAdmin, PASSWORD_DEFAULT);

$c = require dirname(__DIR__) . '/config/schogms_mysql.php';
$conn = new mysqli($c['host'], $c['username'], $c['password'], $c['database']);
if ($conn->connect_error) {
    out('MySQL failed: ' . $conn->connect_error);
    exit(1);
}
$conn->set_charset('utf8mb4');

/** @var list<array{login: string, name?: string, email?: string, role: string, campus?: string}> */
$mysqlUsers = [
    ['login' => 'access', 'name' => 'access', 'email' => 'access@mail', 'role' => 'coordinator', 'campus' => 'ACCESS'],
    ['login' => 'registrar access', 'name' => 'registrar access', 'email' => 'registrar.access@schogms.demo', 'role' => 'registrar', 'campus' => 'ACCESS'],
    ['login' => 'chairman', 'name' => 'chairman', 'email' => 'chairman@schogms.demo', 'role' => 'chairman', 'campus' => ''],
    ['login' => 'Campus Director Isulan', 'name' => 'Campus Director Isulan', 'email' => 'director.isulan@schogms.demo', 'role' => 'director', 'campus' => 'ISULAN'],
];

out('=== SchoGMS fix demo logins ===');
out('Main password: ' . $pwMain);
out('Dean / program chair password: ' . $pwDean);
out('Admin password: ' . $pwAdmin);
out('');

foreach ($mysqlUsers as $spec) {
    $login = $spec['login'];
    $stmt = $conn->prepare(
        'SELECT user_id, name, email, role FROM users
         WHERE LOWER(TRIM(name)) = LOWER(TRIM(?)) OR LOWER(TRIM(email)) = LOWER(TRIM(?))
         LIMIT 1'
    );
    $stmt->bind_param('ss', $login, $login);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if ($row) {
        $uid = (int) $row['user_id'];
        $ev = $spec['role'] === 'coordinator' ? 0 : 1;
        $upd = $conn->prepare(
            'UPDATE users SET name = ?, email = ?, role = ?, campus = ?, password = ?,
                    status = \'active\', email_verified = ?
             WHERE user_id = ?'
        );
        $name = $spec['name'] ?? $login;
        $email = $spec['email'] ?? ($row['email'] ?? '');
        $role = $spec['role'];
        $campus = $spec['campus'] ?? '';
        $upd->bind_param('sssssii', $name, $email, $role, $campus, $hashMain, $ev, $uid);
        $upd->execute();
        $upd->close();
        out("MySQL users: updated \"{$login}\" (user_id={$uid})");
    } else {
        if ($spec['role'] === 'chairman') {
            $chk = $conn->query("SELECT user_id, name FROM users WHERE role = 'chairman' LIMIT 1");
            if ($chk && ($existing = $chk->fetch_assoc())) {
                $uid = (int) $existing['user_id'];
                $upd = $conn->prepare(
                    'UPDATE users SET name = ?, email = ?, password = ?, status = \'active\', email_verified = 1, campus = \'\' WHERE user_id = ?'
                );
                $name = 'chairman';
                $email = 'chairman@schogms.demo';
                $upd->bind_param('sssi', $name, $email, $hashMain, $uid);
                $upd->execute();
                $upd->close();
                out('MySQL users: renamed existing chairman to login "chairman" (user_id=' . $uid . ')');
                continue;
            }
        }
        $ins = $conn->prepare(
            'INSERT INTO users (name, email, role, campus, password, verification_code, verification_expires, email_verified, status)
             VALUES (?, ?, ?, ?, ?, NULL, NULL, ?, \'active\')'
        );
        $name = $spec['name'] ?? $login;
        $email = $spec['email'] ?? ($login . '@schogms.demo');
        $role = $spec['role'];
        $campus = $spec['campus'] ?? '';
        $ev = $role === 'coordinator' ? 0 : 1;
        $ins->bind_param('sssssi', $name, $email, $role, $campus, $hashMain, $ev);
        $ins->execute();
        out('MySQL users: inserted "' . $login . '" (id=' . $conn->insert_id . ')');
        $ins->close();
    }
}

// Demo dean
$deanName = 'DemoDean';
$deanEmail = 'demo.dean@schogms.local';
$deanCampus = 'ISULAN';
$deanCollege = 'College of Engineering and Technology';
$chk = $conn->prepare('SELECT id FROM assigned_dean WHERE LOWER(email) = LOWER(?) OR LOWER(dean) = LOWER(?) LIMIT 1');
$chk->bind_param('ss', $deanEmail, $deanName);
$chk->execute();
$deanRow = $chk->get_result()->fetch_assoc();
$chk->close();
if ($deanRow) {
    $id = (int) $deanRow['id'];
    $upd = $conn->prepare(
        'UPDATE assigned_dean SET dean = ?, email = ?, password = ?, status = \'active\', campus = ?, college_name = ? WHERE id = ?'
    );
    $upd->bind_param('sssssi', $deanName, $deanEmail, $hashDean, $deanCampus, $deanCollege, $id);
    $upd->execute();
    $upd->close();
    out('assigned_dean: updated DemoDean (id=' . $id . ')');
} else {
    $ins = $conn->prepare(
        'INSERT INTO assigned_dean (campus, college_name, course_program, dean, email, password, status)
         VALUES (?, ?, ?, ?, ?, ?, \'active\')'
    );
    $course = $deanCollege;
    $ins->bind_param('ssssss', $deanCampus, $deanCollege, $course, $deanName, $deanEmail, $hashDean);
    $ins->execute();
    out('assigned_dean: inserted DemoDean (id=' . $conn->insert_id . ')');
    $ins->close();
}

// Program chair
$pcName = 'ProgramChairDemo';
$pcEmail = 'programchair.schogms.demo@local';
$chk = $conn->prepare('SELECT id FROM assigned_program_chairs WHERE LOWER(program_chair) = LOWER(?) LIMIT 1');
$chk->bind_param('s', $pcName);
$chk->execute();
$pcRow = $chk->get_result()->fetch_assoc();
$chk->close();
if ($pcRow) {
    $id = (int) $pcRow['id'];
    $upd = $conn->prepare(
        'UPDATE assigned_program_chairs SET email = ?, password = ?, status = \'active\', campus = \'ISULAN\' WHERE id = ?'
    );
    $upd->bind_param('ssi', $pcEmail, $hashDean, $id);
    $upd->execute();
    $upd->close();
    out('assigned_program_chairs: updated ProgramChairDemo (id=' . $id . ')');
} else {
    $ins = $conn->prepare(
        'INSERT INTO assigned_program_chairs (campus, course_program, program_chair, email, password, status)
         VALUES (\'ISULAN\', \'BSIT (demo)\', ?, ?, ?, \'active\')'
    );
    $ins->bind_param('sss', $pcName, $pcEmail, $hashDean);
    $ins->execute();
    out('assigned_program_chairs: inserted ProgramChairDemo');
    $ins->close();
}

// Admin
$admUser = 'admin';
$adm = $conn->prepare('UPDATE admin SET password = ? WHERE username = ?');
$adm->bind_param('ss', $hashAdmin, $admUser);
$adm->execute();
if ($adm->affected_rows === 0) {
    $ins = $conn->prepare('INSERT INTO admin (username, password) VALUES (?, ?)');
    $ins->bind_param('ss', $admUser, $hashAdmin);
    $ins->execute();
    out('admin: inserted admin');
    $ins->close();
} else {
    out('admin: password reset for admin');
}
$adm->close();

// MongoDB users.json
$jsonFile = dirname(__DIR__) . '/mongodb_data/schogms/users.json';
if (!is_readable($jsonFile)) {
    out('Mongo JSON not found — skipped');
} else {
    $users = json_decode((string) file_get_contents($jsonFile), true);
    if (!is_array($users)) {
        out('Mongo JSON parse failed');
    } else {
        $logins = ['access', 'chairman', 'registrar access', 'Campus Director Isulan', 'Coordinator', 'test@mail'];
        $byId = [];
        foreach ($users as $u) {
            $id = $u['user_id'] ?? null;
            if ($id === null) {
                continue;
            }
            if (!isset($byId[$id])) {
                $byId[$id] = $u;
            }
        }
        foreach ($byId as $id => $u) {
            $name = (string) ($u['name'] ?? '');
            $email = (string) ($u['email'] ?? '');
            $match = false;
            foreach ($logins as $want) {
                if (strcasecmp(trim($name), trim($want)) === 0 || strcasecmp(trim($email), trim($want)) === 0) {
                    $match = true;
                    break;
                }
            }
            if (!$match && ($u['role'] ?? '') !== 'coordinator' && ($u['role'] ?? '') !== 'registrar') {
                continue;
            }
            if (($u['role'] ?? '') === 'registrar' && strcasecmp($name, 'registrar access') !== 0) {
                continue;
            }
            $byId[$id]['password'] = $hashMain;
            $byId[$id]['status'] = 'active';
            if (($byId[$id]['role'] ?? '') !== 'coordinator') {
                $byId[$id]['email_verified'] = true;
            }
            $byId[$id]['updated_at'] = date('Y-m-d H:i:s');
        }
        // Ensure registrar access exists in Mongo
        $hasRegAccess = false;
        foreach ($byId as $u) {
            if (strcasecmp((string) ($u['name'] ?? ''), 'registrar access') === 0) {
                $hasRegAccess = true;
                break;
            }
        }
        if (!$hasRegAccess) {
            $maxId = 0;
            foreach ($byId as $u) {
                $maxId = max($maxId, (int) ($u['user_id'] ?? 0));
            }
            $byId[$maxId + 1] = [
                'user_id' => $maxId + 1,
                'name' => 'registrar access',
                'email' => 'registrar.access@schogms.demo',
                'role' => 'registrar',
                'password' => $hashMain,
                'campus' => 'ACCESS',
                'email_verified' => true,
                'status' => 'active',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ];
        }
        $outUsers = array_values($byId);
        file_put_contents($jsonFile, json_encode($outUsers, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
        touch($jsonFile);
        out('Mongo users.json: synced (' . count($outUsers) . ' users)');
    }
}

out('');
out('=== Verify ===');
$verify = [
    ['access', $pwMain, 'mysql'],
    ['registrar access', $pwMain, 'mysql'],
    ['chairman', $pwMain, 'mysql'],
    ['Campus Director Isulan', $pwMain, 'mysql'],
    ['DemoDean', $pwDean, 'dean'],
    ['ProgramChairDemo', $pwDean, 'apc'],
];
foreach ($verify as [$login, $pass, $type]) {
    $ok = false;
    if ($type === 'mysql') {
        $stmt = $conn->prepare('SELECT password FROM users WHERE LOWER(name)=LOWER(?) LIMIT 1');
        $stmt->bind_param('s', $login);
        $stmt->execute();
        $r = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        $ok = $r && password_verify($pass, (string) $r['password']);
    } elseif ($type === 'dean') {
        $stmt = $conn->prepare('SELECT password FROM assigned_dean WHERE LOWER(dean)=LOWER(?) LIMIT 1');
        $stmt->bind_param('s', $login);
        $stmt->execute();
        $r = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        $ok = $r && password_verify($pass, (string) $r['password']);
    } else {
        $stmt = $conn->prepare('SELECT password FROM assigned_program_chairs WHERE LOWER(program_chair)=LOWER(?) LIMIT 1');
        $stmt->bind_param('s', $login);
        $stmt->execute();
        $r = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        $ok = $r && password_verify($pass, (string) $r['password']);
    }
    out(($ok ? 'OK' : 'FAIL') . "  {$login} / {$pass}");
}

$admCheck = $conn->prepare('SELECT password FROM admin WHERE username = ?');
$admCheck->bind_param('s', $admUser);
$admCheck->execute();
$ar = $admCheck->get_result()->fetch_assoc();
$admCheck->close();
out((password_verify($pwAdmin, (string) ($ar['password'] ?? '')) ? 'OK' : 'FAIL') . "  admin / {$pwAdmin}");

out('');
out('Login: http://localhost/SchoGMS/index.php');
out('Admin: http://localhost/SchoGMS/admin-12-02/');
out('Dean login: DemoDean / schogms123');

$conn->close();
