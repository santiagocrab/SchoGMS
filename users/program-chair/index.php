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
    <style>.preloader{display:none!important}#main-wrapper{opacity:1!important}</style>
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
                                        class="text-dark"><?= $program_chair ?></span> <i data-feather="chevron-down"
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
                        <?php require __DIR__ . "/inc/program_chair_sidebar_menu.php"; ?>
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
$totalCourses = $totalFileGroups = $totalFilenames = 0;

// Session + mysqli are loaded at the top of this page.
$campus = isset($_SESSION['campus']) ? $conn->real_escape_string((string) $_SESSION['campus']) : '';
$file_group = isset($_SESSION['course_program']) ? $conn->real_escape_string((string) $_SESSION['course_program']) : '';

if ($campus === '' || $file_group === '') {
    echo '<div class="alert alert-danger">Campus or program is not set for this program chair account. '
        . 'Ask your dean to assign a campus and course program, or sign in with your assigned program chair credentials.</div>';
    exit;
}
$totalQuery = "
    SELECT COUNT(DISTINCT id) AS total_students
    FROM ched_masterlist 
    WHERE sheet_name = ?";
$stmt = $conn->prepare($totalQuery);
$stmt->bind_param("s", $campus);
$stmt->execute();
$totalResult = $stmt->get_result();
$totalRow = $totalResult->fetch_assoc();
$totalStudentsCampus = $totalRow['total_students'] ?? 0;
$stmt->close();

// Fetch count of students under the "TDP" scholarship program
$countQueryTdp = "
    SELECT COUNT(DISTINCT cm.id) AS total_students
    FROM ched_masterlist cm
    INNER JOIN registrar_master_list rm
        ON cm.lastname COLLATE utf8mb4_general_ci = rm.last_name COLLATE utf8mb4_general_ci 
        AND cm.firstname COLLATE utf8mb4_general_ci = rm.first_name COLLATE utf8mb4_general_ci
        AND (
            cm.middlename COLLATE utf8mb4_general_ci = rm.middle_name COLLATE utf8mb4_general_ci 
            OR cm.middlename IS NULL 
            OR rm.middle_name IS NULL 
            OR cm.middlename = '' 
            OR rm.middle_name = ''
        )
    WHERE cm.sheet_name = ? 
    AND cm.course_program_enrolled = ?"; 

$stmt = $conn->prepare($countQueryTdp);
$stmt->bind_param("ss", $campus, $file_group);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$totalStudentsTdp = $row['total_students'] ?? 0;

// Calculate percentage of TDP students
$percentageTdp = ($totalStudentsCampus > 0) ? round(($totalStudentsTdp / $totalStudentsCampus) * 100, 2) : 0;

// Define text color based on percentage
$textColorClassTdp = ($percentageTdp >= 75) ? "text-success" : (($percentageTdp >= 50) ? "text-warning" : "text-danger");

// ==================================================================================================

$totalQueryTes = "
    SELECT COUNT(DISTINCT id) AS total_students
    FROM ched_masterlist_tes 
    WHERE campus = ?";
$stmt = $conn->prepare($totalQueryTes);
$stmt->bind_param("s", $campus);
$stmt->execute();
$totalResult = $stmt->get_result();
$totalRow = $totalResult->fetch_assoc();
$totalStudentsCampusTes = $totalRow['total_students'] ?? 0;
$stmt->close();

$countQueryTes = "
    SELECT COUNT(DISTINCT cm.id) AS total_students
    FROM ched_masterlist_tes cm
    INNER JOIN registrar_master_list rm
        ON cm.lastname COLLATE utf8mb4_general_ci = rm.last_name COLLATE utf8mb4_general_ci 
        AND cm.firstname COLLATE utf8mb4_general_ci = rm.first_name COLLATE utf8mb4_general_ci
        AND (
            cm.middlename COLLATE utf8mb4_general_ci = rm.middle_name COLLATE utf8mb4_general_ci 
            OR cm.middlename IS NULL 
            OR rm.middle_name IS NULL 
            OR cm.middlename = '' 
            OR rm.middle_name = ''
        )
    WHERE cm.campus = ? 
    AND cm.course_program_enrolled = ?"; 

$stmt = $conn->prepare($countQueryTes);
$stmt->bind_param("ss", $campus, $file_group);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$totalStudentsTes = $row['total_students'] ?? 0;

// Calculate percentage of TDP students
$percentageTes = ($totalStudentsCampusTes > 0) ? round(($totalStudentsTes / $totalStudentsCampusTes) * 100, 2) : 0;

// Define text color based on percentage
$textColorClassTes = ($percentageTes >= 75) ? "text-success" : (($percentageTes >= 50) ? "text-warning" : "text-danger");
$tdpSubtext = ($totalStudentsCampus > 0)
    ? number_format($percentageTdp, 1) . '% of campus TDP masterlist'
    : 'No campus TDP records available.';
$tesSubtext = ($totalStudentsCampusTes > 0)
    ? number_format($percentageTes, 1) . '% of campus TES masterlist'
    : 'No campus TES records available.';

