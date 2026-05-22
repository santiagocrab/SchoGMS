<?php
http_response_code(404);
$base = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '/SchoGMS')), '/');
if ($base === '' || $base === '.') {
    $base = '/SchoGMS';
}
$home = $base . '/index.php';
$adminHome = $base . '/admin/dashboard.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Page Not Found — SchoGMS</title>
    <link rel="icon" type="image/png" href="<?= htmlspecialchars($base) ?>/assets/images/logo.png">
    <link href="<?= htmlspecialchars($base) ?>/dist/css/style.min.css" rel="stylesheet">
    <style>
        .error-page { min-height: 100vh; display: flex; align-items: center; justify-content: center; background: linear-gradient(135deg, #f5f7fb 0%, #e8eef8 100%); }
        .error-card { max-width: 520px; text-align: center; padding: 3rem 2rem; border-radius: 12px; box-shadow: 0 10px 40px rgba(0,0,0,.08); background: #fff; }
        .error-code { font-size: 5rem; font-weight: 700; color: #5c6bc0; line-height: 1; margin-bottom: .5rem; }
        .error-title { font-size: 1.5rem; font-weight: 600; color: #2c3e50; margin-bottom: 1rem; }
        .error-text { color: #6c757d; margin-bottom: 2rem; }
    </style>
</head>
<body>
    <div class="error-page">
        <div class="error-card">
            <img src="<?= htmlspecialchars($base) ?>/assets/images/logo.png" alt="SchoGMS" style="max-width:180px;margin-bottom:1.5rem;">
            <div class="error-code">404</div>
            <h1 class="error-title">Page Not Found</h1>
            <p class="error-text">The page you requested does not exist or may have been moved. Please check the URL or return to your dashboard.</p>
            <div class="d-flex flex-column flex-sm-row justify-content-center gap-2">
                <a href="<?= htmlspecialchars($home) ?>" class="btn btn-primary mr-sm-2 mb-2 mb-sm-0">Back to Login</a>
                <a href="<?= htmlspecialchars($adminHome) ?>" class="btn btn-outline-secondary">Admin Dashboard</a>
            </div>
        </div>
    </div>
</body>
</html>
