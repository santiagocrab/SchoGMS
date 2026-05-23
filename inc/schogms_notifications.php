<?php
/**
 * In-app notifications + email for file group upload / review workflow.
 */

require_once __DIR__ . '/../config/mail.php';

if (!function_exists('schogms_notifications_ensure_table')) {
    function schogms_notifications_ensure_table(mysqli $conn): void
    {
        $conn->query(
            "CREATE TABLE IF NOT EXISTS schogms_notifications (
                id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                user_id INT UNSIGNED NOT NULL,
                type VARCHAR(64) NOT NULL,
                title VARCHAR(255) NOT NULL,
                message TEXT NOT NULL,
                link_url VARCHAR(512) NULL,
                program VARCHAR(8) NULL,
                campus VARCHAR(128) NULL,
                file_group VARCHAR(512) NULL,
                is_read TINYINT(1) NOT NULL DEFAULT 0,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                KEY idx_user_read (user_id, is_read, created_at),
                KEY idx_user_created (user_id, created_at)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci"
        );
    }
}

if (!function_exists('schogms_notifications_upload_roles')) {
    /** Roles that see file-group notification bell. */
    function schogms_notifications_upload_roles(): array
    {
        return ['chairman', 'coordinator', 'registrar'];
    }
}

if (!function_exists('schogms_notifications_role_show_bell')) {
    function schogms_notifications_role_show_bell(string $role): bool
    {
        return in_array(strtolower(trim($role)), schogms_notifications_upload_roles(), true);
    }
}

if (!function_exists('schogms_notifications_create')) {
    function schogms_notifications_create(
        mysqli $conn,
        int $userId,
        string $type,
        string $title,
        string $message,
        ?string $linkUrl = null,
        ?string $program = null,
        ?string $campus = null,
        ?string $fileGroup = null
    ): void {
        if ($userId < 1) {
            return;
        }
        schogms_notifications_ensure_table($conn);
        $stmt = $conn->prepare(
            'INSERT INTO schogms_notifications (user_id, type, title, message, link_url, program, campus, file_group)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?)'
        );
        if (!$stmt) {
            return;
        }
        $stmt->bind_param('isssssss', $userId, $type, $title, $message, $linkUrl, $program, $campus, $fileGroup);
        $stmt->execute();
        $stmt->close();
    }
}

if (!function_exists('schogms_notifications_unread_count')) {
    function schogms_notifications_unread_count(mysqli $conn, int $userId): int
    {
        if ($userId < 1) {
            return 0;
        }
        schogms_notifications_ensure_table($conn);
        $stmt = $conn->prepare('SELECT COUNT(*) AS n FROM schogms_notifications WHERE user_id = ? AND is_read = 0');
        if (!$stmt) {
            return 0;
        }
        $stmt->bind_param('i', $userId);
        $stmt->execute();
        $res = $stmt->get_result();
        $n = $res ? (int) ($res->fetch_assoc()['n'] ?? 0) : 0;
        $stmt->close();

        return $n;
    }
}

if (!function_exists('schogms_notifications_list')) {
    /**
     * @return list<array<string, mixed>>
     */
    function schogms_notifications_list(mysqli $conn, int $userId, int $limit = 25): array
    {
        if ($userId < 1) {
            return [];
        }
        schogms_notifications_ensure_table($conn);
        $limit = max(1, min(50, $limit));
        $stmt = $conn->prepare(
            'SELECT id, type, title, message, link_url, program, campus, file_group, is_read, created_at
             FROM schogms_notifications WHERE user_id = ?
             ORDER BY created_at DESC, id DESC LIMIT ' . $limit
        );
        if (!$stmt) {
            return [];
        }
        $stmt->bind_param('i', $userId);
        $stmt->execute();
        $res = $stmt->get_result();
        $rows = [];
        while ($res && ($row = $res->fetch_assoc())) {
            $rows[] = $row;
        }
        $stmt->close();

        return $rows;
    }
}

if (!function_exists('schogms_notifications_mark_read')) {
    function schogms_notifications_mark_read(mysqli $conn, int $userId, ?int $notificationId = null): void
    {
        if ($userId < 1) {
            return;
        }
        schogms_notifications_ensure_table($conn);
        if ($notificationId !== null && $notificationId > 0) {
            $stmt = $conn->prepare(
                'UPDATE schogms_notifications SET is_read = 1 WHERE id = ? AND user_id = ?'
            );
            if ($stmt) {
                $stmt->bind_param('ii', $notificationId, $userId);
                $stmt->execute();
                $stmt->close();
            }

            return;
        }
        $stmt = $conn->prepare('UPDATE schogms_notifications SET is_read = 1 WHERE user_id = ? AND is_read = 0');
        if ($stmt) {
            $stmt->bind_param('i', $userId);
            $stmt->execute();
            $stmt->close();
        }
    }
}

