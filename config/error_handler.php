<?php
/**
 * Friendly error handling — log details, show safe messages to users.
 */
require_once __DIR__ . '/schogms_helpers.php';

if (!function_exists('schogms_register_error_handlers')) {
    function schogms_register_error_handlers(): void
    {
        set_error_handler(static function (int $errno, string $errstr, string $errfile, int $errline): bool {
            if (!(error_reporting() & $errno)) {
                return false;
            }
            schogms_log_error("PHP [$errno] $errstr", ['file' => $errfile, 'line' => $errline]);
            return true;
        });

        set_exception_handler(static function (Throwable $e): void {
            schogms_log_error('Uncaught ' . get_class($e) . ': ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);
            if (!headers_sent()) {
                http_response_code(500);
            }
            if (defined('SCHOGMS_DEBUG') && SCHOGMS_DEBUG) {
                echo '<p>An error occurred. Please try again or contact support.</p>';
                echo '<pre>' . schogms_e($e->getMessage()) . '</pre>';
            } else {
                echo '<p>An unexpected error occurred. Please try again later or contact the system administrator.</p>';
            }
        });
    }
}
