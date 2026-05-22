<?php
/**
 * MySQL data access for registrar pages (replaces MongoDB for mysql-auth users).
 */

if (!function_exists('schogms_registrar_db')) {
    function schogms_registrar_db(): mysqli
    {
        static $conn = null;
        if ($conn instanceof mysqli) {
            return $conn;
        }
        require_once __DIR__ . '/../config/conn.php';
        if (!isset($conn) || !($conn instanceof mysqli)) {
            throw new RuntimeException('Registrar database connection unavailable.');
        }

        return $conn;
    }
}

if (!function_exists('schogms_registrar_campus_clause')) {
    function schogms_registrar_campus_clause(mysqli $db, string $column, ?string $campus): string
    {
        $campus = trim((string) $campus);
        if ($campus === '') {
            return '';
        }

        return ' AND ' . $column . " = '" . $db->real_escape_string($campus) . "'";
    }
}

if (!function_exists('schogms_registrar_masterlist_categories')) {
    function schogms_registrar_masterlist_categories(?string $campus = null): array
    {
        $db = schogms_registrar_db();
        $sql = "SELECT DISTINCT filename FROM registrar_master_list WHERE filename IS NOT NULL AND filename <> ''"
            . schogms_registrar_campus_clause($db, 'campus', $campus)
            . ' ORDER BY filename ASC';
        $categories = [];
        $result = $db->query($sql);
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $categories[] = $row['filename'];
            }
        }

        return $categories;
    }
}

if (!function_exists('schogms_registrar_masterlist_fetch')) {
    /**
     * @param array<string, mixed> $params
     * @return array{data: array<int, array<string, mixed>>, total: int, pages: int, page: int, limit: int}
     */
    function schogms_registrar_masterlist_fetch(array $params, ?string $campus = null): array
    {
        $db = schogms_registrar_db();
        $page = max(1, (int) ($params['page'] ?? 1));
        $limit = (int) ($params['limit'] ?? 100);
        $allowedLimits = [10, 25, 50, 100, 200, 500];
        if (!in_array($limit, $allowedLimits, true)) {
            $limit = 100;
        }
        $offset = ($page - 1) * $limit;

        $where = ['1=1'];
        $category = trim((string) ($params['category'] ?? ''));
        $academicYear = trim((string) ($params['academic_year'] ?? ''));
        $semester = trim((string) ($params['semester'] ?? ''));
        $search = trim((string) ($params['search'] ?? ''));

        if ($category !== '') {
            $where[] = "filename = '" . $db->real_escape_string($category) . "'";
        }
        if ($academicYear !== '') {
            $where[] = "file_group LIKE '%" . $db->real_escape_string($academicYear) . "%'";
        }
        if ($semester !== '') {
            $where[] = "file_group LIKE '%" . $db->real_escape_string($semester) . "%'";
        }
        if ($search !== '') {
            $s = $db->real_escape_string($search);
            $where[] = "(last_name LIKE '%{$s}%' OR first_name LIKE '%{$s}%' OR middle_name LIKE '%{$s}%'"
                . " OR id_number LIKE '%{$s}%' OR course LIKE '%{$s}%' OR scholarship LIKE '%{$s}%')";
        }
        $campusClause = schogms_registrar_campus_clause($db, 'campus', $campus);
        if ($campusClause !== '') {
            $where[] = trim(str_replace('AND', '', $campusClause));
        }

        $whereSql = implode(' AND ', $where);

        $countSql = "SELECT COUNT(*) AS n FROM registrar_master_list WHERE {$whereSql}";
        $total = 0;
        if ($countRes = $db->query($countSql)) {
            $total = (int) ($countRes->fetch_assoc()['n'] ?? 0);
        }

        $sql = "SELECT * FROM registrar_master_list WHERE {$whereSql}"
            . " ORDER BY last_name ASC, first_name ASC, middle_name ASC"
            . " LIMIT {$limit} OFFSET {$offset}";
        $data = [];
        if ($result = $db->query($sql)) {
            while ($row = $result->fetch_assoc()) {
                $data[] = $row;
            }
        }

        $pages = $limit > 0 ? (int) max(1, ceil($total / $limit)) : 1;

        return [
            'data' => $data,
            'total' => $total,
            'pages' => $pages,
            'page' => $page,
            'limit' => $limit,
        ];
    }
}

