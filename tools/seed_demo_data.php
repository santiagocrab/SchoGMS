<?php
/**
 * Seed large-scale DEMO data across campuses for SchoGMS testing & documentation screenshots.
 *
 * CLI:  php tools/seed_demo_data.php
 *       php tools/seed_demo_data.php --light   (smaller set)
 * Web:  http://localhost/SchoGMS/tools/seed_demo_data.php?key=schogms_demo&confirm=1
 *
 * Removes previous DEMO rows (prefix "DEMO |", DEMO_SEED, DEMO-OR-) then inserts fresh samples.
 */
declare(strict_types=1);

$isCli = PHP_SAPI === 'cli';
$webKey = $_GET['key'] ?? '';
$webConfirm = isset($_GET['confirm']);
$lightMode = in_array('--light', $argv ?? [], true) || isset($_GET['light']);

if (!$isCli) {
    if ($webKey !== 'schogms_demo' || !$webConfirm) {
        header('Content-Type: text/html; charset=utf-8');
        echo '<h1>SchoGMS demo seed (massive)</h1>';
        echo '<p>Full: <code>?key=schogms_demo&amp;confirm=1</code></p>';
        echo '<p>Light: <code>?key=schogms_demo&amp;confirm=1&amp;light=1</code></p>';
        echo '<p>CLI: <code>php tools/seed_demo_data.php</code> or <code>--light</code></p>';
        exit;
    }
    if (isset($_GET['light'])) {
        $lightMode = true;
    }
    header('Content-Type: text/plain; charset=utf-8');
}

require_once dirname(__DIR__) . '/config/schogms_helpers.php';

$cfg = require dirname(__DIR__) . '/config/schogms_mysql.php';
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    $conn = new mysqli($cfg['host'], $cfg['username'], $cfg['password'], $cfg['database']);
    $conn->set_charset('utf8mb4');
} catch (mysqli_sql_exception $e) {
    out('Database connection failed: ' . $e->getMessage());
    exit(1);
}

const DEMO_FG_PREFIX = 'DEMO |';
const DEMO_FILENAME_TAG = 'DEMO_SEED';
const DEMO_BILLING_OR_PREFIX = 'DEMO-OR-';

/** Scale: "massive" default for paper screenshots; --light for quick dev */
$SCALE = $lightMode ? [
    'tdp_per_campus' => 12,
    'tes_per_campus' => 8,
    'billing_per_campus' => 10,
    'annex_per_campus' => 1,
    'tdp_file_groups' => 1,
] : [
    'tdp_per_campus' => 150,
    'tes_per_campus' => 70,
    'billing_per_campus' => 100,
    'annex_per_campus' => 5,
    'tdp_file_groups' => 4,
];

/** @var list<string> */
const CAMPUSES = [
    'ACCESS',
    'ISULAN',
    'KALAMANSIG',
    'BAGUMBAYAN',
    'PALIMBANG',
    'TACURONG',
    'LUTAYAN',
];

/** @var list<string> */
const SCHOOL_YEARS = ['2022-2023', '2023-2024', '2024-2025', '2025-2026'];

/** @var list<string> */
const LAST_NAMES = [
    'REYES', 'DELA CRUZ', 'GARCIA', 'BAUTISTA', 'FERNANDEZ', 'AQUINO', 'CASTILLO', 'MAGAYON', 'ABACARO', 'MACAPAGAL',
    'SANTIAGO', 'MORALES', 'RAMOS', 'TORRES', 'FLORES', 'GONZALES', 'MENDOZA', 'CRUZ', 'RIVERA', 'LOPEZ',
    'HERRERA', 'JIMENEZ', 'VILLANUEVA', 'SALAZAR', 'DIZON', 'LIM', 'TAN', 'GO', 'SY', 'CHUA',
    'ANG', 'YU', 'ONG', 'CO', 'TEE', 'WONG', 'LEE', 'KIM', 'PARK', 'SANTOS',
    'RODRIGUEZ', 'PEREZ', 'GUTIERREZ', 'ALVAREZ', 'CASTRO', 'ORTIZ', 'RUBIO', 'MARQUEZ', 'DEL ROSARIO', 'BALTAZAR',
];

