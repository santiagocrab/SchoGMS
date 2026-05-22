<?php
/**
 * SchoGMS branded HTML email templates (matches dashboard UI: #5f76e8, clean academic style).
 */
require_once __DIR__ . '/schogms_helpers.php';

if (!function_exists('schogms_default_user_password')) {
    function schogms_default_user_password(): string
    {
        return 'schogms123';
    }
}

if (!function_exists('schogms_app_base_url')) {
    function schogms_app_base_url(): string
    {
        static $cached = null;
        if ($cached !== null) {
            return $cached;
        }
        $local = __DIR__ . '/app_url.local.php';
        if (is_readable($local)) {
            $u = require $local;
            if (is_string($u) && $u !== '') {
                $cached = rtrim($u, '/');
                return $cached;
            }
        }
        $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
        $docRoot = rtrim(str_replace('\\', '/', $_SERVER['DOCUMENT_ROOT'] ?? ''), '/');
        $project = rtrim(str_replace('\\', '/', realpath(__DIR__ . '/..') ?: ''), '/');
        $path = '/SchoGMS';
        if ($docRoot !== '' && str_starts_with($project, $docRoot)) {
            $path = substr($project, strlen($docRoot)) ?: $path;
        }
        $cached = $scheme . '://' . $host . rtrim($path, '/');
        return $cached;
    }
}

