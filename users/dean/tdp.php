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
                // Include the database connection file
                include 'config/conn.php'; // Modify with the actual connection path
                
                // SQL queries to count the required data
                $totalRecordsQuery = "SELECT 
        rm.file_group, 
        cm.sheet_name, 
        COUNT(DISTINCT cm.id) AS total_students
    FROM ched_masterlist cm
    LEFT JOIN registrar_master_list rm
        ON cm.lastname COLLATE utf8mb4_general_ci = rm.last_name COLLATE utf8mb4_general_ci 
        AND cm.firstname COLLATE utf8mb4_general_ci = rm.first_name COLLATE utf8mb4_general_ci
        AND (cm.middlename COLLATE utf8mb4_general_ci = rm.middle_name COLLATE utf8mb4_general_ci 
            OR cm.middlename IS NULL 
            OR rm.middle_name IS NULL 
            OR cm.middlename = '' 
            OR rm.middle_name = '')
    WHERE cm.sheet_name = '$campus'
    AND rm.file_group = '$course_program'
    GROUP BY rm.file_group, cm.sheet_name
    ORDER BY cm.sheet_name ASC, rm.file_group ASC"; 
    
    $totalRecordsQueryTes = "SELECT 
        rm.file_group, 
        cm.campus, 
        COUNT(DISTINCT cm.id) AS total_students
    FROM ched_masterlist_tes cm
    LEFT JOIN registrar_master_list rm
        ON cm.lastname COLLATE utf8mb4_general_ci = rm.last_name COLLATE utf8mb4_general_ci 
        AND cm.firstname COLLATE utf8mb4_general_ci = rm.first_name COLLATE utf8mb4_general_ci
        AND (cm.middlename COLLATE utf8mb4_general_ci = rm.middle_name COLLATE utf8mb4_general_ci 
            OR cm.middlename IS NULL 
            OR rm.middle_name IS NULL 
            OR cm.middlename = '' 
            OR rm.middle_name = '')
    WHERE cm.campus = '$campus'
    AND rm.file_group = '$course_program'
    GROUP BY rm.file_group, cm.campus
    ORDER BY cm.campus ASC, rm.file_group ASC";
    
                $totalCoursesQuery = "SELECT COUNT(DISTINCT course_program_enrolled) AS total_courses FROM ched_masterlist where sheet_name = '$campus'";
                $totalFileGroupsQuery = "SELECT COUNT(DISTINCT file_group) AS total_file_groups FROM ched_masterlist where sheet_name = '$campus'";
                $totalFilenamesQuery = "SELECT COUNT(DISTINCT filename) AS total_filenames FROM ched_masterlist where sheet_name = '$campus'";

                // Execute the queries
                $totalRecordsResult = $conn->query($totalRecordsQuery);
                $totalRecordsResultTes = $conn->query($totalRecordsQueryTes);
                $totalCoursesResult = $conn->query($totalCoursesQuery);
                $totalFileGroupsResult = $conn->query($totalFileGroupsQuery);
                $totalFilenamesResult = $conn->query($totalFilenamesQuery);

                // Fetch results for each query
                $totalRecords = $totalRecordsResult ? $totalRecordsResult->fetch_assoc()['total_students'] : 0;
                $totalRecordsTes = $totalRecordsResultTes ? $totalRecordsResultTes->fetch_assoc()['total_students'] : 0;
                $totalCourses = $totalCoursesResult ? $totalCoursesResult->fetch_assoc()['total_courses'] : 0;
                $totalFileGroups = $totalFileGroupsResult ? $totalFileGroupsResult->fetch_assoc()['total_file_groups'] : 0;
                $totalFilenames = $totalFilenamesResult ? $totalFilenamesResult->fetch_assoc()['total_filenames'] : 0;
                // Close the connection
                $conn->close();
                ?>

                <div class="card-group">
                    <!-- Total Records Card -->
                    <!--<div class="card border-right">-->
                    <!--    <div class="card-body">-->
                    <!--        <div class="d-flex d-lg-flex d-md-block align-items-center">-->
                    <!--            <div>-->
                    <!--                <div class="d-inline-flex align-items-center">-->
                    <!--                    <h2 class="text-dark mb-1 font-weight-medium"><?= $totalRecords; ?></h2>-->
                    <!--                </div>-->
                    <!--                <h6 class="text-muted font-weight-normal mb-0 w-100 text-truncate">Total TDP Records-->
                    <!--                </h6>-->
                    <!--            </div>-->
                    <!--            <div class="ml-auto mt-md-3 mt-lg-0">-->
                    <!--                <span class="opacity-7 text-muted"><i data-feather="database"></i></span>-->
                    <!--            </div>-->
                    <!--        </div>-->
                    <!--    </div>-->
                    <!--</div>-->
                    <!--<div class="card border-right">-->
                    <!--    <div class="card-body">-->
                    <!--        <div class="d-flex d-lg-flex d-md-block align-items-center">-->
                    <!--            <div>-->
                    <!--                <div class="d-inline-flex align-items-center">-->
                    <!--                    <h2 class="text-dark mb-1 font-weight-medium"><?= $totalRecordsTes; ?></h2>-->
                    <!--                </div>-->
                    <!--                <h6 class="text-muted font-weight-normal mb-0 w-100 text-truncate">Total TDP Records-->
                    <!--                </h6>-->
                    <!--            </div>-->
                    <!--            <div class="ml-auto mt-md-3 mt-lg-0">-->
                    <!--                <span class="opacity-7 text-muted"><i data-feather="database"></i></span>-->
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
                    <!-- Total Courses Chart -->
                    
                    <?php
