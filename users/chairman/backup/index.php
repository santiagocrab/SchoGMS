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
                        <li class="sidebar-item"> <a class="sidebar-link" href="ched_masterlist.php"
                                aria-expanded="false"><i data-feather="users" class="feather-icon"></i><span
                                    class="hide-menu">Ched TDP
                                    Masterlist
                                </span></a>
                        </li>
                        <li class="sidebar-item"> <a class="sidebar-link" href="ched_masterlist_tes.php"
                                aria-expanded="false"><i data-feather="users" class="feather-icon"></i><span
                                    class="hide-menu">Ched TES
                                    Masterlist
                                </span></a>
                        </li>
                        <li class="sidebar-item"> <a class="sidebar-link" href="program_list.php"
                                aria-expanded="false"><i data-feather="folder" class="feather-icon"></i><span
                                    class="hide-menu">Program List
                                </span></a>
                        </li>
                        <li class="sidebar-item"> <a class="sidebar-link" href="anex-form2.php" aria-expanded="false"><i
                                    data-feather="folder" class="feather-icon"></i><span class="hide-menu">Anex 7 Form 2
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
                include 'config/conn.php';

                // Define table names
                $table = 'ched_masterlist';
                $tableTes = 'ched_masterlist_tes';

                // Function to execute and fetch count from a query with error handling
                function getCount($conn, $query)
                {
                    $result = $conn->query($query);
                    if ($result) {
                        $row = $result->fetch_assoc();
                        return reset($row); // Return the first column value
                    } else {
                        error_log("Error executing query: " . $conn->error); // Log error instead of displaying
                        return 0;
                    }
                }

                // SQL queries
                $totalRecordsTesQuery = "SELECT COUNT(*) AS total_records_tes FROM $tableTes";
                $totalRecordsQuery = "SELECT COUNT(*) AS total_records FROM $table";
                $totalCampusesTesQuery = "SELECT sheet_name AS campus, COUNT(*) AS count FROM $tableTes GROUP BY sheet_name";
                $totalCampusesQuery = "SELECT sheet_name AS campus, COUNT(*) AS count FROM $table GROUP BY sheet_name";
                $totalBatchesTesQuery = "SELECT file_group AS ched_tdp_batch, COUNT(*) AS count FROM $tableTes GROUP BY file_group";
                $totalBatchesQuery = "SELECT file_group AS ched_tdp_batch, COUNT(*) AS count FROM $table GROUP BY file_group";

                // Execute and fetch results
                $totalTesRecords = getCount($conn, $totalRecordsTesQuery);
                $totalRecords = getCount($conn, $totalRecordsQuery);

                // Fetch campuses
                $campuses = [];
                $campusesTes = [];

                $campusesResult = $conn->query($totalCampusesQuery);
                if ($campusesResult) {
                    while ($row = $campusesResult->fetch_assoc()) {
                        $campuses[] = $row;
                    }
                } else {
                    error_log("Error fetching campuses: " . $conn->error);
                }

                $campusesTesResult = $conn->query($totalCampusesTesQuery);
                if ($campusesTesResult) {
                    while ($row = $campusesTesResult->fetch_assoc()) {
                        $campusesTes[] = $row;
                    }
                } else {
                    error_log("Error fetching TES campuses: " . $conn->error);
                }

                // Fetch CHED TDP Batches
                $batches = [];

                $batchesResult = $conn->query($totalBatchesQuery);
                if ($batchesResult) {
                    while ($row = $batchesResult->fetch_assoc()) {
                        $batches[] = $row;
                    }
                } else {
                    error_log("Error fetching batches: " . $conn->error);
                }

                // Close the connection
                $conn->close();
                ?>


                <div class="container-fluid">
                    <!-- Display Data Cards -->
                    <div class="card-group">
                        <!-- Total Records Card -->
                        <div class="card border-right">
                            <div class="card-body">
                                <div class="d-flex d-lg-flex d-md-block align-items-center">
                                    <div>
                                        <div class="d-inline-flex align-items-center">
                                            <h2 class="text-dark mb-1 font-weight-medium" id="total-records-count">
                                                <?= $totalRecords; ?>
                                            </h2>
                                        </div>
                                        <h6 class="text-muted font-weight-normal mb-0 w-100 text-truncate">Total TDP
                                            Records
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
                                            <h2 class="text-dark mb-1 font-weight-medium" id="total-records-count-tes">
                                                <?= $totalTesRecords; ?>
                                            </h2>
                                        </div>
                                        <h6 class="text-muted font-weight-normal mb-0 w-100 text-truncate">Total TES
                                            Records
                                        </h6>
                                    </div>
                                    <div class="ml-auto mt-md-3 mt-lg-0">
                                        <span class="opacity-7 text-muted"><i data-feather="database"></i></span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Total Campus Card -->
                        <div class="card border-right">
                            <div class="card-body">
                                <div class="d-flex d-lg-flex d-md-block align-items-center">
                                    <div>
                                        <div class="d-inline-flex align-items-center">
                                            <h2 class="text-dark mb-1 font-weight-medium" id="total-campuses-count">
                                                <?= count($campuses); ?>
                                            </h2>
                                        </div>
                                        <h6 class="text-muted font-weight-normal mb-0 w-100 text-truncate">Total
                                            Campuses</h6>
                                    </div>
                                    <div class="ml-auto mt-md-3 mt-lg-0">
                                        <span class="opacity-7 text-muted"><i data-feather="book-open"></i></span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Total File Groups Card -->
                        <div class="card border-right">
                            <div class="card-body">
                                <div class="d-flex d-lg-flex d-md-block align-items-center">
                                    <div>
                                        <div class="d-inline-flex align-items-center">
                                            <h2 class="text-dark mb-1 font-weight-medium" id="total-batches-count">
                                                <?= count($batches); ?>
                                            </h2>
                                        </div>
                                        <h6 class="text-muted font-weight-normal mb-0 w-100 text-truncate">Total File
                                            Groups</h6>
                                    </div>
                                    <div class="ml-auto mt-md-3 mt-lg-0">
                                        <span class="opacity-7 text-muted"><i data-feather="folder"></i></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <style>
                        .chart-container {
                            width: 100%;
                            max-width: 500px;
                            height: 500px !important;
                            margin: auto;
                            display: flex;
                            align-items: center;
                            justify-content: center;
                        }

                        canvas {
                            max-width: 100% !important;
                            height: auto !important;
                        }
                    </style>

                    <!-- Chart Section -->
                    <div class="row">
                        <!-- Total Campuses Chart -->
                        <div class="col-lg-6 col-md-12">
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="card-title">Total TDP Campuses</h5>
                                    <canvas id="total-campuses-chart"></canvas>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-6 col-md-12">
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="card-title">Total TES Campuses</h5>
                                    <canvas id="total-campuses-chart-tes"></canvas>
                                </div>
                            </div>
                        </div>

                        <!-- Total File Groups Chart -->
                        <div class="col-lg-6 col-md-12">
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="card-title">Total File Groups</h5>
                                    <canvas id="total-file-groups-chart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <script>
                   document.addEventListener("DOMContentLoaded", function () {
    var totalRecords = <?php echo json_encode($totalRecords); ?>;
    var totalRecordsTes = <?php echo json_encode($totalTesRecords); ?>;
    var totalCampusesTes = <?php echo json_encode($campusesTes); ?>;
    var totalCampuses = <?php echo json_encode($campuses); ?>;
    var totalBatches = <?php echo json_encode($batches); ?>;

    if (document.getElementById("total-records-count")) {
        document.getElementById("total-records-count").textContent = totalRecords ?? 0;
    }
    if (document.getElementById("total-records-count-tes")) {
        document.getElementById("total-records-count-tes").textContent = totalRecordsTes ?? 0;
    }

    var campusLabels = totalCampuses.map(campus => campus.campus);
    var campusCounts = totalCampuses.map(campus => parseInt(campus.count));

    var campusLabelsTes = totalCampusesTes.map(campus => campus.campus);
    var campusCountsTes = totalCampusesTes.map(campus => parseInt(campus.count));

    var batchLabels = totalBatches.map(batch => batch.ched_tdp_batch);
    var batchCounts = totalBatches.map(batch => parseInt(batch.count));

    var predefinedColors = ['#f56954', '#00a65a', '#f39c12', '#00c0ef', '#3c8dbc', '#d2d6de', '#605ca8', '#f092a2', '#a1887f', '#b7ce63', '#9b59b6'];

    function getColors(count) {
        return predefinedColors.slice(0, count);
    }

    var campusColors = getColors(campusLabels.length);
    var campusColorsTes = getColors(campusLabelsTes.length);
    var batchColors = getColors(batchLabels.length);

    var chartOptions = {
        responsive: true,
        maintainAspectRatio: true, // Keep chart proportions consistent
        plugins: {
            legend: {
                position: 'top'
            }
        },
        scales: {
            y: {
                beginAtZero: true
            }
        }
    };

    function createChart(ctx, type, labels, data, colors, label) {
        if (ctx) {
            new Chart(ctx, {
                type: type,
                data: {
                    labels: labels,
                    datasets: [{
                        label: label,
                        backgroundColor: colors,
                        data: data
                    }]
                },
                options: chartOptions
            });
        }
    }

    createChart(document.getElementById("total-campuses-chart"), 'pie', campusLabels, campusCounts, campusColors, "Campuses");
    createChart(document.getElementById("total-campuses-chart-tes"), 'pie', campusLabelsTes, campusCountsTes, campusColorsTes, "Campuses TES");
    createChart(document.getElementById("total-file-groups-chart"), 'bar', batchLabels, batchCounts, batchColors, "File Groups");
});

                </script>

                <!-- Add more chart containers if needed -->
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