if (!function_exists('schogms_email_escape')) {
    function schogms_email_escape(?string $value): string
    {
        return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('schogms_email_layout')) {
    /**
     * @param string $headline  Header title inside the gradient bar
     * @param string $bodyHtml  Inner HTML (already safe or escaped by caller)
     * @param string $preheader Preview text for inbox
     */
    function schogms_email_layout(string $headline, string $bodyHtml, string $preheader = ''): string
    {
        $year = date('Y');
        $pre = schogms_email_escape($preheader);
        $title = schogms_email_escape($headline);
        $base = schogms_app_base_url();

        return <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<title>{$title} | SchoGMS</title>
<!--[if mso]><noscript><xml><o:OfficeDocumentSettings><o:PixelsPerInch>96</o:PixelsPerInch></o:OfficeDocumentSettings></xml></noscript><![endif]-->
</head>
<body style="margin:0;padding:0;background-color:#eef2f8;font-family:'Segoe UI',Roboto,'Helvetica Neue',Arial,sans-serif;-webkit-font-smoothing:antialiased;">
<span style="display:none!important;visibility:hidden;opacity:0;height:0;width:0;overflow:hidden;">{$pre}</span>
<table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="background-color:#eef2f8;padding:32px 16px;">
<tr><td align="center">
<table role="presentation" width="600" cellspacing="0" cellpadding="0" border="0" style="max-width:600px;width:100%;background:#ffffff;border-radius:14px;overflow:hidden;box-shadow:0 12px 40px rgba(47,64,120,.12);">
<tr>
<td style="background:linear-gradient(135deg,#8971ea 0%,#6a75e9 45%,#5f76e8 100%);padding:28px 32px;text-align:center;">
<table role="presentation" width="100%" cellspacing="0" cellpadding="0"><tr><td align="center">
<p style="margin:0 0 6px;font-size:11px;letter-spacing:.14em;text-transform:uppercase;color:rgba(255,255,255,.85);font-weight:600;">Scholarship &amp; Grants Management</p>
<h1 style="margin:0;font-size:22px;font-weight:700;color:#ffffff;line-height:1.3;">{$title}</h1>
</td></tr></table>
</td>
</tr>
<tr>
<td style="padding:32px 36px 20px;color:#1a2332;font-size:16px;line-height:1.7;">
{$bodyHtml}
</td>
</tr>
<tr>
<td style="padding:0 36px 32px;">
<table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="border-top:2px solid #d8dee9;background-color:#f3f5f9;border-radius:0 0 12px 12px;">
<tr><td style="padding:22px 20px;text-align:center;">
<p style="margin:0 0 10px;font-size:15px;line-height:1.5;color:#1a2332;"><strong style="color:#4a5fd6;">SchoGMS</strong> <span style="color:#334155;">- Scholarship and Grants Management System</span></p>
<p style="margin:0 0 12px;font-size:14px;line-height:1.6;color:#334155;">This is an automated message. Please do not reply directly unless instructed.</p>
<p style="margin:0 0 10px;"><a href="{$base}/index.php" style="display:inline-block;font-size:15px;color:#ffffff;background-color:#5f76e8;padding:10px 22px;text-decoration:none;font-weight:700;border-radius:6px;">Open SchoGMS</a></p>
<p style="margin:0 0 14px;font-size:13px;"><a href="{$base}/index.php" style="color:#4a5fd6;font-weight:700;text-decoration:underline;">{$base}/index.php</a></p>
<p style="margin:0;font-size:13px;line-height:1.5;color:#475569;">&copy; {$year} SchoGMS. All rights reserved.</p>
</td></tr>
</table>
</td>
</tr>
</table>
</td></tr>
</table>
</body>
</html>
HTML;
    }
}

if (!function_exists('schogms_email_plain_link')) {
    /** Clickable full URL below buttons (works when email clients block styled buttons). */
    function schogms_email_plain_link(string $url, string $label = ''): string
    {
        $u = schogms_email_escape($url);
        $lbl = $label !== '' ? schogms_email_escape($label) : $u;
        return '<p style="margin:8px 0 0;text-align:center;font-size:14px;line-height:1.5;">'
            . '<span style="color:#475569;">' . ($label !== '' ? $lbl . '<br>' : '') . '</span>'
            . '<a href="' . $u . '" target="_blank" style="color:#4a5fd6;font-weight:700;word-break:break-all;text-decoration:underline;">' . $u . '</a>'
            . '</p>';
    }
}

if (!function_exists('schogms_email_button')) {
    /**
     * Bulletproof button for Gmail, Outlook, and Apple Mail.
     */
    function schogms_email_button(string $url, string $label, string $color = '#343a40'): string
    {
        $u = schogms_email_escape($url);
        $l = schogms_email_escape($label);
        $btn = '<table role="presentation" cellspacing="0" cellpadding="0" border="0" align="center" style="margin:20px auto;">'
            . '<tr><td align="center" bgcolor="' . $color . '" style="background-color:' . $color . ';border-radius:8px;mso-padding-alt:0;">'
            . '<a href="' . $u . '" target="_blank" rel="noopener noreferrer" '
            . 'style="display:inline-block;padding:16px 40px;font-size:16px;font-family:Arial,Helvetica,sans-serif;font-weight:700;color:#ffffff;text-decoration:none;border-radius:8px;border:1px solid ' . $color . ';">'
            . $l . '</a></td></tr></table>';
        return $btn . schogms_email_plain_link($url, 'Or open this link:');
    }
}

if (!function_exists('schogms_email_info_box')) {
    function schogms_email_info_box(string $label, string $value, string $variant = 'default'): string
    {
        $border = '#5f76e8';
        $labelBg = '#e8ecf8';
        $labelColor = '#1e293b';
        $valueBg = '#ffffff';
        $valueColor = '#0f172a';
        $valueSize = '20px';
        if ($variant === 'code') {
            $border = '#4a5fd6';
            $labelBg = '#dce3fc';
        } elseif ($variant === 'password') {
            $border = '#b45309';
            $labelBg = '#fde68a';
            $labelColor = '#78350f';
            $valueBg = '#fffbeb';
            $valueSize = '22px';
        } elseif ($variant === 'danger') {
            $border = '#dc3545';
            $labelBg = '#fecaca';
        }
        $lbl = schogms_email_escape($label);
        $val = schogms_email_escape($value);
        return '<table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="margin:14px 0;border:2px solid ' . $border . ';border-radius:10px;overflow:hidden;">'
            . '<tr><td style="padding:10px 16px;background-color:' . $labelBg . ';border-bottom:1px solid ' . $border . ';">'
            . '<p style="margin:0;font-size:12px;text-transform:uppercase;letter-spacing:.06em;color:' . $labelColor . ';font-weight:700;">' . $lbl . '</p>'
            . '</td></tr>'
            . '<tr><td style="padding:16px 18px;background-color:' . $valueBg . ';">'
            . '<p style="margin:0;font-size:' . $valueSize . ';font-weight:800;color:' . $valueColor . ';line-height:1.4;word-break:break-all;font-family:Consolas,Monaco,\'Courier New\',monospace;">' . $val . '</p>'
            . '</td></tr></table>';
    }
}

if (!function_exists('schogms_email_warning_confidential')) {
    function schogms_email_warning_confidential(): string
    {
        return '<table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="margin:18px 0;border:2px solid #dc3545;border-radius:10px;background-color:#fff5f5;">'
            . '<tr><td style="padding:18px 20px;">'
            . '<p style="margin:0 0 10px;font-size:16px;font-weight:800;color:#991b1b;">WARNING: Confidential - Do not share</p>'
            . '<p style="margin:0;font-size:15px;color:#1f2937;line-height:1.65;">'
            . 'Your login password is shown below because administrators <strong style="color:#991b1b;">cannot set or change</strong> individual passwords in SchoGMS. '
            . 'Keep this email private. Do not forward it or share your password with anyone. '
            . 'Change your password after your first login if your role allows it.</p>'
            . '</td></tr></table>';
    }
}

if (!function_exists('schogms_email_welcome_verification')) {
    function schogms_email_welcome_verification(array $d): string
    {
        $name = schogms_email_escape($d['name'] ?? 'User');
        $email = schogms_email_escape($d['email'] ?? '');
        $role = schogms_email_escape(ucwords(str_replace('-', ' ', $d['role'] ?? 'User')));
        $campus = schogms_email_escape($d['campus'] ?? 'N/A');
        $code = schogms_email_escape($d['verification_code'] ?? '');
        $password = schogms_email_escape($d['password'] ?? schogms_default_user_password());
        $expires = schogms_email_escape($d['expires_minutes'] ?? '60');
        $base = schogms_app_base_url();
        $verifyUrl = $base . '/verify.php';
        $loginUrl = $base . '/index.php';

        $body = <<<BODY
<p style="margin:0 0 16px;font-size:17px;color:#1a2332;">Hello <strong style="color:#0f172a;">{$name}</strong>,</p>
<p style="margin:0 0 20px;font-size:16px;color:#334155;line-height:1.65;">Welcome to <strong style="color:#4a5fd6;">SchoGMS</strong>. An administrator created your account. Use the details below to verify your email and sign in.</p>

<table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="margin:0 0 20px;border:1px solid #c5cee0;border-radius:10px;background-color:#f8fafc;">
<tr><td style="padding:18px 20px;font-size:15px;line-height:1.7;color:#1a2332;">
<p style="margin:0 0 10px;"><span style="color:#475569;font-weight:600;">Role:</span> <strong style="color:#0f172a;">{$role}</strong></p>
<p style="margin:0 0 10px;"><span style="color:#475569;font-weight:600;">Campus:</span> <strong style="color:#0f172a;">{$campus}</strong></p>
<p style="margin:0;"><span style="color:#475569;font-weight:600;">Email:</span> <strong style="color:#0f172a;">{$email}</strong></p>
</td></tr>
</table>

<p style="margin:0 0 10px;font-size:17px;font-weight:700;color:#0f172a;">Step 1 - Verify your email</p>
BODY;
        $body .= schogms_email_info_box('Verification code', $code, 'code');
        $body .= '<p style="margin:10px 0 22px;font-size:15px;color:#334155;">This code expires in <strong style="color:#0f172a;">' . $expires . ' minutes</strong>.</p>';
        $body .= schogms_email_button($verifyUrl, 'Verify my account', '#5f76e8');

        $body .= '<p style="margin:28px 0 10px;font-size:17px;font-weight:700;color:#0f172a;">Step 2 - Sign in after verification</p>';
        $body .= schogms_email_warning_confidential();
        $body .= schogms_email_info_box('Login email / username', $email, 'default');
        $body .= schogms_email_info_box('Temporary password', $password, 'password');
        $body .= schogms_email_button($loginUrl, 'Go to SchoGMS Login', '#343a40');

        $body .= '<table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="margin:16px 0 0;padding:16px;background:#eef2f8;border-radius:8px;border:1px solid #c5cee0;">'
            . '<tr><td style="font-size:14px;color:#334155;line-height:1.6;">'
            . '<strong style="color:#0f172a;">Local testing (XAMPP):</strong> Buttons must open on the same computer where XAMPP is running. '
            . 'If a button does nothing in Gmail, use the blue underlined links above or copy these URLs into Chrome/Safari on this machine:'
            . '<br><br><strong>Verify:</strong> <a href="' . schogms_email_escape($verifyUrl) . '" style="color:#4a5fd6;font-weight:700;">' . schogms_email_escape($verifyUrl) . '</a>'
            . '<br><strong>Login:</strong> <a href="' . schogms_email_escape($loginUrl) . '" style="color:#4a5fd6;font-weight:700;">' . schogms_email_escape($loginUrl) . '</a>'
            . '</td></tr></table>';

        $body .= '<table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="margin:24px 0 0;border:1px solid #94a3b8;border-radius:8px;background-color:#f1f5f9;">'
            . '<tr><td style="padding:14px 16px;font-size:14px;line-height:1.6;color:#1e293b;font-weight:600;">'
            . 'If you did not expect this account, contact your campus administrator immediately.'
            . '</td></tr></table>';

        return schogms_email_layout('Welcome - Verify and Sign In', $body, 'Your SchoGMS account is ready. Verification code and login password inside.');
    }
}

if (!function_exists('schogms_email_duplicate_registration')) {
    function schogms_email_duplicate_registration(array $d): string
    {
        $name = schogms_email_escape($d['name'] ?? 'User');
        $base = schogms_app_base_url();
        $body = <<<BODY
<p style="margin:0 0 16px;font-size:17px;color:#1a2332;">Hello <strong style="color:#0f172a;">{$name}</strong>,</p>
<p style="margin:0 0 16px;font-size:16px;color:#334155;">The email address you used is <strong style="color:#0f172a;">already registered</strong> in SchoGMS. No new account was created.</p>
<p style="margin:0 0 16px;font-size:16px;color:#334155;">If you forgot your password, please contact your system administrator. Admins cannot view your existing password for security reasons.</p>
BODY;
        $body .= schogms_email_button($base . '/index.php', 'Go to Login', '#5f76e8');
        return schogms_email_layout('Email Already Registered', $body, 'This email is already registered in SchoGMS.');
    }
}

if (!function_exists('schogms_email_export_processed')) {
    function schogms_email_export_processed(array $d): string
    {
        $recipient = schogms_email_escape($d['recipient_label'] ?? 'Colleague');
        $sheet = schogms_email_escape($d['sheet_name'] ?? '');
        $detail = schogms_email_escape($d['detail'] ?? 'Your export has been processed successfully.');
        $body = <<<BODY
<p style="margin:0 0 16px;font-size:17px;color:#1a2332;">Dear <strong style="color:#0f172a;">{$recipient}</strong>,</p>
<p style="margin:0 0 16px;font-size:16px;color:#334155;">{$detail}</p>
BODY;
        if ($sheet !== '') {
            $body .= schogms_email_info_box('Sheet / Campus', $sheet, 'default');
        }
        $body .= '<p style="margin:16px 0 0;font-size:16px;color:#334155;">The exported file is <strong style="color:#0f172a;">attached</strong> to this email. Please review it and keep it secure.</p>';
        return schogms_email_layout('Export Ready', $body, 'Your SchoGMS export is attached.');
    }
}

if (!function_exists('schogms_email_chairman_approved')) {
    function schogms_email_chairman_approved(array $d): string
    {
        $campus = schogms_email_escape($d['campus'] ?? '');
        $fileName = schogms_email_escape($d['file_name'] ?? '');
        $userId = schogms_email_escape($d['user_id'] ?? '');
        $body = <<<BODY
<p style="margin:0 0 16px;">Dear Coordinator,</p>
<p style="margin:0 0 16px;font-size:16px;color:#334155;">Great news - your <strong style="color:#0f172a;">Annex 7 form</strong> has been <span style="color:#15803d;font-weight:700;">approved</span> by the Chairman and is ready for submission to <strong>CHED Region XII</strong>.</p>
BODY;
        $body .= schogms_email_info_box('Campus', $campus, 'default');
        $body .= schogms_email_info_box('File name', $fileName, 'default');
        if ($userId !== '') {
            $body .= schogms_email_info_box('Reference ID', $userId, 'default');
        }
        $body .= '<p style="margin:16px 0 0;">Thank you for your continued coordination.</p>';
        return schogms_email_layout('Annex 7 Approved', $body, 'Your Annex 7 form was approved by the Chairman.');
    }
}

if (!function_exists('schogms_email_role_assignment')) {
    function schogms_email_role_assignment(array $d): string
    {
        $roleTitle = schogms_email_escape($d['role_title'] ?? 'Role Assignment');
        $name = schogms_email_escape($d['name'] ?? '');
        $email = schogms_email_escape($d['email'] ?? '');
        $course = schogms_email_escape($d['course'] ?? '');
        $campus = schogms_email_escape($d['campus'] ?? '');
        $password = schogms_email_escape($d['password'] ?? schogms_default_user_password());
        $confirmUrlRaw = (string) ($d['confirm_url'] ?? schogms_app_base_url() . '/index.php');
        $loginUrl = schogms_app_base_url() . '/index.php';

        $body = <<<BODY
<p style="margin:0 0 16px;font-size:17px;color:#1a2332;">Hello <strong style="color:#0f172a;">{$name}</strong>,</p>
<p style="margin:0 0 16px;font-size:16px;color:#334155;">You have been assigned a new role in <strong style="color:#4a5fd6;">SchoGMS</strong>.</p>
BODY;
        $body .= schogms_email_info_box('Assignment', $roleTitle, 'default');
        if ($course !== '') {
            $body .= schogms_email_info_box('Program / Course', $course, 'default');
        }
        if ($campus !== '') {
            $body .= schogms_email_info_box('Campus', $campus, 'default');
        }
        $body .= schogms_email_warning_confidential();
        $body .= schogms_email_info_box('Login email', $email, 'default');
        $body .= schogms_email_info_box('Temporary password', $password, 'password');
        $body .= schogms_email_button($confirmUrlRaw, 'Confirm and continue', '#5f76e8');
        $body .= schogms_email_button($loginUrl, 'Sign in to SchoGMS', '#343a40');
        return schogms_email_layout($roleTitle, $body, 'Your SchoGMS assignment and login credentials.');
    }
}

if (!function_exists('schogms_email_file_group_pending_chairman')) {
    function schogms_email_file_group_pending_chairman(array $d): string
    {
        $name = schogms_email_escape($d['name'] ?? 'Chairman');
        $program = schogms_email_escape($d['program'] ?? 'TDP');
        $campus = schogms_email_escape($d['campus'] ?? '');
        $fileGroup = schogms_email_escape($d['file_group'] ?? '');
        $uploader = schogms_email_escape($d['uploader'] ?? '');
        $link = (string) ($d['link'] ?? schogms_app_base_url() . '/users/chairman/file_groups.php?status=pending');

        $body = <<<BODY
<p style="margin:0 0 16px;font-size:17px;color:#1a2332;">Hello <strong>{$name}</strong>,</p>
<p style="margin:0 0 16px;font-size:16px;color:#334155;line-height:1.65;">
A new file group was uploaded and is <strong>waiting for your approval</strong>. Please sign in to SchoGMS and review it on the <strong>File groups</strong> page.
</p>
BODY;
        $body .= schogms_email_info_box('Program', $program, 'default');
        $body .= schogms_email_info_box('Campus', $campus, 'default');
        $body .= schogms_email_info_box('File group', $fileGroup, 'default');
        $body .= schogms_email_info_box('Uploaded by', $uploader, 'default');
        $body .= schogms_email_button($link, 'Review file groups', '#5f76e8');

        return schogms_email_layout('New upload awaiting review', $body, 'New file group uploaded — please check SchoGMS.');
    }
}

if (!function_exists('schogms_email_file_group_waiting')) {
    function schogms_email_file_group_waiting(array $d): string
    {
        $name = schogms_email_escape($d['name'] ?? 'User');
        $program = schogms_email_escape($d['program'] ?? 'TDP');
        $campus = schogms_email_escape($d['campus'] ?? '');
        $fileGroup = schogms_email_escape($d['file_group'] ?? '');
        $link = (string) ($d['link'] ?? schogms_app_base_url() . '/index.php');

        $body = <<<BODY
<p style="margin:0 0 16px;font-size:17px;color:#1a2332;">Hello <strong>{$name}</strong>,</p>
<p style="margin:0 0 16px;font-size:16px;color:#334155;line-height:1.65;">
Your file group was submitted successfully. It is now <strong>waiting for chairman approval</strong>. You will receive another notification when it is approved or denied.
</p>
BODY;
        $body .= schogms_email_info_box('Program', $program, 'default');
        $body .= schogms_email_info_box('Campus', $campus, 'default');
        $body .= schogms_email_info_box('File group', $fileGroup, 'default');
        $body .= schogms_email_button($link, 'Open SchoGMS', '#5f76e8');

        return schogms_email_layout('File group submitted', $body, 'Your upload is waiting for chairman approval.');
    }
}

if (!function_exists('schogms_email_file_group_approved')) {
    function schogms_email_file_group_approved(array $d): string
    {
        $name = schogms_email_escape($d['name'] ?? 'User');
        $program = schogms_email_escape($d['program'] ?? 'TDP');
        $campus = schogms_email_escape($d['campus'] ?? '');
        $fileGroup = schogms_email_escape($d['file_group'] ?? '');
        $reviewer = schogms_email_escape($d['reviewer'] ?? 'Chairman');
        $link = (string) ($d['link'] ?? schogms_app_base_url() . '/index.php');

        $body = <<<BODY
<p style="margin:0 0 16px;font-size:17px;color:#1a2332;">Hello <strong>{$name}</strong>,</p>
<p style="margin:0 0 16px;font-size:16px;color:#334155;line-height:1.65;">
<strong>Good news!</strong> The chairman approved your file group. You can check the scholars and batch details in SchoGMS.
</p>
BODY;
        $body .= schogms_email_info_box('Program', $program, 'default');
        $body .= schogms_email_info_box('Campus', $campus, 'default');
        $body .= schogms_email_info_box('File group', $fileGroup, 'default');
        $body .= schogms_email_info_box('Reviewed by', $reviewer, 'default');
        $body .= schogms_email_button($link, 'View in SchoGMS', '#198754');

        return schogms_email_layout('File group approved', $body, 'Your file group was approved — open SchoGMS to view it.');
    }
}

if (!function_exists('schogms_email_file_group_denied')) {
    function schogms_email_file_group_denied(array $d): string
    {
        $name = schogms_email_escape($d['name'] ?? 'User');
        $program = schogms_email_escape($d['program'] ?? 'TDP');
        $campus = schogms_email_escape($d['campus'] ?? '');
        $fileGroup = schogms_email_escape($d['file_group'] ?? '');
        $reviewer = schogms_email_escape($d['reviewer'] ?? 'Chairman');
        $link = (string) ($d['link'] ?? schogms_app_base_url() . '/index.php');

        $body = <<<BODY
<p style="margin:0 0 16px;font-size:17px;color:#1a2332;">Hello <strong>{$name}</strong>,</p>
<p style="margin:0 0 16px;font-size:16px;color:#334155;line-height:1.65;">
Your file group was <strong>denied</strong> by the chairman. Please sign in to SchoGMS for details and next steps.
</p>
BODY;
        $body .= schogms_email_info_box('Program', $program, 'default');
        $body .= schogms_email_info_box('Campus', $campus, 'default');
        $body .= schogms_email_info_box('File group', $fileGroup, 'default');
        $body .= schogms_email_info_box('Reviewed by', $reviewer, 'default');
        $body .= schogms_email_button($link, 'Open SchoGMS', '#dc3545');

        return schogms_email_layout('File group denied', $body, 'Your file group was denied — check SchoGMS.');
    }
}
