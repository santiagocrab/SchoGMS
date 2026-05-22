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
    <!-- Custom CSS -->    <?php require_once __DIR__ . '/inc/assets.php'; schogms_coordinator_head(true); ?>
    
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
    <!-- Preloader disabled for faster loading -->
    <style>
        .preloader { display: none !important; }
    </style>
    <!-- ============================================================== -->
    <!-- Main wrapper - style you can find in pages.scss -->
    <!-- ============================================================== -->
    <?php require_once __DIR__ . '/inc/coordinator_nav.php'; schogms_coordinator_shell_open('Dashboard'); ?>

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
                require_once __DIR__ . '/inc/coordinator_dashboard_stats.php';
                set_time_limit(15);

                $file_group_filter = trim((string) ($_GET['file_group'] ?? ''));
                $dashCampus = schogms_coordinator_dashboard_campus();
                $dashStats = ($conn instanceof mysqli)
                    ? schogms_coordinator_dashboard_stats($conn, $dashCampus, $file_group_filter)
                    : [
                        'campus' => $dashCampus,
                        'tdp_records' => 0,
                        'tes_records' => 0,
                        'tdp_courses' => 0,
                        'tdp_file_groups' => 0,
                        'file_groups' => [],
                    ];

                $totalRecords = $dashStats['tdp_records'];
                $totalRecordsTes = $dashStats['tes_records'];
                $totalCourses = $dashStats['tdp_courses'];
                $totalFileGroups = $dashStats['tdp_file_groups'];
                ?>

                <?php if ($dashCampus === ''): ?>
                <div class="alert alert-warning">
                    No campus is assigned to your account. Statistics cannot be loaded until an administrator sets your campus.
                </div>
                <?php else: ?>
                <p class="text-muted mb-3">Campus: <strong><?= htmlspecialchars($dashCampus) ?></strong></p>
                <?php endif; ?>

                <!-- Analytics Filters -->
                <div class="row mb-3">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">Filter analytics (TDP)</h5>
                                <form method="GET" action="" class="form-inline flex-wrap">
                                    <div class="form-group mr-2 mb-2">
                                        <label for="file_group_filter" class="mr-2">File group</label>
                                        <select name="file_group" id="file_group_filter" class="form-control">
                                            <option value="">All file groups</option>
                                            <?php foreach ($dashStats['file_groups'] as $fgValue): ?>
                                            <option value="<?= htmlspecialchars($fgValue) ?>" <?= $file_group_filter === $fgValue ? 'selected' : '' ?>><?= htmlspecialchars($fgValue) ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <button type="submit" class="btn btn-primary mr-2 mb-2">Apply</button>
                                    <a href="index.php" class="btn btn-secondary mb-2">Clear</a>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <div class="card border-right h-100">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div>
                                        <h2 class="text-dark mb-1 font-weight-medium" id="stat-tdp-records"><?= (int) $totalRecords ?></h2>
                                        <h6 class="text-muted font-weight-normal mb-0">Total TDP scholars</h6>
                                    </div>
                                    <div class="ml-auto"><i data-feather="database" class="text-muted"></i></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="card border-right h-100">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div>
                                        <h2 class="text-dark mb-1 font-weight-medium" id="stat-tes-records"><?= (int) $totalRecordsTes ?></h2>
                                        <h6 class="text-muted font-weight-normal mb-0">Total TES scholars</h6>
                                    </div>
                                    <div class="ml-auto"><i data-feather="database" class="text-muted"></i></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="card h-100">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div>
                                        <h2 class="text-dark mb-1 font-weight-medium" id="stat-tdp-courses"><?= (int) $totalCourses ?></h2>
                                        <h6 class="text-muted font-weight-normal mb-0">TDP courses / programs</h6>
                                    </div>
                                    <div class="ml-auto"><i data-feather="book-open" class="text-muted"></i></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <style>
                    .dashboard-chart-wrap {
                        position: relative;
                        width: 100%;
                        height: 320px;
                        max-width: 100%;
                    }
                    .dashboard-chart-wrap canvas {
                        max-width: 100%;
                    }
                    .chart-empty-msg {
                        padding: 2rem;
                        color: #6c757d;
                        text-align: center;
                    }
                </style>
                <!-- *************************************************************** -->
                <!-- End First Cards -->
                <div class="row">
                    <div class="col-lg-6 col-md-12 mb-4">
                        <div class="card h-100">
                            <div class="card-body">
                                <h4 class="card-title">TDP scholars by course</h4>
                                <div class="dashboard-chart-wrap" id="wrap-tdp-courses">
                                    <canvas id="total-courses-chart"></canvas>
                                    <div class="chart-empty-msg d-none" id="empty-tdp-courses">No TDP data for this campus<?= $file_group_filter !== '' ? ' and file group' : '' ?>.</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-12 mb-4">
                        <div class="card h-100">
                            <div class="card-body">
                                <h4 class="card-title">TES scholars by course</h4>
                                <div class="dashboard-chart-wrap" id="wrap-tes-courses">
                                    <canvas id="total-courses-chart-tes"></canvas>
                                    <div class="chart-empty-msg d-none" id="empty-tes-courses">No TES data for this campus.</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-12 mb-4">
                        <div class="card h-100">
                            <div class="card-body">
                                <h4 class="card-title">TDP scholars by file group</h4>
                                <div class="dashboard-chart-wrap" id="wrap-file-groups">
                                    <canvas id="total-file-groups-chart"></canvas>
                                    <div class="chart-empty-msg d-none" id="empty-file-groups">No file groups for this campus.</div>
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
    <?php require_once __DIR__ . '/inc/assets.php'; schogms_coordinator_footer_scripts(['chart' => true]); ?>
    <script>
    (function () {
        var chartInstances = {};
        var palette = ['#5f76e8', '#01caf1', '#ff4f70', '#28a745', '#ffcd56', '#9966ff', '#4bc0c0', '#f39c12'];

        function shortLabel(text, max) {
            text = String(text || '—');
            return text.length > max ? text.slice(0, max - 1) + '…' : text;
        }

        function barOptions() {
            return {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    x: {
                        ticks: { maxRotation: 45, minRotation: 0, autoSkip: true, maxTicksLimit: 8 }
                    },
                    y: { beginAtZero: true, ticks: { precision: 0 } }
                }
            };
        }

        function renderBar(canvasId, emptyId, labels, counts, label) {
            var canvas = document.getElementById(canvasId);
            var emptyEl = document.getElementById(emptyId);
            if (!canvas) return;
            if (chartInstances[canvasId]) {
                chartInstances[canvasId].destroy();
            }
            if (!labels.length) {
                canvas.classList.add('d-none');
                if (emptyEl) emptyEl.classList.remove('d-none');
                return;
            }
            canvas.classList.remove('d-none');
            if (emptyEl) emptyEl.classList.add('d-none');
            chartInstances[canvasId] = new Chart(canvas, {
                type: 'bar',
                data: {
                    labels: labels.map(function (l) { return shortLabel(l, 28); }),
                    datasets: [{
                        label: label,
                        data: counts,
                        backgroundColor: labels.map(function (_, i) { return palette[i % palette.length]; })
                    }]
                },
                options: barOptions()
            });
        }

        function loadDashboardCharts() {
            var params = new URLSearchParams(window.location.search);
            $.ajax({
                url: 'fetch_dashboard_charts.php',
                method: 'GET',
                data: { file_group: params.get('file_group') || '' },
                dataType: 'json',
                timeout: 15000
            }).done(function (data) {
                if (!data || !data.success) return;
                $('#stat-tdp-records').text(data.total_records || 0);
                $('#stat-tes-records').text(data.total_records_tes || 0);

                var tdpCourses = data.total_courses || [];
                var tesCourses = data.total_courses_tes || [];
                var groups = data.total_file_groups || [];

                renderBar(
                    'total-courses-chart', 'empty-tdp-courses',
                    tdpCourses.map(function (r) { return r.course_program_enrolled; }),
                    tdpCourses.map(function (r) { return Number(r.count); }),
                    'TDP scholars'
                );
                renderBar(
                    'total-courses-chart-tes', 'empty-tes-courses',
                    tesCourses.map(function (r) { return r.course_program_enrolled; }),
                    tesCourses.map(function (r) { return Number(r.count); }),
                    'TES scholars'
                );
                renderBar(
                    'total-file-groups-chart', 'empty-file-groups',
                    groups.map(function (r) { return r.file_group; }),
                    groups.map(function (r) { return Number(r.count); }),
                    'File groups'
                );
            }).fail(function () {
                ['empty-tdp-courses', 'empty-tes-courses', 'empty-file-groups'].forEach(function (id) {
                    var el = document.getElementById(id);
                    if (el) {
                        el.classList.remove('d-none');
                        el.textContent = 'Could not load chart data. Refresh the page.';
                    }
                });
            });
        }

        $(function () {
            if (typeof feather !== 'undefined') feather.replace();
            if (typeof Chart !== 'undefined') loadDashboardCharts();
        });
    })();
    </script>

    <footer> 
    <script>
if(navigator.serviceWorker) { // Ensure this path is correct
    navigator.serviceWorker.register('serviceWorker-sw.js') 
}
</script></footer>
</body>

</html>