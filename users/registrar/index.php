<?php
include 'config/session.php';
require_once __DIR__ . '/inc/registrar_data.php';
require_once __DIR__ . '/inc/assets.php';
require_once __DIR__ . '/inc/registrar_nav.php';

$dashCounts = schogms_registrar_dashboard_counts($sheet_name ?? null);
$totalRecords = $dashCounts['masterlist'];
$totalCourses = $dashCounts['courses'];
$totalFileGroups = $dashCounts['file_groups'];
$corCount = $dashCounts['cor'];
$cogCount = $dashCounts['cog'];
$reqCount = $corCount + $cogCount;
?>
<!DOCTYPE html>
<html dir="ltr" lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Registrar Dashboard - SchoGMS</title>
    <?php schogms_registrar_head(); ?>
    <link href="../../assets/extra-libs/c3/c3.min.css" rel="stylesheet">
    <link href="../../assets/libs/chartist/dist/chartist.min.css" rel="stylesheet">
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
        canvas { max-width: 100% !important; height: auto !important; }
        a.registrar-dash-card { color: inherit; text-decoration: none; }
        a.registrar-dash-card:hover .card { box-shadow: 0 4px 12px rgba(0,0,0,.12); }
    </style>
</head>
<body>
<?php schogms_loading_screen_once(); ?>

<?php schogms_registrar_shell_open('Registrar dashboard'); ?>
            <div class="container-fluid">
                <div class="card-group">
                    <a href="masterlist.php" class="registrar-dash-card">
                        <div class="card border-right">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div>
                                        <h2 class="text-dark mb-1 font-weight-medium"><?= (int) $totalRecords ?></h2>
                                        <h6 class="text-muted font-weight-normal mb-0">Total Records</h6>
                                    </div>
                                    <div class="ml-auto"><i data-feather="database" class="feather-icon text-muted"></i></div>
                                </div>
                            </div>
                        </div>
                    </a>
                    <div class="card border-right">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div>
                                    <h2 class="text-dark mb-1 font-weight-medium"><?= (int) $totalCourses ?></h2>
                                    <h6 class="text-muted font-weight-normal mb-0">Total Courses</h6>
                                </div>
                                <div class="ml-auto"><i data-feather="book-open" class="feather-icon text-muted"></i></div>
                            </div>
                        </div>
                    </div>
                    <div class="card border-right">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div>
                                    <h2 class="text-dark mb-1 font-weight-medium"><?= (int) $totalFileGroups ?></h2>
                                    <h6 class="text-muted font-weight-normal mb-0">Total File Groups</h6>
                                </div>
                                <div class="ml-auto"><i data-feather="folder" class="feather-icon text-muted"></i></div>
                            </div>
                        </div>
                    </div>
                    <a href="cor.php" class="registrar-dash-card">
                        <div class="card border-right">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div>
                                        <h2 class="text-dark mb-1 font-weight-medium"><?= (int) $corCount ?></h2>
                                        <h6 class="text-muted font-weight-normal mb-0">COR Documents</h6>
                                    </div>
                                    <div class="ml-auto"><i data-feather="file-text" class="feather-icon text-muted"></i></div>
                                </div>
                            </div>
                        </div>
                    </a>
                    <a href="cog.php" class="registrar-dash-card">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div>
                                        <h2 class="text-dark mb-1 font-weight-medium"><?= (int) $cogCount ?></h2>
                                        <h6 class="text-muted font-weight-normal mb-0">COG Documents</h6>
                                    </div>
                                    <div class="ml-auto"><i data-feather="file-text" class="feather-icon text-muted"></i></div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>

                <div class="row mt-3 mb-2">
                    <div class="col-12">
                        <div class="card border-0 bg-light">
                            <div class="card-body py-3 d-flex flex-wrap align-items-center">
                                <span class="text-muted mr-3 mb-1 mb-md-0">Quick links:</span>
                                <a href="masterlist.php" class="btn btn-outline-dark btn-sm mr-2 mb-1">Masterlist</a>
                                <a href="cor-cog.php" class="btn btn-outline-primary btn-sm mr-2 mb-1">COR &amp; COG</a>
                                <a href="enrollment_status.php" class="btn btn-outline-info btn-sm mr-2 mb-1">Enrollment status</a>
                                <a href="requirements.php" class="btn btn-outline-success btn-sm mr-2 mb-1">
                                    Requirements<?= $reqCount > 0 ? ' (' . (int) $reqCount . ')' : '' ?>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
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
<?php
schogms_registrar_shell_close();
schogms_registrar_footer_scripts(['chart' => true]);
?>
    <script>
    $(document).ready(function() {
        var courseLabels = ["BSIT", "BSCS", "BSIS", "BSCE", "BSME", "BSEE"];
        var courseCounts = [150, 120, 100, 80, 60, 40];
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
                scales: { y: { beginAtZero: true } },
                plugins: { legend: { display: false } }
            }
        });
        new Chart(document.getElementById("document-types-chart"), {
            type: 'doughnut',
            data: {
                labels: ["COR Documents", "COG Documents"],
                datasets: [{
                    data: [<?= (int) $corCount ?>, <?= (int) $cogCount ?>],
                    backgroundColor: ["#28a745", "#17a2b8"],
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { position: 'bottom' } }
            }
        });
    });
    </script>
</body>
</html>
