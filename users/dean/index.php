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
                        <?php require __DIR__ . "/inc/dean_sidebar_menu.php"; ?>
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
// Include the database connection
include 'config/conn.php';

// 1. Get TOTAL students enrolled in registrar master list for the campus
$totalEnrolledQuery = "SELECT COUNT(DISTINCT id) AS total_enrolled 
    FROM registrar_master_list 
    WHERE campus = '$campus'";

$totalEnrolledResult = $conn->query($totalEnrolledQuery);
$totalEnrolled = ($totalEnrolledResult && $row = $totalEnrolledResult->fetch_assoc()) ? $row['total_enrolled'] : 0;

// 2. Get TOTAL students in CHED master list for the campus
$totalMasterlistQuery = "SELECT COUNT(DISTINCT id) AS total_masterlist 
    FROM ched_masterlist 
    WHERE sheet_name = '$campus'";

$totalMasterlistResult = $conn->query($totalMasterlistQuery);
$totalMasterlist = ($totalMasterlistResult && $row = $totalMasterlistResult->fetch_assoc()) ? $row['total_masterlist'] : 0;

// 3. Get TOTAL students in CHED masterlist TES for the campus
$totalTesMasterlistQuery = "SELECT COUNT(DISTINCT id) AS total_tes_masterlist 
    FROM ched_masterlist_tes 
    WHERE campus = '$campus'";

$totalTesMasterlistResult = $conn->query($totalTesMasterlistQuery);
$totalTesMasterlist = ($totalTesMasterlistResult && $row = $totalTesMasterlistResult->fetch_assoc()) ? $row['total_tes_masterlist'] : 0;

// 4. Get total ENROLLED students under the specific course
$enrolledByCourseQuery = "SELECT COUNT(DISTINCT id) AS enrolled_course 
    FROM registrar_master_list 
    WHERE campus = '$campus' 
      AND file_group = '$course_program'";

$enrolledByCourseResult = $conn->query($enrolledByCourseQuery);
$enrolledCourse = ($enrolledByCourseResult && $row = $enrolledByCourseResult->fetch_assoc()) ? $row['enrolled_course'] : 0;

// 5. Get total CHED MASTERLIST students matching course
$masterlistByCourseQuery = "SELECT COUNT(DISTINCT cm.id) AS masterlist_course
    FROM ched_masterlist cm
    LEFT JOIN registrar_master_list rm
        ON cm.lastname COLLATE utf8mb4_general_ci = rm.last_name COLLATE utf8mb4_general_ci
        AND cm.firstname COLLATE utf8mb4_general_ci = rm.first_name COLLATE utf8mb4_general_ci
        AND (
            cm.middlename COLLATE utf8mb4_general_ci = rm.middle_name COLLATE utf8mb4_general_ci
            OR cm.middlename IS NULL
            OR cm.middlename = ''
            OR rm.middle_name IS NULL
            OR rm.middle_name = ''
        )
    WHERE cm.sheet_name = '$campus'
      AND rm.file_group = '$course_program'";

$masterlistByCourseResult = $conn->query($masterlistByCourseQuery);
$masterlistCourse = ($masterlistByCourseResult && $row = $masterlistByCourseResult->fetch_assoc()) ? $row['masterlist_course'] : 0;

// 6. Get total TES MASTERLIST students matching course
$tesMasterlistByCourseQuery = "SELECT COUNT(DISTINCT cm.id) AS tes_masterlist_course
    FROM ched_masterlist_tes cm
    LEFT JOIN registrar_master_list rm
        ON cm.lastname COLLATE utf8mb4_general_ci = rm.last_name COLLATE utf8mb4_general_ci
        AND cm.firstname COLLATE utf8mb4_general_ci = rm.first_name COLLATE utf8mb4_general_ci
        AND (
            cm.middlename COLLATE utf8mb4_general_ci = rm.middle_name COLLATE utf8mb4_general_ci
            OR cm.middlename IS NULL
            OR cm.middlename = ''
            OR rm.middle_name IS NULL
            OR rm.middle_name = ''
        )
    WHERE cm.campus = '$campus' and course_program_enrolled != 'BACHELOR OF SCIENCE IN INDUSTRIAL TECHNOLOGY MAJOR IN CIVIL TECHNOLOGY' 
      AND rm.file_group = '$course_program'";

