<?php
include '../config/session.php';
require_once __DIR__ . '/inc/director_assets.php';
require_once __DIR__ . '/inc/director_nav.php';
require_once __DIR__ . '/inc/director_scholar_list.php';

$campus = trim((string) ($sheet_name ?? ''));
?>
<!DOCTYPE html>
<html dir="ltr" lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/png" sizes="16x16" href="../../assets/images/logo.png">
    <title>TES scholars | SchoGMS</title>
    <?php schogms_director_head(true); ?>
</head>
<body>
<?php schogms_director_shell_open('TES scholars'); ?>
            <div class="page-breadcrumb">
                <div class="row">
                    <div class="col-7 align-self-center">
                        <div class="d-flex align-items-center">
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb m-0 p-0">
                                    <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                                    <li class="breadcrumb-item active">TES scholars</li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
            <div class="container-fluid">
                <?php schogms_director_render_scholar_list($conn, $campus, 'tes'); ?>
            </div>
            <footer class="footer text-center text-muted">
                All Rights Reserved 2026. Scholarship and Grants Management System (SchoGMS).
            </footer>
<?php schogms_director_shell_close(); ?>
<?php schogms_director_footer_scripts(['datatables' => true]); ?>
</body>
</html>
