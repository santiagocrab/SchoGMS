<?php
include '../config/session.php';
require_once __DIR__ . '/../../inc/campus_access.php';

schogms_ensure_campus_access_tables($conn);
schogms_seed_campus_access_catalog($conn);

$colleges = [];
$campusFilter = trim((string) ($sheet_name ?? ''));
if ($campusFilter !== '') {
    $colleges = schogms_get_colleges_for_campus($conn, $campusFilter);
}

$deanRows = [];
$deanCampusWarning = '';
if ($campusFilter === '') {
    $deanCampusWarning = 'No campus is assigned to your director account. You cannot view or assign deans until a coordinator assigns your campus.';
} else {
    $deanStmt = $conn->prepare(
        'SELECT id, campus, college_name, course_program, dean, status, assigned_at
         FROM assigned_dean
         WHERE UPPER(TRIM(campus)) = UPPER(TRIM(?))
         ORDER BY assigned_at DESC
         LIMIT 500'
    );
    if ($deanStmt) {
        $deanStmt->bind_param('s', $campusFilter);
        $deanStmt->execute();
        $deanRes = $deanStmt->get_result();
        while ($row = $deanRes->fetch_assoc()) {
            $deanRows[] = $row;
        }
        $deanStmt->close();
    }
}
?>
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
    <!-- Custom CSS -->

    <!-- This page plugin CSS -->
    <link href="../../assets/extra-libs/datatables.net-bs4/css/dataTables.bootstrap4.css" rel="stylesheet">
    <link href="../../dist/css/style.min.css" rel="stylesheet">
    <style>.preloader { display: none !important; }</style>
    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
<![endif]-->
</head>