if (!function_exists('schogms_registrar_document_fetch')) {
    /**
     * @param array<string, mixed> $params
     * @return array{
     *   rows: array<int, array<string, mixed>>,
     *   total: int,
     *   per_page: int,
     *   page: int,
     *   total_pages: int,
     *   display_count: int
     * }
     */
    function schogms_registrar_document_fetch(array $params, ?string $campus = null): array
    {
        $db = schogms_registrar_db();
        $page = max(1, (int) ($params['page'] ?? 1));
        $perPage = (int) ($params['per_page'] ?? 20);
        if (!in_array($perPage, [10, 20, 50], true)) {
            $perPage = 20;
        }
        $offset = ($page - 1) * $perPage;

        $where = ['1=1'];
        $category = trim((string) ($params['category'] ?? ''));
        $filterCampus = trim((string) ($params['campus'] ?? ''));
        $academicYear = trim((string) ($params['academic_year'] ?? ''));
        $semester = trim((string) ($params['semester'] ?? ''));
        $lastName = trim((string) ($params['lastname'] ?? ''));
        $firstName = trim((string) ($params['firstname'] ?? ''));
        $search = trim((string) ($params['search'] ?? ''));

        if ($category !== '') {
            $where[] = "category = '" . $db->real_escape_string($category) . "'";
        }
        if ($filterCampus !== '') {
            $where[] = "campus = '" . $db->real_escape_string($filterCampus) . "'";
        } elseif (trim((string) $campus) !== '') {
            $where[] = "campus = '" . $db->real_escape_string((string) $campus) . "'";
        }
        if ($academicYear !== '') {
            $where[] = "file_group LIKE '%" . $db->real_escape_string($academicYear) . "%'";
        }
        if ($semester !== '') {
            $where[] = "file_group LIKE '%" . $db->real_escape_string($semester) . "%'";
        }
        if ($lastName !== '') {
            $where[] = "file_name LIKE '%" . $db->real_escape_string($lastName) . "%'";
        }
        if ($firstName !== '') {
            $where[] = "file_name LIKE '%" . $db->real_escape_string($firstName) . "%'";
        }
        if ($search !== '') {
            $s = $db->real_escape_string($search);
            $where[] = "(file_name LIKE '%{$s}%' OR file_group LIKE '%{$s}%' OR campus LIKE '%{$s}%' OR category LIKE '%{$s}%')";
        }

        $whereSql = implode(' AND ', $where);

        $total = 0;
        if ($countRes = $db->query("SELECT COUNT(*) AS n FROM document_uploads WHERE {$whereSql}")) {
            $total = (int) ($countRes->fetch_assoc()['n'] ?? 0);
        }

        $sql = "SELECT id, campus, file_group, category, file_name, file_path, uploaded_at"
            . " FROM document_uploads WHERE {$whereSql}"
            . " ORDER BY file_name ASC"
            . " LIMIT {$perPage} OFFSET {$offset}";
        $rows = [];
        if ($result = $db->query($sql)) {
            while ($row = $result->fetch_assoc()) {
                $fg = (string) ($row['file_group'] ?? '');
                $academic = '';
                $sem = '';
                if (preg_match('/\d{4}-\d{4}/', $fg, $m)) {
                    $academic = $m[0];
                }
                if (stripos($fg, '2nd') !== false) {
                    $sem = '2nd Semester';
                } elseif (stripos($fg, '1st') !== false) {
                    $sem = '1st Semester';
                } elseif (stripos($fg, 'summer') !== false) {
                    $sem = 'Summer';
                }
                $filePath = (string) ($row['file_path'] ?? '');
                if ($filePath !== '' && $filePath[0] !== '/') {
                    $filePath = '../../' . ltrim($filePath, '/');
                }
                $rows[] = [
                    'category' => $row['category'] ?? '',
                    'original_name' => $row['file_name'] ?? '',
                    'academic_year' => $academic !== '' ? $academic : '—',
                    'semester' => $sem !== '' ? $sem : '—',
                    'campus' => $row['campus'] ?? '',
                    'uploaded_by' => $fg !== '' ? $fg : 'Registrar',
                    'uploaded_at' => $row['uploaded_at'] ?? '',
                    'file_path' => $filePath,
                ];
            }
        }

        $totalPages = $perPage > 0 ? (int) max(1, ceil($total / $perPage)) : 1;

        return [
            'rows' => $rows,
            'total' => $total,
            'per_page' => $perPage,
            'page' => $page,
            'total_pages' => $totalPages,
            'display_count' => count($rows),
        ];
    }
}

if (!function_exists('schogms_registrar_dashboard_counts')) {
    function schogms_registrar_dashboard_counts(?string $campus = null): array
    {
        $db = schogms_registrar_db();
        $campusSql = schogms_registrar_campus_clause($db, 'campus', $campus);

        $masterlist = 0;
        $cor = 0;
        $cog = 0;
        $groups = 0;

        if ($r = $db->query('SELECT COUNT(*) AS n FROM registrar_master_list WHERE 1=1' . $campusSql)) {
            $masterlist = (int) ($r->fetch_assoc()['n'] ?? 0);
        }
        if ($r = $db->query("SELECT COUNT(*) AS n FROM document_uploads WHERE category='COR'" . $campusSql)) {
            $cor = (int) ($r->fetch_assoc()['n'] ?? 0);
        }
        if ($r = $db->query("SELECT COUNT(*) AS n FROM document_uploads WHERE category='COG'" . $campusSql)) {
            $cog = (int) ($r->fetch_assoc()['n'] ?? 0);
        }
        if ($r = $db->query('SELECT COUNT(DISTINCT file_group) AS n FROM document_uploads WHERE file_group IS NOT NULL AND file_group <> ""' . $campusSql)) {
            $groups = (int) ($r->fetch_assoc()['n'] ?? 0);
        }

        $courses = 0;
        if ($r = $db->query('SELECT COUNT(DISTINCT course) AS n FROM registrar_master_list WHERE course IS NOT NULL AND course <> ""' . $campusSql)) {
            $courses = (int) ($r->fetch_assoc()['n'] ?? 0);
        }

        return [
            'masterlist' => $masterlist,
            'cor' => $cor,
            'cog' => $cog,
            'file_groups' => $groups,
            'courses' => $courses,
        ];
    }
}
