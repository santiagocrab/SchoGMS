<?php
/**
 * Copy this file to smtp.local.php and fill in your SMTP credentials.
 * smtp.local.php is gitignored — do not commit real passwords.
 */
return [
    'host'      => 'smtp.gmail.com',
    'username'  => 'your-email@gmail.com',
    'password'  => 'your-app-password-no-spaces',
    'port'      => 587,
    'secure'    => 'tls', // tls (587) or ssl (465)
    'from_name' => 'SchoGMS 2FA Verification',
    'reply_name'=> 'SchoGMS Support',
];