$stmt->close();
$conn->close();
?>



                <div class="card-group">
                    <!-- Total Records Card -->
                     
<div class="card border-right">
    <div class="card-body">
        <div class="d-flex d-lg-flex d-md-block align-items-center">
            <div>
                <h2 class="text-dark mb-1 font-weight-medium"><?php echo (int) $totalStudentsTdp; ?></h2>
                <?php if ((int) $totalStudentsCampus > 0) : ?>
                <p class="mb-1 <?php echo $textColorClassTdp; ?> small font-weight-medium"><?php echo htmlspecialchars($tdpSubtext); ?></p>
                <?php else: ?>
                <p class="mb-1 text-muted small">Add records to view the TDP share.</p>
                <?php endif; ?>
                <h6 class="text-muted font-weight-normal mb-0 w-100 text-truncate">
                    Total TDP scholars in <?php echo htmlspecialchars($_SESSION['campus']); ?>
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
                <h2 class="text-dark mb-1 font-weight-medium"><?php echo (int) $totalStudentsTes; ?></h2>
                <?php if ((int) $totalStudentsCampusTes > 0) : ?>
                <p class="mb-1 <?php echo $textColorClassTes; ?> small font-weight-medium"><?php echo htmlspecialchars($tesSubtext); ?></p>
                <?php else: ?>
                <p class="mb-1 text-muted small">Add records to view the TES share.</p>
                <?php endif; ?>
                <h6 class="text-muted font-weight-normal mb-0 w-100 text-truncate">
                    Total TES scholars in <?php echo htmlspecialchars($_SESSION['campus']); ?>
                </h6>
            </div>
            <div class="ml-auto mt-md-3 mt-lg-0">
                <span class="opacity-7 text-muted"><i data-feather="database"></i></span>
            </div>
        </div>
    </div>
