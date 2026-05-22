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
<?php schogms_loading_screen_once(); ?>

    <?php include 'loading-screen.php'; ?>
    <!-- ============================================================== -->
    <!-- Preloader - style you can find in spinners.css -->
    <!-- ============================================================== -->
    <!-- Preloader removed for faster loading - uncomment if needed -->
    <!--     <!-- Preloader disabled for faster loading -->
    <style>
        .preloader { display: none !important; }
    </style> -->
    <!-- ============================================================== -->
    <!-- Main wrapper - style you can find in pages.scss -->
    <!-- ============================================================== -->
    <?php require_once __DIR__ . '/inc/coordinator_nav.php'; schogms_coordinator_shell_open('Validate TDP'); ?>

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
                    <div class="col-5 align-self-center" hidden>
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
                                    require_once __DIR__ . '/inc/tdp_bulk_validate.php';
                                    require_once __DIR__ . '/inc/validation_filters.php';
                                    require_once __DIR__ . '/inc/validation_edit_guide.php';
                                    set_time_limit(120);

                                    $tdpSheet = trim((string) ($sheet_name ?? ''));
                                    $bulkStats = null;
                                    $runBulk = isset($_GET['bulk']) && $_GET['bulk'] !== '0';
                                    $tdpRows = [];
                                    $vfOptions = [];

                                    if ($tdpSheet === '') {
                                        echo '<div class="alert alert-warning">No campus assigned to your account.</div>';
                                    } elseif ($conn instanceof mysqli) {
                                        if ($runBulk) {
                                            $bulkStats = schogms_tdp_bulk_validate_campus($conn, $tdpSheet, $_GET);
                                        }
                                        $tdpRows = schogms_validation_fetch_rows($conn, 'tdp', $tdpSheet, $_GET, true);
                                        $vfOptions = schogms_validation_filter_options($conn, 'tdp', $tdpSheet);
                                    }

                                    $totalRecords = count($tdpRows);
                                    $countPassed = 0;
                                    $countFailed = 0;
                                    $countNoCor = 0;
                                    foreach ($tdpRows as $r) {
                                        $c = $r['_check'] ?? schogms_validation_row_check($r, [], 'tdp');
                                        if ($c['passed']) {
                                            $countPassed++;
                                        } else {
                                            $countFailed++;
                                        }
                                        if (!$c['has_cor']) {
                                            $countNoCor++;
                                        }
                                    }
                                    ?>
                                    <div class="table-responsive">
                                        <h5 class="mb-2">TDP validation — campus:
                                            <strong><?php echo htmlspecialchars($tdpSheet); ?></strong>
                                            <span class="badge badge-info ml-2"><?php echo (int) $totalRecords; ?> scholars (all listed)</span>
                                        </h5>
                                        <p class="text-muted small mb-3">
                                            Validates every scholar at once (course &amp; year level vs registrar, COR/COG on file).
                                            Same workflow as <strong>Validate TES</strong> — no per-row buttons.
                                        </p>

                                        <?php if ($bulkStats !== null): ?>
                                        <div class="alert alert-success">
                                            Bulk validation complete:
                                            <strong><?php echo (int) $bulkStats['passed']; ?> passed</strong>,
                                            <strong><?php echo (int) $bulkStats['failed']; ?> failed</strong>
                                            (<?php echo (int) $bulkStats['total']; ?> total).
                                        </div>
                                        <?php endif; ?>

                                        <div class="row mb-3">
                                            <div class="col-md-3">
                                                <div class="card bg-light mb-2 mb-md-0">
                                                    <div class="card-body py-2 text-center">
                                                        <div class="text-muted small">Total</div>
                                                        <div class="h4 mb-0"><?php echo (int) $totalRecords; ?></div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="card border-success mb-2 mb-md-0">
                                                    <div class="card-body py-2 text-center">
                                                        <div class="text-muted small">Passed</div>
                                                        <div class="h4 mb-0 text-success"><?php echo (int) $countPassed; ?></div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="card border-danger mb-2 mb-md-0">
                                                    <div class="card-body py-2 text-center">
                                                        <div class="text-muted small">Failed</div>
                                                        <div class="h4 mb-0 text-danger"><?php echo (int) $countFailed; ?></div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="card border-warning mb-2 mb-md-0">
                                                    <div class="card-body py-2 text-center">
                                                        <div class="text-muted small">No COR</div>
                                                        <div class="h4 mb-0 text-warning"><?php echo (int) $countNoCor; ?></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <button type="button" id="bulkValidateTdpBtn" class="btn btn-primary btn-rounded mb-3"
                                            data-sheet-name="<?php echo htmlspecialchars($tdpSheet); ?>">
                                            Re-validate all scholars
                                        </button>

                                        <?php
                                        $vfProgram = 'tdp';
                                        $vfCampus = $tdpSheet;
                                        $vfGet = $_GET;
                                        $vfPage = 'validate.php';
                                        require __DIR__ . '/inc/validation_filters_ui.php';
                                        ?>
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
                                                    <th>VALIDATION</th>
                                                    <th>Edit</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                $viewBase = '../../view_document.php?path=';
                                                if (count($tdpRows) > 0):
                                                    foreach ($tdpRows as $row):
                                                        $check = $row['_check'] ?? schogms_validation_row_check($row, [], 'tdp');
                                                        $cor_path = $check['cor_path'];
                                                        $cog_path = $check['cog_path'];
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
                                                        <td><?php echo htmlspecialchars($row['enrolled']); ?>
                                                        </td>
                                                        
                                                        <!-- COR & COG Viewing -->
                                                        <td>
                                                            <div class="btn-group" role="group">
                                                                <?php 
                                                                // Check if COR document exists in MongoDB - show button if found
                                                                // Debug: Check if corDoc is set
                                                                $hasCorDoc = !empty($cor_path);
                                                                
                                                                if ($hasCorDoc): 
                                                                    // Use document viewer script to serve the file
                                                                    $filePath = $cor_path;
                                                                    // Encode the path for URL
                                                                    $encodedPath = base64_encode($filePath);
                                                                    $corPath = '../../view_document.php?path=' . urlencode($encodedPath);
                                                                    $corPath = $viewBase . urlencode(base64_encode($filePath));
                                                                ?>
                                                                    <a href="<?php echo htmlspecialchars($corPath); ?>" 
                                                                       target="_blank" 
                                                                       class="btn btn-sm btn-success" 
                                                                       title="View COR">
                                                                        COR
                                                                    </a>
                                                                <?php else: ?>
                                                                    <span class="badge badge-secondary">No COR</span>
                                                                <?php endif; ?>
                                                                
                                                                <?php 
                                                                if (!empty($cog_path)):
                                                                    $filePath = $cog_path;
                                                                    // Encode the path for URL
                                                                    $encodedPath = base64_encode($filePath);
                                                                    $cogPath = '../../view_document.php?path=' . urlencode($encodedPath);
                                                                    $cogPath = $viewBase . urlencode(base64_encode($filePath));
                                                                ?>
                                                                    <a href="<?php echo htmlspecialchars($cogPath); ?>" 
                                                                       target="_blank" 
                                                                       class="btn btn-sm btn-primary" 
                                                                       title="View COG">
                                                                        COG
                                                                    </a>
                                                                <?php else: ?>
                                                                    <span class="badge badge-secondary">No COG</span>
                                                                <?php endif; ?>
                                                            </div>
                                                        </td>

                                                        <!-- Enrollment Status -->
                                                        <td>
                                                            <?php if ($check['has_cor']): ?>
                                                                <span class="badge badge-success">Enrolled</span>
                                                            <?php else: ?>
                                                                <span class="badge badge-warning">Not Enrolled</span>
                                                            <?php endif; ?>
                                                        </td>
                                                        
                                                        <td><?php echo htmlspecialchars($row['remarks'] ?? ''); ?></td>
                                                        
                                                        <td>
                                                            <?php if ($check['passed']): ?>
                                                                <span class="badge badge-success" title="Course and year level match registrar">Validated</span>
                                                            <?php else: ?>
                                                                <span class="badge badge-danger" title="<?php
                                                                    $tips = [];
                                                                    if (!$check['course_match']) {
                                                                        $tips[] = 'Course mismatch';
                                                                    }
                                                                    if (!$check['year_level_match']) {
                                                                        $tips[] = 'Year level mismatch';
                                                                    }
                                                                    echo htmlspecialchars(implode('; ', $tips));
                                                                ?>">Failed</span>
                                                            <?php endif; ?>
                                                            <div class="small text-muted mt-1">
                                                                <?php if ($check['course_match']): ?>✓ Course<?php else: ?>✗ Course<?php endif; ?>
                                                                ·
                                                                <?php if ($check['year_level_match']): ?>✓ Year<?php else: ?>✗ Year<?php endif; ?>
                                                            </div>
                                                            <?php if (!$check['passed']): ?>
                                                            <div class="small mt-1">
                                                                <?php if (!$check['course_match'] && trim((string) ($row['reg_course'] ?? '')) !== ''): ?>
                                                                    <div>Reg. course: <em><?= htmlspecialchars((string) $row['reg_course']) ?></em></div>
                                                                <?php endif; ?>
                                                                <?php if (!$check['year_level_match'] && trim((string) ($row['reg_year_level'] ?? '')) !== ''): ?>
                                                                    <div>Reg. year: <em><?= htmlspecialchars((string) $row['reg_year_level']) ?></em></div>
                                                                <?php endif; ?>
                                                            </div>
                                                            <?php endif; ?>
                                                        </td>
                                                        <td>
                                                            <button type="button" class="btn btn-sm <?= $check['passed'] ? 'btn-outline-secondary' : 'btn-warning' ?> btn-edit-student"
                                                                data-id="<?= (int) ($row['id'] ?? 0) ?>"
                                                                data-guide="<?= schogms_validation_edit_guide_attr($row, $check) ?>">
                                                                <?= $check['passed'] ? 'Edit' : 'Fix' ?>
                                                            </button>
                                                        </td>
                                                    </tr>
                                                <?php 
                                                    endforeach; 
                                                else: 
                                                ?>
                                                    <tr>
                                                        <td colspan="16" class="text-center">No records found.</td>
                                                    </tr>
                                                <?php endif; ?>
                                            </tbody>
                                        </table>
                                    </div>

                                    <?php
                                    // Don't close connection yet - might need it for pagination count
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
        document.getElementById('uploadForm').addEventListener('submit', function (event) {
            event.preventDefault();

            const fileInput = document.getElementById('excelFile');
            const file = fileInput.files[0];

            if (!file) {
                showToast("Please select a file!", "error");
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
                        .then(response => {
                            if (!response.ok) {
                                throw new Error(`HTTP error! status: ${response.status}`);
                            }
                            return response.json();
                        })
                        .then(data => {
                            if (data.success) {
                                Swal.fire({
                                    title: "Success!",
                                    text: data.message || "File processed successfully!",
                                    icon: "success",
                                    timer: 3000
                                });
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

        /**
         * Displays a toast message using Toastify.
         * @param {string} message - The message to display.
         * @param {string} type - The type of the message (success, error).
         */
        function showToast(message, type) {
            let style;
            switch (type) {
                case "success":
                    style = "linear-gradient(to right, #00b09b, #96c93d)";
                    break;
                case "error":
                    style = "linear-gradient(to right, #ff5f6d, #ffc371)";
                    break;
                default:
                    style = "linear-gradient(to right, #555, #888)";
            }

            Toastify({
                text: message,
                duration: 3000,
                close: true,
                gravity: "top",
                position: "center",
                style: { background: style }
            }).showToast();
        }
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
    <?php require_once __DIR__ . '/inc/assets.php'; schogms_coordinator_footer_scripts(['datatables' => true]); ?>

    
    <!-- Force hide preloader after page load -->
    <script>
        // Hide preloader immediately - don't wait for document ready
        (function() {
            var preloader = document.querySelector('.preloader');
            if (preloader) {
                setTimeout(function() {
                    preloader.style.display = 'none';
                }, 500);
            }
        })();
        
        // jQuery fallback
        $(document).ready(function() {
            $(".preloader").fadeOut(300);
        });
        
        // Ultimate fallback: hide preloader after 2 seconds
        setTimeout(function() {
            var preloader = document.querySelector('.preloader');
            if (preloader) {
                preloader.style.display = 'none';
                preloader.style.opacity = '0';
            }
            $(".preloader").hide();
        }, 2000);
    </script>
    
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function () {
            $('#bulkValidateTdpBtn').on('click', function () {
                var sheetName = $(this).data('sheet-name');
                if (!sheetName) return;

                Swal.fire({
                    title: 'Validating all scholars…',
                    text: 'Please wait. This may take a minute for large lists.',
                    icon: 'info',
                    allowOutsideClick: false,
                    showConfirmButton: false,
                    willOpen: function () { Swal.showLoading(); }
                });

                $.ajax({
                    url: 'bulk_validate.php',
                    method: 'POST',
                    data: { sheet_name: sheetName, program: 'tdp' },
                    dataType: 'json'
                }).done(function (response) {
                    if (response.success) {
                        Swal.fire({
                            title: 'Done',
                            text: response.message || 'Bulk validation complete.',
                            icon: 'success'
                        }).then(function () {
                            var url = new URL(window.location.href);
                            url.searchParams.set('bulk', '1');
                            window.location.href = url.toString();
                        });
                    } else {
                        Swal.fire({ title: 'Error', text: response.error || 'Validation failed.', icon: 'error' });
                    }
                }).fail(function () {
                    Swal.fire({ title: 'Error', text: 'Request failed. Please try again.', icon: 'error' });
                });
            });
        });
    </script>
    <?php
    $mlProgram = 'tdp';
    $mlCampus = $tdpSheet ?? (string) ($sheet_name ?? '');
    require __DIR__ . '/inc/masterlist_edit_ui.php';
    ?>
</body>

</html>