<?php
/**
 * document_uploads table helpers (COR / COG).
 */

if (!function_exists('schogms_document_uploads_file_group_max')) {
    function schogms_document_uploads_file_group_max(): int
    {
        return 512;
    }
}

if (!function_exists('schogms_document_uploads_ensure_schema')) {
    function schogms_document_uploads_ensure_schema(mysqli $conn): void
    {
        $res = $conn->query(
            "SELECT CHARACTER_MAXIMUM_LENGTH AS n FROM information_schema.COLUMNS
             WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'document_uploads' AND COLUMN_NAME = 'file_group'"
        );
        $len = 100;
        if ($res && ($row = $res->fetch_assoc())) {
            $len = (int) ($row['n'] ?? 100);
        }
        if ($len < schogms_document_uploads_file_group_max()) {
            $conn->query(
                'ALTER TABLE document_uploads MODIFY COLUMN file_group VARCHAR(512) NULL DEFAULT NULL'
            );
        }
    }
}

if (!function_exists('schogms_document_uploads_normalize_file_group')) {
    /**
     * @return array{ok: bool, value: string, error: string}
     */
    function schogms_document_uploads_normalize_file_group(mysqli $conn, string $fileGroup): array
    {
        schogms_document_uploads_ensure_schema($conn);

        $fileGroup = trim($fileGroup);
        if ($fileGroup === '') {
            return ['ok' => false, 'value' => '', 'error' => 'File group is required.'];
        }
        $max = schogms_document_uploads_file_group_max();
        if (strlen($fileGroup) > $max) {
            return [
                'ok' => false,
                'value' => '',
                'error' => "File group is too long (max {$max} characters). Shorten the batch name and try again.",
            ];
        }

        return ['ok' => true, 'value' => $fileGroup, 'error' => ''];
    }
}