/** @var list<string> */
const FIRST_NAMES = [
    'MARIA', 'JUAN', 'ANALYN', 'CARLO', 'SOPHIA', 'PATRICK', 'HONEY', 'JERICHO', 'ROSE ANN', 'DANICA',
    'LEO', 'KRISTINE', 'JAMES', 'ANGELA', 'MICHAEL', 'JENNY', 'MARK', 'PAUL', 'ANNA', 'CHRIS',
    'JOHN', 'MARY', 'JOSE', 'ANTONIO', 'FRANCISCO', 'MANUEL', 'PEDRO', 'RICARDO', 'EDUARDO', 'ROBERTO',
    'ELENA', 'TERESA', 'CARMEN', 'LUCIA', 'ROSA', 'ISABEL', 'ANA', 'SOFIA', 'GABRIEL', 'DANIEL',
    'EMMANUEL', 'JOSHUA', 'NATHAN', 'ETHAN', 'LIAM', 'NOAH', 'MIA', 'EMMA', 'OLIVIA', 'AVA',
];

/** @var list<string> */
const MIDDLE_NAMES = [
    'SANTOS', 'TORRES', 'MENDOZA', 'LIM', 'RAMOS', 'DIZON', 'VILLANUEVA', 'SALAZAR', 'GO', 'CRUZ',
    'FLORES', 'REYES', 'GARCIA', 'BAUTISTA', 'AQUINO', 'CASTILLO', 'PIQUE', 'FERNANDEZ', 'MAGAYON', 'SY',
];

/** @var list<string> */
const EXT_NAMES = ['', '', '', 'JR', 'SR', 'II', 'III', 'IV'];

/** @var list<string> */
const COURSES = [
    'Bachelor of Science in Information Technology',
    'Bachelor of Science in Computer Science',
    'Bachelor of Science in Criminology',
    'Bachelor of Elementary Education',
    'Bachelor of Secondary Education',
    'Bachelor of Science in Agriculture',
    'Bachelor of Science in Hospitality Management',
    'Bachelor of Science in Nursing',
    'Bachelor of Science in Accountancy',
    'Bachelor of Science in Biology',
    'Bachelor of Science in Entrepreneurship',
    'Bachelor of Science in Tourism Management',
    'Bachelor of Science in Civil Engineering',
    'Bachelor of Science in Fisheries',
    'Bachelor of Industrial Technology',
    'Bachelor of Arts in Economics',
    'Bachelor of Arts in Political Science',
    'Bachelor of Science in Midwifery',
    'Bachelor of Science in Medical Technology',
    'Bachelor of Science in Agribusiness',
];

/** @var list<string|int> */
const YEAR_LEVELS = ['1', '2', '3', '4', '1st year', '2nd year', '3rd year', '4th year'];

/** @var list<string> */
const SCHOLARSHIP_TYPES = ['CHED TDP', 'CHED TES', 'FSSP', 'HSSP', 'CONG. HERNANDEZ', 'FS101'];
const PAYMENT_STATUSES = ['PAID', 'ON PROCESS', 'N/A', 'PAID/LIQUIDATED', 'B2 HELP', 'Verified'];
const ENROLL_STATUSES = ['Enrolled', 'Not Enrolled', 'LOA', 'Graduated'];

function out(string $msg): void
{
    echo $msg . PHP_EOL;
    if (PHP_SAPI !== 'cli') {
        echo '<br>';
        @ob_flush();
        @flush();
    }
}

function tableExists(mysqli $conn, string $table): bool
{
    $safe = $conn->real_escape_string($table);
    $res = $conn->query("SHOW TABLES LIKE '{$safe}'");

    return $res !== false && $res->num_rows > 0;
}

function pickName(int $seed): array
{
    $ln = LAST_NAMES[$seed % count(LAST_NAMES)];
    $fn = FIRST_NAMES[($seed * 7) % count(FIRST_NAMES)];
    $mn = MIDDLE_NAMES[($seed * 13) % count(MIDDLE_NAMES)];
    $ext = EXT_NAMES[($seed * 3) % count(EXT_NAMES)];

    return ['last' => $ln, 'first' => $fn, 'mid' => $mn, 'ext' => $ext];
}

function demoDocBasename(string $last, string $first, string $mid): string
{
    $base = trim($last) . ', ' . trim($first);
    if (trim($mid) !== '') {
        $base .= ' ' . trim($mid);
    }

    return $base;
}

