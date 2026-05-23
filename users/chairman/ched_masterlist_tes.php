<?php
include 'config/session.php';
require_once __DIR__ . '/../../inc/schogms_upload_format.php';
require_once __DIR__ . '/../../inc/schogms_ched_masterlist_upload.php';
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
<?php schogms_loading_screen_once(); ?>

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
    <?php require_once __DIR__ . '/inc/chairman_nav.php'; schogms_chairman_shell_open('TES masterlist'); ?>

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
                        <?php schogms_ched_masterlist_upload_button(); ?>
                        <div class="customize-input float-right ml-2">
                            <a href="upload_ched_tes.php" class="btn btn-outline-secondary btn-rounded btn-sm">Full upload guide</a>
                        </div>
                    </div>
                </div>
            </div>
            <?php
            schogms_ched_masterlist_upload_modal([
                'program' => 'tes',
                'role' => 'chairman',
                'base_path' => '../../',
                'campus_editable' => true,
                'campuses' => schogms_ched_masterlist_upload_campus_options($conn),
                'submit_url' => 'submit_ched_masterlist_tes.php',
                'guide_url' => 'upload_ched_tes.php',
            ]);
            ?>
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
                                        $sheet_name = isset($_GET['campus']) ? $_GET['campus'] : '';

                                        // Query to get distinct sheet names from ched_masterlist_tes table
                                        $sheetNameQuery = "SELECT DISTINCT campus FROM ched_masterlist_tes";
                                        $sheetNameResult = $conn->query($sheetNameQuery);
                                        if (!$sheetNameResult) {
                                            die("Query failed: " . $conn->error);
                                        }

                                        // Query to retrieve data from ched_masterlist
                                        $query = "SELECT * FROM ched_masterlist_tes";
                                        if (!empty($sheet_name)) {
                                            $query .= " WHERE campus = '" . $conn->real_escape_string($sheet_name) . "'";
                                        }

                                        $result = $conn->query($query);
                                        if (!$result) {
                                            die("Query failed: " . $conn->error);
                                        }
                                        ?>

                                        <div class="table-responsive">
                                            <form method="GET" action="">
                                                <label for="sheetNameFilter">Select Campus:</label>
                                                <select id="sheetNameFilter" name="campus" class="form-control">
                                                    <option value="">All</option>
                                                    <?php while ($row = $sheetNameResult->fetch_assoc()): ?>
                                                        <option value="<?php echo htmlspecialchars($row['campus']); ?>"
                                                            <?php echo ($sheet_name == $row['campus']) ? 'selected' : ''; ?>>
                                                            <?php echo htmlspecialchars($row['campus']); ?>
                                                        </option>
                                                    <?php endwhile; ?>
                                                </select>
                                                <br>
                                                <button type="submit" class="btn btn-success">Apply Filter</button>
                                                <br><br>
                                            </form>

                                            <table id="zero_config" class="table table-striped table-bordered no-wrap">
                                                <thead>
                                                    <tr>
                                                        <th>CAMPUS</th>
                                                        <th>SEQ</th>
                                                        <th>APP NO</th>
                                                        <th>LASTNAME</th>
                                                        <th>FIRSTNAME</th>
                                                        <th>EXT</th>
                                                        <th>MIDDLENAME</th>
                                                        <th>SEX</th>
                                                        <th>COURSE</th>
                                                        <th>YR</th>
                                                        <th>STREET</th>
                                                        <th>TOWN/CITY</th>
                                                        <th>CONTACT</th>
                                                        <th>BATCH NO</th>
                                                        <th>UPLOAD TIME</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php while ($row = $result->fetch_assoc()): ?>
                                                        <tr>
                                                            <td><?php echo htmlspecialchars($row['campus']); ?></td>
                                                            <td><?php echo htmlspecialchars($row['seq']); ?></td>
                                                            <td><?php echo htmlspecialchars($row['app_no']); ?></td>
                                                            <td><?php echo htmlspecialchars($row['lastname']); ?></td>
                                                            <td><?php echo htmlspecialchars($row['firstname']); ?></td>
                                                            <td><?php echo htmlspecialchars($row['ext']); ?></td>
                                                            <td><?php echo htmlspecialchars($row['middlename']); ?></td>
                                                            <td><?php echo htmlspecialchars($row['sex']); ?></td>
                                                            <td><?php echo htmlspecialchars($row['course_program_enrolled']); ?>
                                                            </td>
                                                            <td><?php echo htmlspecialchars($row['year_level']); ?></td>
                                                            <td><?php echo htmlspecialchars($row['street']); ?></td>
                                                            <td><?php echo htmlspecialchars($row['town_city']); ?></td>
                                                            <td><?php echo htmlspecialchars($row['contact']); ?></td>
                                                            <td><?php echo htmlspecialchars($row['batch_no']); ?></td>
                                                            <td><?php echo htmlspecialchars($row['upload_time']); ?></td>
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
<?php
    schogms_chairman_shell_close(['datatables' => true, 'sweetalert' => true]);
    schogms_ched_masterlist_upload_scripts();
?>
</body>

</html>