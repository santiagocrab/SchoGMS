<?php
/**
 * Campus access catalog (colleges/courses) and assignment scoping helpers.
 */
require_once __DIR__ . '/../config/schogms_helpers.php';

if (!function_exists('schogms_campus_catalog')) {
    /** @return array<string, array<string, list<string>>> */
    function schogms_campus_catalog(): array
    {
        static $catalog = null;
        if ($catalog === null) {
            $path = __DIR__ . '/../config/campus_access_catalog.php';
            $catalog = is_readable($path) ? require $path : [];
        }

        return is_array($catalog) ? $catalog : [];
    }
}

if (!function_exists('schogms_table_exists')) {
    function schogms_table_exists(mysqli $conn, string $table): bool
    {
        $safe = $conn->real_escape_string($table);
        $res = $conn->query("SHOW TABLES LIKE '{$safe}'");

        return $res !== false && $res->num_rows > 0;
    }
}

if (!function_exists('schogms_campus_catalog_names')) {
    /** @return list<string> */
    function schogms_campus_catalog_names(): array
    {
        return array_keys(schogms_campus_catalog());
    }
}

if (!function_exists('schogms_legacy_campus_aliases')) {
    /**
     * Maps short coordinator/registrar campus codes to catalog campus names.
     *
     * @return array<string, string>
     */
    function schogms_legacy_campus_aliases(): array
    {
        return [
            'ACCESS' => 'ACCESS',
            'ISULAN' => 'Isulan Campus',
            'KALAMANSIG' => 'Kalamansig',
            'BAGUMBAYAN' => 'Bagumbayan',
            'PALIMBANG' => 'Palimbang Campus',
            'TACURONG' => 'Tacurong Campus',
            'LUTAYAN' => 'Lutayan',
        ];
    }
}

if (!function_exists('schogms_resolve_catalog_campus')) {
    /** Resolve legacy sheet_name / campus code to a catalog campus label if possible. */
    function schogms_resolve_catalog_campus(string $campusOrCode): string
    {
        $campusOrCode = trim($campusOrCode);
        if ($campusOrCode === '') {
            return '';
        }
        $aliases = schogms_legacy_campus_aliases();
        $upper = strtoupper($campusOrCode);
        if (isset($aliases[$upper])) {
            return $aliases[$upper];
        }
        foreach (schogms_campus_catalog_names() as $name) {
            if (strcasecmp($name, $campusOrCode) === 0) {
                return $name;
            }
        }

        return $campusOrCode;
    }
}

if (!function_exists('schogms_normalize_access_label')) {
    function schogms_normalize_access_label(string $value): string
    {
        $value = trim($value);
        $value = preg_replace('/\s+/u', ' ', $value) ?? $value;

        return mb_strtolower($value, 'UTF-8');
    }
}

if (!function_exists('schogms_course_enrolled_matches')) {
    /**
     * Match masterlist course_program_enrolled to a catalog course name.
     */
    function schogms_course_enrolled_matches(string $enrolled, string $catalogCourse): bool
    {
        $a = schogms_normalize_access_label($enrolled);
        $b = schogms_normalize_access_label($catalogCourse);
        if ($a === '' || $b === '') {
            return false;
        }
        if ($a === $b) {
            return true;
        }

        return str_contains($a, $b) || str_contains($b, $a);
    }
}

if (!function_exists('schogms_ensure_campus_access_tables')) {
    function schogms_ensure_campus_access_tables(mysqli $conn): void
    {
        $conn->query(
            'CREATE TABLE IF NOT EXISTS schogms_colleges (
                id INT AUTO_INCREMENT PRIMARY KEY,
                campus VARCHAR(128) NOT NULL,
                college_name VARCHAR(512) NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                UNIQUE KEY uq_campus_college (campus, college_name(255))
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci'
        );

        $conn->query(
            'CREATE TABLE IF NOT EXISTS schogms_courses (
                id INT AUTO_INCREMENT PRIMARY KEY,
                college_id INT NOT NULL,
                course_name VARCHAR(512) NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                UNIQUE KEY uq_college_course (college_id, course_name(255)),
                CONSTRAINT fk_schogms_course_college
                    FOREIGN KEY (college_id) REFERENCES schogms_colleges(id) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci'
        );

        if (schogms_table_exists($conn, 'assigned_dean')) {
            $collegeCol = $conn->query("SHOW COLUMNS FROM assigned_dean LIKE 'college_name'");
            if ($collegeCol && $collegeCol->num_rows === 0) {
                $conn->query(
                    'ALTER TABLE assigned_dean
                     ADD COLUMN college_name VARCHAR(512) NULL AFTER campus'
                );
            }
        }

        if (schogms_table_exists($conn, 'assigned_program_chairs')) {
            $apcCollege = $conn->query("SHOW COLUMNS FROM assigned_program_chairs LIKE 'college_name'");
            if ($apcCollege && $apcCollege->num_rows === 0) {
                $conn->query(
                    'ALTER TABLE assigned_program_chairs
                     ADD COLUMN college_name VARCHAR(512) NULL AFTER campus'
                );
            }
        }
    }
}

