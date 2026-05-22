<?php
require_once __DIR__ . '/../../../config/schogms_helpers.php';
require_once __DIR__ . '/../../coordinator/inc/coordinator_data.php';

if (!function_exists('schogms_chairman_billing_rows')) {
    /**
     * @return array{rows: array<int, array<string, mixed>>, error: string, total: int}
     */
    function schogms_chairman_billing_rows(mysqli $conn, string $campusFilter = '', int $limit = 1500): array
    {
        $rows = [];
        $error = '';
        $total = 0;
        $limit = max(1, min($limit, 3000));

        try {
            $tableCheck = $conn->query("SHOW TABLES LIKE 'billing_table'");
            if (!$tableCheck || $tableCheck->num_rows === 0) {
                return ['rows' => [], 'error' => 'Billing table is not set up yet. Upload a file to create records.', 'total' => 0];
            }

            if ($campusFilter !== '') {
                $countStmt = $conn->prepare('SELECT COUNT(*) AS n FROM billing_table WHERE campus = ?');
                $countStmt->bind_param('s', $campusFilter);
                $countStmt->execute();
                $total = (int) ($countStmt->get_result()->fetch_assoc()['n'] ?? 0);
                $countStmt->close();

                $stmt = $conn->prepare(
                    'SELECT last_name, first_name, scholarship_type, units_enrolled, course, campus,
                            year_and_date_submitted_ched, amount, first_semester, second_semester, status,
                            payment_scholarship_type, payment_amount, payment_year_and_date, payment_or_number,
                            payment_amount_per_or, refund_first_sem, refund_second_sem, refund_year_and_date_released
                     FROM billing_table
                     WHERE campus = ?
                     ORDER BY id DESC
                     LIMIT ?'
                );
                $stmt->bind_param('si', $campusFilter, $limit);
            } else {
                $countRes = $conn->query('SELECT COUNT(*) AS n FROM billing_table');
                if ($countRes) {
                    $total = (int) ($countRes->fetch_assoc()['n'] ?? 0);
                }
                $stmt = $conn->prepare(
                    'SELECT last_name, first_name, scholarship_type, units_enrolled, course, campus,
                            year_and_date_submitted_ched, amount, first_semester, second_semester, status,
                            payment_scholarship_type, payment_amount, payment_year_and_date, payment_or_number,
                            payment_amount_per_or, refund_first_sem, refund_second_sem, refund_year_and_date_released
                     FROM billing_table
                     ORDER BY id DESC
                     LIMIT ?'
                );
                $stmt->bind_param('i', $limit);
            }

            $stmt->execute();
            $res = $stmt->get_result();
            while ($res && ($row = $res->fetch_assoc())) {
                $rows[] = $row;
            }
            $stmt->close();
        } catch (Throwable $e) {
            $error = 'Could not load billing records.';
            schogms_log_error('Chairman billing_table: ' . $e->getMessage());
        }

        return ['rows' => $rows, 'error' => $error, 'total' => $total];
    }
}

if (!function_exists('schogms_chairman_billing_campuses')) {
    /** @return list<string> */
    function schogms_chairman_billing_campuses(mysqli $conn): array
    {
        $list = [];
        try {
            $res = $conn->query(
                'SELECT DISTINCT campus FROM billing_table WHERE campus IS NOT NULL AND TRIM(campus) <> "" ORDER BY campus ASC LIMIT 200'
            );
            if ($res) {
                while ($row = $res->fetch_assoc()) {
                    $list[] = (string) $row['campus'];
                }
            }
        } catch (Throwable $e) {
            schogms_log_error('Chairman billing campuses: ' . $e->getMessage());
        }

        return $list;
    }
}
