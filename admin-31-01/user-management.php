<?php
include 'config/conn.php';
include 'config/session.php';
?>
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
    <link rel="icon" type="image/png" sizes="16x16" href="../assets/images/logo.png">
    <title> Scholarship and Grants Management System | SchoGMS </title>
    <!-- Custom CSS -->

    <!-- This page plugin CSS -->
    <link href="../assets/extra-libs/datatables.net-bs4/css/dataTables.bootstrap4.css" rel="stylesheet">
    <link href="../assets/extra-libs/c3/c3.min.css" rel="stylesheet">
    <link href="../assets/libs/chartist/dist/chartist.min.css" rel="stylesheet">
    <link href="../assets/extra-libs/jvector/jquery-jvectormap-2.0.2.css" rel="stylesheet" />
    <!-- Custom CSS -->
    <link href="../dist/css/style.min.css" rel="stylesheet">
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
    <div class="preloader">
        <div class="lds-ripple">
            <div class="lds-pos"></div>
            <div class="lds-pos"></div>
        </div>
    </div>
    <!-- ============================================================== -->
    <!-- Main wrapper - style you can find in pages.scss -->
    <!-- ============================================================== -->
    <div id="main-wrapper" data-theme="light" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full"
        data-sidebar-position="fixed" data-header-position="fixed" data-boxed-layout="full">
        <!-- ============================================================== -->
        <!-- Topbar header - style you can find in pages.scss -->
        <!-- ============================================================== -->
        <header class="topbar" data-navbarbg="skin6">
            <nav class="navbar top-navbar navbar-expand-md">
                <div class="navbar-header" data-logobg="skin6">
                    <!-- This is for the sidebar toggle which is visible on mobile only -->
                    <a class="nav-toggler waves-effect waves-light d-block d-md-none" href="javascript:void(0)"><i
                            class="ti-menu ti-close"></i></a>
                    <!-- ============================================================== -->
                    <!-- Logo -->
                    <!-- ============================================================== -->
                    <div class="navbar-brand">
                        <!-- Logo icon -->
                        <a href="dashboard.php">
                            <b class="logo-icon">
                                <!-- Dark Logo icon -->
                                <img src="../assets/images/logo.png" style="height: auto; width: 200px;" alt="homepage"
                                    class="dark-logo" />
                                <!-- Light Logo icon -->
                                <img src="../assets/images/logo.png" alt="homepage" class="light-logo" />
                            </b>
                        </a>
                    </div>
                    <!-- ============================================================== -->
                    <!-- End Logo -->
                    <!-- ============================================================== -->
                    <!-- ============================================================== -->
                    <!-- Toggle which is visible on mobile only -->
                    <!-- ============================================================== -->
                    <a class="topbartoggler d-block d-md-none waves-effect waves-light" href="javascript:void(0)"
                        data-toggle="collapse" data-target="#navbarSupportedContent"
                        aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation"><i
                            class="ti-more"></i></a>
                </div>
                <!-- ============================================================== -->
                <!-- End Logo -->
                <!-- ============================================================== -->
                <div class="navbar-collapse collapse" id="navbarSupportedContent">
                    <!-- ============================================================== -->
                    <!-- toggle and nav items -->
                    <!-- ============================================================== -->
                    <ul class="navbar-nav float-left mr-auto ml-3 pl-1">
                        <!-- Notification -->
                        <h3 class="page-title text-truncate text-dark font-weight-medium mb-1">Scholarship and Grants
                            Management System</h3>

                        <!-- End Notification -->
                        <!-- ============================================================== -->
                        <!-- create new -->
                        <!-- ============================================================== -->

                    </ul>
                    <!-- ============================================================== -->
                    <!-- Right side toggle and nav items -->
                    <!-- ============================================================== -->
                    <ul class="navbar-nav float-right">
                        <!-- ============================================================== -->
                        <!-- User profile and search -->
                        <!-- ============================================================== -->
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="javascript:void(0)" data-toggle="dropdown"
                                aria-haspopup="true" aria-expanded="false">
                                <img src="../assets/images/users/image.png" alt="user" class="rounded-circle"
                                    width="40">
                                <span class="ml-2 d-none d-lg-inline-block"><span>Hello,</span> <span
                                        class="text-dark"><?= $row['username']; ?></span> <i data-feather="chevron-down"
                                        class="svg-icon"></i></span>
                            </a>
                            <div class="dropdown-menu dropdown-menu-right user-dd animated flipInY">
                                <!-- <a class="dropdown-item" href="javascript:void(0)"><i data-feather="user"
                                        class="svg-icon mr-2 ml-1"></i>
                                    My Profile</a>
                                <div class="dropdown-divider"></div> -->
                                <a class="dropdown-item" href="javascript:void(0)"><i data-feather="power"
                                        class="svg-icon mr-2 ml-1"></i>
                                    Logout</a>
                            </div>
                        </li>
                        <!-- ============================================================== -->
                        <!-- User profile and search -->
                        <!-- ============================================================== -->
                    </ul>
                </div>
            </nav>
        </header>
        <!-- ============================================================== -->
        <!-- End Topbar header -->
        <!-- ============================================================== -->
        <!-- ============================================================== -->
        <!-- Left Sidebar - style you can find in sidebar.scss  -->
        <!-- ============================================================== -->
        <aside class="left-sidebar" data-sidebarbg="skin6">
            <!-- Sidebar scroll-->
            <div class="scroll-sidebar" data-sidebarbg="skin6">
                <!-- Sidebar navigation-->
                <nav class="sidebar-nav">
                    <ul id="sidebarnav">
                        <li class="sidebar-item"> <a class="sidebar-link sidebar-link" href="dashboard.php"
                                aria-expanded="false"><i data-feather="home" class="feather-icon"></i><span
                                    class="hide-menu">Dashboard</span></a></li>
                        <li class="list-divider"></li>
                        <li class="sidebar-item"> <a class="sidebar-link sidebar-link" href="user-management.php"
                                aria-expanded="false"><i data-feather="users" class="feather-icon"></i><span
                                    class="hide-menu">User Management</span></a></li>
                        <!-- <li class="sidebar-item"> <a class="sidebar-link sidebar-link" href="data-management.php"
                                aria-expanded="false"><i data-feather="file" class="feather-icon"></i><span
                                    class="hide-menu">Data Management</span></a></li>
                        <li class="sidebar-item"> <a class="sidebar-link has-arrow" href="javascript:void(0)"
                                aria-expanded="false"><i data-feather="file-text" class="feather-icon"></i><span
                                    class="hide-menu">Report </span></a>
                            <ul aria-expanded="false" class="collapse  first-level base-level-line">
                                <li class="sidebar-item"><a href="logs.php" class="sidebar-link"><span
                                            class="hide-menu"> Logs
                                        </span></a>
                                </li>
                            </ul>
                        </li> -->
                    </ul>
                </nav>
                <!-- End Sidebar navigation -->
            </div>
            <!-- End Sidebar scroll-->
        </aside>
        <!-- ============================================================== -->
        <!-- End Left Sidebar - style you can find in sidebar.scss  -->
        <!-- ============================================================== -->
        <!-- ============================================================== -->
        <!-- Page wrapper  -->
        <!-- ============================================================== -->
        <div class="page-wrapper">
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
                                    <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a>
                                    </li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                    <div class="col-5 align-self-center">
                        <div class="customize-input float-right">
                            <button type="button" class="btn waves-effect waves-light btn-rounded btn-success"
                                data-toggle="modal" data-target="#userCreateModal">
                                Create User
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal fade" id="userCreateModal" tabindex="-1" role="dialog"
                aria-labelledby="userCreateModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="userCreateModalLabel">Create New User</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <form action="submit_user.php" method="post">
                                <!-- User Name -->
                                <div class="form-group">
                                    <label for="userName">Full Name</label>
                                    <input type="text" class="form-control" id="userName" name="userName"
                                        placeholder="Enter full name" required>
                                </div>

                                <!-- Email -->
                                <div class="form-group">
                                    <label for="userEmail">Email</label>
                                    <input type="email" class="form-control" id="userEmail" name="userEmail"
                                        placeholder="Enter email address" required>
                                </div>

                                <!-- Role Selection -->
                                <div class="form-group">
                                    <label for="userRole">Role</label>
                                    <select class="form-control" id="userRole" name="userRole" required>
                                        <option value="" disabled selected>Select role</option>
                                        <option value="coordinator">Coordinator</option>
                                        <option value="chairman">Chairman</option>
                                        <option value="registrar">Registrar</option>
                                    </select>
                                </div>

                                <!-- Submit Button -->
                                <div class="form-group">
                                    <button type="submit" class="btn btn-primary">Create User</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal -->
            <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

            <script>
                document.querySelector('form').addEventListener('submit', async (event) => {
                    event.preventDefault(); // Prevent default form submission

                    const formData = new FormData(event.target);

                    // Send form data to the server using Fetch API
                    const response = await fetch('submit_user.php', {
                        method: 'POST',
                        body: formData,
                    });

                    const result = await response.json();

                    if (result.success) {
                        // Show success alert
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: result.message,
                            timer: 2000,
                            showConfirmButton: false,
                        });

                        // Reset the form and close the modal
                        event.target.reset();
                        $('#userCreateModal').modal('hide');
                        setTimeout(() => {
                            location.reload();
                        }, 1500);
                    } else {
                        // Show error alert
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: result.message,
                        });
                    }
                });
            </script>

            <!-- Update User Modal -->
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
                <!-- Modal for updating user -->

                <!-- Modal for updating user -->

                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table id="zero_config" class="table table-striped table-bordered no-wrap">
                                        <thead>
                                            <tr>
                                                <th>Status</th>
                                                <th>Full Name</th>
                                                <th>Email</th>
                                                <th>Date Created</th>
                                                <th>Role</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <!-- Loop through each user to generate table rows -->
                                            <?php
                                            include 'config/conn.php';
                                            // Assuming you've fetched users from the database
                                            $sql = "SELECT user_id, name, email, created_at, role, status FROM users";
                                            $result = $conn->query($sql);

                                            if ($result->num_rows > 0) {
                                                while ($row = $result->fetch_assoc()) {
                                                    $statusBadge = '';
                                                    switch ($row['status']) {
                                                        case 'active':
                                                            $statusBadge = '<span class="badge badge-success btn-rounded">Active</span>';
                                                            break;
                                                        case 'pending':
                                                            $statusBadge = '<span class="badge badge-warning btn-rounded">Pending</span>';
                                                            break;
                                                        case 'restricted':
                                                            $statusBadge = '<span class="badge badge-secondary btn-rounded">Restricted</span>';
                                                            break;
                                                    }
                                                    // Display user data in the table
                                                    echo '<tr>';
                                                    echo '<td>' . $statusBadge . '</td>';
                                                    echo '<td>' . $row['name'] . '</td>';
                                                    echo '<td>' . $row['email'] . '</td>';
                                                    echo '<td>' . $row['created_at'] . '</td>';
                                                    echo '<td>' . ucfirst($row['role']) . '</td>';
                                                    echo '<td>';
                                                    echo '<button class="btn btn-sm btn-primary" data-toggle="modal" data-target="#updateUserModal" onclick="loadUserData(' . $row['user_id'] . ')">Update</button> ';
                                                    echo '<button class="btn btn-sm btn-danger" onclick="toggleUserRestriction(' . $row['user_id'] . ', \'' . $row['status'] . '\')">Restrict</button> ';
                                                    echo '<button class="btn btn-sm btn-danger" onclick="toggleUserDelete(' . $row['user_id'] . ')"> <i class="fa fa-trash"></i></button>';

                                                    echo '</td>';
                                                    echo '</td>';
                                                    echo '</tr>';
                                                }
                                            } else {
                                                echo '<tr><td colspan="6">No users found.</td></tr>';
                                            }
                                            ?>
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <th>Status</th>
                                                <th>Full Name</th>
                                                <th>Email</th>
                                                <th>Date Created</th>
                                                <th>Role</th>
                                                <th>Action</th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Update User Modal -->
                <div class="modal fade" id="updateUserModal" tabindex="-1" role="dialog"
                    aria-labelledby="updateUserModalLabel" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="updateUserModalLabel">Update User</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <form id="updateUserForm">
                                    <div class="form-group">
                                        <label for="newUserName">New Username</label>
                                        <input type="text" class="form-control" id="newUserName" name="newUserName"
                                            required>
                                    </div>
                                    <div class="form-group">
                                        <label for="newUserEmail">New Email</label>
                                        <input type="text" class="form-control" id="newUserEmail" name="newUserEmail"
                                            required>
                                    </div>
                                    <div class="form-group">
                                        <label for="newUserRole">Role</label>
                                        <select class="form-control" id="newUserRole" name="newUserRole" required>
                                            <option value="coordinator">Coordinator</option>
                                            <option value="chairman">Chairman</option>
                                            <option value="registrar">Registrar</option>
                                        </select>
                                    </div>
                                    <input type="hidden" id="userId" name="userId">
                                    <button type="submit" class="btn btn-primary">Update Name</button>
                                </form>
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
                All Rights Reserved 2025. Scholarship and Grants Management System <a href="">(SchoGMS)</a>.
            </footer>>
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
    <script src="../assets/libs/jquery/dist/jquery.min.js"></script>
    <script src="../assets/libs/popper.js/dist/umd/popper.min.js"></script>
    <script src="../assets/libs/bootstrap/dist/js/bootstrap.min.js"></script>
    <!-- apps -->

    <!-- apps -->
    <script src="../dist/js/app-style-switcher.js"></script>
    <script src="../dist/js/feather.min.js"></script>
    <script src="../assets/libs/perfect-scrollbar/dist/perfect-scrollbar.jquery.min.js"></script>
    <script src="../dist/js/sidebarmenu.js"></script>
    <!--Custom JavaScript -->
    <script src="../dist/js/custom.min.js"></script>
    <!--This page JavaScript -->
    <script src="../assets/extra-libs/c3/d3.min.js"></script>
    <script src="../assets/extra-libs/c3/c3.min.js"></script>
    <script src="../assets/libs/chartist/dist/chartist.min.js"></script>
    <script src="../assets/libs/chartist-plugin-tooltips/dist/chartist-plugin-tooltip.min.js"></script>
    <script src="../assets/extra-libs/jvector/jquery-jvectormap-2.0.2.min.js"></script>
    <script src="../assets/extra-libs/jvector/jquery-jvectormap-world-mill-en.js"></script>
    <script src="../dist/js/pages/dashboards/dashboard1.min.js"></script>

    <!--This page plugins -->
    <script src="../assets/extra-libs/datatables.net/js/jquery.dataTables.min.js"></script>
    <script src="../dist/js/pages/datatable/datatable-basic.init.js"></script>
    <script>
        // Function to load user data and show the modal
        // Function to load user data and open the modal
        function loadUserData(userId) {
            $.ajax({
                url: 'get_user_data.php',
                type: 'GET',
                data: { userId: userId },
                dataType: 'json',
                success: function (userData) {
                    console.log(userData); // Debugging: Check response in console

                    if (userData.success) {
                        // Populate the modal fields
                        $('#userId').val(userData.user_id);  // Hidden user ID
                        $('#newUserName').val(userData.name);  // Fill username field
                        $('#newUserEmail').val(userData.email);  // Fill username field
                        $('#newUserRole').val(userData.role);  // Fill username field
                        $('#updateUserModal').modal('show'); // Open the modal
                    } else {
                        Swal.fire('Error', 'User not found.', 'error');
                    }
                },
                error: function (xhr, status, error) {
                    console.error("AJAX Error: " + status + ": " + error);
                    Swal.fire('Error', 'An error occurred while fetching user data.', 'error');
                }
            });
        }
        // Submit Update Form (AJAX)
        $('#updateUserForm').submit(function (event) {
            event.preventDefault(); // Prevent default form submission
            let formData = $(this).serialize(); // Serialize form data
            $.ajax({
                url: 'update_user_name.php',
                type: 'POST',
                data: formData,
                dataType: 'json',
                success: function (response) {
                    if (response.success) {
                        Swal.fire('Success', 'User name updated successfully!', 'success')
                            .then(() => {
                                location.reload(); // Reload page to see changes
                            });
                    } else {
                        Swal.fire('Error', 'Failed to update user name.', 'error');
                    }
                },
                error: function () {
                    Swal.fire('Error', 'An error occurred during the update.', 'error');
                }
            });
        });

    </script>
    <script>
        // Function to restrict a user
        function toggleUserRestriction(userId, currentStatus) {
            let newStatus = (currentStatus === 'restricted') ? 'active' : 'restricted'; // Toggle the status

            // SweetAlert confirmation
            Swal.fire({
                title: 'Are you sure?',
                text: `You are about to change the user status to ${newStatus}.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: `Yes, set to ${newStatus}!`,
                cancelButtonText: 'No, cancel!',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    // If confirmed, proceed to update the user status
                    $.ajax({
                        url: 'update_user_status.php', // Your PHP script to update status
                        type: 'POST',
                        data: {
                            userId: userId,
                            status: newStatus // The new status
                        },
                        dataType: 'json',
                        success: function (response) {
                            if (response.success) {
                                Swal.fire(
                                    'Updated!',
                                    `User status changed to ${newStatus}.`,
                                    'success'
                                ).then(() => {
                                    location.reload(); // Reload page to reflect changes
                                });
                            } else {
                                Swal.fire(
                                    'Failed!',
                                    'Could not update the user status. Try again later.',
                                    'error'
                                );
                            }
                        },
                        error: function () {
                            Swal.fire(
                                'Error!',
                                'An error occurred while updating the status.',
                                'error'
                            );
                        }
                    });
                } else {
                    Swal.fire(
                        'Cancelled',
                        'User status remains unchanged.',
                        'info'
                    );
                }
            });
        }

    </script>
    <script>
        function toggleUserDelete(userId) {
            // SweetAlert confirmation
            Swal.fire({
                title: 'Are you sure?',
                text: `You are about to Delete User.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: `Yes, Delete User!`,
                cancelButtonText: 'No, cancel!',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    // If confirmed, proceed to delete the user
                    $.ajax({
                        url: 'delete_user.php', // PHP script to handle user deletion
                        type: 'POST',
                        data: {
                            userId: userId
                        },
                        dataType: 'json',
                        success: function (response) {
                            if (response.success) {
                                Swal.fire('Success', 'User Deleted successfully!', 'success')
                                    .then(() => {
                                        setTimeout(() => {
                                            location.reload(); // Reload page to see changes
                                        }, 1500);
                                    });
                            } else {
                                Swal.fire('Failed!', 'Could not Delete the user. Try again later.', 'error');
                            }
                        },
                        error: function () {
                            Swal.fire('Error!', 'An error occurred while deleting the user.', 'error');
                        }
                    });
                } else {
                    Swal.fire('Cancelled', 'User status remains unchanged.', 'info');
                }
            });
        }

    </script>
</body>

</html>