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
    <!-- Preloader disabled for faster loading -->
    <style>
        .preloader { display: none !important; }
    </style>
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
                        <li class="sidebar-item"> <a class="sidebar-link sidebar-link" href="index.php"
                                aria-expanded="false"><i data-feather="home" class="feather-icon"></i><span
                                    class="hide-menu">Dashboard</span></a></li>
                        <li class="list-divider"></li>
                        <li class="nav-small-cap"><span class="hide-menu">Applications</span></li>
                         <li class="sidebar-item"> <a class="sidebar-link sidebar-link" href="ched_masterlist.php"
                                aria-expanded="false"><i data-feather="folder" class="feather-icon"></i><span
                                    class="hide-menu">CHED TDP Masterlist</span></a></li>
                                    <li class="sidebar-item"> <a class="sidebar-link sidebar-link" href="ched_masterlist_tes.php"
                                aria-expanded="false"><i data-feather="folder" class="feather-icon"></i><span
                                    class="hide-menu">CHED TES Masterlist</span></a></li>
                        <li class="sidebar-item"> <a class="sidebar-link sidebar-link" href="submit_form.php"
                                aria-expanded="false"><i data-feather="folder" class="feather-icon"></i><span
                                    class="hide-menu">Submit Form</span></a></li>
                    </ul>
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
                // Include the database connection file
                include '../config/conn.php'; // Modify with the actual connection path
                
                // Set timeouts
                set_time_limit(10);
                
                // Get filter parameters
                $academic_year_filter = isset($_GET['academic_year']) ? $_GET['academic_year'] : '';
                $semester_filter = isset($_GET['semester']) ? $_GET['semester'] : '';
                
                // Build WHERE clause with filters
                $whereClause = "where sheet_name = '" . $conn->real_escape_string($sheet_name) . "'";
                if (!empty($academic_year_filter)) {
                    $whereClause .= " AND academic_year = '" . $conn->real_escape_string($academic_year_filter) . "'";
                }
                if (!empty($semester_filter)) {
                    $whereClause .= " AND semester = '" . $conn->real_escape_string($semester_filter) . "'";
                }
                
                // Optimized SQL queries - use simple COUNT queries for speed
                $totalRecordsQuery = "SELECT COUNT(*) AS total_records FROM ched_masterlist $whereClause";
                $totalRecordsQueryTes = "SELECT COUNT(*) AS total_records_tes FROM ched_masterlist_tes where campus = '" . $conn->real_escape_string($sheet_name) . "'";
                $totalCoursesQuery = "SELECT COUNT(DISTINCT course_program_enrolled) AS total_courses FROM ched_masterlist $whereClause";
                $totalFileGroupsQuery = "SELECT COUNT(DISTINCT file_group) AS total_file_groups FROM ched_masterlist $whereClause";

                // Execute queries with error handling
                $totalRecordsResult = @$conn->query($totalRecordsQuery);
                $totalRecordsResultTes = @$conn->query($totalRecordsQueryTes);
                $totalCoursesResult = @$conn->query($totalCoursesQuery);
                $totalFileGroupsResult = @$conn->query($totalFileGroupsQuery);

                // Fetch results with defaults
                $totalRecordsTes = 0;
                $totalRecords = 0;
                $totalCourses = 0;
                $totalFileGroups = 0;
                
                if ($totalRecordsResultTes) {
                    $totalRecordsTes = $totalRecordsResultTes->fetch_assoc()['total_records_tes'] ?? 0;
                }
                if ($totalRecordsResult) {
                    $totalRecords = $totalRecordsResult->fetch_assoc()['total_records'] ?? 0;
                }
                if ($totalCoursesResult) {
                    $totalCourses = $totalCoursesResult->fetch_assoc()['total_courses'] ?? 0;
                }
                if ($totalFileGroupsResult) {
                    $totalFileGroups = $totalFileGroupsResult->fetch_assoc()['total_file_groups'] ?? 0;
                }
                
                // Don't close connection - might be needed for charts
                ?>

                <!-- Analytics Filters -->
                <div class="row mb-3">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">Filter Analytics</h5>
                                <form method="GET" action="" class="form-inline">
                                    <div class="form-group mr-2">
                                        <label for="academic_year_filter" class="mr-2">Academic Year:</label>
                                        <select name="academic_year" id="academic_year_filter" class="form-control">
                                            <option value="">All Academic Years</option>
                                            <?php
                                            $ayQuery = "SELECT DISTINCT academic_year FROM ched_masterlist WHERE sheet_name = '$sheet_name' ORDER BY academic_year DESC";
                                            $ayResult = $conn->query($ayQuery);
                                            if ($ayResult) {
                                                while ($ayRow = $ayResult->fetch_assoc()) {
                                                    $ayValue = $ayRow['academic_year'];
                                                    $selected = ($academic_year_filter == $ayValue) ? 'selected' : '';
                                                    echo "<option value='" . htmlspecialchars($ayValue) . "' $selected>" . htmlspecialchars($ayValue) . "</option>";
                                                }
                                            }
                                            ?>
                                        </select>
                                    </div>
                                    
                                    <div class="form-group mr-2">
                                        <label for="semester_filter" class="mr-2">Semester:</label>
                                        <select name="semester" id="semester_filter" class="form-control">
                                            <option value="">All Semesters</option>
                                            <option value="1st Semester" <?php echo ($semester_filter == '1st Semester') ? 'selected' : ''; ?>>1st Semester</option>
                                            <option value="2nd Semester" <?php echo ($semester_filter == '2nd Semester') ? 'selected' : ''; ?>>2nd Semester</option>
                                            <option value="Midyear" <?php echo ($semester_filter == 'Midyear') ? 'selected' : ''; ?>>Midyear</option>
                                        </select>
                                    </div>
                                    
                                    <button type="submit" class="btn btn-primary mr-2">Apply Filters</button>
                                    <a href="index.php" class="btn btn-secondary">Clear Filters</a>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-group">
                    <!-- Total Records Card -->
                    <div class="card border-right">
                        <div class="card-body">
                            <div class="d-flex d-lg-flex d-md-block align-items-center">
                                <div>
                                    <div class="d-inline-flex align-items-center">
                                        <h2 class="text-dark mb-1 font-weight-medium"><?= $totalRecords; ?></h2>
                                    </div>
                                    <h6 class="text-muted font-weight-normal mb-0 w-100 text-truncate">Total TDP Records
                                    </h6>
                                </div>
                                <div class="ml-auto mt-md-3 mt-lg-0">
                                    <span class="opacity-7 text-muted"><i data-feather="database"></i></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card border-right">
                        <div class="card-body">
                            <div class="d-flex d-lg-flex d-md-block align-items-center">
                                <div>
                                    <div class="d-inline-flex align-items-center">
                                        <h2 class="text-dark mb-1 font-weight-medium"><?= $totalRecordsTes; ?></h2>
                                    </div>
                                    <h6 class="text-muted font-weight-normal mb-0 w-100 text-truncate">Total TES Records
                                    </h6>
                                </div>
                                <div class="ml-auto mt-md-3 mt-lg-0">
                                    <span class="opacity-7 text-muted"><i data-feather="database"></i></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Total Courses Card -->
                    <div class="card border-right">
                        <div class="card-body">
                            <div class="d-flex d-lg-flex d-md-block align-items-center">
                                <div>
                                    <div class="d-inline-flex align-items-center">
                                        <h2 class="text-dark mb-1 font-weight-medium"><?= $totalCourses; ?></h2>
                                    </div>
                                    <h6 class="text-muted font-weight-normal mb-0 w-100 text-truncate">Total
                                        Courses/Programs</h6>
                                </div>
                                <div class="ml-auto mt-md-3 mt-lg-0">
                                    <span class="opacity-7 text-muted"><i data-feather="book-open"></i></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Total File Groups Card -->
                    <!-- <div class="card border-right">
                        <div class="card-body">
                            <div class="d-flex d-lg-flex d-md-block align-items-center">
                                <div>
                                    <div class="d-inline-flex align-items-center">
                                        <h2 class="text-dark mb-1 font-weight-medium"><?= $totalFileGroups; ?></h2>
                                    </div>
                                    <h6 class="text-muted font-weight-normal mb-0 w-100 text-truncate">Total File Groups
                                    </h6>
                                </div>
                                <div class="ml-auto mt-md-3 mt-lg-0">
                                    <span class="opacity-7 text-muted"><i data-feather="folder"></i></span>
                                </div>
                            </div>
                        </div>
                    </div> -->

                    <!-- Total Filenames Card -->
                    <!-- <div class="card">
                        <div class="card-body">
                            <div class="d-flex d-lg-flex d-md-block align-items-center">
                                <div>
                                    <h2 class="text-dark mb-1 font-weight-medium"><?= $totalFilenames; ?></h2>
                                    <h6 class="text-muted font-weight-normal mb-0 w-100 text-truncate">Total Filenames
                                    </h6>
                                </div>
                                <div class="ml-auto mt-md-3 mt-lg-0">
                                    <span class="opacity-7 text-muted"><i data-feather="file-text"></i></span>
                                </div>
                            </div>
                        </div>
                    </div> -->
                </div>


                <style>
                    .chart-container {
                        width: 100%;
                        max-width: 500px;
                        /* Controls the width of the chart */
                        height: 500px !important;
                        /* Fix the height */
                        margin: auto;
                        /* Centers the chart */
                        display: flex;
                        align-items: center;
                        justify-content: center;
                    }

                    canvas {
                        max-width: 100% !important;
                        height: auto !important;
                    }
                </style>
                <!-- *************************************************************** -->
                <!-- End First Cards -->
                <div class="row">
                    <!-- Total Courses Chart -->
                    <div class="col-lg-6 col-md-12">
                        <div class="card">
                            <div class="card-body text-center">
                                <h4 class="card-title">Total TDP Scholars Per Campus</h4>
                                <div class="chart-container">
                                    <canvas id="total-courses-chart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-lg-6 col-md-12">
                        <div class="card">
                            <div class="card-body text-center">
                                <h4 class="card-title">Total TES Scholars Per Campus</h4>
                                <div class="chart-container">
                                    <canvas id="total-courses-chart-tes"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Total File Groups Chart -->
                    <div class="col-lg-6 col-md-12">
                        <div class="card">
                            <div class="card-body text-center">
                                <h4 class="card-title">Total File Groups</h4>
                                <div class="chart-container">
                                    <canvas id="total-file-groups-chart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row" hidden>
                    <!-- Total Filenames Chart -->
                    <div class="col-lg-12 col-md-12">
                        <div class="card">
                            <div class="card-body text-center">
                                <h4 class="card-title">Total Filenames</h4>
                                <div class="chart-container">
                                    <canvas id="total-filenames-chart"></canvas>
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
    <script src="../../assets/libs/jquery/dist/jquery.min.js"></script>
    <script src="../../assets/libs/popper.js/dist/umd/popper.min.js"></script>
    <script src="../../assets/libs/bootstrap/dist/js/bootstrap.min.js"></script>
    <!-- jQuery -->
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- apps -->

    <script>
        // Hide preloader immediately when scripts load
        $(".preloader").hide();
        
        $(document).ready(function () {
            // Load charts asynchronously - don't block page rendering
            setTimeout(function() {
                $.ajax({
                    url: 'fetch_chart.php',
                    method: 'GET',
                    dataType: 'json',
                    timeout: 5000, // 5 second timeout
        success: function (data) {
            var totalRecords = data.total_records;
            var totalRecordsTes = data.total_records_tes;
            var totalCourses = data.total_courses;
            var totalFileGroups = data.total_file_groups;
            var totalFilenames = data.total_filenames;

            // Update counters
            $('#total-records-count').text(totalRecords);

            // Prepare data for charts
            var courseLabels = totalCourses.map(course => course.course_program_enrolled);
            var courseCounts = totalCourses.map(course => course.count);

            var fileGroupLabels = totalFileGroups.map(group => group.file_group);
            var fileGroupCounts = totalFileGroups.map(group => group.count);

            var filenameLabels = totalFilenames.map(file => file.filename);
            var filenameCounts = totalFilenames.map(file => file.count);

            // Chart options
            var chartOptions = {
                scales: {
                    x: {
                        display: false
                    },
                    y: {
                        beginAtZero: true
                    }
                },
                plugins: {
                    legend: {
                        display: false // Hide legend if not needed
                    }
                }
            };

            // Total Courses Chart (Bar Chart)
            new Chart(document.getElementById("total-courses-chart"), {
                type: 'bar',
                data: {
                    labels: courseLabels,
                    datasets: [{
                        label: "Students",
                        backgroundColor: ["#ff4f70", "#01caf1", "#ffcd56", "#4bc0c0", "#9966ff"],
                        data: courseCounts
                    }]
                },
                options: chartOptions
            });

            // Total File Groups Chart (Bar Chart)
            new Chart(document.getElementById("total-file-groups-chart"), {
                type: 'bar',
                data: {
                    labels: fileGroupLabels,
                    datasets: [{
                        label: "File Groups",
                        backgroundColor: "#28a745",
                        data: fileGroupCounts
                    }]
                },
                options: chartOptions
            });

            // Total Filenames Chart (Bar Chart)
            new Chart(document.getElementById("total-filenames-chart"), {
                type: 'bar',
                data: {
                    labels: filenameLabels,
                    datasets: [{
                        label: "Filenames",
                        backgroundColor: "#f39c12",
                        data: filenameCounts
                    }]
                },
                options: chartOptions
            });

                    },
                    error: function (err) {
                        console.log("Error fetching chart data", err);
                    }
                });
            }, 200); // Load after 200ms
            
            // TES chart - load separately
            setTimeout(function() {
                $.ajax({
                    url: 'fetch_chart_tes.php',
                    method: 'GET',
                    dataType: 'json',
                    timeout: 5000,
        success: function (data) {
            var totalRecords = data.total_records;
            var totalRecordsTes = data.total_records_tes;
            var totalCourses = data.total_courses;
            var totalFileGroups = data.total_file_groups;
            var totalFilenames = data.total_filenames;

            // Update counters
            $('#total-records-count').text(totalRecords);

                        // Prepare data for charts
                        var courseLabels = totalCourses.map(course => course.course_program_enrolled);
                        var courseCounts = totalCourses.map(course => course.count);

                        var fileGroupLabels = totalFileGroups.map(group => group.file_group);
                        var fileGroupCounts = totalFileGroups.map(group => group.count);

                        var filenameLabels = totalFilenames.map(file => file.filename);
                        var filenameCounts = totalFilenames.map(file => file.count);

                        // Chart options
                        var chartOptions = {
                            scales: {
                                x: {
                                    display: false
                                },
                                y: {
                                    beginAtZero: true
                                }
                            },
                            plugins: {
                                legend: {
                                    display: false // Hide legend if not needed
                                }
                            }
                        };

                        // Total Courses Chart (Bar Chart)
                        if (document.getElementById("total-courses-chart-tes")) {
                            new Chart(document.getElementById("total-courses-chart-tes"), {
                                type: 'bar',
                                data: {
                                    labels: courseLabels,
                                    datasets: [{
                                        label: "Students",
                                        backgroundColor: ["#36a2eb", "#ff9f40", "#c9cbcf", "#2ecc71", "#e74c3c"],
                                        data: courseCounts
                                    }]
                                },
                                options: chartOptions
                            });
                        }

                    },
                    error: function (err) {
                        console.log("Error fetching TES chart data", err);
                    }
                });
            }, 400); // Load TES chart after 400ms
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
    
    <!-- Force hide preloader -->
    <script>
        // Hide preloader immediately
        (function() {
            var preloader = document.querySelector('.preloader');
            if (preloader) {
                preloader.style.display = 'none';
            }
        })();
        
        $(document).ready(function() {
            $(".preloader").hide();
        });
        
        // Ultimate fallback
        setTimeout(function() {
            $(".preloader").hide();
            document.querySelectorAll('.preloader').forEach(function(el) {
                el.style.display = 'none';
            });
        }, 500);
    </script>
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
    <footer> 
    <script>
if(navigator.serviceWorker) { // Ensure this path is correct
    navigator.serviceWorker.register('serviceWorker-sw.js') 
}
</script></footer>
</body>

</html>