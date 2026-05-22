<?php
include 'config/session.php';
require_once __DIR__ . '/../../inc/campus_access.php';
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
include 'config/conn.php'; // Database connection

// Start session if not started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Get campus and selected course from session
$campus = isset($_SESSION['campus']) ? $conn->real_escape_string($_SESSION['campus']) : '';
$selectedCourse = isset($_SESSION['course_program']) ? $conn->real_escape_string($_SESSION['course_program']) : '';

// Validate inputs
if (empty($campus) || empty($selectedCourse)) {
    echo "<p class='alert alert-danger'>Campus or course not found on your program chair account.</p>";
    exit();
}
$chairCollege = trim((string) ($_SESSION['college_name'] ?? ''));

// Query to count students in the selected course
$countQuery = "
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
    AND cm.course_program_enrolled = ?
    ORDER BY cm.lastname ASC
";

$stmt = $conn->prepare($countQuery);
if (!$stmt) {
    die("<p class='alert alert-danger'>Query preparation failed: " . $conn->error . "</p>");
}

$stmt->bind_param("ss", $campus, $selectedCourse);
if (!$stmt->execute()) {
    die("<p class='alert alert-danger'>Execution failed: " . $stmt->error . "</p>");
}

$result = $stmt->get_result();
$row = $result->fetch_assoc();
$totalStudents = $row['total_students'];


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
    AND cm.course_program_enrolled = ?
    ORDER BY cm.lastname ASC
";

$stmt = $conn->prepare($countQueryTes);
if (!$stmt) {
    die("<p class='alert alert-danger'>Query preparation failed: " . $conn->error . "</p>");
}

$stmt->bind_param("ss", $campus, $selectedCourse);
if (!$stmt->execute()) {
    die("<p class='alert alert-danger'>Execution failed: " . $stmt->error . "</p>");
}

$result = $stmt->get_result();
$row = $result->fetch_assoc();
$totalStudentsTes = $row['total_students'];

$stmt->close();
$conn->close();

// Display the total student count
// echo "<h5>Total Students Enrolled: <strong>" . htmlspecialchars($totalStudents) . "</strong></h5>";
?>





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
    <?php
     include 'config/conn.php'; // Database connection
     
     // Start session if not started
     if (session_status() === PHP_SESSION_NONE) {
         session_start();
     }
     
     // Get campus and selected course from session
     $campus = isset($_SESSION['campus']) ? $conn->real_escape_string($_SESSION['campus']) : '';
     $selectedCourse = isset($_SESSION['course_program']) ? $conn->real_escape_string($_SESSION['course_program']) : '';
     
     // Validate inputs
     if (empty($campus) || empty($selectedCourse)) {
         echo "<p class='alert alert-danger'>Campus or Course Program not found.</p>";
         exit();
     }
     
     // Fetch student names by joining `ched_masterlist` and `registrar_master_list`
     $studentQuery = "
         SELECT 
             cm.course_program_enrolled, 
             cm.lastname, 
             cm.firstname, 
             cm.middlename
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
         AND cm.course_program_enrolled = ?
         ORDER BY cm.lastname ASC
     ";
     
     $stmt = $conn->prepare($studentQuery);
     if (!$stmt) {
         die("<p class='alert alert-danger'>Query preparation failed: " . $conn->error . "</p>");
     }
     
     $stmt->bind_param("ss", $campus, $selectedCourse);
     if (!$stmt->execute()) {
         die("<p class='alert alert-danger'>Execution failed: " . $stmt->error . "</p>");
     }
     
     $result = $stmt->get_result();
     ?>

    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <h5>Displaying Students TDP Campus: <strong><?php echo htmlspecialchars($campus); ?></strong></h5>
                    <h6>Course: <strong><?php echo htmlspecialchars($selectedCourse); ?></strong></h6>

                    <table id="zero_config" class="table table-striped table-bordered no-wrap">
                        <thead>
                            <tr>
                                <th>Last Name</th>
                                <th>First Name</th>
                                <th>Middle Name</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if ($result->num_rows > 0) {
                                while ($row = $result->fetch_assoc()) {
                                    echo "<tr>
                                        <td>" . htmlspecialchars($row['lastname']) . "</td>
                                        <td>" . htmlspecialchars($row['firstname']) . "</td>
                                        <td>" . htmlspecialchars($row['middlename']) . "</td>
                                    </tr>";
                                }
                            } else {
                                echo "<tr><td colspan='3' class='text-center'>No students found.</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <?php
    $stmt->close();
    $conn->close();
    ?>

</div>

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