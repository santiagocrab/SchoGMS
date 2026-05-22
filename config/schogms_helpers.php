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
