<?php
/**
 * File group batch registry (approval workflow) for CHED TDP/TES uploads.
 */

require_once __DIR__ . '/../users/registrar/inc/registrar_data.php';

if (!function_exists('schogms_file_group_meta_ensure_table')) {
    function schogms_file_group_meta_ensure_table(mysqli $conn): void
    {
        $conn->query(
            "CREATE TABLE IF NOT EXISTS schogms_file_group_batches (
                id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                program VARCHAR(8) NOT NULL,
                campus VARCHAR(128) NOT NULL,
                file_group VARCHAR(512) NOT NULL,
                status ENUM('pending','approved','denied') NOT NULL DEFAULT 'pending',
                uploaded_by_role VARCHAR(32) NULL,
                uploaded_by_name VARCHAR(255) NULL,
                uploaded_by_id INT UNSIGNED NULL,
                uploaded_at DATETIME NULL,
                review_notes TEXT NULL,
                reviewed_by VARCHAR(255) NULL,
                reviewed_at DATETIME NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                UNIQUE KEY uq_fg_batch (program, campus, file_group(191)),
                KEY idx_status (status),
                KEY idx_program_campus (program, campus)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci"
        );
        $migrations = [
            'uploaded_by_role' => 'VARCHAR(32) NULL AFTER status',
            'uploaded_by_name' => 'VARCHAR(255) NULL AFTER uploaded_by_role',
            'uploaded_by_id' => 'INT UNSIGNED NULL AFTER uploaded_by_name',
            'uploaded_at' => 'DATETIME NULL AFTER uploaded_by_id',
        ];
        foreach ($migrations as $col => $def) {
            $chk = $conn->query("SHOW COLUMNS FROM schogms_file_group_batches LIKE '{$col}'");
            if ($chk && $chk->num_rows === 0) {
                $conn->query("ALTER TABLE schogms_file_group_batches ADD COLUMN {$col} {$def}");
            }
        }
    }
}

if (!function_exists('schogms_file_group_meta_uploader_from_session')) {
    /** @return array{role: string, name: string, id: int|null} */
    function schogms_file_group_meta_uploader_from_session(): array
    {
        $name = trim((string) (isset($GLOBALS['fullname']) ? $GLOBALS['fullname'] : ($_SESSION['username'] ?? '')));
        $role = trim((string) (isset($GLOBALS['role']) ? $GLOBALS['role'] : ($_SESSION['role'] ?? '')));
        $id = (int) (isset($GLOBALS['user_id']) ? $GLOBALS['user_id'] : ($_SESSION['user_id'] ?? 0));

        return [
            'role' => $role,
            'name' => $name,
            'id' => $id > 0 ? $id : null,
        ];
    }
}

if (!function_exists('schogms_file_group_meta_role_label')) {
    function schogms_file_group_meta_role_label(string $role): string
    {
        return match (strtolower(trim($role))) {
            'chairman' => 'Chairman',
            'coordinator' => 'Coordinator',
            'registrar' => 'Registrar',
            'director' => 'Director',
            'dean' => 'Dean',
            'program-chair', 'program_chair', 'program chair' => 'Program chair',
            default => $role !== '' ? ucwords(str_replace(['_', '-'], ' ', $role)) : 'Unknown',
        };
    }
}

if (!function_exists('schogms_file_group_meta_uploader_display')) {
    /** @param array<string, mixed> $row */
    function schogms_file_group_meta_uploader_display(array $row): string
    {
        $name = trim((string) ($row['uploaded_by_name'] ?? ''));
        $role = trim((string) ($row['uploaded_by_role'] ?? ''));
        if ($name === '' && $role === '') {
            return '—';
        }
        $parts = [];
        if ($role !== '') {
            $parts[] = schogms_file_group_meta_role_label($role);
        }
        if ($name !== '') {
            $parts[] = $name;
        }

        return implode(' · ', $parts);
    }
}

if (!function_exists('schogms_file_group_meta_default_row')) {
    /** @return array<string, string> */
    function schogms_file_group_meta_default_row(): array
    {
        return [
            'status' => 'approved',
            'review_notes' => '',
            'reviewed_by' => '',
            'reviewed_at' => '',
            'uploaded_by_role' => '',
            'uploaded_by_name' => '',
            'uploaded_by_id' => '',
            'uploaded_at' => '',
        ];
    }
}

if (!function_exists('schogms_file_group_meta_program_table')) {
    /** @return array{table: string, campus_column: string}|null */
    function schogms_file_group_meta_program_table(string $program): ?array
    {
        $meta = schogms_registrar_program_list_table($program);

        return $meta ? ['table' => $meta['table'], 'campus_column' => $meta['campus_column']] : null;
    }
}

