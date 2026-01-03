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
                            <!--End Logo icon -->
                            <!-- Logo text -->
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
                        <li class="sidebar-item"> <a class="sidebar-link" href="masterlist.php" aria-expanded="false"><i
                                    data-feather="users" class="feather-icon"></i><span class="hide-menu">Master list
                                </span></a>
                        </li>
                        <li class="sidebar-item"> <a class="sidebar-link sidebar-link" href="enrollment_status.php"
                                aria-expanded="false"><i data-feather="folder" class="feather-icon"></i><span
                                    class="hide-menu">Enrollment Status</span></a></li>
                        <li class="sidebar-item"> <a class="sidebar-link sidebar-link" href="validate.php"
                                aria-expanded="false"><i data-feather="user-check" class="feather-icon"></i><span
                                    class="hide-menu">Validate</span></a></li>
                        <li class="sidebar-item"> <a class="sidebar-link sidebar-link" href="validate.php"
                                aria-expanded="false"><i data-feather="dollar-sign" class="feather-icon"></i><span
                                    class="hide-menu">Billing</span></a></li>
                                    
                        <li class="list-divider"></li>
                        <li class="nav-small-cap"><span class="hide-menu">Documents</span></li>
                        <li class="sidebar-item"> <a class="sidebar-link sidebar-link" href="validate.php"
                                aria-expanded="false"><i data-feather="file" class="feather-icon"></i><span
                                    class="hide-menu">Anex 7 Form 2</span></a></li>
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
                        <div class="d-flex align-items-center">
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb m-0 p-0">
                                    <li class="breadcrumb-item"><a href="index.php">Dashboard</a>
                                    </li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ============================================================== -->
            <!-- End Bread crumb and right sidebar toggle -->
            <!-- ============================================================== -->
            <!-- ============================================================== -->
            <!-- Container fluid  -->
            <!-- ============================================================== -->
            <!-- Container fluid  -->
            <!-- ============================================================== -->
            <div class="container-fluid">
                <?php
                // Assuming the database connection is established already
                include '../config/conn.php';
                // Query to fetch the total count of students
                $query_total_students = "SELECT COUNT(*) AS total_students FROM billing_table";
                $result_total_students = $conn->query($query_total_students);
                $total_students = $result_total_students->fetch_assoc()['total_students'];

                // Query to fetch 1st semester students count
                $query_first_sem = "SELECT COUNT(*) AS first_sem FROM billing_table WHERE first_semester IS NOT NULL";
                $result_first_sem = $conn->query($query_first_sem);
                $first_semester_students = $result_first_sem->fetch_assoc()['first_sem'];

                // Query to fetch 2nd semester students count
                $query_second_sem = "SELECT COUNT(*) AS second_sem FROM billing_table WHERE second_semester IS NOT NULL";
                $result_second_sem = $conn->query($query_second_sem);
                $second_semester_students = $result_second_sem->fetch_assoc()['second_sem'];

                // Query to fetch the total amount paid for scholarships
                $query_total_amount = "SELECT SUM(amount) AS total_amount FROM billing_table";
                $result_total_amount = $conn->query($query_total_amount);
                $total_amount = $result_total_amount->fetch_assoc()['total_amount'];

                // Query to fetch count per campus
                $query_campus = "SELECT campus, COUNT(*) AS count FROM billing_table GROUP BY campus";
                $result_campus = $conn->query($query_campus);

                // Query to fetch scholarship types
                $query_scholarship_types = "SELECT scholarship_type, COUNT(*) AS count FROM billing_table GROUP BY scholarship_type";
                $result_scholarship_types = $conn->query($query_scholarship_types);
                ?>

                <div class="row">
                    <div class="col-lg-3 col-md-6 col-sm-12 mb-4">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div>
                                        <h2 class="text-dark mb-1 font-weight-medium"><?php echo $total_students; ?>
                                        </h2>
                                        <h6 class="text-muted font-weight-normal mb-0">Total Students</h6>
                                    </div>
                                    <div class="ml-auto mt-md-3 mt-lg-0">
                                        <span class="opacity-7 text-muted"><i data-feather="user-plus"></i></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-3 col-md-6 col-sm-12 mb-4">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div>
                                        <h2 class="text-dark mb-1 font-weight-medium">
                                            <?php echo $first_semester_students; ?>
                                        </h2>
                                        <h6 class="text-muted font-weight-normal mb-0">1st Semester Students</h6>
                                    </div>
                                    <div class="ml-auto mt-md-3 mt-lg-0">
                                        <span class="opacity-7 text-muted"><i data-feather="arrow-up"></i></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-3 col-md-6 col-sm-12 mb-4">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div>
                                        <h2 class="text-dark mb-1 font-weight-medium">
                                            <?php echo $second_semester_students; ?>
                                        </h2>
                                        <h6 class="text-muted font-weight-normal mb-0">2nd Semester Students</h6>
                                    </div>
                                    <div class="ml-auto mt-md-3 mt-lg-0">
                                        <span class="opacity-7 text-muted"><i data-feather="users"></i></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-3 col-md-6 col-sm-12 mb-4">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div>
                                        <h2 class="text-dark mb-1 font-weight-medium">
                                            <?php echo number_format($total_amount, 2); ?>
                                        </h2>
                                        <h6 class="text-muted font-weight-normal mb-0">Total Scholarship Amount</h6>
                                    </div>
                                    <div class="ml-auto mt-md-3 mt-lg-0">
                                        <!-- <span class="opacity-7 text-muted"><i data-feather="dollar-sign"></i></span> -->
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <?php while ($row_campus = $result_campus->fetch_assoc()): ?>
                        <div class="col-lg-3 col-md-6 col-sm-12 mb-4">
                            <div class="card">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <div>
                                            <h2 class="text-dark mb-1 font-weight-medium">
                                                <?php echo $row_campus['count']; ?>
                                            </h2>
                                            <h6 class="text-muted font-weight-normal mb-0">
                                                <?php echo $row_campus['campus']; ?> Campus
                                            </h6>
                                        </div>
                                        <div class="ml-auto mt-md-3 mt-lg-0">
                                            <span class="opacity-7 text-muted"><i data-feather="users"></i></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>

                    <?php while ($row_scholarship = $result_scholarship_types->fetch_assoc()): ?>
                        <div class="col-lg-3 col-md-6 col-sm-12 mb-4">
                            <div class="card">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <div>
                                            <h2 class="text-dark mb-1 font-weight-medium">
                                                <?php echo $row_scholarship['count']; ?>
                                            </h2>
                                            <h6 class="text-muted font-weight-normal mb-0">
                                                <?php echo $row_scholarship['scholarship_type']; ?> Scholarship
                                            </h6>
                                        </div>
                                        <div class="ml-auto mt-md-3 mt-lg-0">
                                            <span class="opacity-7 text-muted"><i data-feather="award"></i></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>



                <!-- *************************************************************** -->
                <!-- End First Cards -->
                <div class="row">
                    <!-- Total Students Chart -->
                    <div class="col-lg-4 col-md-12">
                        <div class="card">
                            <div class="card-body">
                                <h4 class="card-title">Total Students</h4>
                                <div id="total-students-chart" style="height: 283px; width: 100%;"></div>
                                <ul class="list-style-none mb-0">
                                    <li class="mt-3">
                                        <i class="fas fa-circle text-danger font-10 mr-2"></i>
                                        <span class="text-muted">1st Semester</span>
                                        <span class="text-dark float-right font-weight-medium"
                                            id="first-semester-count"></span>
                                    </li>
                                    <li class="mt-3">
                                        <i class="fas fa-circle text-cyan font-10 mr-2"></i>
                                        <span class="text-muted">2nd Semester</span>
                                        <span class="text-dark float-right font-weight-medium"
                                            id="second-semester-count"></span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- Scholarship Distribution -->
                    <div class="col-lg-4 col-md-12">
                        <div class="card">
                            <div class="card-body">
                                <h4 class="card-title">Scholarship Distribution</h4>
                                <div id="scholarship-type-chart" style="height: 283px; width: 100%;"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Campus Distribution -->
                    <div class="col-lg-4 col-md-12">
                        <div class="card">
                            <div class="card-body">
                                <h4 class="card-title">Campus-wise Distribution</h4>
                                <div id="campus-distribution-chart" style="height: 283px; width: 100%;"></div>
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
    <script src="../../assets/libs/jquery/dist/jquery.min.js"></script>
    <script src="../../assets/libs/popper.js/dist/umd/popper.min.js"></script>
    <script src="../../assets/libs/bootstrap/dist/js/bootstrap.min.js"></script>
    <!-- apps -->
    <script>
        $(function () {
            // Fetch data from PHP
            $.ajax({
                url: 'fetch_chart.php', // Change this to your PHP script
                method: 'GET',
                success: function (data) {
                    // Assuming data is returned in this structure
                    var totalStudents = data.total_students;
                    var firstSemesterStudents = data.first_semester_students;
                    var secondSemesterStudents = data.second_semester_students;
                    var totalAmount = data.total_amount;
                    var campusCount = data.campus_count;  // array of {campus, count}
                    var scholarshipTypes = data.scholarship_types; // array of {scholarship_type, count}

                    // Total Students Chart (Bar Chart)
                    new Chartist.Bar("#total-students-chart", {
                        labels: ["Total", "1st Semester", "2nd Semester"],
                        series: [
                            [totalStudents, firstSemesterStudents, secondSemesterStudents]
                        ]
                    }, {
                        axisX: {
                            showGrid: false
                        },
                        seriesBarDistance: 1,
                        chartPadding: {
                            top: 15,
                            right: 15,
                            bottom: 5,
                            left: 0
                        },
                        plugins: [Chartist.plugins.tooltip()],
                        width: "100%"
                    });

                    // Update first and second semester counts dynamically
                    $('#first-semester-count').text(firstSemesterStudents);
                    $('#second-semester-count').text(secondSemesterStudents);

                    // Scholarship Distribution Chart (Donut Chart)
                    var scholarshipData = scholarshipTypes.map(function (type) {
                        return [type.scholarship_type, type.count];
                    });

                    c3.generate({
                        bindto: "#scholarship-type-chart",
                        data: {
                            columns: scholarshipData,
                            type: "donut",
                            tooltip: {
                                show: true
                            }
                        },
                        donut: {
                            label: {
                                show: false
                            },
                            title: "Scholarship Distribution",
                            width: 18
                        },
                        legend: {
                            hide: false
                        },
                        color: {
                            pattern: ["#ff4f70", "#01caf1", "#28a745"] // Customize as needed
                        }
                    });

                    // Campus Distribution Chart (Bar Chart)
                    var campusLabels = campusCount.map(function (campus) {
                        return campus.campus;
                    });
                    var campusValues = campusCount.map(function (campus) {
                        return campus.count;
                    });

                    new Chartist.Bar("#campus-distribution-chart", {
                        labels: campusLabels,
                        series: [campusValues]
                    }, {
                        axisX: {
                            showGrid: false
                        },
                        seriesBarDistance: 1,
                        chartPadding: {
                            top: 15,
                            right: 15,
                            bottom: 5,
                            left: 0
                        },
                        plugins: [Chartist.plugins.tooltip()],
                        width: "100%"
                    });
                },
                error: function (err) {
                    console.log("Error fetching data", err);
                }
            });
        });

    </script>
    <!-- Include C3.js (for Donut Chart) -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/c3/0.7.20/c3.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/c3/0.7.20/c3.min.js"></script>

    <!-- Include Chartist.js (for Bar Chart) -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/chartist/dist/chartist.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chartist/dist/chartist.min.js"></script>

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