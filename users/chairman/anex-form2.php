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
    <style>
        #annexPreviewModal .modal-dialog { max-width: 96%; margin: 1rem auto; }
        #annexPreviewModal .modal-body {
            max-height: calc(100vh - 140px);
            overflow: auto;
            background: #f8fafc;
        }
        #annexPreviewBody .annex-preview-meta {
            font-size: 0.9rem;
            color: #475569;
            margin-bottom: 0.75rem;
        }
        #annexPreviewBody .table-responsive {
            background: #fff;
            border: 1px solid #dee2e6;
            border-radius: 6px;
        }
        #annexPreviewBody table { font-size: 12px; margin-bottom: 0; }
        #annexPreviewBody table th {
            position: sticky;
            top: 0;
            background: #eef2ff;
            z-index: 1;
        }
        .btn-view-annex { min-width: 4.5rem; }
    </style>

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
    <?php require_once __DIR__ . '/inc/chairman_nav.php'; schogms_chairman_shell_open('Annex 7 review'); ?>

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
                                    <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                                    <li class="breadcrumb-item active">Annex 7 review</li>
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
                                        $pendingAnnex = 0;
                                        $pRes = $conn->query("SELECT COUNT(*) AS n FROM file_submissions WHERE status = 'Pending'");
                                        if ($pRes) {
                                            $pendingAnnex = (int) ($pRes->fetch_assoc()['n'] ?? 0);
                                        }
                                        $result = $conn->query(
                                            'SELECT id, user_email, campus, file_name, file_path, uploaded_at, status
                                             FROM file_submissions ORDER BY uploaded_at DESC'
                                        );
                                        ?>
                                        <?php if ($pendingAnnex > 0): ?>
                                        <div class="alert alert-warning d-flex align-items-center mb-3" role="alert">
                                            <i data-feather="bell" class="feather-icon mr-2"></i>
                                            <span><strong><?= $pendingAnnex ?></strong> submission(s) awaiting your review. Use <strong>View</strong> to scroll through the file before approving.</span>
                                        </div>
                                        <?php endif; ?>
                                        <table id="zero_config" class="table table-striped table-bordered no-wrap">
                                            <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>User Email</th>
                                                <th>Campus</th>
                                                <th>File Name</th>
                                                <th>Preview</th>
                                                <th>Uploaded At</th>
                                                <th>Status</th>
                                                <th>Action</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <?php while ($row = $result->fetch_assoc()):
                                                $fid = (int) $row['id'];
                                                $fname = (string) $row['file_name'];
                                                $ext = strtolower(pathinfo($fname, PATHINFO_EXTENSION));
                                                $isPending = ($row['status'] ?? '') === 'Pending';
                                                $downloadUrl = '../coordinator/' . htmlspecialchars((string) $row['file_path'], ENT_QUOTES, 'UTF-8');
                                            ?>
                                                <tr class="<?= $isPending ? 'table-warning' : '' ?>">
                                                    <td><?= $fid ?></td>
                                                    <td><?= htmlspecialchars((string) $row['user_email'], ENT_QUOTES, 'UTF-8') ?></td>
                                                    <td><?= htmlspecialchars((string) $row['campus'], ENT_QUOTES, 'UTF-8') ?></td>
                                                    <td><?= htmlspecialchars($fname, ENT_QUOTES, 'UTF-8') ?></td>
                                                    <td class="text-nowrap">
                                                        <button type="button"
                                                            class="btn btn-sm btn-primary btn-view-annex"
                                                            data-id="<?= $fid ?>"
                                                            data-name="<?= htmlspecialchars($fname, ENT_QUOTES, 'UTF-8') ?>"
                                                            data-campus="<?= htmlspecialchars((string) $row['campus'], ENT_QUOTES, 'UTF-8') ?>"
                                                            data-email="<?= htmlspecialchars((string) $row['user_email'], ENT_QUOTES, 'UTF-8') ?>"
                                                            data-ext="<?= htmlspecialchars($ext, ENT_QUOTES, 'UTF-8') ?>">
                                                            View
                                                        </button>
                                                        <?php if ($isPending): ?>
                                                        <a class="btn btn-sm btn-outline-secondary"
                                                            href="<?= $downloadUrl ?>"
                                                            download
                                                            title="Download file">Save</a>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td><?= htmlspecialchars((string) $row['uploaded_at'], ENT_QUOTES, 'UTF-8') ?></td>
                                                    <td><?= htmlspecialchars((string) $row['status'], ENT_QUOTES, 'UTF-8') ?></td>
                                                    <td>
                                                        <?php if ($isPending): ?>
                                                        <form class="statusForm d-inline" method="post">
                                                            <input type="hidden" name="file_id" value="<?= $fid ?>">
                                                            <button type="button" class="updateStatusBtn btn btn-sm btn-success"
                                                                data-status="Approved">Approve</button>
                                                            <button type="button" class="updateStatusBtn btn btn-sm btn-danger"
                                                                data-status="Rejected">Decline</button>
                                                        </form>
                                                        <?php else: ?>
                                                        <span class="text-muted small">—</span>
                                                        <?php endif; ?>
                                                    </td>
                                                </tr>
                                            <?php endwhile; ?>
                                            </tbody>
                                        </table>
                                        <?php $conn->close(); ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            <!-- Annex file preview (scroll in modal, no forced download) -->
            <div class="modal fade" id="annexPreviewModal" tabindex="-1" role="dialog" aria-labelledby="annexPreviewTitle" aria-hidden="true">
                <div class="modal-dialog modal-xl" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="annexPreviewTitle">Annex 7 preview</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div id="annexPreviewBody">
                                <p class="text-muted mb-0">Loading…</p>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <a href="#" id="annexPreviewDownload" class="btn btn-outline-secondary btn-sm d-none" download>Download file</a>
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>

        <?php require_once __DIR__ . '/inc/assets.php'; schogms_chairman_footer_scripts(['datatables' => true, 'sweetalert' => true]); ?>
        <script src="https://cdn.sheetjs.com/xlsx-0.20.1/package/dist/xlsx.full.min.js"></script>
        <script>
            function annexEscapeHtml(text) {
                const d = document.createElement('div');
                d.textContent = text == null ? '' : String(text);
                return d.innerHTML;
            }

            function annexSheetToTable(sheet) {
                const rows = XLSX.utils.sheet_to_json(sheet, { header: 1, defval: '' });
                if (!rows.length) {
                    return '<p class="text-muted">This sheet is empty.</p>';
                }
                let html = '<div class="table-responsive"><table class="table table-sm table-bordered table-hover mb-0"><tbody>';
                rows.forEach((row, ri) => {
                    html += '<tr>';
                    (row || []).forEach((cell) => {
                        const tag = ri === 0 ? 'th' : 'td';
                        html += '<' + tag + '>' + annexEscapeHtml(cell) + '</' + tag + '>';
                    });
                    html += '</tr>';
                });
                html += '</tbody></table></div>';
                return html;
            }

            document.querySelectorAll('.btn-view-annex').forEach((btn) => {
                btn.addEventListener('click', async function () {
                    const id = this.getAttribute('data-id');
                    const name = this.getAttribute('data-name') || 'Submission';
                    const campus = this.getAttribute('data-campus') || '';
                    const email = this.getAttribute('data-email') || '';
                    const ext = (this.getAttribute('data-ext') || '').toLowerCase();
                    const body = document.getElementById('annexPreviewBody');
                    const title = document.getElementById('annexPreviewTitle');
                    const dl = document.getElementById('annexPreviewDownload');
                    const viewUrl = 'view_annex_file.php?id=' + encodeURIComponent(id);

                    title.textContent = name;
                    body.innerHTML = '<p class="text-muted">Loading preview…</p>';
                    dl.href = viewUrl;
                    dl.setAttribute('download', name);
                    dl.classList.remove('d-none');
                    $('#annexPreviewModal').modal('show');

                    try {
                        const res = await fetch(viewUrl);
                        if (!res.ok) {
                            throw new Error('Could not load file (HTTP ' + res.status + ').');
                        }
                        const meta = '<div class="annex-preview-meta"><strong>Campus:</strong> ' + annexEscapeHtml(campus)
                            + ' &nbsp;|&nbsp; <strong>Coordinator:</strong> ' + annexEscapeHtml(email)
                            + ' &nbsp;|&nbsp; <span class="text-muted">Scroll to review all rows</span></div>';

                        if (ext === 'csv') {
                            const text = await res.text();
                            const wb = XLSX.read(text, { type: 'string' });
                            const sheet = wb.Sheets[wb.SheetNames[0]];
                            body.innerHTML = meta + annexSheetToTable(sheet);
                        } else if (ext === 'xlsx' || ext === 'xls') {
                            const buf = await res.arrayBuffer();
                            const wb = XLSX.read(buf, { type: 'array' });
                            const sheet = wb.Sheets[wb.SheetNames[0]];
                            body.innerHTML = meta + annexSheetToTable(sheet);
                        } else {
                            body.innerHTML = meta + '<p class="alert alert-info mb-0">Preview is only available for Excel (.xls, .xlsx) and CSV. Use <strong>Save</strong> to download this file.</p>';
                        }
                    } catch (err) {
                        body.innerHTML = '<p class="alert alert-danger mb-0">' + annexEscapeHtml(err.message || 'Failed to load preview.') + '</p>';
                    }
                    if (typeof feather !== 'undefined') {
                        feather.replace();
                    }
                });
            });

            document.querySelectorAll('.updateStatusBtn').forEach((button) => {
                button.addEventListener('click', function () {
                    const form = this.closest('.statusForm');
                    const fileId = form.querySelector("input[name='file_id']").value;
                    const status = this.getAttribute('data-status');

                    Swal.fire({
                        title: 'Are you sure?',
                        text: 'Mark this submission as "' + status + '"?',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Yes, ' + status,
                        cancelButtonText: 'Cancel',
                        reverseButtons: true
                    }).then((result) => {
                        if (!result.isConfirmed) return;
                        fetch('update_status.php', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                            body: 'file_id=' + encodeURIComponent(fileId) + '&status=' + encodeURIComponent(status)
                        })
                            .then((r) => r.json())
                            .then((data) => {
                                if (data.success) {
                                    Swal.fire({ title: 'Updated!', icon: 'success', timer: 2000, showConfirmButton: false });
                                    setTimeout(() => location.reload(), 1200);
                                } else {
                                    Swal.fire('Error!', data.error || 'Failed to update status.', 'error');
                                }
                            })
                            .catch(() => Swal.fire('Error!', 'An error occurred while updating.', 'error'));
                    });
                });
            });

            const uploadFormEl = document.getElementById('uploadForm');
            if (uploadFormEl) uploadFormEl.addEventListener('submit', function (event) {
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



            <?php schogms_chairman_shell_close(); ?>
</body>

</html>