if (!function_exists('schogms_seed_campus_access_catalog')) {
    function schogms_seed_campus_access_catalog(mysqli $conn): int
    {
        schogms_ensure_campus_access_tables($conn);
        $inserted = 0;
        $catalog = schogms_campus_catalog();

        $colStmt = $conn->prepare(
            'INSERT IGNORE INTO schogms_colleges (campus, college_name) VALUES (?, ?)'
        );
        $courseStmt = $conn->prepare(
            'INSERT IGNORE INTO schogms_courses (college_id, course_name)
             SELECT id, ? FROM schogms_colleges WHERE campus = ? AND college_name = ? LIMIT 1'
        );

        foreach ($catalog as $campus => $colleges) {
            foreach ($colleges as $collegeName => $courses) {
                $colStmt->bind_param('ss', $campus, $collegeName);
                $colStmt->execute();
                if ($colStmt->affected_rows > 0) {
                    $inserted++;
                }
                foreach ($courses as $courseName) {
                    $courseStmt->bind_param('sss', $courseName, $campus, $collegeName);
                    $courseStmt->execute();
                    if ($courseStmt->affected_rows > 0) {
                        $inserted++;
                    }
                }
            }
        }

        $colStmt->close();
        $courseStmt->close();

        return $inserted;
    }
}

if (!function_exists('schogms_get_colleges_for_campus')) {
    /** @return list<array{id:int, campus:string, college_name:string}> */
    function schogms_get_colleges_for_campus(mysqli $conn, string $campus): array
    {
        schogms_ensure_campus_access_tables($conn);
        $rows = [];
        $stmt = $conn->prepare(
            'SELECT id, campus, college_name FROM schogms_colleges
             WHERE UPPER(TRIM(campus)) = UPPER(TRIM(?))
             ORDER BY college_name ASC'
        );
        $stmt->bind_param('s', $campus);
        $stmt->execute();
        $res = $stmt->get_result();
        while ($row = $res->fetch_assoc()) {
            $rows[] = $row;
        }
        $stmt->close();

        return $rows;
    }
}

if (!function_exists('schogms_get_courses_for_college')) {
    /** @return list<array{id:int, course_name:string}> */
    function schogms_get_courses_for_college(mysqli $conn, string $campus, string $collegeName): array
    {
        schogms_ensure_campus_access_tables($conn);
        $rows = [];
        $stmt = $conn->prepare(
            'SELECT c.id, c.course_name
             FROM schogms_courses c
             INNER JOIN schogms_colleges col ON col.id = c.college_id
             WHERE UPPER(TRIM(col.campus)) = UPPER(TRIM(?))
               AND col.college_name = ?
             ORDER BY c.course_name ASC'
        );
        $stmt->bind_param('ss', $campus, $collegeName);
        $stmt->execute();
        $res = $stmt->get_result();
        while ($row = $res->fetch_assoc()) {
            $rows[] = $row;
        }
        $stmt->close();

        return $rows;
    }
}

if (!function_exists('schogms_get_course_names_for_college')) {
    /** @return list<string> */
    function schogms_get_course_names_for_college(mysqli $conn, string $campus, string $collegeName): array
    {
        $names = [];
        foreach (schogms_get_courses_for_college($conn, $campus, $collegeName) as $row) {
            $names[] = (string) $row['course_name'];
        }

        return $names;
    }
}

if (!function_exists('schogms_masterlist_course_in_college')) {
    function schogms_masterlist_course_in_college(string $enrolled, array $catalogCourses): bool
    {
        foreach ($catalogCourses as $catalog) {
            if (schogms_course_enrolled_matches($enrolled, $catalog)) {
                return true;
            }
        }

        return false;
    }
}

if (!function_exists('schogms_resolve_dean_college_name')) {
    /**
     * Dean row may use college_name (new) or course_program (legacy file group).
     */
    function schogms_resolve_dean_college_name(array $deanRow): string
    {
        $college = trim((string) ($deanRow['college_name'] ?? ''));
        if ($college !== '') {
            return $college;
        }

        return trim((string) ($deanRow['course_program'] ?? ''));
    }
}
