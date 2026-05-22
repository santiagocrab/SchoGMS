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
    <?php require_once __DIR__ . '/inc/coordinator_nav.php'; schogms_coordinator_shell_open('Validate TES'); ?>

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
                                    <?php require __DIR__ . '/inc/validate_tes_body.php'; ?>
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
    <script>
        $(document).ready(function () {
            var btn = document.getElementById('bulkValidateTesBtn');
            if (!btn) return;
            btn.addEventListener('click', function () {
                var sheetName = btn.getAttribute('data-sheet-name');
                if (!sheetName) return;
                Swal.fire({
                    title: 'Validating all scholars…',
                    text: 'Please wait.',
                    icon: 'info',
                    allowOutsideClick: false,
                    showConfirmButton: false,
                    willOpen: function () { Swal.showLoading(); }
                });
                $.ajax({
                    url: 'bulk_validate.php',
                    method: 'POST',
                    data: { sheet_name: sheetName, program: 'tes' },
                    dataType: 'json'
                }).done(function (response) {
                    if (response.success) {
                        Swal.fire({ title: 'Done', text: response.message, icon: 'success' }).then(function () {
                            var url = new URL(window.location.href);
                            url.searchParams.set('bulk', '1');
                            window.location.href = url.toString();
                        });
                    } else {
                        Swal.fire({ title: 'Error', text: response.error || 'Failed', icon: 'error' });
                    }
                }).fail(function () {
                    Swal.fire({ title: 'Error', text: 'Request failed.', icon: 'error' });
                });
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
    <?php require_once __DIR__ . '/inc/assets.php'; schogms_coordinator_footer_scripts(['datatables' => true]); ?>
    <?php
    $mlProgram = 'tes';
    $mlCampus = trim((string) ($sheet_name ?? ''));
    require __DIR__ . '/inc/masterlist_edit_ui.php';
    ?>

</body>

</html>