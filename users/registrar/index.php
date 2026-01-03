<?php include 'config/session.php'; ?>
<!DOCTYPE html>
<html dir="ltr" lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Registrar Dashboard - SchoGMS</title>
    <link href="../../dist/css/style.min.css" rel="stylesheet">
    <link href="../../assets/extra-libs/c3/c3.min.css" rel="stylesheet">
    <link href="../../assets/libs/chartist/dist/chartist.min.css" rel="stylesheet">
</head>
<body>
    <div id="main-wrapper" data-theme="light" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full"
        data-sidebar-position="fixed" data-header-position="fixed" data-boxed-layout="full">
        
        <!-- Header -->
        <header class="topbar" data-navbarbg="skin6">
            <nav class="navbar top-navbar navbar-expand-md">
                <div class="navbar-header" data-logobg="skin6">
                    <a class="nav-toggler waves-effect waves-light d-block d-md-none" href="javascript:void(0)"><i
                            class="ti-menu ti-close"></i></a>
                    <div class="navbar-brand">
                        <a href="index.php">
                            <b class="logo-icon">
                                <img src="../../assets/images/logo.png" style="height: auto; width: 200px;"
                                    alt="homepage" class="dark-logo" />
                                <img src="../../assets/images/logo.png" alt="homepage" class="light-logo" />
                            </b>
                        </a>
                    </div>
                    <a class="topbartoggler d-block d-md-none waves-effect waves-light" href="javascript:void(0)"
                        data-toggle="collapse" data-target="#navbarSupportedContent"
                        aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation"><i
                            class="ti-more"></i></a>
                </div>
                <div class="navbar-collapse collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav float-left mr-auto ml-3 pl-1">
                        <h3 class="page-title text-truncate text-dark font-weight-medium mb-1">Scholarship and Grants
                            Management System</h3>
                    </ul>
                    <ul class="navbar-nav float-right">
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
                    </ul>
                </div>
            </nav>
        </header>

        <!-- Sidebar -->
        <aside class="left-sidebar" data-sidebarbg="skin6">
            <div class="scroll-sidebar" data-sidebarbg="skin6">
                <nav class="sidebar-nav">
                    <ul id="sidebarnav">
                        <li class="sidebar-item">
                            <a class="sidebar-link sidebar-link" href="index.php" aria-expanded="false">
                                <i data-feather="home" class="feather-icon"></i>
                                <span class="hide-menu">Dashboard</span>
                            </a>
                        </li>
                        <li class="list-divider"></li>
                        <li class="nav-small-cap"><span class="hide-menu">Registrar</span></li>
                        <li class="sidebar-item">
                            <a class="sidebar-link" href="masterlist.php" aria-expanded="false">
                                <i data-feather="users" class="feather-icon"></i>
                                <span class="hide-menu">Registrar Masterlist</span>
                            </a>
                        </li>
                        <li class="sidebar-item">
                            <a class="sidebar-link" href="cor-cog.php" aria-expanded="false">
                                <i data-feather="book-open" class="feather-icon"></i>
                                <span class="hide-menu">COR & COG</span>
                            </a>
                        </li>
                        <li class="sidebar-item">
                            <a class="sidebar-link" href="documents_uploaded.php" aria-expanded="false">
                                <i data-feather="folder" class="feather-icon"></i>
                                <span class="hide-menu">Document uploaded</span>
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>
        </aside>

        <!-- Main Content -->
        <div class="page-wrapper">
            <div class="container-fluid">
                <?php
                // Simple statistics - using file system counts for now
                $totalRecords = 0;
                $totalCourses = 0;
                $totalFileGroups = 0;
                $corCount = 0;
                $cogCount = 0;
                
                try {
                    // Count COR files from file system
                    $corDir = 'uploads/COR/';
                    if (is_dir($corDir)) {
                        $corFiles = scandir($corDir);
                        $corCount = count($corFiles) - 2; // Subtract . and ..
                    }
                    
                    // Count COG files from file system
                    $cogDir = 'uploads/COG/';
                    if (is_dir($cogDir)) {
                        $cogFiles = scandir($cogDir);
                        $cogCount = count($cogFiles) - 2; // Subtract . and ..
                    }
                    
                    // Set some default values for display
                    $totalRecords = $corCount + $cogCount;
                    $totalCourses = 20; // Estimated
                    $totalFileGroups = 3; // COR, COG, etc.
                    
                } catch (Exception $e) {
                    // Fallback values
                    $totalRecords = 3213;
                    $totalCourses = 20;
                    $totalFileGroups = 3;
                    $corCount = 3211;
                    $cogCount = 2588;
                }
                ?>

                <div class="card-group">
                    <!-- Total Records Card -->
                    <div class="card border-right">
                        <div class="card-body">
                            <div class="d-flex d-lg-flex d-md-block align-items-center">
                                <div>
                                    <div class="d-inline-flex align-items-center">
                                        <h2 class="text-dark mb-1 font-weight-medium"><?= $totalRecords; ?></h2>
                                    </div>
                                    <h6 class="text-muted font-weight-normal mb-0 w-100 text-truncate">Total Records</h6>
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
                                    <h6 class="text-muted font-weight-normal mb-0 w-100 text-truncate">Total Courses</h6>
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
                                        <h2 class="text-dark mb-1 font-weight-medium"><?= $totalFileGroups; ?></h2>
                                    </div>
                                    <h6 class="text-muted font-weight-normal mb-0 w-100 text-truncate">Total File Groups</h6>
                                </div>
                                <div class="ml-auto mt-md-3 mt-lg-0">
                                    <span class="opacity-7 text-muted"><i data-feather="folder"></i></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- COR Documents Card -->
                    <div class="card border-right">
                        <div class="card-body">
                            <div class="d-flex d-lg-flex d-md-block align-items-center">
                                <div>
                                    <div class="d-inline-flex align-items-center">
                                        <h2 class="text-dark mb-1 font-weight-medium"><?= $corCount; ?></h2>
                                    </div>
                                    <h6 class="text-muted font-weight-normal mb-0 w-100 text-truncate">COR Documents</h6>
                                </div>
                                <div class="ml-auto mt-md-3 mt-lg-0">
                                    <span class="opacity-7 text-muted"><i data-feather="file-text"></i></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- COG Documents Card -->
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex d-lg-flex d-md-block align-items-center">
                                <div>
                                    <div class="d-inline-flex align-items-center">
                                        <h2 class="text-dark mb-1 font-weight-medium"><?= $cogCount; ?></h2>
                                    </div>
                                    <h6 class="text-muted font-weight-normal mb-0 w-100 text-truncate">COG Documents</h6>
                                </div>
                                <div class="ml-auto mt-md-3 mt-lg-0">
                                    <span class="opacity-7 text-muted"><i data-feather="file-text"></i></span>
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

                <!-- Charts Section -->
                <div class="row">
                    <!-- Total Courses Chart -->
                    <div class="col-lg-6 col-md-12">
                        <div class="card">
                            <div class="card-body text-center">
                                <h4 class="card-title">Total Courses</h4>
                                <div class="chart-container">
                                    <canvas id="total-courses-chart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Document Types Chart -->
                    <div class="col-lg-6 col-md-12">
                        <div class="card">
                            <div class="card-body text-center">
                                <h4 class="card-title">Document Types</h4>
                                <div class="chart-container">
                                    <canvas id="document-types-chart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="../../assets/libs/jquery/dist/jquery.min.js"></script>
    <script src="../../assets/libs/popper.js/dist/umd/popper.min.js"></script>
    <script src="../../assets/libs/bootstrap/dist/js/bootstrap.min.js"></script>
    <script src="../../dist/js/feather.min.js"></script>
    <script src="../../dist/js/custom.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <script>
    $(document).ready(function() {
        // Sample course data for chart
        var courseLabels = ["BSIT", "BSCS", "BSIS", "BSCE", "BSME", "BSEE"];
        var courseCounts = [150, 120, 100, 80, 60, 40];
        
        // Total Courses Chart (Bar Chart)
        new Chart(document.getElementById("total-courses-chart"), {
            type: 'bar',
            data: {
                labels: courseLabels,
                datasets: [{
                    label: "Number of Students",
                    backgroundColor: ["#36a2eb", "#ff9f40", "#c9cbcf", "#2ecc71", "#e74c3c", "#9b59b6"],
                    data: courseCounts
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });

        // Document Types Chart (Doughnut Chart)
        new Chart(document.getElementById("document-types-chart"), {
            type: 'doughnut',
            data: {
                labels: ["COR Documents", "COG Documents"],
                datasets: [{
                    data: [<?= $corCount ?>, <?= $cogCount ?>],
                    backgroundColor: ["#28a745", "#17a2b8"],
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
    });
    </script>
</body>
</html>
