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
    <!-- Custom CSS -->

    <!-- This page plugin CSS -->
    <link href="../../assets/extra-libs/datatables.net-bs4/css/dataTables.bootstrap4.css" rel="stylesheet">
    <link href="../../assets/extra-libs/c3/c3.min.css" rel="stylesheet">
    <link href="../../assets/libs/chartist/dist/chartist.min.css" rel="stylesheet">
    <link href="../../assets/extra-libs/jvector/jquery-jvectormap-2.0.2.css" rel="stylesheet" />
    <!-- Custom CSS -->
    <link href="../../dist/css/style.min.css" rel="stylesheet">
    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
<![endif]-->
</head>

<body>
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
                                <img src="../../assets/images/users/image.png" alt="user" class="rounded-circle" width="40">
                                <span class="ml-2 d-none d-lg-inline-block"><span>Hello,</span> <span
                                        class="text-dark"><?= $fullname ?></span> <i data-feather="chevron-down"
                                        class="svg-icon"></i></span>
                            </a>
                            <div class="dropdown-menu dropdown-menu-right user-dd animated flipInY">
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
                        <li class="sidebar-item"> <a class="sidebar-link sidebar-link" href="index.php"
                                aria-expanded="false"><i data-feather="home" class="feather-icon"></i><span
                                    class="hide-menu">Dashboard</span></a></li>
                        <li class="list-divider"></li>
                        <li class="nav-small-cap"><span class="hide-menu">Applications</span></li>
                        <li class="sidebar-item"> <a class="sidebar-link" href="masterlist.php" aria-expanded="false"><i
                                    data-feather="users" class="feather-icon"></i><span class="hide-menu">Master list
                                </span></a>
                        </li>
                        <li class="sidebar-item"> <a class="sidebar-link sidebar-link" href="enrollment_status.php"
                                aria-expanded="false"><i data-feather="folder" class="feather-icon"></i><span
                                    class="hide-menu">Enrollment Status</span></a></li>
                        <li class="sidebar-item"> <a class="sidebar-link sidebar-link" href="cor.php"
                                aria-expanded="false"><i data-feather="book-open" class="feather-icon"></i><span
                                    class="hide-menu">COR</span></a></li>
                        <li class="sidebar-item"> <a class="sidebar-link sidebar-link" href="cog.php"
                                aria-expanded="false"><i data-feather="book-open" class="feather-icon"></i><span
                                    class="hide-menu">COG</span></a></li>
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
                                Upload File
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal fade" id="uploadModal" tabindex="-1" role="dialog" aria-labelledby="uploadModalLabel"
                aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="uploadModalLabel">Upload Student Data</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <form id="uploadForm">
                                <div class="mb-3">
                                    <label for="excelFile" class="form-label">Choose Excel File</label>
                                    <input type="file" class="form-control" id="excelFile" name="excelFile"
                                        accept=".xlsx, .xls">
                                </div>
                                <button type="submit" class="btn btn-primary">Upload</button>
                            </form>
                            <div id="message"></div>
                        </div>
                    </div>
                </div>
            </div>
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
                                        // Include your database connection
                                        require '../config/conn.php';

                                        // Query to select data from billing_table
                                        $query = "SELECT * FROM billing_table";
                                        $result = $conn->query($query);

                                        // Check for errors in the query
                                        if (!$result) {
                                            die("Query failed: " . $conn->error);
                                        }
                                        ?>

                                        <table id="zero_config" class="table table-striped table-bordered no-wrap">
                                            <thead>
                                                <tr>
                                                    <!-- <th>Status</th> -->
                                                    <th>Last Name</th>
                                                    <th>First Name</th>
                                                    <th>Scholarship Type</th>
                                                    <th>Units Enrolled</th>
                                                    <th>Course</th>
                                                    <th>Campus</th>
                                                    <th>Year & Date Submitted (CHED)</th>
                                                    <th>Amount</th>
                                                    <th>First Semester</th>
                                                    <th>Second Semester</th>
                                                    <th>Payment Scholarship Type</th>
                                                    <th>Payment Amount</th>
                                                    <th>Payment Year & Date</th>
                                                    <th>Payment OR Number</th>
                                                    <th>Payment Amount Per OR</th>
                                                    <th>Refund First Semester</th>
                                                    <th>Refund Second Semester</th>
                                                    <th>Refund Year & Date Released</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php while ($row = $result->fetch_assoc()): ?>
                                                    <tr>
                                                        <!-- <td> -->
                                                        <?php
                                                        // // Displaying status with different badge colors
                                                        // switch ($row['status']) {
                                                        //     case 'In Progress':
                                                        //         echo '<span class="badge badge-light-warning">In Progress</span>';
                                                        //         break;
                                                        //     case 'Closed':
                                                        //         echo '<span class="badge badge-light-danger">Closed</span>';
                                                        //         break;
                                                        //     case 'Opened':
                                                        //         echo '<span class="badge badge-light-success">Opened</span>';
                                                        //         break;
                                                        //     default:
                                                        //         echo '<span class="badge badge-light-secondary">Unknown</span>';
                                                        // }
                                                        ?>
                                                        <!-- </td> -->
                                                        <td><?php echo htmlspecialchars($row['last_name']); ?></td>
                                                        <td><?php echo htmlspecialchars($row['first_name']); ?></td>
                                                        <td><?php echo htmlspecialchars($row['scholarship_type']); ?></td>
                                                        <td><?php echo htmlspecialchars($row['units_enrolled']); ?></td>
                                                        <td><?php echo htmlspecialchars($row['course']); ?></td>
                                                        <td><?php echo htmlspecialchars($row['campus']); ?></td>
                                                        <td><?php echo htmlspecialchars($row['year_and_date_submitted_ched']); ?>
                                                        </td>
                                                        <td><?php echo htmlspecialchars($row['amount']); ?></td>
                                                        <td><?php echo htmlspecialchars($row['first_semester']); ?></td>
                                                        <td><?php echo htmlspecialchars($row['second_semester']); ?></td>
                                                        <td><?php echo htmlspecialchars($row['payment_scholarship_type']); ?>
                                                        </td>
                                                        <td><?php echo htmlspecialchars($row['payment_amount']); ?></td>
                                                        <td><?php echo htmlspecialchars($row['payment_year_and_date']); ?>
                                                        </td>
                                                        <td><?php echo htmlspecialchars($row['payment_or_number']); ?></td>
                                                        <td><?php echo htmlspecialchars($row['payment_amount_per_or']); ?>
                                                        </td>
                                                        <td><?php echo htmlspecialchars($row['refund_first_sem']); ?></td>
                                                        <td><?php echo htmlspecialchars($row['refund_second_sem']); ?></td>
                                                        <td><?php echo htmlspecialchars($row['refund_year_and_date_released']); ?>
                                                        </td>
                                                    </tr>
                                                <?php endwhile; ?>
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <!-- <th>Status</th> -->
                                                    <th>Last Name</th>
                                                    <th>First Name</th>
                                                    <th>Scholarship Type</th>
                                                    <th>Units Enrolled</th>
                                                    <th>Course</th>
                                                    <th>Campus</th>
                                                    <th>Year & Date Submitted (CHED)</th>
                                                    <th>Amount</th>
                                                    <th>First Semester</th>
                                                    <th>Second Semester</th>
                                                    <th>Payment Scholarship Type</th>
                                                    <th>Payment Amount</th>
                                                    <th>Payment Year & Date</th>
                                                    <th>Payment OR Number</th>
                                                    <th>Payment Amount Per OR</th>
                                                    <th>Refund First Semester</th>
                                                    <th>Refund Second Semester</th>
                                                    <th>Refund Year & Date Released</th>
                                                </tr>
                                            </tfoot>
                                        </table>

                                        <?php
                                        // Close the database connection
                                        $conn->close();
                                        ?>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- ============================================================== -->
                    <!-- End PAge Content -->
                    <!-- ============================================================== -->
                </div>
                <!-- ============================================================== -->
                <!-- End Container fluid  -->
                <!-- ============================================================== -->
                <!-- ============================================================== -->
                <!-- footer -->
                <!-- ============================================================== -->
                <footer class="footer text-center text-muted">
                    All Rights Reserved 2025. Scholarship and Grants Management System <a href="">(SchoGMS)</a>.
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
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
        <script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
        <script>
            document.getElementById('uploadForm').addEventListener('submit', function (event) {
                event.preventDefault();

                const fileInput = document.getElementById('excelFile');
                const file = fileInput.files[0];

                if (!file) {
                    // No file selected
                    showToast("Please select a file!", "error");
                    return;
                }

                // Validate file type
                const allowedTypes = [
                    'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                    'application/vnd.ms-excel'
                ];

                if (!allowedTypes.includes(file.type)) {
                    showToast("Please upload a valid Excel file.", "error");
                    console.error("Invalid file type:", file.type);
                    return;
                }

                const formData = new FormData();
                formData.append('excelFile', file);

                // Display loading message
                showToast("Uploading and processing file...", "loading");

                // Send file via Fetch API
                fetch('process_excel.php', {
                    method: 'POST',
                    body: formData
                })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error(`HTTP error! status: ${response.status}`);
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.success) {
                            showToast(data.message || "File processed successfully!", "success");
                        } else {
                            showToast(data.error || "An error occurred during file processing.", "error");
                            console.error("Server error:", data.error);
                        }
                    })
                    .catch(error => {
                        showToast("An error occurred while uploading the file.", "error");
                        console.error("Fetch error:", error);
                    });
            });

            /**
             * Displays a toast message using Toastify.
             * @param {string} message - The message to display.
             * @param {string} type - The type of the message (success, error, loading).
             */
            function showToast(message, type) {
                let style;
                switch (type) {
                    case "success":
                        style = "linear-gradient(to right, #00b09b, #96c93d)";
                        break;
                    case "error":
                        style = "linear-gradient(to right, #ff5f6d, #ffc371)";
                        break;
                    case "loading":
                        style = "linear-gradient(to right, #0078D7, #00b4d8)";
                        break;
                    default:
                        style = "linear-gradient(to right, #555, #888)";
                }

                Toastify({
                    text: message,
                    duration: type === "loading" ? 5000 : 3000,
                    close: true,
                    gravity: "top",
                    position: "center",
                    style: { background: style }
                }).showToast();
            }
        </script>



        <script src="../../assets/libs/jquery/dist/jquery.min.js"></script>
        <script src="../../assets/libs/popper.js/dist/umd/popper.min.js"></script>
        <script src="../../assets/libs/bootstrap/dist/js/bootstrap.min.js"></script>
        <!-- apps -->
        <!-- apps -->
        <script src="../../dist/js/app-style-switcher.js"></script>
        <script src="../../dist/js/feather.min.js"></script>
        <script src="../../assets/libs/perfect-scrollbar/dist/perfect-scrollbar.jquery.min.js"></script>
        <script src="../../dist/js/sidebarmenu.js"></script>
        <!--Custom JavaScript -->
        <script src="../../dist/js/custom.min.js"></script>
        <!--This page JavaScript -->
        <script src="../../assets/extra-libs/c3/d3.min.js"></script>
        <script src="../../assets/extra-libs/c3/c3.min.js"></script>
        <script src="../../assets/libs/chartist/dist/chartist.min.js"></script>
        <script src="../../assets/libs/chartist-plugin-tooltips/dist/chartist-plugin-tooltip.min.js"></script>
        <script src="../../assets/extra-libs/jvector/jquery-jvectormap-2.0.2.min.js"></script>
        <script src="../../assets/extra-libs/jvector/jquery-jvectormap-world-mill-en.js"></script>
        <script src="../../dist/js/pages/dashboards/dashboard1.min.js"></script>

        <!--This page plugins -->
        <script src="../../assets/extra-libs/datatables.net/js/jquery.dataTables.min.js"></script>
        <script src="../../dist/js/pages/datatable/datatable-basic.init.js"></script>
</body>

</html>