$tesMasterlistByCourseResult = $conn->query($tesMasterlistByCourseQuery);
$tesMasterlistCourse = ($tesMasterlistByCourseResult && $row = $tesMasterlistByCourseResult->fetch_assoc()) ? $row['tes_masterlist_course'] : 0;

// 7. Calculate Percentages
// 7. Calculate Percentages correctly based on ENROLLED
$enrolledCoursePercentage = ($totalEnrolled > 0) ? ($enrolledCourse / $totalEnrolled) * 100 : 0;
$masterlistCoursePercentage = ($enrolledCourse > 0) ? ($masterlistCourse / $enrolledCourse) * 100 : 0;
$tesMasterlistCoursePercentage = ($enrolledCourse > 0) ? ($tesMasterlistCourse / $enrolledCourse) * 100 : 0;


// Define text colors
$enrolledTextColor = ($enrolledCoursePercentage >= 75) ? "text-success" : (($enrolledCoursePercentage >= 50) ? "text-warning" : "text-danger");
$masterlistTextColor = ($masterlistCoursePercentage >= 75) ? "text-success" : (($masterlistCoursePercentage >= 50) ? "text-warning" : "text-danger");
$tesMasterlistTextColor = ($tesMasterlistCoursePercentage >= 75) ? "text-success" : (($tesMasterlistCoursePercentage >= 50) ? "text-warning" : "text-danger");

// Close the connection
$conn->close();
?>

                <div class="card-group">
                    <div class="card border-right" hidden>
        <div class="card-body">
            <h2 class="text-dark mb-1 font-weight-medium"><?= $enrolledCourse; ?></h2>
            <h2 class="<?= $enrolledTextColor; ?>">(<?= number_format($enrolledCoursePercentage, 2); ?>%) Availed</h2>
            <h6 class="text-muted font-weight-normal mb-0">Enrolled Students in <?= htmlspecialchars($course_program); ?> at <?= htmlspecialchars($campus); ?></h6>
        </div>
    </div>
      <div class="card border-right">
        <div class="card-body">
            <h2 class="text-dark mb-1 font-weight-medium"><?= (int) $masterlistCourse; ?></h2>
            <?php if ((int) $enrolledCourse > 0) : ?>
            <p class="mb-1 <?= $masterlistTextColor; ?> small font-weight-medium">
                <?= number_format($masterlistCoursePercentage, 1) ?>% of <?= (int) $enrolledCourse; ?> program enrollees
            </p>
            <?php else: ?>
            <p class="mb-1 text-muted small">Add enrollees in the registrar for this file group to see a percentage.</p>
            <?php endif; ?>
            <h6 class="text-muted font-weight-normal mb-0">Total TDP scholars from College of Engineering</h6>
        </div>
    </div>

    <div class="card border-right">
       
        <div class="card-body">
            <h2 class="text-dark mb-1 font-weight-medium"><?= (int) $tesMasterlistCourse; ?></h2>
            <?php if ((int) $enrolledCourse > 0) : ?>
            <p class="mb-1 <?= $tesMasterlistTextColor; ?> small font-weight-medium">
                <?= number_format($tesMasterlistCoursePercentage, 1) ?>% of <?= (int) $enrolledCourse; ?> program enrollees
            </p>
            <?php else: ?>
            <p class="mb-1 text-muted small">Add enrollees in the registrar for this file group to see a percentage.</p>
            <?php endif; ?>
            <h6 class="text-muted font-weight-normal mb-0">Total TES scholars from College of Engineering</h6>
        </div>
    </div>

                <?php
//                 // Include the database connection file
//                 include 'config/conn.php'; // Modify with the actual connection path
                
