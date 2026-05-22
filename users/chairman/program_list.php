<?php include 'config/session.php'; ?>
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

    <!-- This page plugin CSS --><!-- Custom CSS -->
        <?php require_once __DIR__ . '/inc/assets.php'; schogms_chairman_head(true); ?>

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
    <?php require_once __DIR__ . '/inc/chairman_nav.php'; schogms_chairman_shell_open('Program list'); ?>

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
                            <form id="uploadForm" enctype="multipart/form-data" method="POST">
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
            <div class="modal fade" id="updateModal2" tabindex="-1" aria-labelledby="updateModalLabel"
                aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="updateModalLabel">Update File Info</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form id="updateForm2">
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
                                        require 'config/conn.php';

                                        // Get filters from GET request
                                        $sheet_name = isset($_GET['sheet_name']) ? $_GET['sheet_name'] : '';

                                        // Query to get distinct sheet names from ched_masterlist table
                                        $sheetNameQuery = "SELECT DISTINCT sheet_name FROM ched_masterlist";
                                        $sheetNameResult = $conn->query($sheetNameQuery);
                                        if (!$sheetNameResult) {
                                            die("Query failed: " . $conn->error);
                                        }

                                        // Query to retrieve data from ched_masterlist
                                        $query = "SELECT DISTINCT id, file_group, filename, COUNT(*) as total_entries 
                                        FROM ched_masterlist 
                                        GROUP BY file_group, filename";

                                        if (!empty($sheet_name)) {
                                            $query .= " WHERE sheet_name = '" . $conn->real_escape_string($sheet_name) . "'";
                                        }

                                        $result = $conn->query($query);
                                        if (!$result) {
                                            die("Query failed: " . $conn->error);
                                        }
                                        ?>

                                        <div class="table-responsive">
                                            <table id="zero_config" class="table table-striped table-bordered">
                                                <thead>
                                                    <tr>
                                                        <th>File Group</th>
                                                        <th>Filename</th>
                                                        <th>Total Entries</th>
                                                        <th>Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php
                                                    $result = $conn->query($query);
                                                    while ($row = $result->fetch_assoc()): ?>
                                                        <tr>
                                                            <td><?php echo htmlspecialchars($row['file_group']); ?></td>
                                                            <td><?php echo htmlspecialchars($row['filename']); ?></td>
                                                            <td><?php echo htmlspecialchars($row['total_entries']); ?></td>
                                                            <td>
                                                                <div class="form-group" style="display:flex;">
                                                                    <button class="btn btn-info edit-btn btn-rounded"
                                                                        data-id="<?php echo $row['id']; ?>"
                                                                        data-filegroup="<?php echo htmlspecialchars($row['file_group']); ?>"
                                                                        data-filename="<?php echo htmlspecialchars($row['filename']); ?>">
                                                                        Edit
                                                                    </button>
                                                                    <button class="btn btn-danger delete-btn btn-rounded"
                                                                        data-filename="<?php echo htmlspecialchars($row['filename']); ?>">
                                                                        <i class="fa fa-trash"></i>
                                                                    </button>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    <?php endwhile; ?>
                                                </tbody>
                                            </table>
                                        </div>


                                        <?php
                                        $conn->close();
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
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="row">
                                    <div class="table-responsive">
                                        <?php
                                        require 'config/conn.php';

                                        // Get filters from GET request
                                        $sheet_name = isset($_GET['sheet_name']) ? $_GET['sheet_name'] : '';

                                        // Query to get distinct sheet names from ched_masterlist table
                                        $sheetNameQuery = "SELECT DISTINCT campus FROM ched_masterlist_tes";
                                        $sheetNameResult = $conn->query($sheetNameQuery);
                                        if (!$sheetNameResult) {
                                            die("Query failed: " . $conn->error);
                                        }

                                        // Query to retrieve data from ched_masterlist
                                        $query = "SELECT DISTINCT id, file_group, filename, COUNT(*) as total_entries 
                                        FROM ched_masterlist_tes 
                                        GROUP BY file_group, filename";

                                        if (!empty($sheet_name)) {
                                            $query .= " WHERE campus = '" . $conn->real_escape_string($sheet_name) . "'";
                                        }

                                        $result = $conn->query($query);
                                        if (!$result) {
                                            die("Query failed: " . $conn->error);
                                        }
                                        ?>

                                        <div class="table-responsive">
                                            <table id="zero_config" class="table table-striped table-bordered">
                                                <thead>
                                                    <tr>
                                                        <th>File Group</th>
                                                        <th>Filename</th>
                                                        <th>Total Entries</th>
                                                        <th>Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php
                                                    $result = $conn->query($query);
                                                    while ($row = $result->fetch_assoc()): ?>
                                                        <tr>
                                                            <td><?php echo htmlspecialchars($row['file_group']); ?></td>
                                                            <td><?php echo htmlspecialchars($row['filename']); ?></td>
                                                            <td><?php echo htmlspecialchars($row['total_entries']); ?></td>
                                                            <td>
                                                                <div class="form-group" style="display:flex;">
                                                                    <button class="btn btn-info edit-btn2 btn-rounded"
                                                                        data-id="<?php echo $row['id']; ?>"
                                                                        data-filegroup="<?php echo htmlspecialchars($row['file_group']); ?>"
                                                                        data-filename="<?php echo htmlspecialchars($row['filename']); ?>">
                                                                        Edit
                                                                    </button>
                                                                    <button class="btn btn-danger delete-btn2 btn-rounded"
                                                                        data-filename="<?php echo htmlspecialchars($row['filename']); ?>">
                                                                        <i class="fa fa-trash"></i>
                                                                    </button>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    <?php endwhile; ?>
                                                </tbody>
                                            </table>
                                        </div>


                                        <?php
                                        $conn->close();
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
<!-- ============================================================== -->
                <!-- End footer -->
                <!-- ============================================================== -->
            </div>
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


        <script>
            // Event listener for the Edit button to open the modal
            document.querySelectorAll('.edit-btn2').forEach(button => {
                button.addEventListener('click', function () {
                    const fileId = this.getAttribute('data-id');
                    const fileGroup = this.getAttribute('data-filegroup');
                    const filename = this.getAttribute('data-filename');

                    // Set modal fields with current data
                    document.getElementById('fileId').value = fileId;
                    document.getElementById('updateFileGroup').value = fileGroup;
                    document.getElementById('updateFilename').value = filename;

                    // Show the modal
                    $('#updateModal2').modal('show');
                });
            });

            // Handle the update form submission
            document.getElementById('updateForm2').addEventListener('submit', function (event) {
                event.preventDefault();

                const fileId = document.getElementById('fileId').value;
                const fileGroup = document.getElementById('updateFileGroup').value;
                const filename = document.getElementById('updateFilename').value;

                // Send an AJAX request to update the data
                fetch('update_file2.php', {
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

        <script>
            document.querySelectorAll('.delete-btn2').forEach(button => {
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
                            fetch('delete_file2.php', {
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
            <?php schogms_chairman_shell_close(); ?>
    <?php require_once __DIR__ . '/inc/assets.php'; schogms_chairman_footer_scripts(['datatables' => true, 'sweetalert' => true]); ?>
</body>

</html>