<?php
session_start();
$sheet_name = $_SESSION['sheet_name'] ?? 'Export';
$success = $_SESSION['export_success'] ?? false;

// Clear session so alert only shows once
unset($_SESSION['export_success']);
unset($_SESSION['sheet_name']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Export Success</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
<?php schogms_loading_screen_once(); ?>

<?php if ($success): ?>
<script>
    Swal.fire({
        title: 'Success!',
        text: 'Export successful for <?= htmlspecialchars($sheet_name) ?>. An email has been sent to the chairman.',
        icon: 'success',
        confirmButtonText: 'Close'
    }).then(() => {
        window.close(); // This will close the current window/tab (works if it was opened via script)
        // Or you can use: window.location.href = 'dashboard.php'; to redirect somewhere else
    });
</script>
<?php endif; ?>
</body>
</html>
