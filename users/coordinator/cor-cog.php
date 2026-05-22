<?php
/**
 * COR & COG hub — links to separate Certificate of Registration and Grades pages.
 */
require __DIR__ . '/../config/session.php';
?>
<!DOCTYPE html>
<html dir="ltr" lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/png" href="../../assets/images/logo.png">
    <title>COR &amp; COG — SchoGMS</title>
    <?php require_once __DIR__ . '/inc/assets.php'; schogms_coordinator_head(false); ?>
</head>
<body>
<?php require_once __DIR__ . '/inc/coordinator_nav.php'; schogms_coordinator_shell_open('COR & COG'); ?>
        <div class="container-fluid">
            <div class="page-breadcrumb"><nav aria-label="breadcrumb"><ol class="breadcrumb"><li class="breadcrumb-item"><a href="index.php">Dashboard</a></li><li class="breadcrumb-item active">COR &amp; COG</li></ol></nav></div>
            <div class="row">
                <div class="col-md-6 mb-4">
                    <div class="card h-100">
                        <div class="card-body text-center">
                            <i data-feather="file-text" class="feather-icon text-primary" style="width:48px;height:48px"></i>
                            <h4 class="mt-3">Certificate of Registration (COR)</h4>
                            <p class="text-muted">View and upload scholar COR documents for <?= schogms_e($sheet_name) ?>.</p>
                            <a href="cor.php" class="btn btn-primary">Open COR</a>
                            <a href="cor.php#upload-format" class="btn btn-outline-primary btn-sm ml-1">Upload format</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 mb-4">
                    <div class="card h-100">
                        <div class="card-body text-center">
                            <i data-feather="award" class="feather-icon text-success" style="width:48px;height:48px"></i>
                            <h4 class="mt-3">Certificate of Grades (COG)</h4>
                            <p class="text-muted">View and upload scholar COG documents for <?= schogms_e($sheet_name) ?>.</p>
                            <a href="cog.php" class="btn btn-success">Open COG</a>
                            <a href="cog.php#upload-format" class="btn btn-outline-success btn-sm ml-1">Upload format</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <div class="card border-primary">
                        <div class="card-body">
                            <h4 class="card-title text-primary mb-3">Upload format (COR &amp; COG)</h4>
                            <p class="text-muted mb-2">
                                One PDF or image per scholar. Filename pattern:
                                <code class="text-dark">LASTNAME, FIRSTNAME MIDDLENAME.pdf</code>
                                — must match the CHED masterlist name (e.g. <code>ABACARO, ROSE ANN PIQUE.pdf</code>).
                            </p>
                            <p class="text-muted mb-0 small">
                                Use <strong>Upload File</strong> on the COR or COG page. Full instructions and examples are at the bottom of each page under <strong>Here is the format</strong>.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php require_once __DIR__ . '/inc/assets.php'; schogms_coordinator_footer_scripts(); ?>
</body>
</html>