function ensureUploadDirs(): array
{
    $paths = [
        'coordinator' => dirname(__DIR__) . '/users/coordinator/uploads',
        'annex7' => dirname(__DIR__) . '/users/coordinator/uploads/annex7',
        'exports' => dirname(__DIR__) . '/users/coordinator/data/masterlist_exports',
        'chairman' => dirname(__DIR__) . '/users/chairman/uploads',
        'registrar' => dirname(__DIR__) . '/users/registrar/uploads',
    ];
    foreach (['COR', 'COG'] as $cat) {
        $dir = $paths['coordinator'] . '/' . $cat;
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
    }
    foreach ($paths as $p) {
        if (!is_dir($p)) {
            mkdir($p, 0755, true);
        }
    }

    return $paths;
}

function writePlaceholderPdf(string $fullPath, string $label): void
{
    require_once dirname(__DIR__) . '/config/schogms_helpers.php';
    schogms_write_viewable_pdf($fullPath, $label, 'SchoGMS demo document');
}

function writePlaceholderXlsx(string $fullPath, string $title): void
{
    // Minimal OOXML-ish zip not needed — chairman preview uses SheetJS; empty file is enough for listing
    $csv = "Annex 7 DEMO,{$title}\n";
    $csv .= "Last name,First name,Scholarship,Units,Course,Campus\n";
    $csv .= "Dela Cruz,Juan,TDP,21,BS IT,ACCESS\n";
    file_put_contents($fullPath, $csv);
}

function purgeDemoData(mysqli $conn): void
{
    $like = DEMO_FG_PREFIX . '%';
    $tables = [
        'ched_masterlist' => 'file_group',
        'ched_masterlist_tes' => 'file_group',
        'document_uploads' => 'file_group',
        'registrar_master_list' => 'file_group',
    ];
    foreach ($tables as $table => $col) {
        if (!tableExists($conn, $table)) {
            continue;
        }
        $stmt = $conn->prepare("DELETE FROM {$table} WHERE {$col} LIKE ?");
        $stmt->bind_param('s', $like);
        $stmt->execute();
        out("Cleared {$stmt->affected_rows} rows from {$table}");
        $stmt->close();
    }

    if (tableExists($conn, 'file_submissions')) {
        $stmt = $conn->prepare('DELETE FROM file_submissions WHERE file_name LIKE ?');
        $tag = '%' . DEMO_FILENAME_TAG . '%';
        $stmt->bind_param('s', $tag);
        $stmt->execute();
        out('Cleared ' . $stmt->affected_rows . ' file_submissions');
        $stmt->close();
    }

    if (tableExists($conn, 'billing_table')) {
        $stmt = $conn->prepare('DELETE FROM billing_table WHERE payment_or_number LIKE ?');
        $orLike = DEMO_BILLING_OR_PREFIX . '%';
        $stmt->bind_param('s', $orLike);
        $stmt->execute();
        out('Cleared ' . $stmt->affected_rows . ' billing_table (DEMO OR prefix)');
        $stmt->close();
    }

    // Remove old DEMO export CSVs
    $exportDir = dirname(__DIR__) . '/users/coordinator/data/masterlist_exports';
    if (is_dir($exportDir)) {
        foreach (glob($exportDir . '/DEMO_*') ?: [] as $f) {
            @unlink($f);
        }
        out('Removed old DEMO_* export files');
    }
}

/**
 * @return array{tdp: int, tes: int, reg: int, cor: int, cog: int, files: int}
 */
