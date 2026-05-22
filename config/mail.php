<?php
/**
 * Send branded SchoGMS emails via shared SMTP config.
 */
require_once __DIR__ . '/smtp.php';
require_once __DIR__ . '/email_templates.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if (!function_exists('schogms_send_mail')) {
    /**
     * @param array<int, string> $attachments Absolute file paths
     * @return array{ok: bool, error?: string}
     */
    function schogms_send_mail(
        string $toEmail,
        string $subject,
        string $htmlBody,
        ?string $toName = null,
        ?string $fromName = null,
        array $attachments = []
    ): array {
        if (!class_exists(PHPMailer::class)) {
            $autoload = dirname(__DIR__) . '/users/vendor/autoload.php';
            if (is_readable($autoload)) {
                require_once $autoload;
            }
        }

        $smtp = schogms_smtp_config();
        $mail = new PHPMailer(true);

        try {
            schogms_phpmailer_configure($mail, $smtp);
            schogms_phpmailer_from($mail, $fromName ?? ($smtp['from_name'] ?? 'SchoGMS'), $smtp);
            $mail->addAddress($toEmail, $toName ?? '');
            $mail->CharSet = PHPMailer::CHARSET_UTF8;
            $mail->Encoding = PHPMailer::ENCODING_BASE64;
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body = $htmlBody;
            $mail->AltBody = html_entity_decode(
                strip_tags(str_replace(['<br>', '<br/>', '<br />', '</p>', '</tr>'], ["\n", "\n", "\n", "\n", "\n"], $htmlBody)),
                ENT_QUOTES,
                'UTF-8'
            );

            foreach ($attachments as $path) {
                if (is_string($path) && $path !== '' && is_readable($path)) {
                    $mail->addAttachment($path);
                }
            }

            $mail->send();
            return ['ok' => true];
        } catch (Exception $e) {
            $err = $mail->ErrorInfo ?: $e->getMessage();
            schogms_log_error('Email send failed: ' . $err, ['to' => $toEmail, 'subject' => $subject]);
            return ['ok' => false, 'error' => $err];
        }
    }
}