// Include database connection and session
include 'config/conn.php';

// Ensure session variables are set
$campus = isset($_SESSION['campus']) ? $conn->real_escape_string($_SESSION['campus']) : '';
$file_group = isset($_SESSION['course_program']) ? $conn->real_escape_string($_SESSION['course_program']) : '';
$selected_course = isset($_GET['course_program_enrolled']) ? $conn->real_escape_string($_GET['course_program_enrolled']) : '';

// Start building the query
$courseQuery = "
    SELECT DISTINCT cm.course_program_enrolled 
    FROM ched_masterlist cm
    INNER JOIN registrar_master_list rm
        ON cm.lastname COLLATE utf8mb4_general_ci = rm.last_name COLLATE utf8mb4_general_ci 
        AND cm.firstname COLLATE utf8mb4_general_ci = rm.first_name COLLATE utf8mb4_general_ci
        AND (cm.middlename COLLATE utf8mb4_general_ci = rm.middle_name COLLATE utf8mb4_general_ci 
            OR cm.middlename IS NULL 
            OR rm.middle_name IS NULL 
            OR cm.middlename = '' 
            OR rm.middle_name = '')
    WHERE cm.sheet_name = ? 
    AND rm.file_group = ?
    AND cm.course_program_enrolled IS NOT NULL";

// Add an additional filter if a course is selected
$params = ["ss"];
$bindValues = [$campus, $file_group];

if (!empty($selected_course)) {
    $courseQuery .= " AND cm.course_program_enrolled = ?";
    $params[0] .= "s"; // Add one more string parameter
    $bindValues[] = $selected_course;
}

// Append ORDER BY clause
$courseQuery .= " ORDER BY cm.course_program_enrolled ASC";

// Prepare and execute the statement
$stmt = $conn->prepare($courseQuery);
$stmt->bind_param(...array_merge($params, $bindValues));
$stmt->execute();
$courseResult = $stmt->get_result();

$courses = [];
while ($row = $courseResult->fetch_assoc()) {
    $courses[] = $row['course_program_enrolled'];
}
$stmt->close();


// Fetch students based on selected course
$students = [];
if (!empty($selected_course)) {
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
        AND (cm.middlename COLLATE utf8mb4_general_ci = rm.middle_name COLLATE utf8mb4_general_ci 
            OR cm.middlename IS NULL 
            OR rm.middle_name IS NULL 
            OR cm.middlename = '' 
            OR rm.middle_name = '')
    WHERE cm.sheet_name = ? 
    AND cm.course_program_enrolled = ?
    AND rm.file_group = ?
    ORDER BY cm.lastname ASC";

    $stmt = $conn->prepare($studentQuery);
    $stmt->bind_param("sss", $campus, $selected_course, $file_group);
    $stmt->execute();

    $studentResult = $stmt->get_result();

    while ($row = $studentResult->fetch_assoc()) {
        $students[] = $row;
    }
    $stmt->close();
}

