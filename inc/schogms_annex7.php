<?php
/**
 * Annex 7 coordinator submissions (file_submissions table).
 */
declare(strict_types=1);

if (!function_exists('schogms_annex7_normalize_status')) {
    function schogms_annex7_normalize_status(string $status): string
    {
        $s = strtolower(trim($status));
        if ($s === 'approved') {
            return 'Approved';
        }
        if ($s === 'rejected' || $s === 'denied') {
            return 'Rejected';
        }

        return 'Pending';
    }
}

if (!function_exists('schogms_annex7_counts')) {
    /**
     * @return array{all: int, pending: int, approved: int, rejected: int}
     */
    function schogms_annex7_counts(mysqli $conn): array
    {
        $out = ['all' => 0, 'pending' => 0, 'approved' => 0, 'rejected' => 0];
        $res = $conn->query(
            "SELECT status, COUNT(*) AS n FROM file_submissions GROUP BY status"
        );
        if (!$res) {
            return $out;
        }
        while ($row = $res->fetch_assoc()) {
            $n = (int) ($row['n'] ?? 0);
            $out['all'] += $n;
            $st = strtolower(trim((string) ($row['status'] ?? '')));
            if ($st === 'approved') {
                $out['approved'] += $n;
            } elseif ($st === 'rejected' || $st === 'denied') {
                $out['rejected'] += $n;
            } else {
                $out['pending'] += $n;
            }
        }

        return $out;
    }
}

if (!function_exists('schogms_annex7_list')) {
    /**
     * @return list<array<string, mixed>>
     */
    function schogms_annex7_list(mysqli $conn, ?string $statusFilter = null): array
    {
        $sql = 'SELECT id, user_id, user_email, campus, file_name, file_path, uploaded_at, status
                FROM file_submissions';
        $params = [];
        $types = '';

        if ($statusFilter !== null && $statusFilter !== '' && $statusFilter !== 'all') {
            if ($statusFilter === 'pending') {
                $sql .= " WHERE status = 'Pending' OR LOWER(TRIM(status)) = 'pending'";
            } elseif ($statusFilter === 'approved') {
                $sql .= " WHERE status = 'Approved' OR LOWER(TRIM(status)) = 'approved'";
            } elseif ($statusFilter === 'rejected') {
                $sql .= " WHERE status IN ('Rejected', 'Denied') OR LOWER(TRIM(status)) IN ('rejected', 'denied')";
            }
        }
        $sql .= ' ORDER BY uploaded_at DESC, id DESC';

        $rows = [];
        $res = $conn->query($sql);
        if ($res) {
            while ($row = $res->fetch_assoc()) {
                $rows[] = $row;
            }

            return $rows;
        }

        return $rows;
    }
}

if (!function_exists('schogms_annex7_fetch')) {
    /** @return array<string, mixed>|null */
    function schogms_annex7_fetch(mysqli $conn, int $id): ?array
    {
        if ($id < 1) {
            return null;
        }
        $stmt = $conn->prepare(
            'SELECT id, user_id, user_email, campus, file_name, file_path, uploaded_at, status
             FROM file_submissions WHERE id = ? LIMIT 1'
        );
        if (!$stmt) {
            return null;
        }
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $res = $stmt->get_result();
        $row = $res ? $res->fetch_assoc() : null;
        $stmt->close();

        return $row ?: null;
    }
}
