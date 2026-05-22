<?php include '../config/session.php'; ?>
<!DOCTYPE html>
<html dir="ltr" lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <!-- Tell the browser to be responsive to screen width -->
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <!-- Favicon icon -->
    <link rel="icon" type="image/png" sizes="16x16" href="../../assets/images/logo.png">
    <title> Scholarship and Grants Management System | SchoGMS </title>
    <!-- Custom CSS -->    <?php require_once __DIR__ . '/inc/assets.php'; schogms_coordinator_head(true); ?>
    <style>.preloader{display:none!important}.format-sample-table{font-size:13px}.format-sample-table th{background:#f8f9fa}</style>
    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
<![endif]-->
</head>

<body>
<?php schogms_loading_screen_once(); ?>

    <?php include 'loading-screen.php'; ?>
    <!-- ============================================================== -->
    <!-- Preloader - style you can find in spinners.css -->
    <!-- ============================================================== -->
    <div class="preloader">
        <div class="lds-ripple">
            <div class="lds-pos"></div>
            <div class="lds-pos"></div>
        </div>
    </div>
    <!-- ============================================================== -->
    <!-- Main wrapper - style you can find in pages.scss -->
    <!-- ============================================================== -->
    <?php require_once __DIR__ . '/inc/coordinator_nav.php'; schogms_coordinator_shell_open(); ?>

            <!-- ============================================================== -->
            <!-- Bread crumb and right sidebar toggle -->
            <!-- ============================================================== -->
            <div class="page-breadcrumb">
                <div class="row">
                    <div class="col-7 align-self-center">
                        <!-- <h3 class="page-title text-truncate text-dark font-weight-medium mb-1">Good Coordinator!</h3> -->
                        <div class="d-flex align-items-center">
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb m-0 p-0">
                                    <li class="breadcrumb-item"><a href="index.php">Dashboard</a>
                                    </li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                    <div class="col-5 align-self-center">
                        <div class="customize-input float-right">
                            <button type="button" class="btn waves-effect waves-light btn-rounded btn-success"
                                data-toggle="modal" data-target="#uploadModal">
                                Bulk upload COR
                            </button>
                            <a href="cor-cog.php#bulk-cor-cog-upload" class="btn btn-outline-primary btn-rounded ml-1">COR &amp; COG bulk</a>
                        </div>
                    </div>
                </div>
            </div>
            <?php
            $corCogCategory = 'COR';
            $corCogCampus = trim((string) ($sheet_name ?? ''));
            require __DIR__ . '/inc/cor_cog_upload_modal.php';
            ?>
            <!-- Modal -->
            <!-- ============================================================== -->
            <!-- End Bread crumb and right sidebar toggle -->
            <!-- ============================================================== -->
            <!-- ============================================================== -->
            <!-- Container fluid  -->
            <!-- ============================================================== -->
            <!-- Container fluid  -->
            <!-- ============================================================== -->
            <div class="container-fluid">
                <!-- ============================================================== -->
                <!-- Start Page Content -->
                <!-- ============================================================== -->
                <!-- basic table -->
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="row">
                                    <div class="table-responsive">
                                        <?php
                                        require_once __DIR__ . '/inc/coordinator_data.php';
                                        $campusFilter = trim((string) ($sheet_name ?? ''));
                                        $docData = schogms_coordinator_documents($conn, $campusFilter, 'COR');
                                        $docRows = $docData['rows'];
                                        $docError = $docData['error'];
                                        if ($docError !== ''): ?>
                                            <div class="alert alert-warning"><?= htmlspecialchars($docError) ?></div>
                                        <?php endif; ?>

                                        <table id="zero_config" class="table table-striped table-bordered no-wrap">
                                            <thead>
                                                <tr>
                                                    <th>Campus</th>
                                                    <th>File Group</th>
                                                    <th>File Name</th>
                                                    <th>Uploaded</th>
                                                    <th>View</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php if (count($docRows) === 0): ?>
                                                    <tr><td colspan="5" class="text-center text-muted">No COR documents uploaded<?= $campusFilter !== '' ? ' for ' . htmlspecialchars($campusFilter) : '' ?>.</td></tr>
                                                <?php else: ?>
                                                <?php foreach ($docRows as $row): ?>
                                                    <tr>
                                                        <td><?= htmlspecialchars((string) ($row['campus'] ?? '')) ?></td>
                                                        <td><?= htmlspecialchars((string) ($row['file_group'] ?? '')) ?></td>
                                                        <td><?= htmlspecialchars((string) ($row['file_name'] ?? '')) ?></td>
                                                        <td><?= htmlspecialchars((string) ($row['uploaded_at'] ?? '')) ?></td>
                                                        <td><?php if (!empty($row['file_path'])): ?><a href="<?= htmlspecialchars((string) $row['file_path']) ?>" target="_blank" rel="noopener">Open</a><?php else: ?>—<?php endif; ?></td>
                                                    </tr>
                                                <?php endforeach; ?>
                                                <?php endif; ?>
                                            </tbody>
                                        </table>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- ============================================================== -->
                    <!-- End PAge Content -->
                    <!-- ============================================================== -->
                </div>

                <?php
                require_once __DIR__ . '/inc/cor_cog_format_guide.php';
                schogms_coordinator_render_cor_cog_format('COR', trim((string) ($sheet_name ?? '')));
                ?>
            </div>
                <!-- ============================================================== -->
                <!-- End Container fluid  -->
                <!-- ============================================================== -->
                <!-- ============================================================== -->
                <!-- footer -->
                <!-- ============================================================== -->
                <footer class="footer text-center text-muted">
                    All Rights Reserved 2026. Scholarship and Grants Management System <a href="">(SchoGMS)</a>.
                </footer>
                <!-- ============================================================== -->
                <!-- End footer -->
                <!-- ============================================================== -->
            </div>
            <!-- ============================================================== -->
            <!-- End Page wrapper  -->
            <!-- ============================================================== -->
        </div>
        <!-- ============================================================== -->
        <!-- End Wrapper -->
        <!-- ============================================================== -->
        <!-- End Wrapper -->
        <!-- ============================================================== -->
    <?php schogms_coordinator_footer_scripts(['datatables' => true, 'sweetalert' => true]); ?>
    <?php require __DIR__ . '/inc/cor_cog_upload_script.php'; ?>

</body>

</html>