if (!function_exists('schogms_notifications_chairman_users')) {
    /** @return list<array{user_id: int, email: string, name: string}> */
    function schogms_notifications_chairman_users(mysqli $conn): array
    {
        $users = [];
        $res = $conn->query(
            "SELECT user_id, email, name FROM users
             WHERE LOWER(TRIM(role)) = 'chairman' AND LOWER(TRIM(COALESCE(status, ''))) = 'active'"
        );
        if ($res) {
            while ($row = $res->fetch_assoc()) {
                $users[] = [
                    'user_id' => (int) ($row['user_id'] ?? 0),
                    'email' => trim((string) ($row['email'] ?? '')),
                    'name' => trim((string) ($row['name'] ?? '')),
                ];
            }
        }

        return $users;
    }
}

if (!function_exists('schogms_notifications_user_by_id')) {
    /** @return array{user_id: int, email: string, name: string}|null */
    function schogms_notifications_user_by_id(mysqli $conn, int $userId): ?array
    {
        if ($userId < 1) {
            return null;
        }
        $stmt = $conn->prepare('SELECT user_id, email, name FROM users WHERE user_id = ? LIMIT 1');
        if (!$stmt) {
            return null;
        }
        $stmt->bind_param('i', $userId);
        $stmt->execute();
        $res = $stmt->get_result();
        $row = $res ? $res->fetch_assoc() : null;
        $stmt->close();
        if (!$row) {
            return null;
        }

        return [
            'user_id' => (int) ($row['user_id'] ?? 0),
            'email' => trim((string) ($row['email'] ?? '')),
            'name' => trim((string) ($row['name'] ?? '')),
        ];
    }
}

if (!function_exists('schogms_notifications_file_group_link')) {
    function schogms_notifications_file_group_link(string $role, string $program, string $status = 'pending'): string
    {
        $role = strtolower(trim($role));
        $program = strtolower(trim($program)) === 'tes' ? 'tes' : 'tdp';
        $status = in_array($status, ['pending', 'approved', 'denied', 'all'], true) ? $status : 'pending';

        return match ($role) {
            'chairman' => 'file_groups.php?program=' . rawurlencode($program) . '&status=' . rawurlencode($status),
            'coordinator' => $program === 'tes' ? 'ched_masterlist_tes.php' : 'ched_masterlist.php',
            'registrar' => 'program_list.php?program=' . rawurlencode($program),
            default => 'index.php',
        };
    }
}

if (!function_exists('schogms_notify_file_group_submitted')) {
    /**
     * Coordinator/registrar upload → pending: notify chairman + uploader.
     *
     * @param array{role?: string, name?: string, id?: int|null} $uploader
     */
    function schogms_notify_file_group_submitted(
        mysqli $conn,
        string $program,
        string $campus,
        string $fileGroup,
        array $uploader
    ): void {
        require_once __DIR__ . '/schogms_file_group_meta.php';
        require_once __DIR__ . '/../config/email_templates.php';

        $program = strtolower(trim($program)) === 'tes' ? 'tes' : 'tdp';
        $campus = trim($campus);
        $fileGroup = trim($fileGroup);
        $programLabel = strtoupper($program);
        $uploaderName = trim((string) ($uploader['name'] ?? 'A campus user'));
        $uploaderRole = trim((string) ($uploader['role'] ?? ''));
        $uploaderId = isset($uploader['id']) ? (int) $uploader['id'] : 0;

        $chairLink = schogms_notifications_file_group_link('chairman', $program, 'pending');
        $chairTitle = 'New file group uploaded';
        $chairMsg = "{$uploaderName} uploaded {$programLabel} file group \"{$fileGroup}\" for {$campus}. Please review and approve.";

        foreach (schogms_notifications_chairman_users($conn) as $chair) {
            if ($chair['user_id'] < 1) {
                continue;
            }
            schogms_notifications_create(
                $conn,
                $chair['user_id'],
                'file_group_pending',
                $chairTitle,
                $chairMsg,
                $chairLink,
                $program,
                $campus,
                $fileGroup
            );
            if ($chair['email'] !== '' && filter_var($chair['email'], FILTER_VALIDATE_EMAIL)) {
                $html = schogms_email_file_group_pending_chairman([
                    'name' => $chair['name'] !== '' ? $chair['name'] : 'Chairman',
                    'program' => $programLabel,
                    'campus' => $campus,
                    'file_group' => $fileGroup,
                    'uploader' => schogms_file_group_meta_role_label($uploaderRole) . ($uploaderName !== '' ? ' · ' . $uploaderName : ''),
                    'link' => schogms_app_base_url() . '/users/chairman/' . $chairLink,
                ]);
                schogms_send_mail(
                    $chair['email'],
                    "SchoGMS: New {$programLabel} upload awaiting your review",
                    $html,
                    $chair['name'] !== '' ? $chair['name'] : null
                );
            }
        }

        if ($uploaderId > 0) {
            $upLink = schogms_notifications_file_group_link('coordinator', $program, 'pending');
            if ($uploaderRole === 'registrar') {
                $upLink = schogms_notifications_file_group_link('registrar', $program, 'pending');
            } elseif ($uploaderRole === 'chairman') {
                $upLink = schogms_notifications_file_group_link('chairman', $program, 'pending');
            }
            schogms_notifications_create(
                $conn,
                $uploaderId,
                'file_group_waiting',
                'File group submitted',
                "Your {$programLabel} file group \"{$fileGroup}\" was submitted and is waiting for chairman approval.",
                $upLink,
                $program,
                $campus,
                $fileGroup
            );
            $upUser = schogms_notifications_user_by_id($conn, $uploaderId);
            if ($upUser && $upUser['email'] !== '' && filter_var($upUser['email'], FILTER_VALIDATE_EMAIL)) {
                $rolePath = match ($uploaderRole) {
                    'registrar' => 'registrar',
                    'chairman' => 'chairman',
                    default => 'coordinator',
                };
                $html = schogms_email_file_group_waiting([
                    'name' => $upUser['name'] !== '' ? $upUser['name'] : $uploaderName,
                    'program' => $programLabel,
                    'campus' => $campus,
                    'file_group' => $fileGroup,
                    'link' => schogms_app_base_url() . '/users/' . $rolePath . '/' . $upLink,
                ]);
                schogms_send_mail(
                    $upUser['email'],
                    "SchoGMS: Your {$programLabel} file group was submitted",
                    $html,
                    $upUser['name'] !== '' ? $upUser['name'] : null
                );
            }
        }
    }
}