function seedCampus(mysqli $conn, string $campus, array $paths, array $scale, int $campusIndex): array
{
    $counts = ['tdp' => 0, 'tes' => 0, 'reg' => 0, 'cor' => 0, 'cog' => 0, 'files' => 0];
    $fgGroups = min($scale['tdp_file_groups'], count(SCHOOL_YEARS));
    $perGroup = (int) ceil($scale['tdp_per_campus'] / max(1, $fgGroups));
    $globalSeed = $campusIndex * 10000;

    $tdpInsert = $conn->prepare(
        'INSERT INTO ched_masterlist (
            sheet_name, filename, file_group, seq, app_no, award_no, lastname, firstname, extname, middlename,
            sex, birthdate, course_program_enrolled, year_level, total_units_enrolled, status_of_enrollment,
            remarks, validation_status
        ) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)'
    );

    $tesInsert = $conn->prepare(
        'INSERT INTO ched_masterlist_tes (
            campus, filename, file_group, seq, app_no, lastname, firstname, ext, middlename, sex,
            course_program_enrolled, year_level, street, town_city, contact, batch_no
        ) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)'
    );

    $regInsert = $conn->prepare(
        'INSERT INTO registrar_master_list (
            campus, file_group, filename, last_name, first_name, middle_name, course, year_level, enrolled
        ) VALUES (?,?,?,?,?,?,?,?,?)'
    );

    $docInsert = $conn->prepare(
        'INSERT INTO document_uploads (campus, file_group, category, file_name, file_path)
         VALUES (?,?,?,?,?)'
    );

    $scholarIdx = 0;
    for ($g = 0; $g < $fgGroups; $g++) {
        $sy = SCHOOL_YEARS[$g % count(SCHOOL_YEARS)];
        $fgTdp = DEMO_FG_PREFIX . " TDP {$campus} SY {$sy}";
        $fgReg = DEMO_FG_PREFIX . " Registrar {$campus} SY {$sy}";
        $fgCor = DEMO_FG_PREFIX . " COR {$campus} SY {$sy}";
        $fgCog = DEMO_FG_PREFIX . " COG {$campus} SY {$sy}";
        $csvName = DEMO_FILENAME_TAG . "_{$campus}_{$sy}_masterlist.csv";

        for ($i = 0; $i < $perGroup; $i++) {
            $seed = $globalSeed + $scholarIdx;
            $n = pickName($seed);
            $course = COURSES[$seed % count(COURSES)];
            $ylMaster = YEAR_LEVELS[$seed % count(YEAR_LEVELS)];
            $seq = sprintf('D%s-%s-%04d', substr($campus, 0, 3), substr(str_replace('-', '', $sy), -4), $scholarIdx + 1);
            $appNo = 'APP-' . strtoupper(substr($campus, 0, 3)) . '-' . $sy . '-' . (10000 + $scholarIdx);
            $award = 'AWD-' . (50000 + $scholarIdx);
            $sex = ($seed % 2 === 0) ? 'F' : 'M';
            $birth = sprintf('200%d-%02d-%02d', ($seed % 5), ($seed % 12) + 1, ($seed % 28) + 1);
            $units = (string) (15 + ($seed % 10));
            $status = ENROLL_STATUSES[$seed % count(ENROLL_STATUSES)];
            $remarks = ($seed % 11 === 0) ? 'DEMO — validation edge case' : (($seed % 7 === 0) ? 'DEMO — remarks sample' : '');
            $valStatus = match ($seed % 5) {
                0 => 'Validated',
                1 => 'Failed',
                2 => 'Pending',
                default => '',
            };

            $ln = $n['last'];
            $fn = $n['first'];
            $ext = $n['ext'];
            $mn = $n['mid'];
            $tdpInsert->bind_param(
                'ssssssssssssssssss',
                $campus,
                $csvName,
                $fgTdp,
                $seq,
                $appNo,
                $award,
                $ln,
                $fn,
                $ext,
                $mn,
                $sex,
                $birth,
                $course,
                $ylMaster,
                $units,
                $status,
                $remarks,
                $valStatus
            );
            $tdpInsert->execute();
            $counts['tdp']++;

            $regYear = ($seed % 9 === 0) ? '2nd year' : $ylMaster;
            $regFilename = DEMO_FILENAME_TAG . "_registrar_{$sy}.csv";
            $regEnrolled = ($status === 'Enrolled') ? 'Yes' : 'No';
            $regInsert->bind_param(
                'sssssssss',
                $campus,
                $fgReg,
                $regFilename,
                $ln,
                $fn,
                $mn,
                $course,
                $regYear,
                $regEnrolled
            );
            $regInsert->execute();
            $counts['reg']++;

            $basename = demoDocBasename($ln, $fn, $mn);
            $storedBase = preg_replace('/[^a-zA-Z0-9,_\-\. ]+/', '_', $basename);

            if ($seed % 6 !== 5) {
                $fnPdf = $storedBase . '_' . $seq . '_COR.pdf';
                $full = $paths['coordinator'] . '/COR/' . $fnPdf;
                writePlaceholderPdf($full, "COR {$basename} {$campus}");
                $counts['files']++;
                $path = 'uploads/COR/' . $fnPdf;
                $catCor = 'COR';
                $docInsert->bind_param('sssss', $campus, $fgCor, $catCor, $fnPdf, $path);
                $docInsert->execute();
                $counts['cor']++;
            }
            if ($seed % 5 !== 4) {
                $fnPdf = $storedBase . '_' . $seq . '_COG.pdf';
                $full = $paths['coordinator'] . '/COG/' . $fnPdf;
                writePlaceholderPdf($full, "COG {$basename} {$campus}");
                $counts['files']++;
                $path = 'uploads/COG/' . $fnPdf;
                $catCog = 'COG';
                $docInsert->bind_param('sssss', $campus, $fgCog, $catCog, $fnPdf, $path);
                $docInsert->execute();
                $counts['cog']++;
            }

            $scholarIdx++;
        }
    }

    $fgTesBase = DEMO_FG_PREFIX . " TES {$campus} SY 2024-2025";
    for ($i = 0; $i < $scale['tes_per_campus']; $i++) {
        $seed = $globalSeed + 5000 + $i;
        $n = pickName($seed);
        $course = COURSES[($seed + 3) % count(COURSES)];
        $yl = ($seed % 4) + 1;
        $seq = sprintf('T%s-%04d', substr($campus, 0, 3), $i + 1);
        $appNo = 'TES-' . strtoupper(substr($campus, 0, 3)) . '-' . (80000 + $i);
        $sex = ($seed % 2) ? 'M' : 'F';
        $street = ($seed % 200) . ' Maharlika St';
        $town = $campus === 'ACCESS' ? 'Esperanza' : $campus . ' Town';
        $contact = '09' . str_pad((string) (1700000000 + ($seed % 99999999)), 10, '0', STR_PAD_LEFT);
        $batch = 'TES-BATCH-' . (2024 - ($i % 3));
        $tesCsv = DEMO_FILENAME_TAG . "_{$campus}_tes.csv";

        $ln = $n['last'];
        $fn = $n['first'];
        $ext = $n['ext'];
        $mn = $n['mid'];
        $tesInsert->bind_param(
            'sssssssssssissss',
            $campus,
            $tesCsv,
            $fgTesBase,
            $seq,
            $appNo,
            $ln,
            $fn,
            $ext,
            $mn,
            $sex,
            $course,
            $yl,
            $street,
            $town,
            $contact,
            $batch
        );
        $tesInsert->execute();
        $counts['tes']++;
    }

    $tdpInsert->close();
    $tesInsert->close();
    $regInsert->close();
    $docInsert->close();

    return $counts;
}

