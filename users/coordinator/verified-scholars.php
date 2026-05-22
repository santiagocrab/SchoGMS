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
    <style>.preloader{display:none!important}</style>
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
    <div class="preloader">
        <div class="lds-ripple">
            <div class="lds-pos"></div>
            <div class="lds-pos"></div>
        </div>
    </div>
    <!-- ============================================================== -->
    <!-- Main wrapper - style you can find in pages.scss -->
    <!-- ============================================================== -->
    <?php require_once __DIR__ . '/inc/coordinator_nav.php'; schogms_coordinator_shell_open('Verified Scholars'); ?>

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
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="uploadModalLabel">Upload verified scholars (billing Excel)</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <?php
                            require_once __DIR__ . '/inc/verified_scholars_upload_guide.php';
                            schogms_render_verified_scholars_upload_guide(true, (string) ($sheet_name ?? ''));
                            ?>
                            <form id="uploadForm">
                                <div class="mb-3">
                                    <label for="excelFile" class="form-label">Choose Excel file (.xlsx or .xls)</label>
                                    <input type="file" class="form-control" id="excelFile" name="excelFile"
                                        accept=".xlsx,.xls,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,application/vnd.ms-excel">
                                </div>
                                <button type="submit" class="btn btn-primary">Upload &amp; import</button>
                            </form>
                            <div id="message" class="mt-2"></div>
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
                <?php
                require_once __DIR__ . '/inc/verified_scholars_upload_guide.php';
                schogms_render_verified_scholars_upload_guide(false, (string) ($sheet_name ?? ''));
                ?>
                <!-- ============================================================== -->
                <!-- Start Page Content -->
                <!-- ============================================================== -->
                <!-- basic table -->
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <h4 class="card-title mb-3">CHED masterlist scholars (your campus)</h4>
                                <p class="text-muted small mb-3">
                                    This table lists scholars from the <strong>CHED TDP Masterlist</strong> for your campus.
                                    To add or update masterlist rows, use <a href="ched_masterlist.php">TDP Masterlist</a>.
                                    Billing / payment data is uploaded with the <strong>Upload File</strong> button above.
                                </p>
                                <div class="row">
                                    <div class="table-responsive">
                                        <?php
                                        require_once __DIR__ . '/inc/coordinator_data.php';
                                        $campusFilter = trim((string) ($sheet_name ?? ''));
                                        $scholarData = schogms_coordinator_ched_scholars($conn, $campusFilter);
                                        $scholarRows = $scholarData['rows'];
                                        $scholarError = $scholarData['error'];
                                        if ($scholarError !== ''): ?>
                                            <div class="alert alert-warning"><?= htmlspecialchars($scholarError) ?></div>
                                        <?php endif; ?>

                                        <table id="zero_config" class="table table-striped table-bordered no-wrap">
                                            <thead>
                                                <tr>
                                                    <th>Last Name</th>
                                                    <th>First Name</th>
                                                    <th>Course</th>
                                                    <th>Year Level</th>
                                                    <th>Units</th>
                                                    <th>Campus</th>
                                                    <th>File Group</th>
                                                    <th>Enrollment</th>
                                                    <th>Remarks</th>
                                                    <th>Uploaded</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php if (count($scholarRows) === 0): ?>
                                                    <tr><td colspan="10" class="text-center text-muted">No scholars found<?= $campusFilter !== '' ? ' for ' . htmlspecialchars($campusFilter) : '' ?>.</td></tr>
                                                <?php else: ?>
                                                <?php foreach ($scholarRows as $row): ?>
                                                    <tr>
                                                        <td><?= htmlspecialchars((string) ($row['lastname'] ?? '')) ?></td>
                                                        <td><?= htmlspecialchars((string) ($row['firstname'] ?? '')) ?></td>
                                                        <td><?= htmlspecialchars((string) ($row['course_program_enrolled'] ?? '')) ?></td>
                                                        <td><?= htmlspecialchars((string) ($row['year_level'] ?? '')) ?></td>
                                                        <td><?= htmlspecialchars((string) ($row['total_units_enrolled'] ?? '')) ?></td>
                                                        <td><?= htmlspecialchars((string) ($row['sheet_name'] ?? '')) ?></td>
                                                        <td><?= htmlspecialchars((string) ($row['file_group'] ?? '')) ?></td>
                                                        <td><?= htmlspecialchars((string) ($row['status_of_enrollment'] ?? '')) ?></td>
                                                        <td><?= htmlspecialchars((string) ($row['remarks'] ?? '')) ?></td>
                                                        <td><?= htmlspecialchars((string) ($row['upload_time'] ?? '')) ?></td>
                                                    </tr>
                                                <?php endforeach; ?>
                                                <?php endif; ?>
                                            </tbody>
                                        </table>

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
        <script>
            document.getElementById('uploadForm').addEventListener('submit', function (event) {
                event.preventDefault();

                const fileInput = document.getElementById('excelFile');
                const file = fileInput.files[0];

                if (!file) {
                    // No file selected
                    showToast("Please select a file!", "error");
                    return;
                }

                // Validate file type
                const allowedTypes = [
                    'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                    'application/vnd.ms-excel'
                ];

                const ext = file.name.split('.').pop().toLowerCase();
                if (!allowedTypes.includes(file.type) && ext !== 'xls' && ext !== 'xlsx') {
                    showToast("Please upload a valid Excel file (.xls or .xlsx).", "error");
                    return;
                }

                const formData = new FormData();
                formData.append('excelFile', file);

                // Display loading message
                showToast("Uploading and processing file...", "loading");

                // Send file via Fetch API
                fetch('process_excel.php', {
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
                            showToast(data.message || "File processed successfully!", "success");
                            setTimeout(function () { window.location.reload(); }, 1500);
                        } else {
                            showToast(data.error || "An error occurred during file processing.", "error");
                            console.error("Server error:", data.error);
                        }
                    })
                    .catch(error => {
                        showToast("An error occurred while uploading the file.", "error");
                        console.error("Fetch error:", error);
                    });
            });

            /**
             * Displays a toast message using Toastify.
             * @param {string} message - The message to display.
             * @param {string} type - The type of the message (success, error, loading).
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
                    case "loading":
                        style = "linear-gradient(to right, #0078D7, #00b4d8)";
                        break;
                    default:
                        style = "linear-gradient(to right, #555, #888)";
                }

                Toastify({
                    text: message,
                    duration: type === "loading" ? 5000 : 3000,
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

</body>

</html>