// Close the connection
$conn->close();
?>

<!-- UI for Course Selection and Student Display -->
<div class="col-12">
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="table-responsive">
                    <h5>Displaying Students TDP Course: <strong><?php echo htmlspecialchars($campus); ?></strong></h5>

                    <!-- Dropdown to select course -->
                    <form method="GET" action="" class="col-md-8">
                        <label for="courseFilter">Select Course:</label>
                        <select id="courseFilter" name="course_program_enrolled" class="form-control">
                            <option value="">Select a Course</option>
                            <?php foreach ($courses as $course): ?>
                                <option value="<?php echo htmlspecialchars($course); ?>" 
                                    <?php echo ($selected_course === $course) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($course); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <br>
                        <button type="submit" class="btn btn-success btn-rounded">Apply Filter</button>
                        <br><br>
                    </form>

                    <!-- Table for displaying students -->
                    <table id="zero_config" class="table table-striped table-bordered no-wrap">
                        <thead>
                            <tr>
                                <th>Course Name</th>
                                <th>Last Name</th>
                                <th>First Name</th>
                                <th>Middle Name</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($students)): ?>
                                <?php foreach ($students as $student): ?>
                                    <tr> 
                                        <td><?php echo htmlspecialchars($student['course_program_enrolled']); ?></td>
                                        <td><?php echo htmlspecialchars($student['lastname']); ?></td>
                                        <td><?php echo htmlspecialchars($student['firstname']); ?></td>
                                        <td><?php echo htmlspecialchars($student['middlename'] ?? ''); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr><td colspan="4" class="text-center">No students found for selected course</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>


                    <script>
                        $(document).ready(function () {
                            // Fetch courses from fetch_chart.php and populate dropdown
                            $.ajax({
                                url: 'fetch_courses.php',
                                method: 'GET',
                                dataType: 'json',
                                success: function (response) {
                                    var courseDropdown = $('#courseFilter');
                                    courseDropdown.empty();
                                    courseDropdown.append('<option value="">Select a Course</option>');

                                    response.courses_data.forEach(function (course) {
                                        courseDropdown.append('<option value="' + course.course_program_enrolled + '">' + course.course_program_enrolled + ' (' + course.total_students + ' students)</option>');
                                    });
                                }
                            });

                            // Fetch students when a course is selected
                            $('#courseFilter').on('change', function () {
                                var selectedCourse = $(this).val();

                                if (selectedCourse) {
                                    $.ajax({
                                        url: 'fetch_students.php',
                                        method: 'GET',
                                        data: { course_program_enrolled: selectedCourse },
                                        dataType: 'json',
                                        success: function (response) {
                                            var studentTable = $('#zero_config tbody');
                                            studentTable.empty();

                                            response.students_data.forEach(function (student) {
                                                studentTable.append('<tr><td>' + student.lastname + '</td><td>' + student.firstname + '</td><td>' + student.middlename + '</td></tr>');
                                            });
                                        }
                                    });
                                }
                            });
                        });
                    </script>




                    <!-- Total File Groups Chart -->

                </div>

                <div class="row">
                    <!-- <div class="col-lg-8 col-md-12">
                        <div class="card">
                            <div class="card-body">
                                <h4 class="card-title">Total File Groups</h4>
                                <canvas id="total-file-groups-chart"></canvas>
                            </div>
                        </div>
                    </div> -->
                    <!-- Total Filenames Chart -->
                    <!-- <div class="col-lg-6 col-md-12">
                        <div class="card">
                            <div class="card-body">
                                <h4 class="card-title">Total Courses</h4>
                                <canvas id="total-courses-chart"></canvas>
                            </div>
                        </div>
                    </div> -->
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