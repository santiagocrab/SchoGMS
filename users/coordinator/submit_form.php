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
    <style>.preloader{display:none!important}.format-sample-table{font-size:13px}.format-sample-table th{background:#f8f9fa;white-space:nowrap}</style>
    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
<![endif]-->
    <style>
        .badge {
            padding: 5px 10px;
            color: white;
            border-radius: 4px;
            /* font-weight: bold; */
        }

        .badge-pending {
            background-color: #ffc107;
            /* Yellow for pending */
        }

        .badge-approved {
            background-color: #28a745;
            /* Green for approved */
        }

        .badge-rejected {
            background-color: #dc3545;
            /* Red for rejected */
        }

        .badge-default {
            background-color: #6c757d;
            /* Gray for undefined or unexpected status */
        }
    </style>
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
    <?php require_once __DIR__ . '/inc/coordinator_nav.php'; schogms_coordinator_shell_open('Submit Form'); ?>

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
                                    <li class="breadcrumb-item"><a href="index.php">Program List</a>
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
                            <h5 class="modal-title" id="uploadModalLabel">Upload Annex 7 Form (Excel / CSV)</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <form id="uploadForm" enctype="multipart/form-data" method="POST">
                                <div class="mb-3">
                                    <label for="user_id" class="form-label" hidden>User ID</label>
                                    <input type="text" class="form-control" id="user_id" name="user_id"
                                        value="<?= $user_id ?>" hidden>
                                </div>
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="text" class="form-control" id="email" name="email"
                                        value="<?= $email ?>" readonly>
                                </div>
                                <div class="mb-3">
                                    <label for="campus" class="form-label">Campus</label>
                                    <input type="text" class="form-control" id="campus" name="campus"
                                        value="<?= $sheet_name ?>" readonly>
                                </div>
                                <div class="mb-3">
                                    <label for="excelFile" class="form-label">Choose Excel or CSV file</label>
                                    <input type="file" class="form-control" id="excelFile" name="excelFile"
                                        accept=".xls,.xlsx,.csv">
                                    <small class="form-text text-muted">Use the same column layout as the sample format below (data from row 3 onward).</small>
                                </div>
                                <button type="submit" class="btn btn-primary">Upload</button>
                            </form>

                            <div id="message"></div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Modal -->
            <!-- Update Modal -->
            <div class="modal fade" id="updateModal" tabindex="-1" aria-labelledby="updateModalLabel"
                aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="updateModalLabel">Update File Info</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form id="updateForm">
                                <div class="mb-3">
                                    <label for="updateFileGroup" class="form-label">File Group</label>
                                    <input type="text" class="form-control" id="updateFileGroup" name="file_group">
                                </div>
                                <div class="mb-3">
                                    <label for="updateFilename" class="form-label">Filename</label>
                                    <input type="text" class="form-control" id="updateFilename" name="filename">
                                </div>
                                <input type="hidden" id="fileId">
                                <button type="submit" class="btn btn-primary">Save Changes</button>
                            </form>
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
                                        $submissions = [];
                                        $campusFilter = trim((string) ($sheet_name ?? ''));
                                        if ($campusFilter !== '') {
                                            $stmt = $conn->prepare('SELECT id, file_name, uploaded_at, status FROM file_submissions WHERE campus = ? ORDER BY uploaded_at DESC');
                                            $stmt->bind_param('s', $campusFilter);
                                            $stmt->execute();
                                            $result = $stmt->get_result();
                                            while ($result && ($row = $result->fetch_assoc())) {
                                                $submissions[] = $row;
                                            }
                                            $stmt->close();
                                        }
                                        ?>
                                        <table id="zero_config" class="table table-striped table-bordered no-wrap">
                                            <thead>
                                                <tr>
                                                    <th>ID</th>
                                                    <th>File Name</th>
                                                    <th>Uploaded At</th>
                                                    <th>Status</th>
                                                </tr>
                                            </thead>
                                            <?php if (count($submissions) === 0): ?>
                                                <tr><td colspan="4" class="text-center text-muted">No forms submitted yet for this campus.</td></tr>
                                            <?php else: ?>
                                            <?php foreach ($submissions as $row): ?>
                                                <tr>
                                                    <td><?= (int) $row['id']; ?></td>
                                                    <td><?= htmlspecialchars((string) $row['file_name']); ?></td>
                                                    <td><?= htmlspecialchars((string) $row['uploaded_at']); ?></td>
                                                    <td>
                                                        <?php
                                                        $status = (string) ($row['status'] ?? '');
                                                        $badgeClass = match ($status) {
                                                            'Pending' => 'badge-pending',
                                                            'Approved' => 'badge-approved',
                                                            'Rejected' => 'badge-rejected',
                                                            default => 'badge-default',
                                                        };
                                                        ?>
                                                        <span class="badge <?= $badgeClass; ?>"><?= htmlspecialchars($status); ?></span>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                            <?php endif; ?>
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

                <div class="row mt-3">
                    <div class="col-12">
                        <div class="card border-primary">
                            <div class="card-body">
                                <h4 class="card-title text-primary mb-3">Here is the format</h4>
                                <p class="text-muted mb-3">
                                    Upload the <strong>Annex 7 — Scholarship Grant Utilization</strong> file for chairman review.
                                    Accepted files: <strong>.xlsx</strong>, <strong>.xls</strong>, or <strong>.csv</strong>.
                                    Row 1 is the report title, row 2 is the column headers, and <strong>student data starts on row 3</strong> (one scholar per row).
                                </p>
                                <div class="mb-3">
                                    <a href="download_submit_form_sample.php" class="btn btn-outline-primary btn-sm">
                                        <i data-feather="download" class="feather-icon"></i> Download sample template (CSV)
                                    </a>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-bordered table-sm format-sample-table mb-0">
                                        <thead>
                                            <tr class="table-active">
                                                <th>A</th><th>B</th><th>C</th><th>D</th><th>E</th><th>F</th><th>G</th>
                                                <th>H</th><th>I</th><th>J</th><th>K</th><th>L</th><th>M</th><th>N</th>
                                                <th>O</th><th>P</th><th>Q</th><th>R</th><th>S</th>
                                            </tr>
                                            <tr>
                                                <th>Last Name</th>
                                                <th>First Name</th>
                                                <th>Scholarship Type</th>
                                                <th>Units Enrolled</th>
                                                <th>Course</th>
                                                <th>Campus</th>
                                                <th>Year &amp; Date Submitted (CHED)</th>
                                                <th>Amount</th>
                                                <th>1st Semester</th>
                                                <th>2nd Semester</th>
                                                <th>Status</th>
                                                <th>Payment Scholarship Type</th>
                                                <th>Payment Amount</th>
                                                <th>Payment Year &amp; Date</th>
                                                <th>Payment OR No.</th>
                                                <th>Payment Amount per OR</th>
                                                <th>Refund 1st Sem</th>
                                                <th>Refund 2nd Sem</th>
                                                <th>Refund Year &amp; Date Released</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr class="text-muted">
                                                <td colspan="19"><em>Row 1 (optional title): Annex 7 - Scholarship Grant Utilization Report</em></td>
                                            </tr>
                                            <tr>
                                                <td>Dela Cruz</td>
                                                <td>Juan</td>
                                                <td>TDP</td>
                                                <td>18</td>
                                                <td>BS Information Technology</td>
                                                <td><?= htmlspecialchars((string) ($sheet_name ?: 'Main')) ?></td>
                                                <td>2025-01-15</td>
                                                <td>15000</td>
                                                <td>7500</td>
                                                <td>7500</td>
                                                <td>Active</td>
                                                <td>TDP</td>
                                                <td>15000</td>
                                                <td>2025-02-01</td>
                                                <td>OR-2025-001</td>
                                                <td>15000</td>
                                                <td>0</td>
                                                <td>0</td>
                                                <td>—</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <p class="small text-muted mt-3 mb-0">
                                    After upload, status will show as <span class="badge badge-pending">Pending</span> until the chairman approves your Annex 7 form.
                                </p>
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


    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.getElementById('uploadForm').addEventListener('submit', function (event) {
            event.preventDefault();

            const user_id = document.getElementById('user_id').value.trim();
            const email = document.getElementById('email').value.trim();
            const campus = document.getElementById('campus').value.trim();
            const fileInput = document.getElementById('excelFile');
            const file = fileInput.files[0];

            if (!file) {
                Swal.fire("Error", "Please select a file!", "error");
                return;
            }

            if (!user_id) {
                Swal.fire("Error", "Please provide a user ID!", "error");
                return;
            }

            if (!email) {
                Swal.fire("Error", "Please enter an email address!", "error");
                return;
            }

            if (!campus) {
                Swal.fire("Error", "Please enter a campus name!", "error");
                return;
            }

            const allowedExtensions = ['xls', 'xlsx', 'csv'];
            const fileExtension = file.name.split('.').pop().toLowerCase();

            if (!allowedExtensions.includes(fileExtension)) {
                Swal.fire("Error", "Please upload a valid Excel or CSV file.", "error");
                return;
            }

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
                    formData.append('user_id', user_id);
                    formData.append('email', email);
                    formData.append('campus', campus);
                    formData.append('excelFile', file);

                    Swal.fire({
                        title: "Uploading...",
                        text: "Please wait while the file is being uploaded and processed.",
                        icon: "info",
                        allowOutsideClick: false,
                        showConfirmButton: false,
                        willOpen: () => Swal.showLoading()
                    });

                    fetch('upload_file.php', {
                        method: 'POST',
                        body: formData
                    })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                Swal.fire("Success!", data.message || "File processed successfully!", "success");
                                setTimeout(() => location.reload(), 1500);
                            } else {
                                Swal.fire("Error!", data.error || "An error occurred during file processing.", "error");
                            }
                        })
                        .catch(error => {
                            Swal.fire("Upload Failed!", "An error occurred while uploading the file.", "error");
                            console.error("Fetch error:", error);
                        });
                }
            });
        });

    </script>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.querySelectorAll('.delete-btn').forEach(button => {
            button.addEventListener('click', function () {
                let filename = this.getAttribute('data-filename');

                Swal.fire({
                    title: "Are you sure?",
                    text: "You won't be able to revert this!",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#d33",
                    cancelButtonColor: "#3085d6",
                    confirmButtonText: "Yes, delete it!"
                }).then((result) => {
                    if (result.isConfirmed) {
                        fetch('delete_file.php', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                            body: 'filename=' + encodeURIComponent(filename)
                        })
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    Swal.fire("Deleted!", "The file has been deleted.", "success").then(() => {
                                        location.reload();
                                    });
                                } else {
                                    Swal.fire("Error!", data.message, "error");
                                }
                            });
                    }
                });
            });
        });
    </script>
    <script>
        // Event listener for the Edit button to open the modal
        document.querySelectorAll('.edit-btn').forEach(button => {
            button.addEventListener('click', function () {
                const fileId = this.getAttribute('data-id');
                const fileGroup = this.getAttribute('data-filegroup');
                const filename = this.getAttribute('data-filename');

                // Set modal fields with current data
                document.getElementById('fileId').value = fileId;
                document.getElementById('updateFileGroup').value = fileGroup;
                document.getElementById('updateFilename').value = filename;

                // Show the modal
                $('#updateModal').modal('show');
            });
        });

        // Handle the update form submission
        document.getElementById('updateForm').addEventListener('submit', function (event) {
            event.preventDefault();

            const fileId = document.getElementById('fileId').value;
            const fileGroup = document.getElementById('updateFileGroup').value;
            const filename = document.getElementById('updateFilename').value;

            // Send an AJAX request to update the data
            fetch('update_file.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `id=${fileId}&file_group=${encodeURIComponent(fileGroup)}&filename=${encodeURIComponent(filename)}`
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Update was successful
                        Swal.fire("Updated!", "The file info has been updated.", "success").then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire("Error!", data.message, "error");
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

</body>

</html>