//                 // SQL queries to count the required data
//               $totalStudentsQuery = "SELECT COUNT(DISTINCT id) AS total_students 
//     FROM ched_masterlist 
//     WHERE sheet_name = '$campus'";

// $totalStudentsResult = $conn->query($totalStudentsQuery);
// $totalStudents = ($totalStudentsResult && $row = $totalStudentsResult->fetch_assoc()) ? $row['total_students'] : 0;

// // Get students who availed (joined in registrar_master_list) for the selected course program
// $joinedStudentsQuery = "SELECT COUNT(DISTINCT rm.id) AS joined_students 
//     FROM ched_masterlist cm
//     LEFT JOIN registrar_master_list rm
//         ON cm.lastname COLLATE utf8mb4_general_ci = rm.last_name COLLATE utf8mb4_general_ci 
//         AND cm.firstname COLLATE utf8mb4_general_ci = rm.first_name COLLATE utf8mb4_general_ci
//         AND (cm.middlename COLLATE utf8mb4_general_ci = rm.middle_name COLLATE utf8mb4_general_ci 
//             OR cm.middlename IS NULL 
//             OR rm.middle_name IS NULL 
//             OR cm.middlename = '' 
//             OR rm.middle_name = '')
//     WHERE cm.sheet_name = '$campus'
//     AND rm.file_group = '$course_program'";

// $joinedStudentsResult = $conn->query($joinedStudentsQuery);
// $joinedStudents = ($joinedStudentsResult && $joinedRow = $joinedStudentsResult->fetch_assoc()) ? $joinedRow['joined_students'] : 0;

// // Calculate the percentage of students who availed
// $joinedPercentage = ($totalStudents > 0) ? ($joinedStudents / $totalStudents) * 100 : 0;

// // Define text color based on percentage
// $textColorClass = ($joinedPercentage >= 75) ? "text-success" : (($joinedPercentage >= 50) ? "text-warning" : "text-danger");


    
    
    
//   $totalStudentsQueryTes = "SELECT COUNT(DISTINCT id) AS total_students 
//     FROM ched_masterlist_tes 
//     WHERE campus = '$campus'";

// $totalStudentsResultTes = $conn->query($totalStudentsQueryTes);
// $totalStudentsTes = ($totalStudentsResultTes && $row = $totalStudentsResultTes->fetch_assoc()) ? $row['total_students'] : 0;

// // Get students who availed (joined in registrar_master_list) for the selected course program
// $joinedStudentsQueryTes = "SELECT COUNT(DISTINCT rm.id) AS joined_students 
//     FROM ched_masterlist_tes cm
//     LEFT JOIN registrar_master_list rm
//         ON cm.lastname COLLATE utf8mb4_general_ci = rm.last_name COLLATE utf8mb4_general_ci 
//         AND cm.firstname COLLATE utf8mb4_general_ci = rm.first_name COLLATE utf8mb4_general_ci
//         AND (cm.middlename COLLATE utf8mb4_general_ci = rm.middle_name COLLATE utf8mb4_general_ci 
//             OR cm.middlename IS NULL 
//             OR rm.middle_name IS NULL 
//             OR cm.middlename = '' 
//             OR rm.middle_name = '')
//     WHERE cm.campus = '$campus'
//     AND rm.file_group = '$course_program'";

// $joinedStudentsResultTes = $conn->query($joinedStudentsQueryTes);
// $joinedStudentsTes = ($joinedStudentsResultTes && $joinedRowTes = $joinedStudentsResultTes->fetch_assoc()) ? $joinedRowTes['joined_students'] : 0;

// // Calculate the percentage of students who availed
// $joinedPercentageTes = ($totalStudentsTes > 0) ? ($joinedStudentsTes / $totalStudentsTes) * 100 : 0;

// // Define text color based on percentage
// $textColorClassTes = ($joinedPercentageTes >= 75) ? "text-success" : (($joinedPercentageTes >= 50) ? "text-warning" : "text-danger");
//                 // Close the connection
//                 $conn->close();
                ?>