</div>


                    <!-- Total Courses Card -->
                    <!-- <div class="card border-right">
                        <div class="card-body">
                            <div class="d-flex d-lg-flex d-md-block align-items-center">
                                <div>
                                    <div class="d-inline-flex align-items-center">
                                        <h2 class="text-dark mb-1 font-weight-medium"><?= $totalCourses; ?></h2>
                                    </div>
                                    <h6 class="text-muted font-weight-normal mb-0 w-100 text-truncate">Total
                                        Courses</h6>
                                </div>
                                <div class="ml-auto mt-md-3 mt-lg-0">
                                    <span class="opacity-7 text-muted"><i data-feather="book-open"></i></span>
                                </div>
                            </div>
                        </div>
                    </div> -->

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
                    <!-- Total Records Card -->
                    <!-- COR Category Card -->
                    <!-- <div class="col-lg-4 col-md-6">
                        <div class="card">
                            <div class="card-body text-center">
                                <h4 class="card-title">COR Documents</h4>
                                <h2 class="font-weight-medium" id="cor-count">0</h2>
                            </div>
                        </div>
                    </div> -->

                    <!-- COG Category Card -->
                    <!-- <div class="col-lg-4 col-md-6">
                        <div class="card">
                            <div class="card-body text-center">
                                <h4 class="card-title">COG Documents</h4>
                                <h2 class="font-weight-medium" id="cog-count">0</h2>
                            </div>
                        </div>
                    </div> -->
                    <!-- <div class="col-lg-8 col-md-12">
                        <div class="card">
                            <div class="card-body">
                                <h4 class="card-title">Total File Groups</h4>
                                <canvas id="total-file-groups-chart"></canvas>
                            </div>
                        </div>
                    </div> -->
                    <!-- Total Filenames Chart -->
                 <div class="col-lg-6 col-md-12">
                        <div class="card">
                            <div class="card-body">
                                <h4 class="card-title mb-3">TDP Scholars by Program</h4>
                                <p id="course-chart-empty" class="text-muted small mb-2 d-none">No TDP data to display for this program.</p>
                                <canvas id="course-chart" width="400" height="200"></canvas>
                            </div>
                        </div>
                    </div>
                     <div class="col-lg-6 col-md-12">
                        <div class="card">
                            <div class="card-body">
                                <h4 class="card-title mb-3">TES Scholars by Program</h4>
                                <p id="course-chart-tes-empty" class="text-muted small mb-2 d-none">No TES data to display for this program.</p>
                                <canvas id="course-chart-tes" width="400" height="200"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Chart Containers -->




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
    <script src="../../assets/libs/jquery/dist/jquery.min.js"></script>
    <script src="../../assets/libs/popper.js/dist/umd/popper.min.js"></script>
    <script src="../../assets/libs/bootstrap/dist/js/bootstrap.min.js"></script>
    <!-- jQuery -->
    <!-- jQuery -->

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- apps -->

     <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- apps -->

    <script>
     $(document).ready(function () {
    $.ajax({
        url: 'fetch_chart.php', // Ensure this matches your PHP script
        method: 'GET',
        dataType: 'json',
        success: function (data) {
            if (!data || data.error) {
                console.error("Error fetching data:", data.error || "Invalid data");
                return;
            }

            var studentsData = data.students_data || [];
            var totalStudents = data.total_students || 0;
            var emptyTdp = document.getElementById("course-chart-empty");
            var tdpCanvas = document.getElementById("course-chart");

            if (studentsData.length === 0) {
                if (emptyTdp) emptyTdp.classList.remove("d-none");
                if (tdpCanvas) tdpCanvas.style.display = "none";
                return;
            }
            if (emptyTdp) emptyTdp.classList.add("d-none");
            if (tdpCanvas) tdpCanvas.style.display = "block";

            // Prepare chart labels and data
            var courseLabels = studentsData.map(item => 
                `${item.course_program_enrolled} (${item.percentage}%)`
            );
            var studentCounts = studentsData.map(item => item.total_students);

            // Define dynamic colors (ensuring variety)
            var dynamicColors = [
                "#ff4f70", "#01caf1", "#ffcd56", "#4bc0c0", "#9966ff",
                "#f56954", "#00a65a", "#f39c12", "#00c0ef", "#3c8dbc", "#d2d6de"
            ];
            var backgroundColors = courseLabels.map((_, i) => dynamicColors[i % dynamicColors.length]);

            // Destroy previous chart instance if exists
            // if (window.courseChart) {
            //     window.courseChart.destroy();
            // }

            // Create the bar chart
            var ctx = document.getElementById("course-chart").getContext('2d');
            window.courseChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: courseLabels,
                    datasets: [{
                        label: "Number of TDP scholars",
                        backgroundColor: backgroundColors,
                        data: studentCounts
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            display: true,
                            position: 'top'
                        },
                        title: {
                            display: true,
                            text: 'TDP Scholar Distribution by Program',
                            font: {
                                size: 16
                            }
                        }
                    },
                    scales: {
                        x: {
                            ticks: {
                                autoSkip: false,
                                maxRotation: 45,
                                minRotation: 0
                            },
                            grid: {
                                display: false
                            }
                        },
                        y: {
                            beginAtZero: true,
                            ticks: {
                                precision: 0
                            },
                            grid: {
                                drawBorder: false
                            }
                        }
                    }
                }
            });
        },
        error: function (err) {
            console.error("AJAX error fetching course data:", err);
        }
    });
});

    </script>
    <script>
     $(document).ready(function () {
    $.ajax({
        url: 'fetch_chart_tes.php', // Ensure this matches your PHP script
        method: 'GET',
        dataType: 'json',
        success: function (data) {
            if (!data || data.error) {
                console.error("Error fetching data:", data.error || "Invalid data");
                return;
            }

            var studentsData = data.students_data_tes || [];
            var totalStudents = data.total_students || 0;
            var emptyTes = document.getElementById("course-chart-tes-empty");
            var tesCanvas = document.getElementById("course-chart-tes");

            if (studentsData.length === 0) {
                if (emptyTes) emptyTes.classList.remove("d-none");
                if (tesCanvas) tesCanvas.style.display = "none";
                return;
            }
            if (emptyTes) emptyTes.classList.add("d-none");
            if (tesCanvas) tesCanvas.style.display = "block";

            // Prepare chart labels and data
            var courseLabels = studentsData.map(item => 
                `${item.course_program_enrolled} (${item.percentage}%)`
            );
            var studentCounts = studentsData.map(item => item.total_students);

            // Define dynamic colors (ensuring variety)
            var dynamicColors = [
                "#ff4f70", "#01caf1", "#ffcd56", "#4bc0c0", "#9966ff",
                "#f56954", "#00a65a", "#f39c12", "#00c0ef", "#3c8dbc", "#d2d6de"
            ];
            var backgroundColors = courseLabels.map((_, i) => dynamicColors[i % dynamicColors.length]);

            // Destroy previous chart instance if exists
            // if (window.courseChart) {
            //     window.courseChart.destroy();
            // }

            // Create the bar chart
            var ctx = document.getElementById("course-chart-tes").getContext('2d');
            window.courseChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: courseLabels,
                    datasets: [{
                        label: "Number of TES scholars",
                        backgroundColor: backgroundColors,
                        data: studentCounts
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            display: true,
                            position: 'top'
                        },
                        title: {
                            display: true,
                            text: 'TES Scholar Distribution by Program',
                            font: {
                                size: 16
                            }
                        }
                    },
                    scales: {
                        x: {
                            ticks: {
                                autoSkip: false,
                                maxRotation: 45,
                                minRotation: 0
                            },
                            grid: {
                                display: false
                            }
                        },
                        y: {
                            beginAtZero: true,
                            ticks: {
                                precision: 0
                            },
                            grid: {
                                drawBorder: false
                            }
                        }
                    }
                }
            });
        },
        error: function (err) {
            console.error("AJAX error fetching course data:", err);
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
    
    <footer> 
    <script>
if(navigator.serviceWorker) { // Ensure this path is correct
    navigator.serviceWorker.register('serviceWorker-sw.js') 
}
</script></footer>
<?php require_once __DIR__ . '/inc/program_chair_assets.php'; schogms_app_emit_nav_init_script(); ?>
</body>

</html>