<body>
<?php schogms_loading_screen_once(); ?>

    <!-- ============================================================== -->
    <!-- Preloader - style you can find in spinners.css -->
    <!-- ============================================================== -->
    <!-- ============================================================== -->
    <!-- Main wrapper - style you can find in pages.scss -->
    <!-- ============================================================== -->
    <div id="main-wrapper" data-theme="light" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full"
        data-sidebar-position="fixed" data-header-position="fixed" data-boxed-layout="full">
        <!-- ============================================================== -->
        <!-- Topbar header - style you can find in pages.scss -->
        <!-- ============================================================== -->
        <header class="topbar" data-navbarbg="skin6">
            <nav class="navbar top-navbar navbar-expand-md">
                <div class="navbar-header" data-logobg="skin6">
                    <!-- This is for the sidebar toggle which is visible on mobile only -->
                    <a class="nav-toggler waves-effect waves-light d-block d-md-none" href="javascript:void(0)"><i
                            class="ti-menu ti-close"></i></a>
                    <!-- ============================================================== -->
                    <!-- Logo -->
                    <!-- ============================================================== -->
                    <div class="navbar-brand">
                        <!-- Logo icon -->
                        <a href="index.php">
                            <b class="logo-icon">
                                <!-- Dark Logo icon -->
                                <img src="../../assets/images/logo.png" style="height: auto; width: 200px;"
                                    alt="homepage" class="dark-logo" />
                                <!-- Light Logo icon -->
                                <img src="../../assets/images/logo.png" alt="homepage" class="light-logo" />
                            </b>
                        </a>
                    </div>
                    <!-- ============================================================== -->
                    <!-- End Logo -->
                    <!-- ============================================================== -->
                    <!-- ============================================================== -->
                    <!-- Toggle which is visible on mobile only -->
                    <!-- ============================================================== -->
                    <a class="topbartoggler d-block d-md-none waves-effect waves-light" href="javascript:void(0)"
                        data-toggle="collapse" data-target="#navbarSupportedContent"
                        aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation"><i
                            class="ti-more"></i></a>
                </div>
                <!-- ============================================================== -->
                <!-- End Logo -->
                <!-- ============================================================== -->
                <div class="navbar-collapse collapse" id="navbarSupportedContent">
                    <!-- ============================================================== -->
                    <!-- toggle and nav items -->
                    <!-- ============================================================== -->
                    <ul class="navbar-nav float-left mr-auto ml-3 pl-1">
                        <!-- Notification -->
                        <h3 class="page-title text-truncate text-dark font-weight-medium mb-1">Scholarship and Grants
                            Management System</h3>

                        <!-- End Notification -->
                        <!-- ============================================================== -->
                        <!-- create new -->
                        <!-- ============================================================== -->

                    </ul>
                    <!-- ============================================================== -->
                    <!-- Right side toggle and nav items -->
                    <!-- ============================================================== -->
                    <ul class="navbar-nav float-right">
                        <!-- ============================================================== -->
                        <!-- User profile and search -->
                        <!-- ============================================================== -->
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="javascript:void(0)" data-toggle="dropdown"
                                aria-haspopup="true" aria-expanded="false">
                                <img src="../../assets/images/users/image.png" alt="user" class="rounded-circle"
                                    width="40">
                                <span class="ml-2 d-none d-lg-inline-block"><span>Hello,</span> <span
                                        class="text-dark"><?= $fullname ?></span> <i data-feather="chevron-down"
                                        class="svg-icon"></i></span>
                            </a>
                            <div class="dropdown-menu dropdown-menu-right user-dd animated flipInY">
                                <a class="dropdown-item" href="change_password.php"><i data-feather="key"
                                        class="svg-icon mr-2 ml-1"></i>
                                    Change Password</a>
                                <a class="dropdown-item" href="logout.php"><i data-feather="power"
                                        class="svg-icon mr-2 ml-1"></i>
                                    Logout</a>
                            </div>
                        </li>
                        <!-- ============================================================== -->
                        <!-- User profile and search -->
                        <!-- ============================================================== -->
                    </ul>
                </div>
            </nav>
        </header>
        <!-- ============================================================== -->
        <!-- End Topbar header -->
        <!-- ============================================================== -->
        <!-- ============================================================== -->
        <!-- Left Sidebar - style you can find in sidebar.scss  -->
        <!-- ============================================================== -->
        <aside class="left-sidebar" data-sidebarbg="skin6">
            <!-- Sidebar scroll-->
            <div class="scroll-sidebar" data-sidebarbg="skin6">
                <!-- Sidebar navigation-->
                <nav class="sidebar-nav">
                    <ul id="sidebarnav">
                        <?php require __DIR__ . "/inc/director_sidebar_menu.php"; ?>
                    </ul>
                </nav>
                <!-- End Sidebar navigation -->
            </div>
            <!-- End Sidebar scroll-->
        </aside>
        <!-- ============================================================== -->
        <!-- End Left Sidebar - style you can find in sidebar.scss  -->
        <!-- ============================================================== -->
        <!-- ============================================================== -->
        <!-- Page wrapper  -->
        <!-- ============================================================== -->
        <div class="page-wrapper">
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
                                    <li class="breadcrumb-item"><a href="index.php">Registrar College Dean</a>
                                    </li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                    <div class="col-5 align-self-center">
                        <div class="customize-input float-right">
                            <button type="button" class="btn waves-effect waves-light btn-rounded btn-success"
                                data-toggle="modal" data-target="#documentUploadModal"
                                <?= $campusFilter === '' ? 'disabled title="Campus not set on your account"' : '' ?>>
                                Assign College Dean
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal fade" id="documentUploadModal" tabindex="-1" role="dialog"
                aria-labelledby="documentUploadModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="documentUploadModalLabel">Assigned College Dean</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <form id="documentUploadForm" action="submit_document.php" method="post"
                                enctype="multipart/form-data">
                                <!-- Campus -->
                                <div class="form-group">
                                    <label for="session_campus">CAMPUS</label>
                                    <input type="text" class="form-control" id="session_campus" name="session_campus"
                                        value="<?= htmlspecialchars($sheet_name); ?>" readonly>

                                    <input type="text" class="form-control" id="role" name="role"
                                        value="<?= htmlspecialchars($sheet_name); ?>" hidden>
                                </div>

                                <!-- College (one dean per college) -->
                                <div class="form-group">
                                    <label for="collegeName">College</label>
                                    <select class="form-control" id="collegeName" name="college_name" required>
                                        <option value="" disabled selected>Select college</option>
                                        <?php foreach ($colleges as $col): ?>
                                            <option value="<?= htmlspecialchars((string) $col['college_name']); ?>">
                                                <?= htmlspecialchars((string) $col['college_name']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <!-- Program Chair Name -->
                                <div class="form-group">
                                    <label for="programChair">Dean Name</label>
                                    <input type="text" class="form-control" id="programChair" name="program_chair_name"
                                        placeholder="Enter dean name" required>
                                </div>

                                <div class="form-group">
                                    <label for="usermail">Dean Email</label>
                                    <input type="text" class="form-control" id="usermail" name="usermail"
                                        placeholder="Enter dean email" required>
                                </div>

                                <!-- Submit Button -->
                                <div class="form-group">
                                    <button type="submit" class="btn btn-primary">Submit Access</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <script>
                document.querySelector('#documentUploadForm').addEventListener('submit', async (event) => {
                    event.preventDefault(); // Prevent default form submission

                    const formData = new FormData(event.target);

                    // Show a confirmation SweetAlert
                    const confirmation = await Swal.fire({
                        title: 'Are you sure?',
                        text: 'Do you want to submit this information?',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Yes, submit it!',
                        cancelButtonText: 'Cancel'
                    });

                    if (!confirmation.isConfirmed) {
                        return; // Stop if user cancels
                    }

                    // Create XMLHttpRequest object to handle the submission
                    const xhr = new XMLHttpRequest();

                    xhr.open('POST', 'submit_chair.php', true);
                    xhr.onload = async () => {
                        const result = JSON.parse(xhr.responseText);
                        if (result.success) {
                            await Swal.fire({
                                icon: 'success',
                                title: 'Success',
                                text: result.message,
                                confirmButtonText: 'OK',
                            });

                            event.target.reset();
                            $('#documentUploadModal').modal('hide');
                            location.reload();
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: result.message,
                            });
                        }
                    };

                    xhr.send(formData);
                });
            </script>

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
                                <h4 class="card-title">College deans — campus: <strong><?= htmlspecialchars($campusFilter !== '' ? $campusFilter : 'Not set') ?></strong></h4>
                                <?php if ($deanCampusWarning !== ''): ?>
                                <div class="alert alert-warning"><?= htmlspecialchars($deanCampusWarning) ?></div>
                                <?php else: ?>
                                <p class="text-muted small">Assign one dean per college on your campus. Each dean may assign program chairs per course in that college.</p>
                                <?php endif; ?>
                                <div class="table-responsive">
                                    <table id="zero_config" class="table table-striped table-bordered no-wrap">
                                        <thead>
                                            <tr>
                                                <th>Status</th>
                                                <th>College</th>
                                                <th>Dean</th>
                                                <th>Date Added</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <!-- Loop through each user to generate table rows -->
                                            <?php
                                            if (count($deanRows) > 0) {
                                                foreach ($deanRows as $row) {
                                                    $statusBadge = match ($row['status']) {
                                                        'active' => '<span class="badge badge-success btn-rounded">Active</span>',
                                                        'pending' => '<span class="badge badge-warning btn-rounded">Pending</span>',
                                                        'restricted' => '<span class="badge badge-secondary btn-rounded">Restricted</span>',
                                                        default => '<span class="badge badge-light btn-rounded">' . htmlspecialchars((string) $row['status']) . '</span>',
                                                    };
                                                    echo '<tr>';
                                                    echo '<td>' . $statusBadge . '</td>';
                                                    $collegeLabel = schogms_resolve_dean_college_name($row);
                                                    echo '<td>' . htmlspecialchars($collegeLabel) . '</td>';
                                                    echo '<td>' . htmlspecialchars((string) $row['dean']) . '</td>';
                                                    echo '<td>' . htmlspecialchars((string) $row['assigned_at']) . '</td>';
                                                    echo '<td><button type="button" class="btn btn-danger delete-btn btn-rounded" data-id="' . (int) $row['id'] . '"><i class="fa fa-trash"></i></button></td>';
                                                    echo '</tr>';
                                                }
                                            } else {
                                                echo '<tr><td colspan="5">No college deans assigned yet.</td></tr>';
                                            }
                                            ?>
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <th>Status</th>
                                                <th>College</th>
                                                <th>Dean</th>
                                                <th>Date Added</th>
                                                <th>Action</th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <!-- ============================================================== -->
                        <!-- End PAge Content -->
                        <!-- ============================================================== -->
                    </div>
                                                <script>
                                                    document.querySelectorAll('.delete-btn').forEach(button => {
                                                        button.addEventListener('click', function () {
                                                            let id = this.getAttribute('data-id');
                                        
                                                            Swal.fire({
                                                                title: "Are you sure?",
                                                                text: "You won't be able to revert this!",
                                                                icon: "warning",
                                                                showCancelButton: true,
                                                                confirmButtonColor: "#d33",
                                                                cancelButtonColor: "#3085d6",
                                                                confirmButtonText: "Yes, delete it!"
                                                            }).then((result) => {
                                                                if (result.isConfirmed) {
                                                                    fetch('delete_file.php', {
                                                                        method: 'POST',
                                                                        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                                                                        body: 'id=' + encodeURIComponent(id)
                                                                    })
                                                                        .then(response => response.json())
                                                                        .then(data => {
                                                                            if (data.success) {
                                                                                Swal.fire("Deleted!", "The file has been deleted.", "success").then(() => {
                                                                                    location.reload();
                                                                                });
                                                                            } else {
                                                                                Swal.fire("Error!", data.message, "error");
                                                                            }
                                                                        });
                                                                }
                                                            });
                                                        });
                                                    });
                                                </script>

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
            <!-- All Jquery -->
            <!-- ============================================================== -->
            <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
            <script src="../../assets/libs/jquery/dist/jquery.min.js"></script>
            <script src="../../assets/libs/popper.js/dist/umd/popper.min.js"></script>
            <script src="../../assets/libs/bootstrap/dist/js/bootstrap.min.js"></script>
            <script src="../../dist/js/app-style-switcher.js"></script>
            <script src="../../dist/js/feather.min.js"></script>
            <script src="../../assets/libs/perfect-scrollbar/dist/perfect-scrollbar.jquery.min.js"></script>
            <script src="../../dist/js/sidebarmenu.js"></script>
            <script src="../../dist/js/custom.min.js"></script>
            <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
            <script src="../../assets/extra-libs/datatables.net/js/jquery.dataTables.min.js"></script>
            <script src="../../assets/extra-libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js"></script>
            <script>
            $(function () {
                if ($.fn.DataTable && $('#zero_config').length) {
                    $('#zero_config').DataTable({
                        pageLength: 25,
                        lengthMenu: [[10, 25, 50, -1], [10, 25, 50, 'All']],
                        deferRender: true,
                        order: [[3, 'desc']],
                        stateSave: false
                    });
                }
                if (typeof feather !== 'undefined') {
                    feather.replace();
                }
            });
            </script>
</body>

</html>