<!--                <div class="card-group">-->
                   
<!--                  <div class="card border-right">-->
<!--    <div class="card-body">-->
<!--        <div class="d-flex d-lg-flex d-md-block align-items-center">-->
<!--            <div>-->
<!--                <div class="d-inline-flex align-items-center">-->
<!--                    <h2 class="text-dark mb-1 font-weight-medium"><?= $joinedStudents; ?></h2>-->
<!--                </div>-->
<!--                <div class="d-inline-flex align-items-center <?= $textColorClass; ?>">-->
<!--                    <h2>(<?= number_format($joinedPercentage, 2); ?>%) Availed</h2>-->
<!--                </div>-->
                
<!--                <h6 class="text-muted font-weight-normal mb-0 w-100 text-truncate">-->
<!--                    Total TDP Scholars in <?= htmlspecialchars($campus); ?>-->
<!--                </h6>-->
<!--            </div>-->
<!--            <div class="ml-auto mt-md-3 mt-lg-0">-->
<!--                <span class="opacity-7 text-muted">-->
<!--                    <i data-feather="database"></i>-->
<!--                </span>-->
<!--            </div>-->
<!--        </div>-->
<!--    </div>-->
<!--</div>-->
<!--                    <div class="card border-right">-->
<!--    <div class="card-body">-->
<!--        <div class="d-flex d-lg-flex d-md-block align-items-center">-->
<!--            <div>-->
<!--                <div class="d-inline-flex align-items-center">-->
<!--                    <h2 class="text-dark mb-1 font-weight-medium"><?= $joinedStudentsTes; ?></h2>-->
<!--                </div>-->
<!--                <div class="d-inline-flex align-items-center <?= $textColorClassTes; ?>">-->
<!--                    <h2>(<?= number_format($joinedPercentageTes, 2); ?>%) Availed</h2>-->
<!--                </div>-->
                
