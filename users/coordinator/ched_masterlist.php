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
                </nav>
                </nav>
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
                        <div class="customize-input float-right" style="margin-left:10px;">
                            <button type="button" class="btn waves-effect waves-light btn-rounded btn-success"
                                onclick="showValidation('validate.php')">
                                Validate TDP
                            </button>
                        </div>
                        <!-- <div class="customize-input float-right">
                            <button type="button" class="btn waves-effect waves-light btn-rounded btn-success"
                                onclick="showValidation('validated2.php')">
                                Validate TES
                            </button>
                        </div> -->
                    </div>

                    <!-- SweetAlert + Delay Before Redirect -->
                    <script>
                        function showValidation(url) {
                            Swal.fire({
                                title: "Validating...",
                                text: "Please wait while we validate the data.",
                                icon: "info",
                                allowOutsideClick: false,
                                showConfirmButton: false,
                                timer: Math.floor(Math.random() * (10000 - 5000 + 1)) + 5000, // Random 5-10 sec delay
                                willOpen: () => {
                                    Swal.showLoading();
                                }
                            }).then(() => {
                                window.location.href = url; // Redirect to the correct page
                            });
                        }
                    </script>

                    <!-- Include SweetAlert2 (If not already included) -->
                    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


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
                                    <?php
                                    // session_start();
                                    require '../config/conn.php';
                                    // $sheet_name = $sheet_name;
                                    
                                    // if (empty($sheet_name)) {
                                    //     die("No campus assigned to this session.");
                                    // }
                                    
                                    // Query to join ched_masterlist with document_uploads for COR & COG validation
                                    $query = "
                                        SELECT 
                                        cm.*, 
                                        rm.id_number, rm.enrolled, rm.zip_code, rm.email_address, rm.mobile_number,

                                        -- Get categories (COR & COG) uploaded for each student
                                        GROUP_CONCAT(DISTINCT du.category ORDER BY du.category SEPARATOR ', ') AS uploaded_categories,
                                        GROUP_CONCAT(DISTINCT du.file_name ORDER BY du.file_name SEPARATOR ', ') AS uploaded_files,

                                        -- Determine enrollment status: Enrolled if both COR & COG exist
                                        CASE 
                                            WHEN SUM(CASE WHEN du.category = 'COR' THEN 1 ELSE 0 END) > 0 
                                            AND SUM(CASE WHEN du.category = 'COG' THEN 1 ELSE 0 END) > 0 
                                            THEN 'Enrolled'
                                            ELSE 'Not Enrolled'
                                        END AS enrollment_status

                                    FROM ched_masterlist cm

                                    -- LEFT JOIN with registrar_master_list to get enrolled status and other details
                                    LEFT JOIN registrar_master_list rm
                                        ON cm.lastname COLLATE utf8mb4_general_ci = rm.last_name COLLATE utf8mb4_general_ci 
                                        AND cm.firstname COLLATE utf8mb4_general_ci = rm.first_name COLLATE utf8mb4_general_ci
                                        AND cm.middlename COLLATE utf8mb4_general_ci = rm.middle_name COLLATE utf8mb4_general_ci

                                    -- LEFT JOIN with document_uploads based on student name AND campus restriction
                                    LEFT JOIN document_uploads du 
                                        ON du.file_name LIKE CONCAT(cm.lastname, ', ', cm.firstname, ' ', cm.middlename, '%')
                                        AND du.campus = '" . $conn->real_escape_string($sheet_name) . "'

                                    WHERE cm.sheet_name = '" . $conn->real_escape_string($sheet_name) . "'

                                    GROUP BY cm.id, cm.lastname, cm.firstname, cm.middlename, rm.id_number, rm.enrolled, rm.zip_code, rm.email_address, rm.mobile_number

                                    ORDER BY cm.sheet_name ASC, cm.id ASC;


                                    "; // Preserve CHED masterlist order
                                    
                                    $result = $conn->query($query);

                                    if (!$result) {
                                        die("Query failed: " . $conn->error);
                                    }
                                    ?>
                                    <div class="table-responsive">
                                        <h5>Displaying records for:
                                            <strong><?php echo htmlspecialchars($sheet_name); ?></strong>
                                        </h5>

                                        <!-- Hidden Form to Open in a New Window -->
                                        <form method="GET" action="validated_masterlist.php" id="filterForm"
                                            onsubmit="return openNewWindow(event)" hidden>
                                            <input type="hidden" name="sheet_name"
                                                value="<?php echo htmlspecialchars($sheet_name); ?>">
                                            <button type="submit" class="btn btn-success btn-rounded">Open in New
                                                Window</button>
                                        </form>
                                        <script>
                                            function openNewWindow(event) {
                                                event.preventDefault(); // Stop normal form submission

                                                // Get the form and its data
                                                var form = document.getElementById('filterForm');
                                                var formData = new FormData(form);
                                                var queryString = new URLSearchParams(formData).toString();

                                                // Construct new window URL with query string
                                                var newWindowUrl = form.action + '?' + queryString;

                                                // Open a new window with custom size and options
                                                window.open(newWindowUrl, '_blank', 'width=1200,height=800,scrollbars=yes');

                                                return false; // Prevent default form submission
                                            }
                                        </script>

                                        <br>

                                        <table id="zero_config" class="table table-striped table-bordered no-wrap">
                                            <thead>
                                                <tr>
                                                    <th>SEQ</th>
                                                    <th>APP NO</th>
                                                    <th>AWARD NO</th>
                                                    <th>LASTNAME</th>
                                                    <th>FIRSTNAME</th>
                                                    <th>MIDDLENAME</th>
                                                    <th>SEX</th>
                                                    <th>BIRTHDATE</th>
                                                    <th>COURSE</th>
                                                    <th>YEAR LEVEL</th>
                                                    <th>UNITS ENROLLED</th>
                                                    <th>COR/COG</th>
                                                    <th>STATUS</th>
                                                    <th>REMARKS</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php 
                                                // Include MongoDB connection for document lookups
                                                require '../../conn_mongodb.php';
                                                $documentCollection = $mongodb->collection('document_uploads');
                                                
                                                while ($row = $result->fetch_assoc()): 
                                                    // Build student name for matching
                                                    $studentName = trim($row['lastname'] . ', ' . $row['firstname'] . ' ' . ($row['middlename'] ?? ''));
                                                    
                                                    // Query MongoDB for COR and COG documents for this student
                                                    $corDoc = null;
                                                    $cogDoc = null;
                                                    
                                                    // Build student name variations for matching
                                                    $lastName = trim($row['lastname'] ?? '');
                                                    $firstName = trim($row['firstname'] ?? '');
                                                    $middleName = trim($row['middlename'] ?? '');
                                                    
                                                    // Create multiple name patterns for flexible matching
                                                    $namePattern1 = $lastName . ', ' . $firstName; // "Lastname, Firstname"
                                                    $namePattern2 = $lastName . ', ' . $firstName . ' ' . $middleName; // "Lastname, Firstname Middlename"
                                                    
                                                    // Escape special regex characters but allow case-insensitive matching
                                                    $pattern1 = preg_quote($namePattern1, '/');
                                                    $pattern2 = preg_quote($namePattern2, '/');
                                                    
                                                    // Find COR document - simplified search
                                                    // Try multiple approaches to find the document
                                                    
                                                    // Approach 1: Search by original_name with campus (account for .pdf extension)
                                                    $corQuery1 = [
                                                        'original_name' => ['$regex' => '^' . $pattern1 . '(\.pdf)?$', '$options' => 'i'],
                                                        'category' => 'COR'
                                                    ];
                                                    if (!empty($sheet_name)) {
                                                        $corQuery1['campus'] = ['$regex' => '^' . preg_quote($sheet_name, '/') . '$', '$options' => 'i'];
                                                    }
                                                    
                                                    $corDocs = $documentCollection->find($corQuery1, ['limit' => 1]);
                                                    foreach ($corDocs as $doc) {
                                                        $corDoc = $doc;
                                                        break;
                                                    }
                                                    
                                                    // Approach 2: If not found, try without campus restriction
                                                    if (!$corDoc) {
                                                        $corQuery2 = [
                                                            'original_name' => ['$regex' => '^' . $pattern1 . '(\.pdf)?$', '$options' => 'i'],
                                                            'category' => 'COR'
                                                        ];
                                                        $corDocs2 = $documentCollection->find($corQuery2, ['limit' => 1]);
                                                        foreach ($corDocs2 as $doc) {
                                                            $corDoc = $doc;
                                                            break;
                                                        }
                                                    }
                                                    
                                                    // Approach 3: Try with pattern2 (with middle name)
                                                    if (!$corDoc && !empty($pattern2)) {
                                                        $corQuery3 = [
                                                            'original_name' => ['$regex' => '^' . $pattern2 . '(\.pdf)?$', '$options' => 'i'],
                                                            'category' => 'COR'
                                                        ];
                                                        if (!empty($sheet_name)) {
                                                            $corQuery3['campus'] = ['$regex' => '^' . preg_quote($sheet_name, '/') . '$', '$options' => 'i'];
                                                        }
                                                        $corDocs3 = $documentCollection->find($corQuery3, ['limit' => 1]);
                                                        foreach ($corDocs3 as $doc) {
                                                            $corDoc = $doc;
                                                            break;
                                                        }
                                                    }
                                                    
                                                    // Approach 4: Try without anchor (contains match)
                                                    if (!$corDoc) {
                                                        $corQuery4 = [
                                                            'original_name' => ['$regex' => $pattern1, '$options' => 'i'],
                                                            'category' => 'COR'
                                                        ];
                                                        if (!empty($sheet_name)) {
                                                            $corQuery4['campus'] = ['$regex' => '^' . preg_quote($sheet_name, '/') . '$', '$options' => 'i'];
                                                        }
                                                        $corDocs4 = $documentCollection->find($corQuery4, ['limit' => 1]);
                                                        foreach ($corDocs4 as $doc) {
                                                            $corDoc = $doc;
                                                            break;
                                                        }
                                                    }
                                                    
                                                    // Approach 5: Try searching in file_name as last resort
                                                    if (!$corDoc) {
                                                        $corQuery5 = [
                                                            'file_name' => ['$regex' => $pattern1, '$options' => 'i'],
                                                            'category' => 'COR'
                                                        ];
                                                        if (!empty($sheet_name)) {
                                                            $corQuery5['campus'] = ['$regex' => '^' . preg_quote($sheet_name, '/') . '$', '$options' => 'i'];
                                                        }
                                                        $corDocs5 = $documentCollection->find($corQuery5, ['limit' => 1]);
                                                        foreach ($corDocs5 as $doc) {
                                                            $corDoc = $doc;
                                                            break;
                                                        }
                                                    }
                                                    
                                                    // Find COG document - simplified search
                                                    // Try multiple approaches to find the document
                                                    
                                                    // Approach 1: Search by original_name with campus (account for .pdf extension)
                                                    $cogQuery1 = [
                                                        'original_name' => ['$regex' => '^' . $pattern1 . '(\.pdf)?$', '$options' => 'i'],
                                                        'category' => 'COG'
                                                    ];
                                                    if (!empty($sheet_name)) {
                                                        $cogQuery1['campus'] = ['$regex' => '^' . preg_quote($sheet_name, '/') . '$', '$options' => 'i'];
                                                    }
                                                    
                                                    $cogDocs = $documentCollection->find($cogQuery1, ['limit' => 1]);
                                                    foreach ($cogDocs as $doc) {
                                                        $cogDoc = $doc;
                                                        break;
                                                    }
                                                    
                                                    // Approach 2: If not found, try without campus restriction
                                                    if (!$cogDoc) {
                                                        $cogQuery2 = [
                                                            'original_name' => ['$regex' => '^' . $pattern1 . '(\.pdf)?$', '$options' => 'i'],
                                                            'category' => 'COG'
                                                        ];
                                                        $cogDocs2 = $documentCollection->find($cogQuery2, ['limit' => 1]);
                                                        foreach ($cogDocs2 as $doc) {
                                                            $cogDoc = $doc;
                                                            break;
                                                        }
                                                    }
                                                    
                                                    // Approach 3: Try with pattern2 (with middle name)
                                                    if (!$cogDoc && !empty($pattern2)) {
                                                        $cogQuery3 = [
                                                            'original_name' => ['$regex' => '^' . $pattern2 . '(\.pdf)?$', '$options' => 'i'],
                                                            'category' => 'COG'
                                                        ];
                                                        if (!empty($sheet_name)) {
                                                            $cogQuery3['campus'] = ['$regex' => '^' . preg_quote($sheet_name, '/') . '$', '$options' => 'i'];
                                                        }
                                                        $cogDocs3 = $documentCollection->find($cogQuery3, ['limit' => 1]);
                                                        foreach ($cogDocs3 as $doc) {
                                                            $cogDoc = $doc;
                                                            break;
                                                        }
                                                    }
                                                    
                                                    // Approach 4: Try without anchor (contains match)
                                                    if (!$cogDoc) {
                                                        $cogQuery4 = [
                                                            'original_name' => ['$regex' => $pattern1, '$options' => 'i'],
                                                            'category' => 'COG'
                                                        ];
                                                        if (!empty($sheet_name)) {
                                                            $cogQuery4['campus'] = ['$regex' => '^' . preg_quote($sheet_name, '/') . '$', '$options' => 'i'];
                                                        }
                                                        $cogDocs4 = $documentCollection->find($cogQuery4, ['limit' => 1]);
                                                        foreach ($cogDocs4 as $doc) {
                                                            $cogDoc = $doc;
                                                            break;
                                                        }
                                                    }
                                                    
                                                    // Approach 5: Try searching in file_name as last resort
                                                    if (!$cogDoc) {
                                                        $cogQuery5 = [
                                                            'file_name' => ['$regex' => $pattern1, '$options' => 'i'],
                                                            'category' => 'COG'
                                                        ];
                                                        if (!empty($sheet_name)) {
                                                            $cogQuery5['campus'] = ['$regex' => '^' . preg_quote($sheet_name, '/') . '$', '$options' => 'i'];
                                                        }
                                                        $cogDocs5 = $documentCollection->find($cogQuery5, ['limit' => 1]);
                                                        foreach ($cogDocs5 as $doc) {
                                                            $cogDoc = $doc;
                                                            break;
                                                        }
                                                    }
                                                ?>
                                                    <tr>
                                                        <td><?php echo htmlspecialchars($row['seq']); ?></td>
                                                        <td><?php echo htmlspecialchars($row['app_no']); ?></td>
                                                        <td><?php echo htmlspecialchars($row['award_no']); ?></td>
                                                        <td><?php echo htmlspecialchars($row['lastname']); ?></td>
                                                        <td><?php echo htmlspecialchars($row['firstname']); ?></td>
                                                        <td><?php echo $row['middlename']; ?></td>
                                                        <td><?php echo htmlspecialchars($row['sex']); ?></td>
                                                        <td><?php echo htmlspecialchars($row['birthdate']); ?></td>
                                                        <td><?php echo htmlspecialchars($row['course_program_enrolled']); ?>
                                                        </td>
                                                        <td><?php echo htmlspecialchars($row['year_level']); ?></td>
                                                        <td><?php // echo htmlspecialchars($row['enrolled']); ?>
                                                        </td>
                                                        
                                                        <!-- COR & COG Viewing -->
                                                        <td>
                                                            <div class="btn-group" role="group">
                                                                <?php 
                                                                // Check if COR document exists in MongoDB - show button if found
                                                                if ($corDoc && isset($corDoc['file_path'])): 
                                                                    // Use document viewer script to serve the file
                                                                    $docId = $corDoc['id'] ?? null;
                                                                    $filePath = $corDoc['file_path'];
                                                                    // Encode the path for URL
                                                                    $encodedPath = base64_encode($filePath);
                                                                    $corPath = '../../view_document.php?path=' . urlencode($encodedPath);
                                                                    if ($docId) {
                                                                        $corPath = '../../view_document.php?id=' . urlencode($docId);
                                                                    }
                                                                ?>
                                                                    <a href="<?php echo htmlspecialchars($corPath); ?>" 
                                                                       target="_blank" 
                                                                       class="btn btn-sm btn-success" 
                                                                       title="View COR - <?php echo htmlspecialchars($corDoc['original_name'] ?? ''); ?>">
                                                                        COR
                                                                    </a>
                                                                <?php else: ?>
                                                                    <span class="badge badge-secondary">No COR</span>
                                                                <?php endif; ?>
                                                                
                                                                <?php 
                                                                // Check if COG document exists in MongoDB - show button if found
                                                                if ($cogDoc && isset($cogDoc['file_path'])): 
                                                                    // Use document viewer script to serve the file
                                                                    $docId = $cogDoc['id'] ?? null;
                                                                    $filePath = $cogDoc['file_path'];
                                                                    // Encode the path for URL
                                                                    $encodedPath = base64_encode($filePath);
                                                                    $cogPath = '../../view_document.php?path=' . urlencode($encodedPath);
                                                                    if ($docId) {
                                                                        $cogPath = '../../view_document.php?id=' . urlencode($docId);
                                                                    }
                                                                ?>
                                                                    <a href="<?php echo htmlspecialchars($cogPath); ?>" 
                                                                       target="_blank" 
                                                                       class="btn btn-sm btn-primary" 
                                                                       title="View COG - <?php echo htmlspecialchars($cogDoc['original_name'] ?? ''); ?>">
                                                                        COG
                                                                    </a>
                                                                <?php else: ?>
                                                                    <span class="badge badge-secondary">No COG</span>
                                                                <?php endif; ?>
                                                            </div>
                                                        </td>

                                                        <!-- Status -->
                                                        <td>
                                                            <?php 
                                                            // Check if documents actually exist (not just if variables are set)
                                                            $hasCOR = ($corDoc !== null && isset($corDoc['file_path']));
                                                            $hasCOG = ($cogDoc !== null && isset($cogDoc['file_path']));
                                                            // Student is enrolled if they have COR (COR is required for enrollment)
                                                            if ($hasCOR): ?>
                                                                <span class="badge badge-success">Enrolled</span>
                                                            <?php else: ?>
                                                                <span class="badge badge-warning">Not Enrolled</span>
                                                            <?php endif; ?>
                                                        </td>
                                                        
                                                        <td><?php echo htmlspecialchars($row['remarks'] ?? ''); ?></td>
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