function seedBilling(mysqli $conn, string $campus, int $count, int $campusIndex): int
{
    if (!tableExists($conn, 'billing_table')) {
        return 0;
    }

    $stmt = $conn->prepare(
        'INSERT INTO billing_table (
            last_name, first_name, scholarship_type, units_enrolled, course, campus,
            year_and_date_submitted_ched, amount, first_semester, second_semester, status,
            payment_scholarship_type, payment_amount, payment_year_and_date, payment_or_number,
            payment_amount_per_or, refund_first_sem, refund_second_sem, refund_year_and_date_released
        ) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)'
    );

    $n = 0;
    $baseSeed = $campusIndex * 2000;
    for ($i = 0; $i < $count; $i++) {
        $seed = $baseSeed + $i;
        $name = pickName($seed);
        $course = COURSES[$seed % count(COURSES)];
        $stype = SCHOLARSHIP_TYPES[$seed % count(SCHOLARSHIP_TYPES)];
        $units = 18 + ($seed % 12);
        $amount = ($seed % 3 === 0) ? 15000.0 : 7500.0;
        $firstSem = PAYMENT_STATUSES[$seed % count(PAYMENT_STATUSES)];
        $secondSem = ($seed % 4 === 0) ? 'PAID' : 'N/A';
        $status = ($seed % 6 === 0) ? 'Verified' : 'Active';
        $payType = ($seed % 2 === 0) ? 'TDP' : 'TES';
        $payAmount = $amount;
        $chedDate = sprintf('2024-%02d-%02d', ($seed % 12) + 1, ($seed % 28) + 1);
        $payDate = sprintf('2025-%02d-%02d', ($seed % 12) + 1, ($seed % 28) + 1);
        $or = DEMO_BILLING_OR_PREFIX . strtoupper(substr($campus, 0, 3)) . '-' . sprintf('%06d', $i + 1);
        $perOr = $amount;
        $ref1 = ($seed % 10 === 0) ? 500.0 : 0.0;
        $ref2 = 0.0;
        $refDate = ($ref1 > 0) ? $payDate : null;

        $ln = $name['last'];
        $fn = $name['first'];
        $stmt->bind_param(
            'sssisssdssssdssddds',
            $ln,
            $fn,
            $stype,
            $units,
            $course,
            $campus,
            $chedDate,
            $amount,
            $firstSem,
            $secondSem,
            $status,
            $payType,
            $payAmount,
            $payDate,
            $or,
            $perOr,
            $ref1,
            $ref2,
            $refDate
        );
        $stmt->execute();
        $n++;
    }
    $stmt->close();

    return $n;
}