<!--                <h6 class="text-muted font-weight-normal mb-0 w-100 text-truncate">-->
<!--                    Total TES Scholars in <?= htmlspecialchars($campus); ?>-->
<!--                </h6>-->
<!--            </div>-->
<!--            <div class="ml-auto mt-md-3 mt-lg-0">-->
<!--                <span class="opacity-7 text-muted">-->
<!--                    <i data-feather="database"></i>-->
<!--                </span>-->
<!--            </div>-->
<!--        </div>-->
<!--    </div>-->
<!--</div>-->

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
                </div>

                <!-- Chart Containers -->
                <div class="row">
                    <div class="col-lg-8 col-md-12">
                        <div class="card">
                            <div class="card-body">
                                <h4 class="card-title mb-3">Number of TDP scholars</h4>
                                <p id="course-chart-tdp-empty" class="text-muted small mb-0 d-none">No TDP data to show for this program yet.</p>
                                <canvas id="course-chart" width="400" height="200"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-8 col-md-12">
                        <div class="card">
                            <div class="card-body">
                                <h4 class="card-title mb-3">Number of TES scholars</h4>
                                <p id="course-chart-tes-empty" class="text-muted small mb-0 d-none">No TES data to show for this program yet.</p>
                                <canvas id="course-chart-tes" width="400" height="200"></canvas>
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
$(document).ready(function () {
    $.ajax({
        url: 'fetch_chart.php',
        method: 'GET',
        dataType: 'json',
        success: function (data) {
            if (data && data.error && !data.students_data_tdp) {
                console.error("Error fetching TDP data:", data.error);
                return;
            }

            const studentsData = (data && data.students_data_tdp) ? data.students_data_tdp : [];
            const totalStudents = data && (data.registrar_total_students != null) ? Number(data.registrar_total_students) : 0;

            const tdpEmpty = document.getElementById('course-chart-tdp-empty');
            const tdpCanvas = document.getElementById('course-chart');
            if (!studentsData.length) {
                if (tdpEmpty) tdpEmpty.classList.remove('d-none');
                if (tdpCanvas) tdpCanvas.style.display = 'none';
                return;
            }
            if (tdpEmpty) tdpEmpty.classList.add('d-none');
            if (tdpCanvas) tdpCanvas.style.display = 'block';
            
            const courseLabels = studentsData.map(item => {
                if (!item.total_students || totalStudents < 1) {
                    return item.course_program_enrolled;
                }
                const percentage = (item.total_students / totalStudents) * 100;
                if (percentage < 0.05) {
                    return item.course_program_enrolled;
                }
                return item.course_program_enrolled + ' (' + percentage.toFixed(0) + '%)';
            });
            
            const studentCounts = studentsData.map(item => item.total_students);

            
            const dynamicColors = [
                "#f56954", "#00a65a", "#f39c12", "#00c0ef",
                "#3c8dbc", "#d2d6de", "#605ca8", "#f092a2",
                "#a1887f", "#b7ce63", "#9b59b6"
            ];
            const backgroundColors = courseLabels.map((_, i) => dynamicColors[i % dynamicColors.length]);

            const ctx = document.getElementById("course-chart").getContext('2d');
            if (window.courseChart) {
                window.courseChart.destroy();
            }

            window.courseChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: courseLabels,
                    datasets: [{
                        label: "Number of TDP Scholars",
                        backgroundColor: backgroundColors,
                        data: studentCounts
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            display: true,
                            position: 'top',
                            labels: {
                                boxWidth: 0,
                                usePointStyle: false
                            }
                        }
                    },
                    scales: {
                        x: {
                            ticks: { display: true },
                            grid: { display: false }
                        },
                        y: {
                            beginAtZero: true,
                            ticks: { precision: 0 }
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
            if (data && data.error && !data.students_data_tes) {
                console.error("Error fetching TES data:", data.error);
                return;
            }

            const studentsData = (data && data.students_data_tes) ? data.students_data_tes : [];
            const enrolledCourse = <?php echo json_encode((int) $enrolledCourse); ?>;

            const tesEmpty = document.getElementById('course-chart-tes-empty');
            const tesCanvas = document.getElementById('course-chart-tes');
            if (!studentsData.length) {
                if (tesEmpty) tesEmpty.classList.remove('d-none');
                if (tesCanvas) tesCanvas.style.display = 'none';
                return;
            }
            if (tesEmpty) tesEmpty.classList.add('d-none');
            if (tesCanvas) tesCanvas.style.display = 'block';

            const courseLabels = studentsData.map(item => {
                if (!item.total_students || enrolledCourse < 1) {
                    return item.course_program_enrolled;
                }
                const percentage = (item.total_students / enrolledCourse) * 100;
                if (percentage < 0.05) {
                    return item.course_program_enrolled;
                }
                return item.course_program_enrolled + ' (' + percentage.toFixed(0) + '%)';
            });
            
            const studentCounts = studentsData.map(item => item.total_students);

            // Generate dynamic colors for each course
            var dynamicColors = [
                "#ff4f70", "#01caf1", "#ffcd56", "#4bc0c0", "#9966ff"
            ];
            var backgroundColors = courseLabels.map((_, i) => dynamicColors[i % dynamicColors.length]);

            if (window.courseChartTes) {
                window.courseChartTes.destroy();
            }

            // Create the chart
            var ctx = document.getElementById("course-chart-tes").getContext('2d');
            window.courseChartTes = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: courseLabels,
                    datasets: [{
                        label: "Number of TES Scholars",
                        backgroundColor: backgroundColors,
                        data: studentCounts
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            display: true,
                            position: 'top',
                            labels: {
                                boxWidth: 0,
                                usePointStyle: false
                            }
                        }
                    },
                    scales: {
                        x: {
                            ticks: {
                                display: true
                            },
                            grid: {
                                display: false
                            }
                        },
                        y: {
                            beginAtZero: true,
                            ticks: {
                                precision: 0
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
</body>

</html>