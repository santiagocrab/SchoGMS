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
    <?php require_once __DIR__ . '/inc/coordinator_nav.php'; schogms_coordinator_shell_open('TES Masterlist'); ?>

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
                                onclick="showValidation('validate_tes.php')">
                                Validate TES
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
                                var dest = url + (url.indexOf('?') >= 0 ? '&' : '?') + 'bulk=1';
                                window.location.href = dest;
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
                                    require_once __DIR__ . '/inc/masterlist_rows.php';
                                    $tesData = schogms_coordinator_ched_tes_rows($conn, (string) ($sheet_name ?? ''));
                                    $masterlistRows = $tesData['rows'];
                                    $masterlistError = $tesData['error'];
                                    if ($masterlistError !== ''): ?>
                                        <div class="alert alert-warning"><?= htmlspecialchars($masterlistError) ?></div>
                                    <?php endif; ?>
                                                                        <div class="table-responsive">
                                        <h5>Displaying records for:
                                            <strong><?php echo htmlspecialchars($sheet_name); ?></strong>
                                        </h5>

                                        <!-- Hidden Form to Open in a New Window -->
                                        <form method="GET" action="validated_masterlist.php" id="filterForm"
                                            onsubmit="return openNewWindow(event)" hidden>
                                            <input type="hidden" name="campus"
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
                                                    <th>LASTNAME</th>
                                                    <th>FIRSTNAME</th>
                                                    <th>EXT</th>
                                                    <th>MIDDLENAME</th>
                                                    <th>SEX</th>
                                                    <th>COURSE</th>
                                                    <th>YEAR LEVEL</th>
                                                    <th>STREET</th>
                                                    <th>TOWN_CITY</th>
                                                    <th>CONTACT</th>
                                                    <th>BATCH NO</th>
                                                    <th>COR/COG</th>
                                                    <th>Status</th>
                                                    <th>Edit</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($masterlistRows as $row):
                                                    $hasCor = !empty($row['cor_path']);
                                                    $hasCog = !empty($row['cog_path']);
                                                    $viewBase = '../../view_document.php?path=';
                                                ?>
                                                    <tr>
                                                        <td><?= htmlspecialchars((string) ($row['seq'] ?? '')) ?></td>
                                                        <td><?= htmlspecialchars((string) ($row['app_no'] ?? '')) ?></td>
                                                        <td><?= htmlspecialchars((string) ($row['lastname'] ?? '')) ?></td>
                                                        <td><?= htmlspecialchars((string) ($row['firstname'] ?? '')) ?></td>
                                                        <td><?= htmlspecialchars((string) ($row['ext'] ?? '')) ?></td>
                                                        <td><?= htmlspecialchars((string) ($row['middlename'] ?? '')) ?></td>
                                                        <td><?= htmlspecialchars((string) ($row['sex'] ?? '')) ?></td>
                                                        <td><?= htmlspecialchars((string) ($row['course_program_enrolled'] ?? '')) ?></td>
                                                        <td><?= htmlspecialchars((string) ($row['year_level'] ?? '')) ?></td>
                                                        <td><?= htmlspecialchars((string) ($row['street'] ?? '')) ?></td>
                                                        <td><?= htmlspecialchars((string) ($row['town_city'] ?? '')) ?></td>
                                                        <td><?= htmlspecialchars((string) ($row['contact'] ?? '')) ?></td>
                                                        <td><?= htmlspecialchars((string) ($row['batch_no'] ?? '')) ?></td>
                                                        <td>
                                                            <div class="btn-group" role="group">
                                                                <?php if ($hasCor): ?>
                                                                    <a href="<?= htmlspecialchars($viewBase . urlencode(base64_encode((string) $row['cor_path']))) ?>" target="_blank" class="btn btn-sm btn-success">COR</a>
                                                                <?php else: ?><span class="badge badge-secondary">No COR</span><?php endif; ?>
                                                                <?php if ($hasCog): ?>
                                                                    <a href="<?= htmlspecialchars($viewBase . urlencode(base64_encode((string) $row['cog_path']))) ?>" target="_blank" class="btn btn-sm btn-primary">COG</a>
                                                                <?php else: ?><span class="badge badge-secondary">No COG</span><?php endif; ?>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <?php if (($row['enrollment_status'] ?? '') === 'Enrolled'): ?>
                                                                <span class="badge badge-success">Enrolled</span>
                                                            <?php else: ?>
                                                                <span class="badge badge-warning">Not Enrolled</span>
                                                            <?php endif; ?>
                                                        </td>
                                                        <td>
                                                            <button type="button" class="btn btn-sm btn-outline-primary btn-edit-student" data-id="<?= (int) ($row['id'] ?? 0) ?>">Edit</button>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>

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
    <?php require_once __DIR__ . '/inc/assets.php'; schogms_coordinator_footer_scripts(['datatables' => true, 'sweetalert' => true]); ?>
    <?php
    $mlProgram = 'tes';
    $mlCampus = (string) ($sheet_name ?? '');
    require __DIR__ . '/inc/masterlist_edit_ui.php';
    ?>

</body>

</html>