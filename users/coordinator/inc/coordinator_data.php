<?php
/**
 * Safe data loaders for coordinator pages (avoids missing billing_table fatals).
 */

if (!function_exists('schogms_coordinator_ched_scholars')) {
    /**
     * @return array{rows: array<int, array<string, mixed>>, error: string}
     */
    function schogms_coordinator_ched_scholars(mysqli $conn, string $campus, int $limit = 500): array
    {
        $rows = [];
        $error = '';
        $limit = max(1, min($limit, 2000));

        try {
            if ($campus !== '') {
                $stmt = $conn->prepare(
                    'SELECT lastname, firstname, middlename, course_program_enrolled, year_level,
                            total_units_enrolled, sheet_name, file_group, remarks, status_of_enrollment, upload_time
                     FROM ched_masterlist
                     WHERE sheet_name = ?
                     ORDER BY upload_time DESC
                     LIMIT ?'
                );
                $stmt->bind_param('si', $campus, $limit);
            } else {
                $stmt = $conn->prepare(
                    'SELECT lastname, firstname, middlename, course_program_enrolled, year_level,
                            total_units_enrolled, sheet_name, file_group, remarks, status_of_enrollment, upload_time
                     FROM ched_masterlist
                     ORDER BY upload_time DESC
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
            $error = 'Could not load scholar list.';
            schogms_log_error('Coordinator ched_masterlist: ' . $e->getMessage());
        }

        return ['rows' => $rows, 'error' => $error];
    }
}

if (!function_exists('schogms_coordinator_documents')) {
    /**
     * @return array{rows: array<int, array<string, mixed>>, error: string}
     */
    function schogms_coordinator_documents(mysqli $conn, string $campus, string $category, int $limit = 500): array
    {
        $rows = [];
        $error = '';
        $category = strtoupper($category);
        if (!in_array($category, ['COR', 'COG'], true)) {
            return ['rows' => [], 'error' => 'Invalid document category.'];
        }
        $limit = max(1, min($limit, 2000));

        try {
            if ($campus !== '') {
                $stmt = $conn->prepare(
                    'SELECT id, campus, file_group, category, file_name, file_path, uploaded_at
                     FROM document_uploads
                     WHERE category = ? AND campus = ?
                     ORDER BY uploaded_at DESC
                     LIMIT ?'
                );
                $stmt->bind_param('ssi', $category, $campus, $limit);
            } else {
                $stmt = $conn->prepare(
                    'SELECT id, campus, file_group, category, file_name, file_path, uploaded_at
                     FROM document_uploads
                     WHERE category = ?
                     ORDER BY uploaded_at DESC
                     LIMIT ?'
                );
                $stmt->bind_param('si', $category, $limit);
            }
            $stmt->execute();
            $res = $stmt->get_result();
            while ($res && ($row = $res->fetch_assoc())) {
                $rows[] = $row;
            }
            $stmt->close();
        } catch (Throwable $e) {
            $error = 'Could not load documents.';
            schogms_log_error('Coordinator document_uploads: ' . $e->getMessage());
        }

        return ['rows' => $rows, 'error' => $error];
    }
}
