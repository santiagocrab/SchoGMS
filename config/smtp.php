<?php
/**
 * Shared SMTP configuration for SchoGMS outbound mail (2FA, notifications).
 */
use PHPMailer\PHPMailer\PHPMailer;

if (!function_exists('schogms_smtp_config')) {
    function schogms_smtp_config(): array
    {
        $local = __DIR__ . '/smtp.local.php';
        if (is_readable($local)) {
            $cfg = require $local;
            if (is_array($cfg)) {
                return $cfg;
            }
        }
        $example = __DIR__ . '/smtp.example.php';
        if (is_readable($example)) {
            return require $example;
        }
        return [
            'host'      => 'smtp.gmail.com',
            'username'  => '',
            'password'  => '',
            'port'      => 587,
            'secure'    => 'tls',
            'from_name' => 'SchoGMS',
            'reply_name'=> 'SchoGMS Support',
        ];
    }
}

if (!function_exists('schogms_phpmailer_configure')) {
    /**
     * Apply SMTP settings to a PHPMailer instance.
     */
    function schogms_phpmailer_configure(PHPMailer $mail, ?array $smtp = null): void
    {
        $smtp = $smtp ?? schogms_smtp_config();
        $mail->isSMTP();
        $mail->Host = $smtp['host'] ?? 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = $smtp['username'] ?? '';
        $mail->Password = $smtp['password'] ?? '';
        $secure = strtolower((string) ($smtp['secure'] ?? 'tls'));
        if ($secure === 'ssl' || (int) ($smtp['port'] ?? 587) === 465) {
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $mail->Port = (int) ($smtp['port'] ?? 465);
        } else {
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = (int) ($smtp['port'] ?? 587);
        }
    }
}

if (!function_exists('schogms_phpmailer_from')) {
    function schogms_phpmailer_from(PHPMailer $mail, ?string $fromName = null, ?array $smtp = null): void
    {
        $smtp = $smtp ?? schogms_smtp_config();
        $user = $smtp['username'] ?? '';
        $name = $fromName ?? ($smtp['from_name'] ?? 'SchoGMS');
        $mail->setFrom($user, $name);
        $replyName = $smtp['reply_name'] ?? $name;
        $mail->addReplyTo($user, $replyName);
    }
}