if (!function_exists('schogms_notify_annex7_submitted')) {
    /**
     * Coordinator Annex 7 upload → notify chairman (+ confirmation to uploader).
     *
     * @param array{id?: int, name?: string, email?: string} $uploader
     */
    function schogms_notify_annex7_submitted(
        mysqli $conn,
        string $campus,
        string $fileName,
        array $uploader
    ): void {
        require_once __DIR__ . '/../config/email_templates.php';

        $campus = trim($campus);
        $fileName = trim($fileName);
        $uploaderName = trim((string) ($uploader['name'] ?? 'Coordinator'));
        $uploaderId = isset($uploader['id']) ? (int) $uploader['id'] : 0;
        $chairLink = 'annex7.php?status=pending';
        $chairTitle = 'New Annex 7 submitted';
        $chairMsg = "{$uploaderName} submitted Annex 7 \"{$fileName}\" for campus {$campus}. Please review and approve.";

        foreach (schogms_notifications_chairman_users($conn) as $chair) {
            if ($chair['user_id'] < 1) {
                continue;
            }
            schogms_notifications_create(
                $conn,
                $chair['user_id'],
                'annex7_pending',
                $chairTitle,
                $chairMsg,
                $chairLink,
                null,
                $campus,
                $fileName
            );
            if ($chair['email'] !== '' && filter_var($chair['email'], FILTER_VALIDATE_EMAIL)) {
                $html = schogms_email_file_group_pending_chairman([
                    'name' => $chair['name'] !== '' ? $chair['name'] : 'Chairman',
                    'program' => 'Annex 7',
                    'campus' => $campus,
                    'file_group' => $fileName,
                    'uploader' => 'Coordinator · ' . $uploaderName,
                    'link' => schogms_app_base_url() . '/users/chairman/' . $chairLink,
                ]);
                schogms_send_mail(
                    $chair['email'],
                    'SchoGMS: New Annex 7 awaiting your review',
                    $html,
                    $chair['name'] !== '' ? $chair['name'] : null
                );
            }
        }

        if ($uploaderId > 0) {
            schogms_notifications_create(
                $conn,
                $uploaderId,
                'annex7_waiting',
                'Annex 7 submitted',
                "Your Annex 7 file \"{$fileName}\" was submitted and is waiting for chairman approval.",
                'submit_form.php',
                null,
                $campus,
                $fileName
            );
        }
    }
}

