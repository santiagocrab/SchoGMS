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
    <?php include 'loading-screen.php'; ?>
    <!-- ============================================================== -->
    <!-- Preloader - style you can find in spinners.css -->
    <!-- ============================================================== -->
    <!-- <div class="preloader">
        <div class="lds-ripple">
            <div class="lds-pos"></div>
            <div class="lds-pos"></div>
        </div>
    </div> -->
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
                        <li class="sidebar-item"> <a class="sidebar-link sidebar-link" href="enrollment_status.php"
                                aria-expanded="false"><i data-feather="folder" class="feather-icon"></i><span
                                    class="hide-menu">Enrollment Status</span></a></li>
                        <li class="sidebar-item"> <a class="sidebar-link sidebar-link" href="validate.php"
                                aria-expanded="false"><i data-feather="user-check" class="feather-icon"></i><span
                                    class="hide-menu">Validate</span></a></li>
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
                    <!-- <div class="col-5 align-self-center">
                        <div class="customize-input float-right">
                            <button type="button" class="btn waves-effect waves-light btn-rounded btn-success"
                                data-toggle="modal" data-target="#uploadModal">
                                Upload File
                            </button>
                        </div>
                    </div> -->
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
                                    <label for="file-group" class="form-label">File Group</label>
                                    <input type="text" class="form-control" id="file_group" name="file_group"
                                        placeholder="Input file group name">
                                </div>
                                <div class="mb-3">
                                    <label for="excelFile" class="form-label">Choose Excel File</label>
                                    <input type="file" class="form-control" id="excelFile" name="excelFile"
                                        accept=".xls,.xlsx, .csv">
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
                                        require '../config/conn.php';

                                        // Get the selected category and file_group from the form (if any)
                                        $category = isset($_GET['category']) ? $_GET['category'] : '';
                                        $file_group = isset($_GET['file_group']) ? $_GET['file_group'] : '';

                                        // Query to get distinct categories and file groups from document_uploads table
                                        $categoryQuery = "SELECT DISTINCT file_group, filename FROM ched_masterlist";
                                        $categoryResult = $conn->query($categoryQuery);

                                        // Check for errors in the query
                                        if (!$categoryResult) {
                                            die("Query failed: " . $conn->error);
                                        }

                                        // Query to select data from document_uploads table (based on selected category and file_group)
                                        $query = "SELECT * FROM ched_masterlist";
                                        $filters = [];
                                        if ($category !== '') {  // Apply the category filter if a category is selected
                                            $filters[] = "filename = '" . $conn->real_escape_string($category) . "'";
                                        }
                                        if ($file_group !== '') {  // Apply the file_group filter if a file_group is selected
                                            $filters[] = "file_group = '" . $conn->real_escape_string($file_group) . "'";
                                        }
                                        if (count($filters) > 0) {
                                            $query .= " WHERE " . implode(" AND ", $filters);
                                        }

                                        $result = $conn->query($query);

                                        // Check for errors in the query
                                        if (!$result) {
                                            die("Query failed: " . $conn->error);
                                        }
                                        ?>

                                        <div class="table-responsive">
                                            <!-- Dropdown to select category and file group -->
                                            <form method="GET" action="" class="col-md-8">
                                                <label for="categoryFilter">Select Category:</label>
                                                <select id="categoryFilter" name="filename" class="form-control">
                                                    <option value="">All</option>
                                                    <?php while ($row = $categoryResult->fetch_assoc()): ?>
                                                        <option value="<?php echo htmlspecialchars($row['filename']); ?>"
                                                            <?php echo ($category == $row['filename']) ? 'selected' : ''; ?>>
                                                            <?php echo htmlspecialchars($row['filename']); ?>
                                                        </option>
                                                    <?php endwhile; ?>
                                                </select>

                                                <label for="fileGroupFilter" class="mt-2">Select File Group:</label>
                                                <select id="fileGroupFilter" name="file_group" class="form-control">
                                                    <option value="">All</option>
                                                    <?php
                                                    // Reset the result pointer for file group options
                                                    $categoryResult->data_seek(0);
                                                    while ($row = $categoryResult->fetch_assoc()): ?>
                                                        <option value="<?php echo htmlspecialchars($row['file_group']); ?>"
                                                            <?php echo ($file_group == $row['file_group']) ? 'selected' : ''; ?>>
                                                            <?php echo htmlspecialchars($row['file_group']); ?>
                                                        </option>
                                                    <?php endwhile; ?>
                                                </select>
                                                <br>
                                                <button type="submit"
                                                    class="btn waves-effect waves-light btn-rounded btn-success">Apply
                                                    Filter Select</button>
                                                <br>
                                                <br>
                                            </form>

                                            <table id="zero_config" class="table table-striped table-bordered no-wrap">
                                                <thead>
                                                    <tr>
                                                        <th>SEQ</th>
                                                        <th>APP NO</th>
                                                        <th>AWARD NO.</th>
                                                        <th>LASTNAME</th>
                                                        <th>FIRSTNAME</th>
                                                        <th>EXTNAME</th>
                                                        <th>MIDDLENAME</th>
                                                        <th>SEX</th>
                                                        <th>BIRTHDATE</th>
                                                        <th>COURSE/PROGRAM ENROLLED</th>
                                                        <th>YEAR LEVEL</th>
                                                        <th>TOTAL UNITS ENROLLED</th>
                                                        <th>STATUS OF ENROLLMENT</th>
                                                        <th>REMARKS</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php while ($row = $result->fetch_assoc()): ?>
                                                        <tr>
                                                            <td><?php echo htmlspecialchars($row['seq']); ?></td>
                                                            <td><?php echo htmlspecialchars($row['app_no']); ?></td>
                                                            <td><?php echo htmlspecialchars($row['award_no']); ?></td>
                                                            <td><?php echo htmlspecialchars($row['lastname']); ?></td>
                                                            <td><?php echo htmlspecialchars($row['firstname']); ?></td>
                                                            <td><?php echo htmlspecialchars($row['extname']); ?></td>
                                                            <td><?php echo htmlspecialchars($row['middlename']); ?></td>
                                                            <td><?php echo htmlspecialchars($row['sex']); ?></td>
                                                            <td><?php echo htmlspecialchars($row['birthdate']); ?></td>
                                                            <td><?php echo htmlspecialchars($row['course_program_enrolled']); ?>
                                                            </td>
                                                            <td><?php echo htmlspecialchars($row['year_level']); ?></td>
                                                            <td><?php echo htmlspecialchars($row['total_units_enrolled']); ?>
                                                            </td>
                                                            <td><?php echo htmlspecialchars($row['status_of_enrollment']); ?>
                                                            </td>
                                                            <td><?php echo htmlspecialchars($row['remarks']); ?></td>
                                                        </tr>
                                                    <?php endwhile; ?>
                                                </tbody>
                                            </table>
                                        </div>

                                        <?php
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
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            document.getElementById('uploadForm').addEventListener('submit', function (event) {
                event.preventDefault();

                const fileGroupInput = document.getElementById('file_group');
                const fileInput = document.getElementById('excelFile');
                const file = fileInput.files[0];

                if (!file) {
                    showToast("Please select a file!", "error");
                    return;
                }

                const fileGroup = fileGroupInput.value.trim();
                if (!fileGroup) {
                    showToast("Please enter a file group name!", "error");
                    return;
                }

                // Validate file type (Accepts CSV, XLS, XLSX)
                const allowedTypes = [
                    'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', // .xlsx
                    'application/vnd.ms-excel', // .xls
                    'text/csv' // .csv
                ];

                const fileExtension = file.name.split('.').pop().toLowerCase();
                const allowedExtensions = ['xls', 'xlsx', 'csv'];

                if (!allowedTypes.includes(file.type) && !allowedExtensions.includes(fileExtension)) {
                    showToast("Please upload a valid Excel or CSV file.", "error");
                    console.error("Invalid file type:", file.type);
                    return;
                }

                // Show SweetAlert confirmation before proceeding
                Swal.fire({
                    title: "Are you sure?",
                    text: "Do you want to upload and process this file?",
                    icon: "question",
                    showCancelButton: true,
                    confirmButtonText: "Yes, upload it!",
                    cancelButtonText: "Cancel",
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        const formData = new FormData();
                        formData.append('file_group', fileGroup);
                        formData.append('excelFile', file);

                        // Display loading message
                        Swal.fire({
                            title: "Uploading...",
                            text: "Please wait while the file is being uploaded and processed.",
                            icon: "info",
                            allowOutsideClick: false,
                            showConfirmButton: false,
                            willOpen: () => {
                                Swal.showLoading();
                            }
                        });

                        // Send file via Fetch API
                        fetch('submit_ched_masterlist.php', {
                            method: 'POST',
                            body: formData
                        })
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    Swal.fire({
                                        title: "Success!",
                                        text: data.message || "File processed successfully!",
                                        icon: "success",
                                        timer: 3000
                                    });
                                    setTimeout(() => {
                                        location.reload();
                                    }, 1500);
                                } else {
                                    Swal.fire({
                                        title: "Error!",
                                        text: data.error || "An error occurred during file processing.",
                                        icon: "error"
                                    });
                                    console.error("Server error:", data.error);
                                }
                            })
                            .catch(error => {
                                Swal.fire({
                                    title: "Upload Failed!",
                                    text: "An error occurred while uploading the file.",
                                    icon: "error"
                                });
                                console.error("Fetch error:", error);
                            });
                    }
                });
            });
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