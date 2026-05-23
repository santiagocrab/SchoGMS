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
    <!-- <div class="preloader">
        <div class="lds-ripple">
            <div class="lds-pos"></div>
            <div class="lds-pos"></div>
        </div>
    </div> -->
    <!-- ============================================================== -->
    <!-- Main wrapper - style you can find in pages.scss -->
    <!-- ============================================================== -->
    <?php
    require_once __DIR__ . '/inc/coordinator_nav.php';
    require_once __DIR__ . '/inc/masterlist_rows.php';
    require_once __DIR__ . '/inc/ched_masterlist_import.php';

    $program = strtolower(trim((string) ($_GET['program'] ?? 'tdp')));
    if (!in_array($program, ['tdp', 'tes'], true)) {
        $program = 'tdp';
    }

    $campus = trim((string) ($sheet_name ?? ''));
    $filterFilename = trim((string) ($_GET['filename'] ?? ''));
    $filterFileGroup = trim((string) ($_GET['file_group'] ?? ''));
    $progLabel = $program === 'tes' ? 'TES' : 'TDP';
    $masterlistPage = $program === 'tes' ? 'ched_masterlist_tes.php' : 'ched_masterlist.php';

    $filterOptions = ['filenames' => [], 'file_groups' => []];
    $enrollmentRows = [];
    $loadError = '';

    if ($campus === '') {
        $loadError = 'No campus assigned to your account.';
    } elseif ($conn instanceof mysqli) {
        $filterOptions = schogms_coordinator_ched_filter_options($conn, $campus, $program);
        $listData = $program === 'tes'
            ? schogms_coordinator_ched_tes_rows($conn, $campus)
            : schogms_coordinator_ched_tdp_rows($conn, $campus);
        $enrollmentRows = $listData['rows'];
        $loadError = $listData['error'];
        $enrollmentRows = schogms_coordinator_ched_apply_row_filters(
            $conn,
            $campus,
            $enrollmentRows,
            $filterFilename,
            $filterFileGroup
        );
    }

    $enrolledCount = 0;
    $notEnrolledCount = 0;
    $chedStatusCount = 0;
    foreach ($enrollmentRows as $r) {
        if (($r['enrollment_status'] ?? '') === 'Enrolled') {
            $enrolledCount++;
        } else {
            $notEnrolledCount++;
        }
        if (schogms_ched_import_status_value($r) !== '') {
            $chedStatusCount++;
        }
    }

    schogms_coordinator_shell_open('Enrollment Status');
    ?>

            <div class="page-breadcrumb">
                <div class="row">
                    <div class="col-12">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb m-0 p-0">
                                <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                                <li class="breadcrumb-item active">Enrollment status</li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>

            <div class="container-fluid">
                <ul class="nav nav-pills mb-3">
                    <li class="nav-item">
                        <a class="nav-link <?= $program === 'tdp' ? 'active' : '' ?>"
                           href="enrollment_status.php?program=tdp">TDP enrollment</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= $program === 'tes' ? 'active' : '' ?>"
                           href="enrollment_status.php?program=tes">TES enrollment</a>
                    </li>
                </ul>

                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <?php if ($loadError !== ''): ?>
                                    <div class="alert alert-warning"><?= htmlspecialchars($loadError, ENT_QUOTES, 'UTF-8') ?></div>
                                <?php else: ?>
                                    <h4 class="card-title">Enrollment status — <?= htmlspecialchars($progLabel, ENT_QUOTES, 'UTF-8') ?> scholars</h4>
                                    <p class="text-muted mb-3">
                                        Campus: <strong><?= htmlspecialchars($campus, ENT_QUOTES, 'UTF-8') ?></strong>.
                                        <strong>Enrollment status</strong> is computed from uploaded <strong>COR</strong> and <strong>COG</strong>
                                        (both required for <span class="badge badge-success">Enrolled</span>).
                                        File group filters include coordinator CHED uploads, <strong>registrar masterlist</strong> batches, and registrar <strong>COR/COG</strong> uploads.
                                        <?php if ($program === 'tdp'): ?>
                                        <strong>CHED status (import)</strong> comes from column <em>Status of enrollment</em> in your TDP masterlist Excel (column M).
                                        <?php endif; ?>
                                    </p>
                                    <div class="mb-3">
                                        <span class="badge badge-success mr-2">Enrolled (COR+COG): <?= (int) $enrolledCount ?></span>
                                        <span class="badge badge-warning mr-2">Not enrolled: <?= (int) $notEnrolledCount ?></span>
                                        <?php if ($program === 'tdp'): ?>
                                            <span class="badge badge-info mr-2">With CHED status: <?= (int) $chedStatusCount ?></span>
                                        <?php endif; ?>
                                        <span class="badge badge-secondary">Total: <?= count($enrollmentRows) ?></span>
                                    </div>
                                    <?php if ($program === 'tdp' && count($enrollmentRows) > 0 && $chedStatusCount === 0): ?>
                                        <div class="alert alert-info py-2">
                                            No CHED import status is stored yet for these rows. Re-upload the masterlist via
                                            <a href="ched_masterlist.php">TDP Masterlist</a> and ensure the Excel file includes
                                            <strong>Status of enrollment</strong> (column M).
                                        </div>
                                    <?php endif; ?>

                                    <form method="get" action="" class="row mb-4">
                                        <input type="hidden" name="program" value="<?= htmlspecialchars($program, ENT_QUOTES, 'UTF-8') ?>">
                                        <div class="col-md-4">
                                            <label for="filename">Source file</label>
                                            <select id="filename" name="filename" class="form-control">
                                                <option value="">All files</option>
                                                <?php foreach ($filterOptions['filenames'] as $fn): ?>
                                                    <option value="<?= htmlspecialchars($fn, ENT_QUOTES, 'UTF-8') ?>"
                                                        <?= $filterFilename === $fn ? 'selected' : '' ?>><?= htmlspecialchars($fn, ENT_QUOTES, 'UTF-8') ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <div class="col-md-4">
                                            <label for="file_group">File group</label>
                                            <select id="file_group" name="file_group" class="form-control">
                                                <option value="">All groups</option>
                                                <?php foreach ($filterOptions['file_groups'] as $fg): ?>
                                                    <option value="<?= htmlspecialchars($fg, ENT_QUOTES, 'UTF-8') ?>"
                                                        <?= $filterFileGroup === $fg ? 'selected' : '' ?>><?= htmlspecialchars($fg, ENT_QUOTES, 'UTF-8') ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <div class="col-md-4 d-flex align-items-end">
                                            <button type="submit" class="btn btn-success btn-rounded mr-2">Apply filter</button>
                                            <a href="enrollment_status.php?program=<?= rawurlencode($program) ?>" class="btn btn-outline-secondary btn-rounded">Clear</a>
                                        </div>
                                    </form>

                                    <div class="table-responsive">
                                        <table id="zero_config" class="table table-striped table-bordered no-wrap">
                                            <thead>
                                                <tr>
                                                    <th>SEQ</th>
                                                    <th>APP NO</th>
                                                    <th>LASTNAME</th>
                                                    <th>FIRSTNAME</th>
                                                    <?php if ($program === 'tes'): ?>
                                                        <th>EXT</th>
                                                    <?php endif; ?>
                                                    <th>COURSE</th>
                                                    <th>YEAR</th>
                                                    <?php if ($program === 'tes'): ?>
                                                        <th>BATCH NO</th>
                                                        <th>CONTACT</th>
                                                    <?php else: ?>
                                                        <th>UNITS</th>
                                                    <?php endif; ?>
                                                    <th>COR / COG</th>
                                                    <th>ENROLLMENT STATUS</th>
                                                    <?php if ($program === 'tdp'): ?>
                                                        <th>CHED STATUS (import)</th>
                                                        <th>REMARKS</th>
                                                    <?php endif; ?>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                $colspan = $program === 'tes' ? 11 : 11;
                                                if (empty($enrollmentRows)): ?>
                                                    <tr>
                                                        <td colspan="<?= $colspan ?>" class="text-center text-muted">
                                                            No <?= htmlspecialchars($progLabel, ENT_QUOTES, 'UTF-8') ?> records for this campus or filter.
                                                            Upload data via <a href="<?= htmlspecialchars($masterlistPage, ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($progLabel, ENT_QUOTES, 'UTF-8') ?> Masterlist</a>.
                                                        </td>
                                                    </tr>
                                                <?php else:
                                                    $viewBase = '../../view_document.php?path=';
                                                    foreach ($enrollmentRows as $row):
                                                        $hasCor = !empty($row['cor_path']);
                                                        $hasCog = !empty($row['cog_path']);
                                                        $isEnrolled = $hasCor && $hasCog;
                                                        $chedStatus = schogms_ched_import_status_value($row);
                                                        $chedBadge = schogms_ched_import_status_badge_class($chedStatus);
                                                ?>
                                                    <tr>
                                                        <td><?= htmlspecialchars((string) ($row['seq'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                                                        <td><?= htmlspecialchars((string) ($row['app_no'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                                                        <td><?= htmlspecialchars((string) ($row['lastname'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                                                        <td><?= htmlspecialchars((string) ($row['firstname'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                                                        <?php if ($program === 'tes'): ?>
                                                            <td><?= htmlspecialchars((string) ($row['ext'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                                                        <?php endif; ?>
                                                        <td><?= htmlspecialchars((string) ($row['course_program_enrolled'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                                                        <td><?= htmlspecialchars((string) ($row['year_level'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                                                        <?php if ($program === 'tes'): ?>
                                                            <td><?= htmlspecialchars((string) ($row['batch_no'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                                                            <td><?= htmlspecialchars((string) ($row['contact'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                                                        <?php else: ?>
                                                            <td><?= htmlspecialchars((string) ($row['total_units_enrolled'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                                                        <?php endif; ?>
                                                        <td>
                                                            <div class="btn-group" role="group">
                                                                <?php if ($hasCor): ?>
                                                                    <a href="<?= htmlspecialchars($viewBase . urlencode(base64_encode((string) $row['cor_path'])), ENT_QUOTES, 'UTF-8') ?>" target="_blank" class="btn btn-sm btn-success">COR</a>
                                                                <?php else: ?><span class="badge badge-secondary">No COR</span><?php endif; ?>
                                                                <?php if ($hasCog): ?>
                                                                    <a href="<?= htmlspecialchars($viewBase . urlencode(base64_encode((string) $row['cog_path'])), ENT_QUOTES, 'UTF-8') ?>" target="_blank" class="btn btn-sm btn-primary">COG</a>
                                                                <?php else: ?><span class="badge badge-secondary">No COG</span><?php endif; ?>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <?php if ($isEnrolled): ?>
                                                                <span class="badge badge-success">Enrolled</span>
                                                            <?php else: ?>
                                                                <span class="badge badge-warning">Not Enrolled</span>
                                                            <?php endif; ?>
                                                        </td>
                                                        <?php if ($program === 'tdp'): ?>
                                                            <td>
                                                                <?php if ($chedStatus !== ''): ?>
                                                                    <span class="badge badge-<?= htmlspecialchars($chedBadge, ENT_QUOTES, 'UTF-8') ?>">
                                                                        <?= htmlspecialchars($chedStatus, ENT_QUOTES, 'UTF-8') ?>
                                                                    </span>
                                                                <?php else: ?>
                                                                    <span class="text-muted" title="Not in database — re-import masterlist with Status of enrollment column">—</span>
                                                                <?php endif; ?>
                                                            </td>
                                                            <td><?= htmlspecialchars((string) ($row['remarks'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                                                        <?php endif; ?>
                                                    </tr>
                                                <?php endforeach; endif; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <footer class="footer text-center text-muted">
                All Rights Reserved 2026. Scholarship and Grants Management System <a href="">(SchoGMS)</a>.
            </footer>
    <?php
    schogms_coordinator_shell_close();
    require_once __DIR__ . '/inc/assets.php';
    schogms_coordinator_footer_scripts(['datatables' => true]);
    ?>

</body>

</html>