function seedAnnexSubmissions(mysqli $conn, array $paths, int $perCampus): int
{
    if (!tableExists($conn, 'file_submissions')) {
        return 0;
    }

    $coordinators = [];
    $res = $conn->query(
        "SELECT user_id, name, email, campus FROM users WHERE role = 'coordinator' AND campus IS NOT NULL AND campus != ''"
    );
    while ($res && ($row = $res->fetch_assoc())) {
        $coordinators[(string) $row['campus']] = $row;
    }

    $stmt = $conn->prepare(
        'INSERT INTO file_submissions (user_id, user_email, campus, file_name, file_path, status)
         VALUES (?,?,?,?,?,?)'
    );
    $statusCycle = ['Pending', 'Approved', 'Pending', 'Rejected', 'Pending', 'Approved'];
    $n = 0;
    $fileCount = 0;

    foreach (CAMPUSES as $idx => $campus) {
        $c = $coordinators[$campus] ?? [
            'user_id' => '0',
            'email' => 'demo.' . strtolower($campus) . '@schogms.test',
            'campus' => $campus,
        ];
        for ($a = 0; $a < $perCampus; $a++) {
            $fname = DEMO_FILENAME_TAG . "_Annex7_{$campus}_{$a}.xlsx";
            $relPath = 'uploads/annex7/' . $fname;
            $full = $paths['annex7'] . '/' . $fname;
            writePlaceholderXlsx($full, "{$campus} submission {$a}");
            $fileCount++;

            $status = $statusCycle[($idx + $a) % count($statusCycle)];
            $uid = (string) ($c['user_id'] ?? '0');
            $email = (string) ($c['email'] ?? 'demo@schogms.test');
            $stmt->bind_param('ssssss', $uid, $email, $campus, $fname, $relPath, $status);
            $stmt->execute();
            $n++;
        }
    }
    $stmt->close();
    out("Wrote {$fileCount} Annex7 placeholder files under users/coordinator/uploads/annex7/");

    return $n;
}

function regenerateDemoCsvExports(mysqli $conn, array $paths): int
{
    $dir = $paths['exports'];
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }

    $written = 0;
    foreach (CAMPUSES as $campus) {
        $safe = preg_replace('/[^a-zA-Z0-9_-]+/', '_', $campus);

        foreach (SCHOOL_YEARS as $sy) {
            $fgLike = DEMO_FG_PREFIX . " TDP {$campus} SY {$sy}";
            $stmt = $conn->prepare(
                'SELECT seq, app_no, award_no, lastname, firstname, extname, middlename, sex, birthdate,
                        course_program_enrolled, year_level, total_units_enrolled, status_of_enrollment, remarks
                 FROM ched_masterlist WHERE sheet_name = ? AND file_group = ?
                 ORDER BY id'
            );
            $stmt->bind_param('ss', $campus, $fgLike);
            $stmt->execute();
            $res = $stmt->get_result();
            $rows = [];
            while ($res && ($r = $res->fetch_assoc())) {
                $rows[] = $r;
            }
            $stmt->close();
            if ($rows === []) {
                continue;
            }

            $path = "{$dir}/DEMO_TDP_{$safe}_{$sy}.csv";
            $fp = fopen($path, 'w');
            if ($fp) {
                fputcsv($fp, ['SEQ', 'APP NO', 'AWARD NO', 'LASTNAME', 'FIRSTNAME', 'EXTNAME', 'MIDDLENAME', 'SEX', 'BIRTHDATE', 'COURSE', 'YEAR LEVEL', 'UNITS', 'STATUS', 'REMARKS']);
                foreach ($rows as $r) {
                    fputcsv($fp, [
                        $r['seq'], $r['app_no'], $r['award_no'], $r['lastname'], $r['firstname'],
                        $r['extname'], $r['middlename'], $r['sex'], $r['birthdate'],
                        $r['course_program_enrolled'], $r['year_level'], $r['total_units_enrolled'],
                        $r['status_of_enrollment'], $r['remarks'],
                    ]);
                }
                fclose($fp);
                $written++;
            }
        }

        $stmt = $conn->prepare(
            'SELECT seq, app_no, lastname, firstname, course_program_enrolled, year_level, contact, batch_no
             FROM ched_masterlist_tes WHERE campus = ? AND file_group LIKE ?
             ORDER BY id'
        );
        $like = DEMO_FG_PREFIX . '%';
        $stmt->bind_param('ss', $campus, $like);
        $stmt->execute();
        $res = $stmt->get_result();
        $path = "{$dir}/DEMO_TES_{$safe}.csv";
        $fp = fopen($path, 'w');
        if ($fp) {
            fputcsv($fp, ['SEQ', 'APP NO', 'LASTNAME', 'FIRSTNAME', 'COURSE', 'YEAR', 'CONTACT', 'BATCH']);
            while ($res && ($r = $res->fetch_assoc())) {
                fputcsv($fp, [
                    $r['seq'], $r['app_no'], $r['lastname'], $r['firstname'],
                    $r['course_program_enrolled'], $r['year_level'], $r['contact'], $r['batch_no'],
                ]);
            }
            fclose($fp);
            $written++;
        }
        $stmt->close();
    }

    return $written;
}

