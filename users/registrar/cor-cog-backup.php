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
<?php schogms_loading_screen_once(); ?>

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
                        <li class="sidebar-item"> <a class="sidebar-link sidebar-link" href="cor-cog.php"
                                aria-expanded="false"><i data-feather="book-open" class="feather-icon"></i><span
                                    class="hide-menu">COR & COG</span></a></li>
                                    
                        <li class="sidebar-item"> <a class="sidebar-link sidebar-link" href="documents_uploaded.php"
                                aria-expanded="false"><i data-feather="folder" class="feather-icon"></i><span
                                    class="hide-menu">Document uploaded</span></a></li>
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
                                    <label for="session_campus" class="form-label">Campus</label>
                                    <input type="text" class="form-control" id="session_campus" name="session_campus"
                                        value="<?= $sheet_name; ?>" readonly style="background-color: #f8f9fa;">
                                    <small class="form-text text-muted">Automatically set based on your session</small>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="academic_year" class="form-label">Academic Year</label>
                                    <select class="form-control" id="academic_year" name="academic_year" required>
                                        <option value="2026-2027">2026-2027</option>
                                        <option value="2025-2026">2025-2026</option>
                                        <option value="2024-2025">2024-2025</option>
                                        <option value="2023-2024">2023-2024</option>
                                        <option value="2022-2023" selected>2022-2023</option>
                                    </select>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="semester" class="form-label">Semester</label>
                                    <select class="form-control" id="semester" name="semester" required>
                                        <option value="">Select Semester</option>
                                        <option value="1st Semester">1st Semester</option>
                                        <option value="2nd Semester">2nd Semester</option>
                                        <option value="Summer">Summer</option>
                                    </select>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="file-group" class="form-label">File Group</label>
                                    <input type="text" class="form-control" id="file_group" name="file_group"
                                        placeholder="Input file group name" required>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="excelFile" class="form-label">Choose Excel File</label>
                                    <input type="file" class="form-control" id="excelFile" name="excelFile"
                                        accept=".xls,.xlsx, .csv" required>
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
                                        require '../../conn_mongodb.php';
                                        
                                        // Performance timing
                                        $startTime = microtime(true);

                                        // Get the selected category, academic_year, and semester from the form (if any)
                                        $category = isset($_GET['category']) ? $_GET['category'] : '';
                                        $academic_year_filter = isset($_GET['academic_year']) ? $_GET['academic_year'] : '';
                                        $semester_filter = isset($_GET['semester']) ? $_GET['semester'] : '';

                                        // Get distinct categories and file groups efficiently
                                        $categories = [];
                                        $fileGroups = [];
                                        
                                        try {
                                            $registrarCollection = $mongodb->collection('registrar_master_list');
                                            
                                            // Get ALL records to ensure we have all categories
                                            $allRecords = $registrarCollection->find([]);
                                            
                                            foreach ($allRecords as $record) {
                                                if (!empty($record['filename'])) {
                                                    $categories[$record['filename']] = $record['filename'];
                                                }
                                                if (!empty($record['file_group'])) {
                                                    $fileGroups[$record['file_group']] = $record['file_group'];
                                                }
                                            }
                                            
                                            // Sort categories alphabetically
                                            ksort($categories);
                                            ksort($fileGroups);
                                            
                                        } catch (Exception $e) {
                                            echo "<!-- Error loading categories: " . $e->getMessage() . " -->";
                                            $categories = [];
                                            $fileGroups = [];
                                        }

                                        // Build filter for MongoDB query
                                        $filter = [];
                                        if ($category !== '') {
                                            $filter['filename'] = $category;
                                        }
                                        if ($academic_year_filter !== '') {
                                            $filter['academic_year'] = $academic_year_filter;
                                        }
                                        if ($semester_filter !== '') {
                                            $filter['semester'] = $semester_filter;
                                        }
                                        
                                        // Debug: Show current filter
                                        if (!empty($filter)) {
                                            echo "<!-- Debug: Filter applied: " . json_encode($filter) . " -->";
                                        }

                                        // Get registrar masterlist data with pagination for better performance
                                        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
                                        $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 50; // Allow user to select records per page
                                        
                                        // Validate limit to prevent invalid values
                                        $allowedLimits = [10, 25, 50];
                                        if (!in_array($limit, $allowedLimits)) {
                                            $limit = 50;
                                        }
                                        
                                        try {
                                            // Clear cache to ensure fresh data for pagination
                                            $registrarCollection = $mongodb->collection('registrar_master_list');
                                            $registrarCollection->clearCache();
                                            
                                            $result = $dbHelper->getRegistrarMasterlistPaginated($filter, $page, $limit);
                                            $registrarData = $result['data'];
                                            $totalRecords = $result['total'];
                                            $totalPages = $result['pages'];
                                            
                                            // Debug output
                                            echo "<!-- Debug: Page {$page}, Limit {$limit}, Total Records: {$totalRecords}, Total Pages: {$totalPages}, Data Count: " . count($registrarData) . " -->";
                                            echo "<!-- Debug: Filter: " . json_encode($filter) . " -->";
                                            if (!empty($registrarData)) {
                                                $firstRecord = $registrarData[0];
                                                echo "<!-- Debug: First record ID: " . ($firstRecord['_id'] ?? 'no_id') . ", Name: " . ($firstRecord['last_name'] ?? 'no_name') . " -->";
                                            }
                                        } catch (Exception $e) {
                                            echo "<!-- Error: " . $e->getMessage() . " -->";
                                            $registrarData = [];
                                            $totalRecords = 0;
                                            $totalPages = 0;
                                        }
                                        ?>

                                        <div class="table-responsive">
                                            <form method="GET" action="" class="col-md-8">
                                                <label for="categoryFilter">Select Category:</label>
                                                <select id="categoryFilter" name="category" class="form-control">
                                                    <option value="">All</option>
                                                    <?php foreach ($categories as $cat): ?>
                                                        <option value="<?php echo htmlspecialchars($cat); ?>"
                                                            <?php echo ($category == $cat) ? 'selected' : ''; ?>>
                                                            <?php echo htmlspecialchars($cat); ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>

                                                <label for="academicYearFilter" class="mt-2">Academic Year:</label>
                                                <select id="academicYearFilter" name="academic_year" class="form-control">
                                                    <option value="">All</option>
                                                    <option value="2026-2027" <?php echo ($academic_year_filter == '2026-2027') ? 'selected' : ''; ?>>2026-2027</option>
                                                    <option value="2025-2026" <?php echo ($academic_year_filter == '2025-2026') ? 'selected' : ''; ?>>2025-2026</option>
                                                    <option value="2024-2025" <?php echo ($academic_year_filter == '2024-2025') ? 'selected' : ''; ?>>2024-2025</option>
                                                    <option value="2023-2024" <?php echo ($academic_year_filter == '2023-2024') ? 'selected' : ''; ?>>2023-2024</option>
                                                    <option value="2022-2023" <?php echo ($academic_year_filter == '2022-2023') ? 'selected' : ''; ?>>2022-2023</option>
                                                </select>
                                                
                                                <label for="semesterFilter" class="mt-2">Semester:</label>
                                                <select id="semesterFilter" name="semester" class="form-control">
                                                    <option value="">All</option>
                                                    <option value="1st Semester" <?php echo ($semester_filter == '1st Semester') ? 'selected' : ''; ?>>1st Semester</option>
                                                    <option value="2nd Semester" <?php echo ($semester_filter == '2nd Semester') ? 'selected' : ''; ?>>2nd Semester</option>
                                                    <option value="Summer" <?php echo ($semester_filter == 'Summer') ? 'selected' : ''; ?>>Summer</option>
                                                </select>
                                                
                                                <label for="limitFilter" class="mt-2">Records per page:</label>
                                                <select id="limitFilter" name="limit" class="form-control">
                                                    <option value="10" <?php echo ($limit == 10) ? 'selected' : ''; ?>>10 records</option>
                                                    <option value="25" <?php echo ($limit == 25) ? 'selected' : ''; ?>>25 records</option>
                                                    <option value="50" <?php echo ($limit == 50) ? 'selected' : ''; ?>>50 records</option>
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
                                                        <th>LASTNAME</th>
                                                        <th>FIRSTNAME</th>
                                                        <th>Ext. Name</th>
                                                        <th>MIDDLENAME</th>
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
                                                    <?php foreach ($registrarData as $row): ?>
                                                        <tr>
                                                            <td hidden><?php echo htmlspecialchars($row['filename'] ?? ''); ?>
                                                            </td>
                                                            <td>
                                                                <?php
                                                                // Check for COR/COG documents
                                                                $documentCollection = $mongodb->collection('document_uploads');
                                                                $studentName = trim($row['last_name'] . ', ' . $row['first_name'] . ' ' . ($row['middle_name'] ?? ''));
                                                                
                                                                $documents = $documentCollection->find([
                                                                    'file_name' => ['$regex' => preg_quote($studentName), '$options' => 'i']
                                                                ]);
                                                                
                                                                $hasCOR = false;
                                                                $hasCOG = false;
                                                                
                                                                foreach ($documents as $doc) {
                                                                    if (isset($doc['category'])) {
                                                                        if (strpos($doc['category'], 'COR') !== false) {
                                                                            $hasCOR = true;
                                                                        }
                                                                        if (strpos($doc['category'], 'COG') !== false) {
                                                                            $hasCOG = true;
                                                                        }
                                                                    }
                                                                }

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
                                                            <td><?php echo htmlspecialchars($row['last_name'] ?? ''); ?></td>
                                                            <td><?php echo htmlspecialchars($row['first_name'] ?? ''); ?></td>
                                                            <td><?php echo htmlspecialchars($row['ext_name'] ?? ''); ?></td>
                                                            <td><?php echo htmlspecialchars($row['middle_name'] ?? ''); ?></td>
                                                            <td><?php echo htmlspecialchars($row['id_number'] ?? ''); ?></td>
                                                            <td><?php echo htmlspecialchars($row['gender'] ?? ''); ?></td>
                                                            <td><?php echo htmlspecialchars($row['student_type'] ?? ''); ?>
                                                            </td>
                                                            <td><?php echo htmlspecialchars($row['year_level'] ?? ''); ?></td>
                                                            <td><?php echo htmlspecialchars($row['attended'] ?? ''); ?></td>
                                                            <td><?php echo htmlspecialchars($row['course'] ?? ''); ?></td>
                                                            <td><?php echo htmlspecialchars($row['curriculum'] ?? ''); ?></td>
                                                            <td><?php echo htmlspecialchars($row['scholarship'] ?? ''); ?>
                                                            </td>
                                                            <td><?php echo htmlspecialchars($row['gpa'] ?? ''); ?></td>
                                                            <td><?php echo htmlspecialchars($row['cgpa'] ?? ''); ?></td>
                                                            <td><?php echo htmlspecialchars($row['pass_percentage'] ?? ''); ?>
                                                            </td>
                                                            <td><?php echo htmlspecialchars($row['grade_remarks'] ?? ''); ?>
                                                            </td>
                                                            <td><?php echo htmlspecialchars($row['enrolled'] ?? ''); ?></td>
                                                            <td><?php echo htmlspecialchars($row['lec_unit'] ?? ''); ?></td>
                                                            <td><?php echo htmlspecialchars($row['lab_unit'] ?? ''); ?></td>
                                                            <td><?php echo htmlspecialchars($row['cor_printed'] ?? ''); ?>
                                                            </td>
                                                            <td><?php echo htmlspecialchars($row['billing_profile'] ?? ''); ?>
                                                            </td>
                                                            <td><?php echo htmlspecialchars($row['misc_fee_total'] ?? ''); ?>
                                                            </td>
                                                            <td><?php echo htmlspecialchars($row['misc_fee_paid'] ?? ''); ?>
                                                            </td>
                                                            <td><?php echo htmlspecialchars($row['tuition_fee_total'] ?? ''); ?>
                                                            </td>
                                                            <td><?php echo htmlspecialchars($row['tuition_fee_paid'] ?? ''); ?>
                                                            </td>
                                                            <td><?php echo htmlspecialchars($row['street'] ?? ''); ?></td>
                                                            <td><?php echo htmlspecialchars($row['barangay'] ?? ''); ?></td>
                                                            <td><?php echo htmlspecialchars($row['municipality_city'] ?? ''); ?>
                                                            </td>
                                                            <td><?php echo htmlspecialchars($row['province'] ?? ''); ?></td>
                                                            <td><?php echo htmlspecialchars($row['zip_code'] ?? ''); ?></td>
                                                            <td><?php echo htmlspecialchars($row['date_of_birth'] ?? ''); ?>
                                                            </td>
                                                            <td><?php echo htmlspecialchars($row['place_of_birth'] ?? ''); ?>
                                                            </td>
                                                            <td><?php echo htmlspecialchars($row['civil_status'] ?? ''); ?>
                                                            </td>
                                                            <td><?php echo htmlspecialchars($row['tribe'] ?? ''); ?></td>
                                                            <td><?php echo htmlspecialchars($row['religion'] ?? ''); ?></td>
                                                            <td><?php echo htmlspecialchars($row['year_admitted'] ?? ''); ?>
                                                            </td>
                                                            <td><?php echo htmlspecialchars($row['semester_admitted'] ?? ''); ?>
                                                            </td>
                                                            <td><?php echo htmlspecialchars($row['school_last_attended'] ?? ''); ?>
                                                            </td>
                                                            <td><?php echo htmlspecialchars($row['year_last_attended'] ?? ''); ?>
                                                            </td>
                                                            <td><?php echo htmlspecialchars($row['semester_last_attended'] ?? ''); ?>
                                                            </td>
                                                            <td><?php echo htmlspecialchars($row['high_school_graduated'] ?? ''); ?>
                                                            </td>
                                                            <td><?php echo htmlspecialchars($row['exam_date'] ?? ''); ?></td>
                                                            <td><?php echo htmlspecialchars($row['exam_rating'] ?? ''); ?>
                                                            </td>
                                                            <td><?php echo htmlspecialchars($row['ref_number'] ?? ''); ?></td>
                                                            <td><?php echo htmlspecialchars($row['guardian'] ?? ''); ?></td>
                                                            <td><?php echo htmlspecialchars($row['guardian_address'] ?? ''); ?>
                                                            </td>
                                                            <td><?php echo htmlspecialchars($row['guardian_contact'] ?? ''); ?>
                                                            </td>
                                                            <td><?php echo htmlspecialchars($row['blood_type'] ?? ''); ?></td>
                                                            <td><?php echo htmlspecialchars($row['email_address'] ?? ''); ?>
                                                            </td>
                                                            <td><?php echo htmlspecialchars($row['mobile_number'] ?? ''); ?>
                                                            </td>
                                                            <td><?php echo htmlspecialchars($row['deped_number'] ?? ''); ?>
                                                            </td>
                                                            <td><?php echo htmlspecialchars($row['scholarship_grant'] ?? ''); ?>
                                                            </td>
                                                            <td><?php echo htmlspecialchars($row['scholarship_allowance'] ?? ''); ?>
                                                            </td>
                                                            <td><?php echo htmlspecialchars($row['documents_submitted'] ?? ''); ?>
                                                            </td>
                                                            <td><?php echo htmlspecialchars($row['lacking_documents'] ?? ''); ?>
                                                            </td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                </tbody>
                                            </table>

                                            <?php
                                            // Display pagination info
                                            echo "<div class='row mt-3'>";
                                            echo "<div class='col-md-6'>";
                                            $endTime = microtime(true);
                                            $loadTime = round(($endTime - $startTime) * 1000, 2);
                                            // Calculate the correct range for display
                                            $startRecord = (($page - 1) * $limit) + 1;
                                            $endRecord = min($page * $limit, $totalRecords);
                                            $actualCount = count($registrarData);
                                            
                                            if ($totalRecords > 0) {
                                                echo "<p>Showing {$startRecord} to {$endRecord} of {$totalRecords} records</p>";
                                            } else {
                                                echo "<p>No records found</p>";
                                            }
                                            echo "<small class='text-success'><i class='fa fa-bolt'></i> Loaded in {$loadTime}ms (Ultra-Fast MongoDB)</small>";
                                            echo "</div>";
                                            echo "<div class='col-md-6'>";
                                            
                                            // Pagination controls
                                            if ($totalPages > 1) {
                                                echo "<nav aria-label='Page navigation'>";
                                                echo "<ul class='pagination justify-content-end'>";
                                                
                                                // Previous button
                                                if ($page > 1) {
                                                    $prevPage = $page - 1;
                                                    $prevUrl = "?page={$prevPage}&limit={$limit}";
                                                    if ($category) $prevUrl .= "&category=" . urlencode($category);
                                                    if ($academic_year_filter) $prevUrl .= "&academic_year=" . urlencode($academic_year_filter);
                                                    if ($semester_filter) $prevUrl .= "&semester=" . urlencode($semester_filter);
                                                    echo "<li class='page-item'><a class='page-link' href='{$prevUrl}'>Previous</a></li>";
                                                }
                                                
                                                // Page numbers
                                                $startPage = max(1, $page - 2);
                                                $endPage = min($totalPages, $page + 2);
                                                
                                                for ($i = $startPage; $i <= $endPage; $i++) {
                                                    $pageUrl = "?page={$i}&limit={$limit}";
                                                    if ($category) $pageUrl .= "&category=" . urlencode($category);
                                                    if ($academic_year_filter) $pageUrl .= "&academic_year=" . urlencode($academic_year_filter);
                                                    if ($semester_filter) $pageUrl .= "&semester=" . urlencode($semester_filter);
                                                    
                                                    $activeClass = ($i == $page) ? 'active' : '';
                                                    echo "<li class='page-item {$activeClass}'><a class='page-link' href='{$pageUrl}'>{$i}</a></li>";
                                                }
                                                
                                                // Next button
                                                if ($page < $totalPages) {
                                                    $nextPage = $page + 1;
                                                    $nextUrl = "?page={$nextPage}&limit={$limit}";
                                                    if ($category) $nextUrl .= "&category=" . urlencode($category);
                                                    if ($academic_year_filter) $nextUrl .= "&academic_year=" . urlencode($academic_year_filter);
                                                    if ($semester_filter) $nextUrl .= "&semester=" . urlencode($semester_filter);
                                                    echo "<li class='page-item'><a class='page-link' href='{$nextUrl}'>Next</a></li>";
                                                }
                                                
                                                echo "</ul>";
                                                echo "</nav>";
                                            }
                                            
                                            echo "</div>";
                                            echo "</div>";
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
                // Toast notification function
                function showToast(message, type = 'info') {
                    Toastify({
                        text: message,
                        duration: 3000,
                        gravity: "top",
                        position: "right",
                        backgroundColor: type === 'error' ? "#dc3545" : type === 'success' ? "#28a745" : "#17a2b8",
                        stopOnFocus: true
                    }).showToast();
                }

                document.getElementById('uploadForm').addEventListener('submit', function (event) {
                    event.preventDefault();

                    const CampuSession = document.getElementById('session_campus');
                    const fileGroupInput = document.getElementById('file_group');
                    const academicYearInput = document.getElementById('academic_year');
                    const semesterInput = document.getElementById('semester');
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

                    const academicYear = academicYearInput.value.trim();
                    if (!academicYear) {
                        showToast("Please select an academic year!", "error");
                        return;
                    }

                    const semester = semesterInput.value.trim();
                    if (!semester) {
                        showToast("Please select a semester!", "error");
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
                            formData.append('session_campus', CampuSession.value.trim());
                            formData.append('file_group', fileGroup);
                            formData.append('academic_year', academicYear);
                            formData.append('semester', semester);
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
                                        let message = data.message || "File processed successfully!";
                                        if (data.stats) {
                                            message += `\n\nStatistics:\n`;
                                            message += `• Records inserted: ${data.stats.inserted}\n`;
                                            message += `• Duplicates skipped: ${data.stats.duplicates}\n`;
                                            message += `• Errors: ${data.stats.errors}`;
                                        }
                                        
                                        Swal.fire({
                                            title: "Success!",
                                            text: message,
                                            icon: "success",
                                            timer: 5000
                                        });
                                        setTimeout(() => {
                                            location.reload();
                                        }, 2000);
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
            <!-- Disable DataTables pagination since we're using custom pagination -->
            <script>
                $(document).ready(function() {
                    $('#zero_config').DataTable({
                        "paging": false,
                        "searching": false,
                        "ordering": false,
                        "info": false,
                        "lengthChange": false
                    });
                    
                    // Reset to page 1 when limit changes
                    $('#limitFilter').on('change', function() {
                        var form = $(this).closest('form');
                        var pageInput = $('<input>').attr({
                            type: 'hidden',
                            name: 'page',
                            value: '1'
                        });
                        form.append(pageInput);
                        form.submit();
                    });
                });
            </script>


</body>

</html>