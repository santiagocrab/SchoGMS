<?php
/**
 * Shared helpers for SchoGMS — role redirects, safe output, logging.
 */

if (!function_exists('schogms_role_home')) {
    /**
     * Dashboard URL path (relative to site root) for a user role.
     */
    function schogms_role_home(string $role): string
    {
        $map = [
            'coordinator'   => 'users/coordinator/',
            'chairman'      => 'users/chairman/',
            'registrar'     => 'users/registrar/',
            'program-head'  => 'users/program-chair/',
            'program-chair' => 'users/program-chair/',
            'director'      => 'users/director/',
            'dean'          => 'users/dean/',
            'admin'         => 'admin/dashboard.php',
        ];
        return $map[$role] ?? 'index.php';
    }
}

if (!function_exists('schogms_role_folder')) {
    /** Role slug expected under users/ for RBAC folder checks. */
    function schogms_role_folder(string $role): ?string
    {
        $map = [
            'coordinator'   => 'coordinator',
            'chairman'      => 'chairman',
            'registrar'     => 'registrar',
            'program-head'  => 'program-chair',
            'program-chair' => 'program-chair',
            'director'      => 'director',
            'dean'          => 'dean',
        ];
        return $map[$role] ?? null;
    }
}

if (!function_exists('schogms_e')) {
    function schogms_e(?string $value): string
    {
        return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('schogms_log_error')) {
    function schogms_log_error(string $message, array $context = []): void
    {
        $line = '[SchoGMS] ' . $message;
        if ($context !== []) {
            $line .= ' ' . json_encode($context);
        }
        error_log($line);
    }
}

if (!function_exists('schogms_status_badge')) {
    function schogms_status_badge(string $status): string
    {
        $status = strtolower(trim($status));
        $classes = [
            'pending'       => 'badge-warning',
            'approved'      => 'badge-success',
            'active'        => 'badge-success',
            'rejected'      => 'badge-danger',
            'restricted'    => 'badge-danger',
            'inactive'      => 'badge-secondary',
            'under review'  => 'badge-info',
            'complete'      => 'badge-success',
            'incomplete'    => 'badge-warning',
        ];
        $class = $classes[$status] ?? 'badge-secondary';
        $label = ucwords($status);
        return '<span class="badge ' . $class . '">' . schogms_e($label) . '</span>';
    }
}

if (!function_exists('schogms_fix_enye_in_name')) {
    /**
     * Fix corrupted enye from imports (?) and mojibake; keeps ñ, uses Ñ only when name is all caps.
     */
    function schogms_fix_enye_in_name(string $value): string
    {
        $value = trim($value);
        if ($value === '') {
            return '';
        }

        $value = str_replace(['Ã±', 'Ã', '±'], ['ñ', 'ñ', 'ñ'], $value);
        $value = str_replace('?', 'ñ', $value);

        // Masterlist-style ALL CAPS: no ASCII a–z except after stripping ñ
        $asciiCheck = str_replace(['ñ', 'Ñ'], '', $value);
        if (!preg_match('/[a-z]/', $asciiCheck)) {
            $value = str_replace('ñ', 'Ñ', $value);
        }

        return $value;
    }
}

if (!function_exists('schogms_assets_rel_from_request')) {
    /** Relative path to assets/ from the current script URL. */
    function schogms_assets_rel_from_request(): string
    {
        $script = str_replace('\\', '/', (string) ($_SERVER['SCRIPT_NAME'] ?? ''));

        if (preg_match('#/(admin(?:-[^/]+)?)/#', $script)) {
            return '../assets';
        }
        if (preg_match('#/users/[^/]+/#', $script)) {
            return '../../assets';
        }

        return 'assets';
    }
}

if (!function_exists('schogms_ensure_writable_upload_dir')) {
    /**
     * Create upload directory and ensure the web server (Apache) can write to it on XAMPP/macOS.
     *
     * @return array{ok: bool, path: string, error: string}
     */
    function schogms_ensure_writable_upload_dir(string $dir): array
    {
        $dir = rtrim($dir, '/') . '/';
        if (!is_dir($dir)) {
            if (!@mkdir($dir, 0777, true) && !is_dir($dir)) {
                return ['ok' => false, 'path' => $dir, 'error' => 'Could not create uploads folder.'];
            }
        }
        @chmod($dir, 0777);
        if (!is_writable($dir)) {
            @chmod($dir, 0775);
        }
        if (!is_writable($dir)) {
            return [
                'ok' => false,
                'path' => $dir,
                'error' => 'Uploads folder is not writable. In Terminal run: chmod -R 777 ' . $dir,
            ];
        }

        return ['ok' => true, 'path' => $dir, 'error' => ''];
    }
}

if (!function_exists('schogms_spreadsheet_cell')) {
    /** @param array<string|int, mixed> $row */
    function schogms_spreadsheet_cell(array $row, string $col): string
    {
        if (isset($row[$col])) {
            return trim((string) $row[$col]);
        }
        $upper = strtoupper($col);

        return isset($row[$upper]) ? trim((string) $row[$upper]) : '';
    }
}

if (!function_exists('schogms_pdf_escape_text')) {
    function schogms_pdf_escape_text(string $text): string
    {
        return str_replace(['\\', '(', ')'], ['\\\\', '\\(', '\\)'], $text);
    }
}

if (!function_exists('schogms_pdf_is_viewable')) {
    /** True when file is large enough and structurally looks like a real PDF (not demo stub). */
    function schogms_pdf_is_viewable(string $path): bool
    {
        if (!is_file($path) || !is_readable($path)) {
            return false;
        }
        $size = filesize($path);
        if ($size === false || $size < 400) {
            return false;
        }
        $handle = fopen($path, 'rb');
        if (!$handle) {
            return false;
        }
        $head = fread($handle, 8);
        $tail = '';
        if ($size > 32) {
            fseek($handle, -32, SEEK_END);
            $tail = fread($handle, 32);
        }
        fclose($handle);
        if ($head === false || !str_starts_with($head, '%PDF')) {
            return false;
        }
        if ($tail === false || !str_contains($tail, '%%EOF')) {
            return false;
        }
        $body = file_get_contents($path, false, null, 0, min((int) $size, 4096));
        if ($body !== false && str_contains($body, 'SchoGMS DEMO') && $size < 2048) {
            return false;
        }

        return true;
    }
}

if (!function_exists('schogms_generate_minimal_pdf')) {
    /**
     * Build a small but valid single-page PDF (viewable in Chrome/Firefox).
     */
    function schogms_generate_minimal_pdf(string $title, string $subtitle = ''): string
    {
        $lines = array_values(array_filter([
            schogms_pdf_escape_text($title),
            $subtitle !== '' ? schogms_pdf_escape_text($subtitle) : '',
            'SchoGMS document preview',
        ], static fn(string $s): bool => $s !== ''));

        $stream = "BT\n/F1 14 Tf 72 720 Td ({$lines[0]}) Tj\n";
        for ($i = 1, $n = count($lines); $i < $n; $i++) {
            $stream .= "0 -22 Td ({$lines[$i]}) Tj\n";
        }
        $stream .= 'ET';
        $streamLen = strlen($stream);

        $objects = [];
        $objects[] = "1 0 obj\n<< /Type /Catalog /Pages 2 0 R >>\nendobj\n";
        $objects[] = "2 0 obj\n<< /Type /Pages /Kids [3 0 R] /Count 1 >>\nendobj\n";
        $objects[] = "3 0 obj\n<< /Type /Page /Parent 2 0 R /MediaBox [0 0 612 792] "
            . "/Resources << /Font << /F1 4 0 R >> >> /Contents 5 0 R >>\nendobj\n";
        $objects[] = "4 0 obj\n<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica >>\nendobj\n";
        $objects[] = "5 0 obj\n<< /Length {$streamLen} >>\nstream\n{$stream}\nendstream\nendobj\n";

        $pdf = "%PDF-1.4\n";
        $offsets = [0];
        foreach ($objects as $obj) {
            $offsets[] = strlen($pdf);
            $pdf .= $obj;
        }

        $xrefPos = strlen($pdf);
        $count = count($objects) + 1;
        $pdf .= "xref\n0 {$count}\n0000000000 65535 f \n";
        for ($i = 1; $i < $count; $i++) {
            $pdf .= sprintf("%010d 00000 n \n", $offsets[$i]);
        }
        $pdf .= "trailer\n<< /Size {$count} /Root 1 0 R >>\n";
        $pdf .= "startxref\n{$xrefPos}\n%%EOF";

        return $pdf;
    }
}

if (!function_exists('schogms_write_viewable_pdf')) {
    /** Write a browser-viewable PDF to disk (replaces invalid demo stubs). */
    function schogms_write_viewable_pdf(string $fullPath, string $title, string $subtitle = ''): bool
    {
        $dir = dirname($fullPath);
        if (!is_dir($dir) && !@mkdir($dir, 0777, true)) {
            return false;
        }
        $bytes = schogms_generate_minimal_pdf($title, $subtitle);

        return file_put_contents($fullPath, $bytes) !== false;
    }
}

if (!function_exists('schogms_loading_screen_once')) {
    /**
     * SchoGMS branded page loader — safe to call multiple times (renders once per request).
     */
    function schogms_loading_screen_once(?string $assetsRel = null): void
    {
        static $rendered = false;
        if ($rendered) {
            return;
        }
        $rendered = true;

        $base = rtrim($assetsRel ?? schogms_assets_rel_from_request(), '/');
        require_once dirname(__DIR__) . '/inc/schogms_page_loader.php';
        schogms_render_page_loader(
            $base . '/images/logo.png',
            $base . '/css/schogms-loader.css',
            $base . '/js/schogms-loader.js'
        );
    }
}