if (!function_exists('schogms_file_group_meta_batch_key')) {
    function schogms_file_group_meta_batch_key(string $program, string $campus, string $fileGroup): string
    {
        return strtolower(trim($program)) . '|' . strtoupper(trim($campus)) . '|' . trim($fileGroup);
    }
}

if (!function_exists('schogms_file_group_meta_register')) {
    /**
     * @param array{role?: string, name?: string, id?: int|null}|null $uploader
     */
    function schogms_file_group_meta_register(
        mysqli $conn,
        string $program,
        string $campus,
        string $fileGroup,
        string $status = 'pending',
        ?array $uploader = null
    ): void {
        schogms_file_group_meta_ensure_table($conn);
        $program = strtolower(trim($program));
        if (!in_array($program, ['tdp', 'tes'], true)) {
            return;
        }
        $campus = trim($campus);
        $fileGroup = trim($fileGroup);
        if ($campus === '' || $fileGroup === '') {
            return;
        }
        if (!in_array($status, ['pending', 'approved', 'denied'], true)) {
            $status = 'pending';
        }

        $upRole = '';
        $upName = '';
        $upId = null;
        if ($uploader !== null) {
            $upRole = trim((string) ($uploader['role'] ?? ''));
            $upName = trim((string) ($uploader['name'] ?? ''));
            $rawId = $uploader['id'] ?? null;
            $upId = $rawId !== null && (int) $rawId > 0 ? (int) $rawId : null;
        }
        $hasUploader = $upName !== '';

        $stmt = $conn->prepare(
            'INSERT INTO schogms_file_group_batches
                (program, campus, file_group, status, uploaded_by_role, uploaded_by_name, uploaded_by_id, uploaded_at)
             VALUES (?, ?, ?, ?, ?, ?, ?, IF(? = 1, NOW(), NULL))
             ON DUPLICATE KEY UPDATE
               status = IF(VALUES(status) = \'pending\', \'pending\', status),
               uploaded_by_role = IF(VALUES(uploaded_by_name) <> \'\', VALUES(uploaded_by_role), uploaded_by_role),
               uploaded_by_name = IF(VALUES(uploaded_by_name) <> \'\', VALUES(uploaded_by_name), uploaded_by_name),
               uploaded_by_id = IF(VALUES(uploaded_by_name) <> \'\', VALUES(uploaded_by_id), uploaded_by_id),
               uploaded_at = IF(VALUES(uploaded_by_name) <> \'\', VALUES(uploaded_at), uploaded_at),
               updated_at = CURRENT_TIMESTAMP'
        );
        if ($stmt) {
            $hasFlag = $hasUploader ? 1 : 0;
            $upIdBind = $upId ?? 0;
            $stmt->bind_param(
                'ssssssii',
                $program,
                $campus,
                $fileGroup,
                $status,
                $upRole,
                $upName,
                $upIdBind,
                $hasFlag
            );
            $stmt->execute();
            $stmt->close();
        }

        if ($status === 'pending' && $hasUploader) {
            require_once __DIR__ . '/schogms_notifications.php';
            schogms_notify_file_group_submitted($conn, $program, $campus, $fileGroup, [
                'role' => $upRole,
                'name' => $upName,
                'id' => $upId,
            ]);
        }
    }
}

if (!function_exists('schogms_file_group_meta_sync_from_masterlist')) {
    /** Register batches found in masterlist that have no meta row yet (legacy = approved). */
    function schogms_file_group_meta_sync_from_masterlist(mysqli $conn, string $program): void
    {
        $pt = schogms_file_group_meta_program_table($program);
        if ($pt === null) {
            return;
        }
        schogms_file_group_meta_ensure_table($conn);
        $program = strtolower(trim($program));
        $table = $pt['table'];
        $campusCol = $pt['campus_column'];
        $sql = "SELECT DISTINCT TRIM({$campusCol}) AS campus, TRIM(file_group) AS file_group
                FROM {$table}
                WHERE file_group IS NOT NULL AND TRIM(file_group) <> ''
                  AND TRIM({$campusCol}) <> ''";
        $res = $conn->query($sql);
        if (!$res) {
            return;
        }
        while ($row = $res->fetch_assoc()) {
            $campus = (string) ($row['campus'] ?? '');
            $fg = (string) ($row['file_group'] ?? '');
            if ($campus === '' || $fg === '') {
                continue;
            }
            $chk = $conn->prepare(
                'SELECT id FROM schogms_file_group_batches WHERE program = ? AND campus = ? AND file_group = ? LIMIT 1'
            );
            if (!$chk) {
                continue;
            }
            $chk->bind_param('sss', $program, $campus, $fg);
            $chk->execute();
            $chk->store_result();
            $exists = $chk->num_rows > 0;
            $chk->close();
            if (!$exists) {
                schogms_file_group_meta_register($conn, $program, $campus, $fg, 'approved');
            }
        }
    }
}

if (!function_exists('schogms_file_group_meta_aggregate_rows')) {
    /**
     * @return list<array<string, mixed>>
     */
    function schogms_file_group_meta_aggregate_rows(mysqli $conn, string $program): array
    {
        $data = schogms_registrar_program_list_fetch($program, null, $conn, true);
        $rows = [];
        foreach ($data['file_groups'] as $fg) {
            $rows[] = [
                'campus' => (string) ($fg['campus'] ?? ''),
                'file_group' => (string) ($fg['file_group'] ?? ''),
                'file_count' => (int) ($fg['file_count'] ?? 0),
                'total_entries' => (int) ($fg['total_entries'] ?? 0),
                'program_count' => (int) ($fg['program_count'] ?? 0),
                'programs_summary' => (string) ($fg['programs_summary'] ?? ''),
            ];
        }

        return $rows;
    }
}

if (!function_exists('schogms_file_group_meta_load_status_map')) {
    /** @return array<string, array<string, string>> */
    function schogms_file_group_meta_load_status_map(mysqli $conn, string $program): array
    {
        schogms_file_group_meta_ensure_table($conn);
        $program = strtolower(trim($program));
        $map = [];
        $stmt = $conn->prepare(
            'SELECT campus, file_group, status, review_notes, reviewed_by, reviewed_at,
                    uploaded_by_role, uploaded_by_name, uploaded_by_id, uploaded_at
             FROM schogms_file_group_batches WHERE program = ?'
        );
        if (!$stmt) {
            return $map;
        }
        $stmt->bind_param('s', $program);
        $stmt->execute();
        $res = $stmt->get_result();
        while ($row = $res->fetch_assoc()) {
            $key = schogms_file_group_meta_batch_key($program, (string) $row['campus'], (string) $row['file_group']);
            $map[$key] = [
                'status' => (string) ($row['status'] ?? 'pending'),
                'review_notes' => (string) ($row['review_notes'] ?? ''),
                'reviewed_by' => (string) ($row['reviewed_by'] ?? ''),
                'reviewed_at' => (string) ($row['reviewed_at'] ?? ''),
                'uploaded_by_role' => (string) ($row['uploaded_by_role'] ?? ''),
                'uploaded_by_name' => (string) ($row['uploaded_by_name'] ?? ''),
                'uploaded_by_id' => (string) ($row['uploaded_by_id'] ?? ''),
                'uploaded_at' => (string) ($row['uploaded_at'] ?? ''),
            ];
        }
        $stmt->close();

        return $map;
    }
}

if (!function_exists('schogms_file_group_meta_fetch_batch')) {
    /** @return array<string, string> */
    function schogms_file_group_meta_fetch_batch(
        mysqli $conn,
        string $program,
        string $campus,
        string $fileGroup
    ): array {
        $program = strtolower(trim($program));
        $map = schogms_file_group_meta_load_status_map($conn, $program);
        $key = schogms_file_group_meta_batch_key($program, $campus, $fileGroup);

        return $map[$key] ?? schogms_file_group_meta_default_row();
    }
}

if (!function_exists('schogms_file_group_meta_attach_uploaders')) {
    /**
     * @param list<array<string, mixed>> $rows
     * @return list<array<string, mixed>>
     */
    function schogms_file_group_meta_attach_uploaders(mysqli $conn, string $program, array $rows): array
    {
        $program = strtolower(trim($program));
        $map = schogms_file_group_meta_load_status_map($conn, $program);
        $out = [];
        foreach ($rows as $row) {
            $campus = (string) ($row['campus'] ?? '');
            $fg = (string) ($row['file_group'] ?? '');
            $key = schogms_file_group_meta_batch_key($program, $campus, $fg);
            $meta = $map[$key] ?? schogms_file_group_meta_default_row();
            $out[] = array_merge($row, $meta);
        }

        return $out;
    }
}

if (!function_exists('schogms_file_group_meta_list')) {
    /**
     * @return array{rows: list<array<string, mixed>>, counts: array{all: int, pending: int, approved: int, denied: int}}
     */
    function schogms_file_group_meta_list(mysqli $conn, string $program, ?string $statusFilter = null): array
    {
        $program = strtolower(trim($program)) === 'tes' ? 'tes' : 'tdp';
        schogms_file_group_meta_sync_from_masterlist($conn, $program);

        $aggregates = schogms_file_group_meta_aggregate_rows($conn, $program);
        $statusMap = schogms_file_group_meta_load_status_map($conn, $program);
        $counts = ['all' => 0, 'pending' => 0, 'approved' => 0, 'denied' => 0];
        $merged = [];

        foreach ($aggregates as $agg) {
            $campus = $agg['campus'];
            $fg = $agg['file_group'];
            if ($campus === '' || $fg === '') {
                continue;
            }
            $key = schogms_file_group_meta_batch_key($program, $campus, $fg);
            $meta = $statusMap[$key] ?? schogms_file_group_meta_default_row();
            $status = $meta['status'];
            $counts['all']++;
            if (isset($counts[$status])) {
                $counts[$status]++;
            }

            if ($statusFilter !== null && $statusFilter !== '' && $statusFilter !== 'all' && $status !== $statusFilter) {
                continue;
            }

            $merged[] = array_merge($agg, [
                'program' => $program,
                'status' => $status,
                'review_notes' => $meta['review_notes'],
                'reviewed_by' => $meta['reviewed_by'],
                'reviewed_at' => $meta['reviewed_at'],
                'uploaded_by_role' => $meta['uploaded_by_role'],
                'uploaded_by_name' => $meta['uploaded_by_name'],
                'uploaded_by_id' => $meta['uploaded_by_id'],
                'uploaded_at' => $meta['uploaded_at'],
            ]);
        }

        usort($merged, static function ($a, $b) {
            $c = strcasecmp((string) $a['campus'], (string) $b['campus']);

            return $c !== 0 ? $c : strcasecmp((string) $a['file_group'], (string) $b['file_group']);
        });

        return ['rows' => $merged, 'counts' => $counts];
    }
}

if (!function_exists('schogms_file_group_meta_set_status')) {
    function schogms_file_group_meta_set_status(
        mysqli $conn,
        string $program,
        string $campus,
        string $fileGroup,
        string $status,
        string $reviewerName,
        ?string $notes = null
    ): bool {
        schogms_file_group_meta_ensure_table($conn);
        $program = strtolower(trim($program));
        if (!in_array($status, ['pending', 'approved', 'denied'], true)) {
            return false;
        }
        schogms_file_group_meta_register($conn, $program, trim($campus), trim($fileGroup), $status);

        $stmt = $conn->prepare(
            'UPDATE schogms_file_group_batches
             SET status = ?, review_notes = ?, reviewed_by = ?, reviewed_at = NOW()
             WHERE program = ? AND campus = ? AND file_group = ?'
        );
        if (!$stmt) {
            return false;
        }
        $notes = $notes ?? '';
        $campus = trim($campus);
        $fileGroup = trim($fileGroup);
        $stmt->bind_param('ssssss', $status, $notes, $reviewerName, $program, $campus, $fileGroup);
        $ok = $stmt->execute();
        $stmt->close();

        if ($ok && in_array($status, ['approved', 'denied'], true)) {
            require_once __DIR__ . '/schogms_notifications.php';
            schogms_notify_file_group_reviewed($conn, $program, $campus, $fileGroup, $status, $reviewerName);
        }

        return $ok;
    }
}

if (!function_exists('schogms_file_group_meta_rename')) {
    function schogms_file_group_meta_rename(
        mysqli $conn,
        string $program,
        string $campus,
        string $oldFileGroup,
        string $newFileGroup
    ): bool {
        $pt = schogms_file_group_meta_program_table($program);
        if ($pt === null) {
            return false;
        }
        $oldFileGroup = trim($oldFileGroup);
        $newFileGroup = trim($newFileGroup);
        $campus = trim($campus);
        if ($oldFileGroup === '' || $newFileGroup === '' || $campus === '') {
            return false;
        }
        if ($oldFileGroup === $newFileGroup) {
            return true;
        }

        $program = strtolower(trim($program));
        $table = $pt['table'];
        $campusCol = $pt['campus_column'];

        $keepStatus = 'approved';
        $notes = '';
        $reviewer = '';
        $reviewedAt = null;
        $upRole = '';
        $upName = '';
        $upId = null;
        $upAt = null;
        $oldMeta = $conn->prepare(
            'SELECT status, review_notes, reviewed_by, reviewed_at,
                    uploaded_by_role, uploaded_by_name, uploaded_by_id, uploaded_at
             FROM schogms_file_group_batches
             WHERE program = ? AND campus = ? AND file_group = ? LIMIT 1'
        );
        if ($oldMeta) {
            $oldMeta->bind_param('sss', $program, $campus, $oldFileGroup);
            $oldMeta->execute();
            $resOld = $oldMeta->get_result();
            if ($resOld && ($or = $resOld->fetch_assoc())) {
                $keepStatus = (string) ($or['status'] ?? 'approved');
                $notes = (string) ($or['review_notes'] ?? '');
                $reviewer = (string) ($or['reviewed_by'] ?? '');
                $reviewedAt = $or['reviewed_at'] ?? null;
                $upRole = (string) ($or['uploaded_by_role'] ?? '');
                $upName = (string) ($or['uploaded_by_name'] ?? '');
                $upId = isset($or['uploaded_by_id']) ? (int) $or['uploaded_by_id'] : null;
                $upAt = $or['uploaded_at'] ?? null;
            }
            $oldMeta->close();
        }

        $conn->begin_transaction();
        try {
            $stmt = $conn->prepare(
                "UPDATE {$table} SET file_group = ? WHERE {$campusCol} = ? AND file_group = ?"
            );
            if (!$stmt) {
                throw new RuntimeException($conn->error);
            }
            $stmt->bind_param('sss', $newFileGroup, $campus, $oldFileGroup);
            $stmt->execute();
            $stmt->close();

            $del = $conn->prepare(
                'DELETE FROM schogms_file_group_batches WHERE program = ? AND campus = ? AND file_group = ?'
            );
            if ($del) {
                $del->bind_param('sss', $program, $campus, $oldFileGroup);
                $del->execute();
                $del->close();
            }

            schogms_file_group_meta_register(
                $conn,
                $program,
                $campus,
                $newFileGroup,
                $keepStatus,
                $upName !== '' ? ['role' => $upRole, 'name' => $upName, 'id' => $upId] : null
            );
            if ($upName !== '' || $reviewer !== '' || $notes !== '') {
                $up = $conn->prepare(
                    'UPDATE schogms_file_group_batches
                     SET review_notes = ?, reviewed_by = ?, reviewed_at = ?,
                         uploaded_by_role = ?, uploaded_by_name = ?, uploaded_by_id = ?, uploaded_at = ?
                     WHERE program = ? AND campus = ? AND file_group = ?'
                );
                if ($up) {
                    $upIdBind = $upId ?? 0;
                    $up->bind_param(
                        'ssssssisss',
                        $notes,
                        $reviewer,
                        $reviewedAt,
                        $upRole,
                        $upName,
                        $upIdBind,
                        $upAt,
                        $program,
                        $campus,
                        $newFileGroup
                    );
                    $up->execute();
                    $up->close();
                }
            }

            $conn->commit();

            return true;
        } catch (Throwable $e) {
            $conn->rollback();
            schogms_log_error('file_group rename: ' . $e->getMessage());

            return false;
        }
    }
}

if (!function_exists('schogms_file_group_meta_delete')) {
    function schogms_file_group_meta_delete(
        mysqli $conn,
        string $program,
        string $campus,
        string $fileGroup
    ): int {
        $pt = schogms_file_group_meta_program_table($program);
        if ($pt === null) {
            return 0;
        }
        $program = strtolower(trim($program));
        $campus = trim($campus);
        $fileGroup = trim($fileGroup);
        $table = $pt['table'];
        $campusCol = $pt['campus_column'];

        $conn->begin_transaction();
        try {
            $stmt = $conn->prepare("DELETE FROM {$table} WHERE {$campusCol} = ? AND file_group = ?");
            if (!$stmt) {
                throw new RuntimeException($conn->error);
            }
            $stmt->bind_param('ss', $campus, $fileGroup);
            $stmt->execute();
            $deleted = $stmt->affected_rows;
            $stmt->close();

            $meta = $conn->prepare(
                'DELETE FROM schogms_file_group_batches WHERE program = ? AND campus = ? AND file_group = ?'
            );
            if ($meta) {
                $meta->bind_param('sss', $program, $campus, $fileGroup);
                $meta->execute();
                $meta->close();
            }

            $conn->commit();

            return $deleted;
        } catch (Throwable $e) {
            $conn->rollback();
            schogms_log_error('file_group delete: ' . $e->getMessage());

            return 0;
        }
    }
}

if (!function_exists('schogms_file_group_status_badge_class')) {
    function schogms_file_group_status_badge_class(string $status): string
    {
        return match ($status) {
            'approved' => 'success',
            'denied' => 'danger',
            default => 'warning',
        };
    }
}
