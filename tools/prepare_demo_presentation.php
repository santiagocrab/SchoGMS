<?php
/**
 * One-command demo prep: build files + print presenter checklist.
 *
 * CLI:  /Applications/XAMPP/xamppfiles/bin/php tools/prepare_demo_presentation.php
 * Web:  http://localhost/SchoGMS/tools/prepare_demo_presentation.php?key=schogms_demo
 */
declare(strict_types=1);

$isCli = PHP_SAPI === 'cli';
if (!$isCli && (($_GET['key'] ?? '') !== 'schogms_demo')) {
    header('Content-Type: text/html; charset=utf-8');
    echo '<h1>Prepare demo presentation</h1><p>Add <code>?key=schogms_demo</code></p>';
    exit;
}

header('Content-Type: text/plain; charset=utf-8');

$root = dirname(__DIR__);

function demo_line(string $msg): void
{
    echo $msg . PHP_EOL;
}

demo_line('=== SchoGMS demo presentation prep ===');
demo_line('');

// 1) Build demo file kit
demo_line('--- Step 1: Building demo/files/ kit ---');
require $root . '/tools/build_demo_package.php';
demo_line('');

// 2) Load manifest
$manifestPath = $root . '/demo/files/manifest.json';
if (!is_readable($manifestPath)) {
    demo_line('ERROR: manifest.json missing after build.');
    exit(1);
}
/** @var array<string, mixed> $manifest */
$manifest = json_decode((string) file_get_contents($manifestPath), true, 512, JSON_THROW_ON_ERROR);

demo_line('--- Step 2: Admin accounts to create (User management) ---');
demo_line('Default password for admin-created users: schogms123');
demo_line('Verify non-chairman accounts at: /verify.php (6-digit code on admin success popup)');
demo_line('');
demo_line(sprintf('%-4s %-22s %-32s %-12s %-8s', '#', 'Name', 'Email', 'Role', 'Campus'));
demo_line(str_repeat('-', 82));
$accounts = [
    ['1', 'Demo Chairman', 'chairman.demo@schogms.local', 'chairman', '—', 'Active immediately; only one chairman'],
    ['2', 'Demo Registrar ACCESS', 'registrar.demo@schogms.local', 'registrar', 'ACCESS', 'Verify then login'],
    ['3', 'Demo Coordinator ACCESS', 'coordinator.demo@schogms.local', 'coordinator', 'ACCESS', 'Verify then login'],
    ['4', 'Demo Director Isulan', 'director.demo@schogms.local', 'director', 'ISULAN', 'Verify then login'],
];
foreach ($accounts as $a) {
    demo_line(sprintf('%-4s %-22s %-32s %-12s %-8s  %s', $a[0], $a[1], $a[2], $a[3], $a[4], $a[5] ?? ''));
}
demo_line('');
demo_line('Shortcut (skip admin): php tools/fix_demo_logins.php  →  password password123');
demo_line('');

demo_line('--- Step 3: File group labels (copy into upload forms) ---');
$groups = $manifest['file_groups'] ?? [];
foreach ($groups as $key => $label) {
    demo_line("  {$key}: {$label}");
}
demo_line('');

demo_line('--- Step 4: Live demo upload order ---');
demo_line(sprintf('Campus: %s', $manifest['campus'] ?? 'ACCESS'));
demo_line('');
foreach ($manifest['upload_order'] ?? [] as $item) {
    if (!is_array($item)) {
        continue;
    }
    $step = (int) ($item['step'] ?? 0);
    $role = (string) ($item['role'] ?? '');
    $file = (string) ($item['file'] ?? '');
    $fg = (string) ($item['file_group'] ?? '');
    $when = (string) ($item['when'] ?? '');
    demo_line("Step {$step} | {$role}");
    demo_line("  File:       demo/files/{$file}");
    demo_line("  File group: {$fg}");
    demo_line("  When:       {$when}");
    demo_line('');
}

demo_line('--- Step 5: COR/COG scholars (filename = Lastname, Firstname Middlename.pdf) ---');
foreach ($manifest['scholars'] ?? [] as $s) {
    if (!is_array($s)) {
        continue;
    }
    demo_line('  · ' . ($s['display_name'] ?? '') . '  →  ' . ($s['cor_file'] ?? ''));
}
demo_line('');

demo_line('--- Step 6: Presenter script ---');
demo_line('Read: docs/SchoGMS_Demo_Script.md');
demo_line('Upload details: docs/SchoGMS_Upload_Workflow.md');
demo_line('');
demo_line('=== Ready. Open index.php and start with Admin or Step 1 (Registrar). ===');