if (!function_exists('schogms_notify_annex7_reviewed')) {
    function schogms_notify_annex7_reviewed(
        mysqli $conn,
        int $submissionId,
        string $status,
        string $reviewerName
    ): void {
        require_once __DIR__ . '/../config/email_templates.php';
        require_once __DIR__ . '/schogms_annex7.php';

        $row = schogms_annex7_fetch($conn, $submissionId);
        if ($row === null) {
            return;
        }

        $uploaderId = (int) ($row['user_id'] ?? 0);
        if ($uploaderId < 1) {
            return;
        }

        $campus = (string) ($row['campus'] ?? '');
        $fileName = (string) ($row['file_name'] ?? '');
        $approved = strtolower($status) === 'approved';

        $title = $approved ? 'Annex 7 approved' : 'Annex 7 declined';
        $message = $approved
            ? "Chairman {$reviewerName} approved your Annex 7 \"{$fileName}\" for {$campus}."
            : "Chairman {$reviewerName} declined your Annex 7 \"{$fileName}\" for {$campus}.";

        schogms_notifications_create(
            $conn,
            $uploaderId,
            $approved ? 'annex7_approved' : 'annex7_rejected',
            $title,
            $message,
            'submit_form.php',
            null,
            $campus,
            $fileName
        );

        $uploader = schogms_notifications_user_by_id($conn, $uploaderId);
        if ($uploader === null || $uploader['email'] === '' || !filter_var($uploader['email'], FILTER_VALIDATE_EMAIL)) {
            return;
        }

        if ($approved) {
            $html = schogms_email_chairman_approved([
                'campus' => $campus,
                'file_name' => $fileName,
                'user_id' => (string) $uploaderId,
            ]);
            schogms_send_mail(
                $uploader['email'],
                'Annex 7 Approved — SchoGMS',
                $html,
                $uploader['name'] !== '' ? $uploader['name'] : 'Coordinator'
            );
        }
    }
}

if (!function_exists('schogms_notify_file_group_reviewed')) {
    function schogms_notify_file_group_reviewed(
        mysqli $conn,
        string $program,
        string $campus,
        string $fileGroup,
        string $status,
        string $reviewerName
    ): void {
        require_once __DIR__ . '/../config/email_templates.php';

        if (!in_array($status, ['approved', 'denied'], true)) {
            return;
        }

        $program = strtolower(trim($program)) === 'tes' ? 'tes' : 'tdp';
        $campus = trim($campus);
        $fileGroup = trim($fileGroup);
        $programLabel = strtoupper($program);

        $meta = schogms_file_group_meta_fetch_batch($conn, $program, $campus, $fileGroup);
        $uploaderId = (int) ($meta['uploaded_by_id'] ?? 0);
        if ($uploaderId < 1) {
            return;
        }

        $uploader = schogms_notifications_user_by_id($conn, $uploaderId);
        if ($uploader === null) {
            return;
        }

        $uploaderRole = trim((string) ($meta['uploaded_by_role'] ?? 'coordinator'));
        $link = schogms_notifications_file_group_link(
            $uploaderRole === 'registrar' ? 'registrar' : ($uploaderRole === 'chairman' ? 'chairman' : 'coordinator'),
            $program,
            $status
        );

        if ($status === 'approved') {
            $title = 'File group approved';
            $message = "Chairman approved your {$programLabel} file group \"{$fileGroup}\" for {$campus}. You can view it in SchoGMS.";
            $type = 'file_group_approved';
        } else {
            $title = 'File group denied';
            $message = "Your {$programLabel} file group \"{$fileGroup}\" for {$campus} was denied by the chairman. Please check SchoGMS for details.";
            $type = 'file_group_denied';
        }

        schogms_notifications_create(
            $conn,
            $uploaderId,
            $type,
            $title,
            $message,
            $link,
            $program,
            $campus,
            $fileGroup
        );

        if ($uploader['email'] === '' || !filter_var($uploader['email'], FILTER_VALIDATE_EMAIL)) {
            return;
        }

        $rolePath = match ($uploaderRole) {
            'registrar' => 'registrar',
            'chairman' => 'chairman',
            default => 'coordinator',
        };
        $fullLink = schogms_app_base_url() . '/users/' . $rolePath . '/' . $link;

        if ($status === 'approved') {
            $html = schogms_email_file_group_approved([
                'name' => $uploader['name'] !== '' ? $uploader['name'] : 'User',
                'program' => $programLabel,
                'campus' => $campus,
                'file_group' => $fileGroup,
                'reviewer' => $reviewerName,
                'link' => $fullLink,
            ]);
            $subject = "SchoGMS: Your {$programLabel} file group was approved";
        } else {
            $html = schogms_email_file_group_denied([
                'name' => $uploader['name'] !== '' ? $uploader['name'] : 'User',
                'program' => $programLabel,
                'campus' => $campus,
                'file_group' => $fileGroup,
                'reviewer' => $reviewerName,
                'link' => $fullLink,
            ]);
            $subject = "SchoGMS: Your {$programLabel} file group was denied";
        }

        schogms_send_mail($uploader['email'], $subject, $html, $uploader['name'] !== '' ? $uploader['name'] : null);
    }
}
