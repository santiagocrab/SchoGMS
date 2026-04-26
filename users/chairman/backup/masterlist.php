<?php include 'config/session.php'; ?>
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
                        <li class="nav-small-cap"><span class="hide-menu">Registrar</span></li>
                        <li class="sidebar-item"> <a class="sidebar-link" href="masterlist.php" aria-expanded="false"><i
                                    data-feather="users" class="feather-icon"></i><span class="hide-menu">Registrar
                                    Masterlist
                                </span></a>
                        </li>

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
                                        // Include your database connection
                                        require 'config/conn.php';

                                        // Get the selected category and file_group from the form (if any)
                                        $category = isset($_GET['category']) ? $_GET['category'] : '';
                                        $file_group = isset($_GET['file_group']) ? $_GET['file_group'] : '';

                                        // Query to get distinct categories and file groups from registrar_master_list table
                                        $categoryQuery = "SELECT DISTINCT filename, file_group FROM registrar_master_list";
                                        $categoryResult = $conn->query($categoryQuery);

                                        if (!$categoryResult) {
                                            die("Query failed: " . $conn->error);
                                        }

                                        // Base query to select data with LEFT JOIN, using GROUP_CONCAT to merge multiple categories per student
                                        $query = "
                                            SELECT rml.*, 
                                                GROUP_CONCAT(DISTINCT du.category ORDER BY du.category SEPARATOR ', ') AS uploaded_categories,
                                                GROUP_CONCAT(DISTINCT du.file_name ORDER BY du.file_name SEPARATOR ', ') AS uploaded_files
                                            FROM registrar_master_list rml
                                            LEFT JOIN document_uploads du 
                                            ON du.file_name LIKE CONCAT(rml.last_name, ', ', rml.first_name, ' ', rml.middle_name, '%')
                                        ";

                                        // Apply filters if selected
                                        $filters = [];
                                        if ($category !== '') {
                                            $filters[] = "rml.filename = '" . $conn->real_escape_string($category) . "'";
                                        }
                                        if ($file_group !== '') {
                                            $filters[] = "rml.file_group = '" . $conn->real_escape_string($file_group) . "'";
                                        }

                                        // Append the WHERE clause if filters are set
                                        if (count($filters) > 0) {
                                            $query .= " WHERE " . implode(" AND ", $filters);
                                        }

                                        // Adding the GROUP BY clause
                                        $query .= " GROUP BY rml.last_name, rml.first_name, rml.middle_name";

                                        // Execute the query
                                        $result = $conn->query($query);

                                        if (!$result) {
                                            die("Query failed: " . $conn->error);
                                        }
                                        ?>


                                        <div class="table-responsive">
                                            <!-- Dropdown to select category and file group -->
                                            <form method="GET" action="" class="col-md-8">
                                                <label for="categoryFilter">Select Category:</label>
                                                <select id="categoryFilter" name="category" class="form-control">
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
                                                    Filter</button>
                                                <br><br>
                                            </form>
                                            <table id="zero_config" class="table table-striped table-bordered no-wrap">
                                                <thead>
                                                    <tr>
                                                        <th hidden>File Name</th>
                                                        <th>COR & COG</th>
                                                        <th>Full Name</th>
                                                        <th>Ext. Name</th>
                                                        <th>ID Number</th>
                                                        <th>Gender</th>
                                                        <th>Student Type</th>
                                                        <th>Year Level</th>
                                                        <th>Attended</th>
                                                        <th>Course</th>
                                                        <th>Curriculum</th>
                                                        <th>Scholarship</th>
                                                        <th>GPA</th>
                                                        <th>CGPA</th>
                                                        <th>% Pass</th>
                                                        <th>Grade Remarks</th>
                                                        <th>Enrolled</th>
                                                        <th>Lec. Unit</th>
                                                        <th>Lab. Unit</th>
                                                        <th>COR Printed</th>
                                                        <th>Billing Profile</th>
                                                        <th>Misc. Fee Total</th>
                                                        <th>Misc. Fee Paid</th>
                                                        <th>Tuition Fee Total</th>
                                                        <th>Tuition Fee Paid</th>
                                                        <th>Street</th>
                                                        <th>Barangay</th>
                                                        <th>Municipality/City</th>
                                                        <th>Province</th>
                                                        <th>Zip Code</th>
                                                        <th>Date of Birth</th>
                                                        <th>Place of Birth</th>
                                                        <th>Civil Status</th>
                                                        <th>Tribe</th>
                                                        <th>Religion</th>
                                                        <th>Year Admitted</th>
                                                        <th>Semester Admitted</th>
                                                        <th>School Last Attended</th>
                                                        <th>Year Last Attended</th>
                                                        <th>Semester Last Attended</th>
                                                        <th>High School Graduated</th>
                                                        <th>Exam Date</th>
                                                        <th>Exam Rating</th>
                                                        <th>Ref. Number</th>
                                                        <th>Guardian</th>
                                                        <th>Address</th>
                                                        <th>Contact Nos.</th>
                                                        <th>Blood Type</th>
                                                        <th>Email Address</th>
                                                        <th>Mobile Number</th>
                                                        <th>DEPED Number</th>
                                                        <th>Scholarship Grant</th>
                                                        <th>Scholarship Allowance</th>
                                                        <th>Documents Submitted</th>
                                                        <th>Lacking Document(s)</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php while ($row = $result->fetch_assoc()): ?>
                                                        <tr>
                                                            <td hidden><?php echo htmlspecialchars($row['filename']); ?>
                                                            </td>
                                                            <!-- Status Column -->
                                                            <!-- Status Column (Check COR or COG) -->
                                                            <td>
                                                                <?php
                                                                $categories = $row['uploaded_categories'] ?? '';

                                                                $hasCOR = strpos($categories, 'COR') !== false;
                                                                $hasCOG = strpos($categories, 'COG') !== false;

                                                                if ($hasCOR && $hasCOG) {
                                                                    echo '<span class="badge badge-success">COR</span> ';
                                                                    echo '<span class="badge badge-primary">COG</span>';
                                                                } elseif ($hasCOR) {
                                                                    echo '<span class="badge badge-success">COR</span>';
                                                                } elseif ($hasCOG) {
                                                                    echo '<span class="badge badge-primary">COG</span>';
                                                                } else {
                                                                    echo '<span class="badge badge-secondary">No COR/COG</span>';
                                                                }
                                                                ?>
                                                            </td>
                                                            <td><?php echo htmlspecialchars($row['last_name'] . ', ' . $row['first_name'] . ' ' . $row['middle_name']); ?>
                                                            </td>
                                                            <td><?php echo htmlspecialchars($row['ext_name']); ?></td>
                                                            <td><?php echo htmlspecialchars($row['id_number']); ?></td>
                                                            <td><?php echo htmlspecialchars($row['gender']); ?></td>
                                                            <td><?php echo htmlspecialchars($row['student_type']); ?>
                                                            </td>
                                                            <td><?php echo htmlspecialchars($row['year_level']); ?></td>
                                                            <td><?php echo htmlspecialchars($row['attended']); ?></td>
                                                            <td><?php echo htmlspecialchars($row['course']); ?></td>
                                                            <td><?php echo htmlspecialchars($row['curriculum']); ?></td>
                                                            <td><?php echo htmlspecialchars($row['scholarship']); ?>
                                                            </td>
                                                            <td><?php echo htmlspecialchars($row['gpa']); ?></td>
                                                            <td><?php echo htmlspecialchars($row['cgpa']); ?></td>
                                                            <td><?php echo htmlspecialchars($row['pass_percentage']); ?>
                                                            </td>
                                                            <td><?php echo htmlspecialchars($row['grade_remarks']); ?>
                                                            </td>
                                                            <td><?php echo htmlspecialchars($row['enrolled']); ?></td>
                                                            <td><?php echo htmlspecialchars($row['lec_unit']); ?></td>
                                                            <td><?php echo htmlspecialchars($row['lab_unit']); ?></td>
                                                            <td><?php echo htmlspecialchars($row['cor_printed']); ?>
                                                            </td>
                                                            <td><?php echo htmlspecialchars($row['billing_profile']); ?>
                                                            </td>
                                                            <td><?php echo htmlspecialchars($row['misc_fee_total']); ?>
                                                            </td>
                                                            <td><?php echo htmlspecialchars($row['misc_fee_paid']); ?>
                                                            </td>
                                                            <td><?php echo htmlspecialchars($row['tuition_fee_total']); ?>
                                                            </td>
                                                            <td><?php echo htmlspecialchars($row['tuition_fee_paid']); ?>
                                                            </td>
                                                            <td><?php echo htmlspecialchars($row['street']); ?></td>
                                                            <td><?php echo htmlspecialchars($row['barangay']); ?></td>
                                                            <td><?php echo htmlspecialchars($row['municipality_city']); ?>
                                                            </td>
                                                            <td><?php echo htmlspecialchars($row['province']); ?></td>
                                                            <td><?php echo htmlspecialchars($row['zip_code']); ?></td>
                                                            <td><?php echo htmlspecialchars($row['date_of_birth']); ?>
                                                            </td>
                                                            <td><?php echo htmlspecialchars($row['place_of_birth']); ?>
                                                            </td>
                                                            <td><?php echo htmlspecialchars($row['civil_status']); ?>
                                                            </td>
                                                            <td><?php echo htmlspecialchars($row['tribe']); ?></td>
                                                            <td><?php echo htmlspecialchars($row['religion']); ?></td>
                                                            <td><?php echo htmlspecialchars($row['year_admitted']); ?>
                                                            </td>
                                                            <td><?php echo htmlspecialchars($row['semester_admitted']); ?>
                                                            </td>
                                                            <td><?php echo htmlspecialchars($row['school_last_attended']); ?>
                                                            </td>
                                                            <td><?php echo htmlspecialchars($row['year_last_attended']); ?>
                                                            </td>
                                                            <td><?php echo htmlspecialchars($row['semester_last_attended']); ?>
                                                            </td>
                                                            <td><?php echo htmlspecialchars($row['high_school_graduated']); ?>
                                                            </td>
                                                            <td><?php echo htmlspecialchars($row['exam_date']); ?></td>
                                                            <td><?php echo htmlspecialchars($row['exam_rating']); ?>
                                                            </td>
                                                            <td><?php echo htmlspecialchars($row['ref_number']); ?></td>
                                                            <td><?php echo htmlspecialchars($row['guardian']); ?></td>
                                                            <td><?php echo htmlspecialchars($row['guardian_address']); ?>
                                                            </td>
                                                            <td><?php echo htmlspecialchars($row['guardian_contact']); ?>
                                                            </td>
                                                            <td><?php echo htmlspecialchars($row['blood_type']); ?></td>
                                                            <td><?php echo htmlspecialchars($row['email_address']); ?>
                                                            </td>
                                                            <td><?php echo htmlspecialchars($row['mobile_number']); ?>
                                                            </td>
                                                            <td><?php echo htmlspecialchars($row['deped_number']); ?>
                                                            </td>
                                                            <td><?php echo htmlspecialchars($row['scholarship_grant']); ?>
                                                            </td>
                                                            <td><?php echo htmlspecialchars($row['scholarship_allowance']); ?>
                                                            </td>
                                                            <td><?php echo htmlspecialchars($row['documents_submitted']); ?>
                                                            </td>
                                                            <td><?php echo htmlspecialchars($row['lacking_documents']); ?>
                                                            </td>
                                                        </tr>
                                                    <?php endwhile; ?>
                                                </tbody>
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
                            fetch('submit_master_list.php', {
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