// --- Run ---
$modeLabel = $lightMode ? 'LIGHT' : 'MASSIVE';
out("=== SchoGMS DEMO seed ({$modeLabel}) ===");
out('Campuses: ' . implode(', ', CAMPUSES));
out('Per campus: TDP~' . ($SCALE['tdp_per_campus']) . ' (' . $SCALE['tdp_file_groups'] . ' file groups), TES ' . $SCALE['tes_per_campus'] . ', Billing ' . $SCALE['billing_per_campus']);
out('File group prefix: "' . DEMO_FG_PREFIX . '"');
out('');

$conn->begin_transaction();
try {
    purgeDemoData($conn);
    $paths = ensureUploadDirs();

    $totals = ['tdp' => 0, 'tes' => 0, 'reg' => 0, 'cor' => 0, 'cog' => 0, 'files' => 0, 'billing' => 0];
    foreach (CAMPUSES as $ci => $campus) {
        $c = seedCampus($conn, $campus, $paths, $SCALE, $ci);
        foreach (['tdp', 'tes', 'reg', 'cor', 'cog', 'files'] as $k) {
            $totals[$k] += $c[$k];
        }
        $bill = seedBilling($conn, $campus, $SCALE['billing_per_campus'], $ci);
        $totals['billing'] += $bill;
        out("{$campus}: TDP {$c['tdp']}, TES {$c['tes']}, Reg {$c['reg']}, COR {$c['cor']}, COG {$c['cog']}, PDFs {$c['files']}, Billing {$bill}");
    }

    $annex = seedAnnexSubmissions($conn, $paths, $SCALE['annex_per_campus']);
    $csvCount = regenerateDemoCsvExports($conn, $paths);

    $conn->commit();

    $approxFiles = $totals['files'] + ($annex) + $csvCount;
    out('');
    out('=== DONE ===');
    out('TDP scholars: ' . $totals['tdp']);
    out('TES scholars: ' . $totals['tes']);
    out('Registrar rows: ' . $totals['reg']);
    out('COR records: ' . $totals['cor']);
    out('COG records: ' . $totals['cog']);
    out('Billing rows: ' . $totals['billing']);
    out('Annex 7 submissions: ' . $annex);
    out('CSV export files: ' . $csvCount);
    out('Placeholder PDF/XLSX files (approx): ' . $approxFiles);
    out('');
    out('Filter in UI: file group starts with "DEMO |"');
    out('Sample names: REYES, MARIA; DELA CRUZ, JUAN; ABACARO, ROSE ANN (generated pool)');
    out('');
    out('How to test:');
    out('  • Coordinator — CHED TDP/TES, Validate, Requirements, Verified scholars');
    out('  • Chairman — Dashboard counts, Annex 7, Verified scholars (billing tab)');
    out('  • Registrar — masterlist + COR/COG (separate from DEMO registrar rows in validation)');
    out('');
    out('Re-run this script anytime to refresh DEMO data.');
} catch (Throwable $e) {
    $conn->rollback();
    out('ERROR: ' . $e->getMessage());
    out($e->getFile() . ':' . $e->getLine());
    exit(1);
}

$conn->close();
