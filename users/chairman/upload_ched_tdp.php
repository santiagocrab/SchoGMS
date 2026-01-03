<?php
include 'config/session.php';
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
    <title>Upload CHED TDP Masterlist | SchoGMS</title>
    <!-- Custom CSS -->
    <link href="../../dist/css/style.min.css" rel="stylesheet">
    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
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
                        <h3 class="page-title text-truncate text-dark font-weight-medium mb-1">Upload CHED TDP Masterlist</h3>
                        <!-- End Notification -->
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
                                    class="hide-menu">CHED TDP
                                    Masterlist
                                </span></a>
                        </li>
                        <li class="sidebar-item"> <a class="sidebar-link sidebar-link" href="upload_ched_tdp.php"
                                aria-expanded="false"><i data-feather="upload" class="feather-icon"></i><span
                                    class="hide-menu">Upload CHED TDP
                                    Masterlist
                                </span></a>
                        </li>
                        <li class="sidebar-item"> <a class="sidebar-link" href="ched_masterlist_tes.php"
                                aria-expanded="false"><i data-feather="users" class="feather-icon"></i><span
                                    class="hide-menu">CHED TES
                                    Masterlist
                                </span></a>
                        </li>
                        <li class="sidebar-item"> <a class="sidebar-link" href="program_list.php"
                                aria-expanded="false"><i data-feather="folder" class="feather-icon"></i><span
                                    class="hide-menu">Program List
                                </span></a>
                        </li>
                        <li class="sidebar-item"> <a class="sidebar-link" href="anex-form2.php" aria-expanded="false"><i
                                    data-feather="folder" class="feather-icon"></i><span class="hide-menu">Annex 7 Form 2
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
                                    <li class="breadcrumb-item active">Upload CHED TDP Masterlist</li>
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
            <div class="container-fluid">
                <!-- ============================================================== -->
                <!-- Start Page Content -->
                <!-- ============================================================== -->
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <h4 class="card-title">Upload CHED TDP Masterlist</h4>
                                <h6 class="card-subtitle">Upload Excel file with student data for CHED TDP program</h6>
                                
                                <?php
                                // Display success/error messages
                                if (isset($_GET['success'])) {
                                    echo '<div class="alert alert-success alert-dismissible fade show" role="alert">';
                                    echo '<i class="fa fa-check-circle"></i> ' . htmlspecialchars($_GET['success']);
                                    echo '<button type="button" class="close" data-dismiss="alert" aria-label="Close">';
                                    echo '<span aria-hidden="true">&times;</span>';
                                    echo '</button>';
                                    echo '</div>';
                                }
                                
                                if (isset($_GET['error'])) {
                                    $error_messages = [
                                        'missing_fields' => 'Please fill in all required fields.',
                                        'file_upload_failed' => 'File upload failed. Please try again.',
                                        'invalid_file_type' => 'Invalid file type. Please upload an Excel file (.xlsx or .xls).',
                                        'file_too_large' => 'File size is too large. Maximum size is 10MB.',
                                        'file_move_failed' => 'Failed to save file. Please try again.',
                                        'processing_failed' => 'Failed to process file. Please check the file format.',
                                        'access_denied' => 'Access denied. Only chairman can upload files.'
                                    ];
                                    
                                    $error_message = $error_messages[$_GET['error']] ?? 'An error occurred. Please try again.';
                                    if (isset($_GET['details'])) {
                                        $error_message .= ' Details: ' . htmlspecialchars($_GET['details']);
                                    }
                                    
                                    echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">';
                                    echo '<i class="fa fa-exclamation-circle"></i> ' . $error_message;
                                    echo '<button type="button" class="close" data-dismiss="alert" aria-label="Close">';
                                    echo '<span aria-hidden="true">&times;</span>';
                                    echo '</button>';
                                    echo '</div>';
                                }
                                ?>
                                
                                <!-- Upload Form -->
                                <form action="submit_ched_tdp_upload.php" method="post" enctype="multipart/form-data" id="uploadForm">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="academic_year">Academic Year</label>
                                                <select class="form-control" id="academic_year" name="academic_year" required>
                                                    <option value="">Select Academic Year</option>
                                                    <option value="2026-2027">2026-2027</option>
                                                    <option value="2025-2026">2025-2026</option>
                                                    <option value="2024-2025" selected>2024-2025</option>
                                                    <option value="2023-2024">2023-2024</option>
                                                    <option value="2022-2023">2022-2023</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="semester">Semester</label>
                                                <select class="form-control" id="semester" name="semester" required>
                                                    <option value="">Select Semester</option>
                                                    <option value="1st Semester" selected>1st Semester</option>
                                                    <option value="2nd Semester">2nd Semester</option>
                                                    <option value="Summer">Summer</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="file_group">File Group</label>
                                        <input type="text" class="form-control" id="file_group" name="file_group" 
                                               placeholder="Input file group name (e.g., CHED TDP 2024-2025 1st Sem)" required>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="excel_file">Choose Excel File</label>
                                        <div class="input-group">
                                            <div class="custom-file">
                                                <input type="file" class="custom-file-input" id="excel_file" name="excel_file" 
                                                       accept=".xlsx,.xls" required>
                                                <label class="custom-file-label" for="excel_file">Choose File</label>
                                            </div>
                                        </div>
                                        <small class="form-text text-muted">Supported formats: .xlsx, .xls</small>
                                    </div>
                                    
                                    <div class="form-group">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fa fa-upload"></i> Upload Masterlist
                                        </button>
                                        <button type="reset" class="btn btn-secondary ml-2">
                                            <i class="fa fa-refresh"></i> Reset Form
                                        </button>
                                    </div>
                                </form>
                                
                                <!-- Upload Instructions -->
                                <div class="alert alert-info mt-4">
                                    <h6><i class="fa fa-info-circle"></i> <strong>Upload Instructions:</strong></h6>
                                    <ul class="mb-0">
                                        <li><strong>File Format:</strong> Excel file (.xlsx or .xls)</li>
                                        <li><strong>Required Columns:</strong> Student ID, Last Name, First Name, Middle Name, Course, Year Level, Campus</li>
                                        <li><strong>File Size:</strong> Maximum 10MB</li>
                                        <li><strong>Academic Year:</strong> Select the appropriate academic year</li>
                                        <li><strong>Semester:</strong> Choose 1st Semester, 2nd Semester, or Summer</li>
                                        <li><strong>File Group:</strong> Provide a descriptive name for this batch of data</li>
                                    </ul>
                                </div>
                                
                                <!-- Recent Uploads -->
                                <div class="mt-4">
                                    <h5>Recent Uploads</h5>
                                    <div class="table-responsive">
                                        <table class="table table-striped">
                                            <thead>
                                                <tr>
                                                    <th>File Group</th>
                                                    <th>Academic Year</th>
                                                    <th>Semester</th>
                                                    <th>Upload Date</th>
                                                    <th>Records</th>
                                                    <th>Status</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                // Display recent uploads from MongoDB
                                                try {
                                                    $ched_upload_log = $mongodb->collection('ched_upload_log');
                                                    $uploads = $ched_upload_log->find([], ['sort' => ['upload_date' => -1], 'limit' => 10]);
                                                    
                                                    if (!empty($uploads)) {
                                                        foreach ($uploads as $row) {
                                                            echo "<tr>";
                                                            echo "<td>" . htmlspecialchars($row['file_group'] ?? '') . "</td>";
                                                            echo "<td>" . htmlspecialchars($row['academic_year'] ?? '') . "</td>";
                                                            echo "<td>" . htmlspecialchars($row['semester'] ?? '') . "</td>";
                                                            echo "<td>" . htmlspecialchars(isset($row['upload_date']) ? $row['upload_date']->toDateTime()->format('Y-m-d H:i:s') : '') . "</td>";
                                                            echo "<td>" . htmlspecialchars($row['record_count'] ?? 0) . "</td>";
                                                            echo "<td><span class='badge badge-success'>Completed</span></td>";
                                                            echo "</tr>";
                                                        }
                                                    } else {
                                                        echo "<tr><td colspan='6' class='text-center text-muted'>No uploads found</td></tr>";
                                                    }
                                                } catch (Exception $e) {
                                                    echo "<tr><td colspan='6' class='text-center text-muted'>Error loading uploads: " . htmlspecialchars($e->getMessage()) . "</td></tr>";
                                                }
                                                ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- ============================================================== -->
                <!-- End Page Content -->
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
    <!-- ============================================================== -->
    <!-- All Jquery -->
    <!-- ============================================================== -->
    <script src="../../assets/libs/jquery/dist/jquery.min.js"></script>
    <script src="../../assets/libs/popper.js/dist/umd/popper.min.js"></script>
    <script src="../../assets/libs/bootstrap/dist/js/bootstrap.min.js"></script>
    <!-- apps -->
    <script src="../../dist/js/app-style-switcher.js"></script>
    <script src="../../dist/js/feather.min.js"></script>
    <script src="../../assets/libs/perfect-scrollbar/dist/perfect-scrollbar.jquery.min.js"></script>
    <script src="../../dist/js/sidebarmenu.js"></script>
    <!--Custom JavaScript -->
    <script src="../../dist/js/custom.min.js"></script>
    
    <!-- File input label update -->
    <script>
        document.getElementById('excel_file').addEventListener('change', function(e) {
            var fileName = e.target.files[0].name;
            var label = e.target.nextElementSibling;
            label.textContent = fileName;
        });
        
        // Form validation
        document.getElementById('uploadForm').addEventListener('submit', function(e) {
            var academicYear = document.getElementById('academic_year').value;
            var semester = document.getElementById('semester').value;
            var fileGroup = document.getElementById('file_group').value;
            var file = document.getElementById('excel_file').files[0];
            
            if (!academicYear || !semester || !fileGroup || !file) {
                e.preventDefault();
                alert('Please fill in all required fields and select a file.');
                return false;
            }
            
            // Check file size (10MB limit)
            if (file.size > 10 * 1024 * 1024) {
                e.preventDefault();
                alert('File size must be less than 10MB.');
                return false;
            }
            
            // Check file type
            var allowedTypes = ['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'application/vnd.ms-excel'];
            if (!allowedTypes.includes(file.type)) {
                e.preventDefault();
                alert('Please select a valid Excel file (.xlsx or .xls).');
                return false;
            }
        });
    </script>
</body>

</html>
