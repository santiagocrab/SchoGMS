<?php 
include 'config/session.php'; 

// Handle incoming COR/COG view requests from masterlist
$viewCor = isset($_GET['view_cor']) ? $_GET['view_cor'] : '';
$viewCog = isset($_GET['view_cog']) ? $_GET['view_cog'] : '';
$studentName = isset($_GET['student_name']) ? urldecode($_GET['student_name']) : '';
$studentId = isset($_GET['student_id']) ? urldecode($_GET['student_id']) : '';
$filePath = isset($_GET['file']) ? urldecode($_GET['file']) : '';

// If viewing a specific COR/COG document, redirect to view_document.php
if (($viewCor || $viewCog) && !empty($filePath)) {
    $type = $viewCor ? 'COR' : 'COG';
    header('Location: view_document.php?file=' . urlencode($filePath) . '&type=' . $type);
    exit;
}
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
                        <h3 class="page-title text-truncate text-dark font-weight-medium mb-1">COR & COG Documents
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
                            <a href="upload_standalone.php" class="btn waves-effect waves-light btn-rounded btn-success">
                                Upload COR Documents
                            </a>
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
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <h4 class="card-title">COR & COG Document Upload</h4>
                                <h6 class="card-subtitle">Upload COR and COG documents</h6>
                                
                                <?php if (!empty($studentName)): ?>
                                <div class="alert alert-info">
                                    <i class="fa fa-info-circle"></i> <strong>Viewing COR/COG for:</strong> <?= htmlspecialchars($studentName) ?> 
                                    <?php if (!empty($studentId)): ?>
                                        (ID: <?= htmlspecialchars($studentId) ?>)
                                    <?php endif; ?>
                                    <br><small>Accessed from Masterlist - <a href="masterlist.php" class="text-primary">← Back to Masterlist</a></small>
                                </div>
                                <?php endif; ?>
                                
                                <!-- Upload System Notice -->
                                <div class="alert alert-info">
                                    <strong>ℹ️ INFO:</strong> For uploading ALL your COR documents at once, use the <strong>UPLOAD ALL FILES AT ONCE</strong> button on the right side. 
                                    The form below is for small uploads only.
                                </div>
                                
                                <!-- Direct Upload Form -->
                                <div class="row mt-3">
                                    <div class="col-md-8">
                                        <form action="submit_document_mongodb.php" method="post" enctype="multipart/form-data">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="session_campus">Campus</label>
                                                        <input type="text" class="form-control" id="session_campus" name="session_campus" value="<?= htmlspecialchars($sheet_name ?: 'ISULAN'); ?>" readonly style="background-color: #f8f9fa;">
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="category">Category</label>
                                                        <input type="text" class="form-control" id="category" name="category" value="COR" readonly style="background-color: #f8f9fa;">
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="academic_year">Academic Year</label>
                                                        <select class="form-control" id="academic_year" name="academic_year" required>
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
                                                            <option value="1st Semester" selected>1st Semester</option>
                                                            <option value="2nd Semester">2nd Semester</option>
                                                            <option value="Summer">Summer</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="form-group">
                                                <label for="fileUpload">Upload COR Documents</label>
                                                <input type="file" class="form-control" id="fileUpload" name="fileUpload[]" multiple accept=".pdf,.jpg,.jpeg,.png,.rar,.zip" required>
                                                <small class="form-text text-muted">Select multiple PDF, RAR, ZIP, JPG, or PNG files</small>
                                            </div>
                                            
                                            <button type="submit" class="btn btn-primary">
                                                <i class="fa fa-upload"></i> Upload Documents
                                        </button>
                                        </form>
                                    </div>
                                    
                                    <div class="col-md-4">
                                        <div class="card bg-light">
                                            <div class="card-body">
                                                <h6>Working Upload System</h6>
                                                <div class="alert alert-success">
                                                    <strong>✅ WORKING:</strong> Upload ALL Files At Once
                                                    <br><small>Unlimited files - Upload everything in one go!</small>
                                                    <br><small><strong>Supported:</strong> PDF, RAR, ZIP, Images</small>
                                                </div>
                                                <a href="upload_all_cor.php" class="btn btn-primary btn-block mb-2">
                                                    <i class="fa fa-upload"></i> UPLOAD ALL 3,000+ COR FILES AT ONCE
                                                </a>
                                                <a href="clean_duplicates.php" class="btn btn-warning btn-block mb-2">
                                                    <i class="fa fa-trash"></i> Clean Duplicates
                                                </a>
                                                <a href="delete_all_cor.php" class="btn btn-danger btn-block mb-2" onclick="return confirm('⚠️ WARNING: This will delete ALL COR documents from database and file system. Are you sure?')">
                                                    <i class="fa fa-trash"></i> Delete ALL COR Documents
                                                </a>
                                                <a href="show_all_cor_names.php" class="btn btn-info btn-block mb-2">
                                                    <i class="fa fa-file-text"></i> Show All COR Names
                                                </a>
                                                <a href="masterlist.php" class="btn btn-secondary btn-block">
                                                    <i class="fa fa-list"></i> Registrar Masterlist
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Document Filter Section -->
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <h4 class="card-title">Filter Uploaded Documents</h4>
                                <h6 class="card-subtitle">Search and filter COR/COG documents</h6>
                                
                                <form method="GET" action="" class="row mt-3">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="filterCategory">Category</label>
                                            <select class="form-control" id="filterCategory" name="category">
                                                <option value="">All Categories</option>
                                                <option value="COR" <?= (isset($_GET['category']) && $_GET['category'] == 'COR') ? 'selected' : '' ?>>COR</option>
                                                <option value="COG" <?= (isset($_GET['category']) && $_GET['category'] == 'COG') ? 'selected' : '' ?>>COG</option>
                                            </select>
                                        </div>
                            </div>
                                    
                                    <div class="col-md-3">
                                    <div class="form-group">
                                            <label for="filterAcademicYear">Academic Year</label>
                                            <select class="form-control" id="filterAcademicYear" name="academic_year">
                                                <option value="">All Years</option>
                                                <option value="2026-2027" <?= (isset($_GET['academic_year']) && $_GET['academic_year'] == '2026-2027') ? 'selected' : '' ?>>2026-2027</option>
                                                <option value="2025-2026" <?= (isset($_GET['academic_year']) && $_GET['academic_year'] == '2025-2026') ? 'selected' : '' ?>>2025-2026</option>
                                                <option value="2024-2025" <?= (isset($_GET['academic_year']) && $_GET['academic_year'] == '2024-2025') ? 'selected' : '' ?>>2024-2025</option>
                                                <option value="2023-2024" <?= (isset($_GET['academic_year']) && $_GET['academic_year'] == '2023-2024') ? 'selected' : '' ?>>2023-2024</option>
                                                <option value="2022-2023" <?= (isset($_GET['academic_year']) && $_GET['academic_year'] == '2022-2023') ? 'selected' : '' ?>>2022-2023</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                    <div class="form-group">
                                            <label for="filterSemester">Semester</label>
                                            <select class="form-control" id="filterSemester" name="semester">
                                                <option value="">All Semesters</option>
                                                <option value="1st Semester" <?= (isset($_GET['semester']) && $_GET['semester'] == '1st Semester') ? 'selected' : '' ?>>1st Semester</option>
                                                <option value="2nd Semester" <?= (isset($_GET['semester']) && $_GET['semester'] == '2nd Semester') ? 'selected' : '' ?>>2nd Semester</option>
                                                <option value="Summer" <?= (isset($_GET['semester']) && $_GET['semester'] == 'Summer') ? 'selected' : '' ?>>Summer</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                    <div class="form-group">
                                            <label for="filterCampus">Campus</label>
                                            <select class="form-control" id="filterCampus" name="campus">
                                                <option value="">All Campuses</option>
                                                <option value="ISULAN" <?= (isset($_GET['campus']) && $_GET['campus'] == 'ISULAN') ? 'selected' : '' ?>>ISULAN</option>
                                                <option value="TACURONG" <?= (isset($_GET['campus']) && $_GET['campus'] == 'TACURONG') ? 'selected' : '' ?>>TACURONG</option>
                                                <option value="KALAMANSIG" <?= (isset($_GET['campus']) && $_GET['campus'] == 'KALAMANSIG') ? 'selected' : '' ?>>KALAMANSIG</option>
                                                <option value="PALIMBANG" <?= (isset($_GET['campus']) && $_GET['campus'] == 'PALIMBANG') ? 'selected' : '' ?>>PALIMBANG</option>
                                        </select>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                    <div class="form-group">
                                            <label for="filterLastName">Last Name</label>
                                            <input type="text" class="form-control" id="filterLastName" name="lastname" 
                                                   placeholder="Enter last name" value="<?= htmlspecialchars($_GET['lastname'] ?? '') ?>">
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                    <div class="form-group">
                                            <label for="filterFirstName">First Name</label>
                                            <input type="text" class="form-control" id="filterFirstName" name="firstname" 
                                                   placeholder="Enter first name" value="<?= htmlspecialchars($_GET['firstname'] ?? '') ?>">
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fa fa-search"></i> Apply Filter
                                        </button>
                                        <a href="cor-cog.php" class="btn btn-secondary">
                                            <i class="fa fa-refresh"></i> Clear Filter
                                        </a>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Filtered Results Table -->
                <div class="row mt-3">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="row mb-3">
                                    <div class="col-md-4">
                                        <h4 class="card-title">Filtered Documents</h4>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="input-group">
                                            <input type="text" class="form-control" id="searchInput" placeholder="Search documents..." value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
                                            <div class="input-group-append">
                                                <button class="btn btn-outline-secondary" type="button" id="searchBtn">
                                                    <i class="fa fa-search"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="d-flex align-items-center">
                                            <label for="perPageSelect" class="mr-2 mb-0">Show:</label>
                                            <select class="form-control form-control-sm" id="perPageSelect" style="width: 80px;">
                                                <option value="10" <?= ($perPage == 10) ? 'selected' : '' ?>>10</option>
                                                <option value="20" <?= ($perPage == 20) ? 'selected' : '' ?>>20</option>
                                                <option value="50" <?= ($perPage == 50) ? 'selected' : '' ?>>50</option>
                                            </select>
                                            <span class="ml-2 text-muted">per page</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="alert alert-info mb-3">
                                    <i class="fa fa-sort-alpha-asc"></i> <strong>Documents are sorted alphabetically by filename (A to Z)</strong>
                                </div>
                                
                                <!-- Record Count Display -->
                                <div class="alert alert-light mb-3">
                                    <span id="recordCount">
                                        <strong>Total Documents:</strong> <?= $totalDocuments ?> | 
                                        <strong>Showing:</strong> <?= $displayCount ?> of <?= $totalDocuments ?>
                                        <?php if ($filterCategory || $filterAcademicYear || $filterSemester || $filterCampus || $filterLastName || $filterFirstName): ?>
                                        (filtered results)
                                        <?php endif; ?>
                                    </span>
                                </div>
                                
                                <!-- Search Help -->
                                <div class="alert alert-info mb-3">
                                    <h6><i class="fa fa-search"></i> <strong>Search Tips:</strong></h6>
                                    <ul class="mb-0">
                                        <li><strong>Search by name:</strong> Try "ABACARO", "ROSE", "ANN", "ABAD"</li>
                                        <li><strong>Search by academic year:</strong> Try "2024-2025", "2023-2024"</li>
                                        <li><strong>Search by semester:</strong> Try "1st Semester", "2nd Semester"</li>
                                        <li><strong>Search by category:</strong> Try "COR", "COG"</li>
                                        <li><strong>Character encoding:</strong> Works with both "?" and "Ñ" characters</li>
                                        <li><strong>Partial names:</strong> Search for "ABA" to find "ABACARO"</li>
                                    </ul>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-striped table-hover" id="documentsTable">
                                        <thead class="table-dark">
                                            <tr>
                                                <th>Category</th>
                                                <th>File Name <small class="text-muted">(A-Z)</small></th>
                                                <th>Academic Year</th>
                                                <th>Semester</th>
                                                <th>Campus</th>
                                                <th>Uploaded By</th>
                                                <th>Upload Date</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            try {
                                                // Get filter parameters
                                                $filterCategory = $_GET['category'] ?? '';
                                                $filterAcademicYear = $_GET['academic_year'] ?? '';
                                                $filterSemester = $_GET['semester'] ?? '';
                                                $filterCampus = $_GET['campus'] ?? '';
                                                $filterLastName = $_GET['lastname'] ?? '';
                                                $filterFirstName = $_GET['firstname'] ?? '';
                                                $searchTerm = $_GET['search'] ?? '';
                                                
                                                // Build MongoDB filter
                                                $filter = [];
                                                if ($filterCategory) $filter['category'] = $filterCategory;
                                                if ($filterAcademicYear) $filter['academic_year'] = $filterAcademicYear;
                                                if ($filterSemester) $filter['semester'] = $filterSemester;
                                                if ($filterCampus) $filter['campus'] = $filterCampus;
                                                
                                                // Get MongoDB collection
                                                $documentCollection = $mongodb->collection('document_uploads');
                                                
                                                // Pagination parameters
                                                $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
                                                $perPage = isset($_GET['per_page']) ? (int)$_GET['per_page'] : 20; // Default 20, user can choose 10, 20, 50
                                                $offset = ($page - 1) * $perPage;
                                                
                                                // Get total count for pagination
                                                $totalDocuments = $documentCollection->count($filter);
                                                
                                                // If no documents in database, count files in filesystem
                                                if ($totalDocuments == 0) {
                                                    $totalDocuments = 0;
                                                    $directories = [
                                                        'uploads/COR/',
                                                        'uploads/COG/',
                                                        'uploads/documents/ISULAN/2024-2025/1st Semester/COR/',
                                                        'uploads/documents/ISULAN/2024-2025/2nd Semester/COR/',
                                                        'uploads/documents/ISULAN/2023-2024/1st Semester/COR/',
                                                        'uploads/documents/ISULAN/2023-2024/2nd Semester/COR/'
                                                    ];
                                                    
                                                    foreach ($directories as $dir) {
                                                        if (is_dir($dir)) {
                                                            $files = scandir($dir);
                                                            foreach ($files as $file) {
                                                                if ($file != '.' && $file != '..' && in_array(strtolower(pathinfo($file, PATHINFO_EXTENSION)), ['pdf', 'rar', 'zip'])) {
                                                                    $totalDocuments++;
                                                                }
                                                            }
                                                        }
                                                    }
                                                }
                                                
                                                $totalPages = ceil($totalDocuments / $perPage);
                                                
                                                // Get documents with filter and pagination - SORTED ALPHABETICALLY BY FILENAME
                                                $documents = $documentCollection->find($filter, [
                                                    'sort' => ['original_name' => 1], // 1 = ascending (A to Z)
                                                    'limit' => $perPage,
                                                    'skip' => $offset
                                                ]);
                                                
                                                // If no documents in database, get files from filesystem
                                                if ($documentCollection->count($filter) == 0) {
                                                    $documents = [];
                                                    $directories = [
                                                        'uploads/COR/',
                                                        'uploads/COG/',
                                                        'uploads/documents/ISULAN/2024-2025/1st Semester/COR/',
                                                        'uploads/documents/ISULAN/2024-2025/2nd Semester/COR/',
                                                        'uploads/documents/ISULAN/2023-2024/1st Semester/COR/',
                                                        'uploads/documents/ISULAN/2023-2024/2nd Semester/COR/'
                                                    ];
                                                    
                                                    $allFiles = [];
                                                    foreach ($directories as $dir) {
                                                        if (is_dir($dir)) {
                                                            $files = scandir($dir);
                                                            foreach ($files as $file) {
                                                                if ($file != '.' && $file != '..' && in_array(strtolower(pathinfo($file, PATHINFO_EXTENSION)), ['pdf', 'rar', 'zip'])) {
                                                                    $filePath = $dir . $file;
                                                                    $fileSize = file_exists($filePath) ? filesize($filePath) : 0;
                                                                    
                                                                    // Determine category from path
                                                                    $category = 'COR';
                                                                    if (strpos($dir, 'COG') !== false) {
                                                                        $category = 'COG';
                                                                    }
                                                                    
                                                                    // Determine academic year and semester from path
                                                                    $academicYear = '2024-2025';
                                                                    $semester = '1st Semester';
                                                                    if (strpos($dir, '2023-2024') !== false) {
                                                                        $academicYear = '2023-2024';
                                                                    }
                                                                    if (strpos($dir, '2nd Semester') !== false) {
                                                                        $semester = '2nd Semester';
                                                                    }
                                                                    
                                                                    // Determine file type
                                                                    $fileType = 'application/pdf';
                                                                    if ($fileExtension === 'rar') {
                                                                        $fileType = 'application/x-rar-compressed';
                                                                    } elseif ($fileExtension === 'zip') {
                                                                        $fileType = 'application/zip';
                                                                    }
                                                                    
                                                                    $allFiles[] = [
                                                                        'original_name' => $file,
                                                                        'file_path' => $filePath,
                                                                        'file_size' => $fileSize,
                                                                        'file_type' => $fileType,
                                                                        'category' => $category,
                                                                        'academic_year' => $academicYear,
                                                                        'semester' => $semester,
                                                                        'campus' => 'ISULAN',
                                                                        'uploaded_by' => 'registrar isulan',
                                                                        'uploaded_at' => date('Y-m-d H:i:s', filemtime($filePath))
                                                                    ];
                                                                }
                                                            }
                                                        }
                                                    }
                                                    
                                                    // Sort alphabetically by filename (A to Z)
                                                    usort($allFiles, function($a, $b) {
                                                        return strcmp(strtolower($a['original_name']), strtolower($b['original_name']));
                                                    });
                                                    
                                                    // Apply pagination
                                                    $documents = array_slice($allFiles, $offset, $perPage);
                                                }
                                                
                                                $displayCount = 0;
                                                
                                                foreach ($documents as $doc) {
                                                    // Apply comprehensive search term filter
                                                    if ($searchTerm) {
                                                        $originalName = $doc['original_name'] ?? '';
                                                        $fixedName = str_replace('?', 'Ñ', $originalName);
                                                        
                                                        // Create comprehensive searchable text with multiple variations
                                                        $searchableText = strtolower(implode(' ', [
                                                            $fixedName,                    // Fixed name with Ñ
                                                            $originalName,                 // Original name with ?
                                                            $doc['academic_year'] ?? '',   // Academic year
                                                            $doc['semester'] ?? '',        // Semester
                                                            $doc['campus'] ?? '',          // Campus
                                                            $doc['uploaded_by'] ?? '',     // Uploaded by
                                                            $doc['category'] ?? '',        // Category (COR/COG)
                                                            $doc['file_name'] ?? '',       // File name
                                                            $doc['file_path'] ?? ''        // File path
                                                        ]));
                                                        
                                                        // Also search in individual name parts
                                                        $nameParts = explode(', ', $originalName);
                                                        $lastName = $nameParts[0] ?? '';
                                                        $firstName = $nameParts[1] ?? '';
                                                        $middleName = $nameParts[2] ?? '';
                                                        
                                                        $searchableText .= ' ' . strtolower(implode(' ', [
                                                            $lastName,
                                                            $firstName,
                                                            $middleName,
                                                            str_replace('?', 'Ñ', $lastName),
                                                            str_replace('?', 'Ñ', $firstName),
                                                            str_replace('?', 'Ñ', $middleName)
                                                        ]));
                                                        
                                                        $searchTermLower = strtolower($searchTerm);
                                                        
                                                        // Enhanced search logic with multiple variations
                                                        $searchVariations = [
                                                            $searchTermLower,
                                                            str_replace('?', 'ñ', $searchTermLower),
                                                            str_replace('ñ', '?', $searchTermLower),
                                                            str_replace(['ñ', 'Ñ'], ['n', 'N'], $searchTermLower),
                                                            preg_replace('/[^a-z0-9]/', '', $searchTermLower) // Remove special characters
                                                        ];
                                                        
                                                        $found = false;
                                                        foreach ($searchVariations as $variation) {
                                                            if (stripos($searchableText, $variation) !== false) {
                                                                $found = true;
                                                                break;
                                                            }
                                                        }
                                                        
                                                        if (!$found) continue;
                                                    }
                                                    
                                                    // Apply name filters if specified
                                                    if ($filterLastName || $filterFirstName) {
                                                        $originalName = $doc['original_name'] ?? '';
                                                        $nameParts = explode(', ', $originalName);
                                                        $lastName = $nameParts[0] ?? '';
                                                        $firstName = $nameParts[1] ?? '';
                                                        
                                                        // Fix character encoding for name filters
                                                        $fixedLastName = str_replace('?', 'Ñ', $lastName);
                                                        $fixedFirstName = str_replace('?', 'Ñ', $firstName);
                                                        
                                                        if ($filterLastName && stripos($fixedLastName, $filterLastName) === false && stripos($lastName, $filterLastName) === false) continue;
                                                        if ($filterFirstName && stripos($fixedFirstName, $filterFirstName) === false && stripos($firstName, $filterFirstName) === false) continue;
                                                    }
                                                    
                                                    $displayCount++;
                                                    
                                                    echo "<tr>";
                                                    echo "<td><span class='badge badge-" . ($doc['category'] == 'COR' ? 'success' : 'primary') . "'>" . htmlspecialchars($doc['category'] ?? 'Unknown') . "</span></td>";
                                                    // Fix character encoding for display
                                                    $displayName = str_replace('?', 'Ñ', $doc['original_name'] ?? 'Unknown');
                                                    echo "<td>" . htmlspecialchars($displayName) . "</td>";
                                                    echo "<td>" . htmlspecialchars($doc['academic_year'] ?? 'Unknown') . "</td>";
                                                    echo "<td>" . htmlspecialchars($doc['semester'] ?? 'Unknown') . "</td>";
                                                    echo "<td>" . htmlspecialchars($doc['campus'] ?? 'Unknown') . "</td>";
                                                    echo "<td>" . htmlspecialchars($doc['uploaded_by'] ?? 'Unknown') . "</td>";
                                                    echo "<td>" . htmlspecialchars($doc['uploaded_at'] ?? 'Unknown') . "</td>";
                                                    echo "<td>";
                                                    if (isset($doc['file_path']) && file_exists($doc['file_path'])) {
                                                        echo "<a href='" . htmlspecialchars($doc['file_path']) . "' target='_blank' class='btn btn-sm btn-primary'>View</a>";
                                                    } else {
                                                        echo "<span class='text-muted'>File missing</span>";
                                                    }
                                                    echo "</td>";
                                                    echo "</tr>";
                                                }
                                                
                                                if ($displayCount == 0) {
                                                    echo "<tr><td colspan='8' class='text-center text-muted'>No documents found matching the filter criteria</td></tr>";
                                                }
                                                
                                            } catch (Exception $e) {
                                                echo "<tr><td colspan='8' class='text-center text-danger'>Error loading documents: " . htmlspecialchars($e->getMessage()) . "</td></tr>";
                                            }
                                            ?>
                                        </tbody>
                                    </table>
                                </div>
                                
                                <!-- Document Count and Pagination -->
                                <div class="mt-3">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <small class="text-muted">
                                                <strong>Total Documents:</strong> <?= $totalDocuments ?> 
                                                <?php 
                                                $corCount = $documentCollection->count(array_merge($filter, ['category' => 'COR']));
                                                if ($corCount == 0) {
                                                    // Count COR files from filesystem
                                                    $corCount = 0;
                                                    $corDirs = ['uploads/COR/', 'uploads/documents/ISULAN/2024-2025/1st Semester/COR/', 'uploads/documents/ISULAN/2024-2025/2nd Semester/COR/', 'uploads/documents/ISULAN/2023-2024/1st Semester/COR/', 'uploads/documents/ISULAN/2023-2024/2nd Semester/COR/'];
                                                    foreach ($corDirs as $dir) {
                                                        if (is_dir($dir)) {
                                                            $files = scandir($dir);
                                                            foreach ($files as $file) {
                                                                if ($file != '.' && $file != '..' && in_array(strtolower(pathinfo($file, PATHINFO_EXTENSION)), ['pdf', 'rar', 'zip'])) {
                                                                    $corCount++;
                                                                }
                                                            }
                                                        }
                                                    }
                                                }
                                                ?>
                                                | <strong>COR Documents:</strong> <?= $corCount ?>
                                                | <strong>Showing:</strong> <?= $displayCount ?> of <?= $totalDocuments ?>
                                                <?php if ($filterCategory || $filterAcademicYear || $filterSemester || $filterCampus || $filterLastName || $filterFirstName): ?>
                                                (filtered results)
                                                <?php endif; ?>
                                            </small>
                                        </div>
                                        <div class="col-md-6 text-right">
                                            <?php if ($totalPages > 1): ?>
                                                <nav aria-label="Document pagination">
                                                    <ul class="pagination pagination-sm justify-content-end">
                                                        <!-- Previous Button -->
                                                        <?php if ($page > 1): ?>
                                                            <li class="page-item">
                                                                <a class="page-link" href="?<?= http_build_query(array_merge($_GET, ['page' => $page - 1])) ?>">
                                                                    <i class="fa fa-chevron-left"></i> Previous
                                                                </a>
                                                            </li>
                                                        <?php endif; ?>
                                                        
                                                        <!-- Page Numbers -->
                                                        <?php 
                                                        $startPage = max(1, $page - 2);
                                                        $endPage = min($totalPages, $page + 2);
                                                        
                                                        if ($startPage > 1): ?>
                                                            <li class="page-item">
                                                                <a class="page-link" href="?<?= http_build_query(array_merge($_GET, ['page' => 1])) ?>">1</a>
                                                            </li>
                                                            <?php if ($startPage > 2): ?>
                                                                <li class="page-item disabled"><span class="page-link">...</span></li>
                                                            <?php endif; ?>
                                                        <?php endif; ?>
                                                        
                                                        <?php for ($i = $startPage; $i <= $endPage; $i++): ?>
                                                            <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                                                                <a class="page-link" href="?<?= http_build_query(array_merge($_GET, ['page' => $i])) ?>"><?= $i ?></a>
                                                            </li>
                                                        <?php endfor; ?>
                                                        
                                                        <?php if ($endPage < $totalPages): ?>
                                                            <?php if ($endPage < $totalPages - 1): ?>
                                                                <li class="page-item disabled"><span class="page-link">...</span></li>
                                                            <?php endif; ?>
                                                            <li class="page-item">
                                                                <a class="page-link" href="?<?= http_build_query(array_merge($_GET, ['page' => $totalPages])) ?>"><?= $totalPages ?></a>
                                                            </li>
                                                        <?php endif; ?>
                                                        
                                                        <!-- Next Button -->
                                                        <?php if ($page < $totalPages): ?>
                                                            <li class="page-item">
                                                                <a class="page-link" href="?<?= http_build_query(array_merge($_GET, ['page' => $page + 1])) ?>">
                                                                    Next <i class="fa fa-chevron-right"></i>
                                                                </a>
                                                            </li>
                                                        <?php endif; ?>
                                                    </ul>
                                                </nav>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>


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
        
        <!-- Search functionality -->
        <script>
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('searchInput');
            const searchBtn = document.getElementById('searchBtn');
            const perPageSelect = document.getElementById('perPageSelect');
            let searchTimeout;
            
            // Real-time search as you type (with 200ms delay for better responsiveness)
            searchInput.addEventListener('input', function() {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(function() {
                    performSearch();
                }, 200); // Wait 200ms after user stops typing
            });
            
            // Search on button click
            searchBtn.addEventListener('click', function() {
                clearTimeout(searchTimeout);
                performSearch();
            });
            
            // Search on Enter key
            searchInput.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    clearTimeout(searchTimeout);
                    performSearch();
                }
            });
            
            // Per page selector change
            perPageSelect.addEventListener('change', function() {
                performSearch();
            });
            
            function performSearch() {
                const searchTerm = searchInput.value.toLowerCase().trim();
                const table = document.getElementById('documentsTable');
                const rows = table.querySelectorAll('tbody tr');
                let visibleCount = 0;
                
                rows.forEach(function(row) {
                    const text = row.textContent.toLowerCase();
                    
                    // Enhanced character encoding handling for search
                    // Convert ? to Ñ for search matching
                    const normalizedText = text.replace(/\?/g, 'ñ');
                    const normalizedSearchTerm = searchTerm.replace(/\?/g, 'ñ');
                    
                    // Also handle reverse - convert Ñ to ? for matching
                    const reverseText = text.replace(/ñ/g, '?');
                    const reverseSearchTerm = searchTerm.replace(/ñ/g, '?');
                    
                    // Handle uppercase/lowercase variations
                    const upperText = text.toUpperCase();
                    const upperSearchTerm = searchTerm.toUpperCase();
                    
                    // Check multiple variations for better search results
                    const searchVariations = [
                        searchTerm,
                        normalizedSearchTerm,
                        reverseSearchTerm,
                        upperSearchTerm,
                        searchTerm.replace(/[^a-z0-9]/g, ''), // Remove special characters
                        searchTerm.replace(/\s+/g, ' '), // Normalize spaces
                        searchTerm.replace(/[ñÑ]/g, 'n'), // Convert ñ to n
                        searchTerm.replace(/[ñÑ]/g, 'N')  // Convert ñ to N
                    ];
                    
                    let found = false;
                    for (let variation of searchVariations) {
                        if (text.indexOf(variation) !== -1 || 
                            normalizedText.indexOf(variation) !== -1 || 
                            reverseText.indexOf(variation) !== -1 ||
                            upperText.indexOf(variation.toUpperCase()) !== -1) {
                            found = true;
                            break;
                        }
                    }
                    
                    if (!found) {
                        row.style.display = 'none';
                    } else {
                        row.style.display = '';
                        visibleCount++;
                    }
                });
                
                // Update the record count display if it exists
                const recordCountElement = document.getElementById('recordCount');
                if (recordCountElement) {
                    const totalRecords = <?= $totalDocuments ?>;
                    if (searchTerm === '') {
                        recordCountElement.textContent = 'Total Documents: ' + totalRecords + ' | Showing: ' + visibleCount + ' records';
                    } else {
                        recordCountElement.textContent = 'Total Documents: ' + totalRecords + ' | Showing: ' + visibleCount + ' records matching "' + searchTerm + '"';
                    }
                }
            }
        });
        </script>
</body>

</html>

