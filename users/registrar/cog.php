<?php
include 'config/session.php';
require_once __DIR__ . '/inc/registrar_data.php';
require_once __DIR__ . '/inc/assets.php';
require_once __DIR__ . '/inc/registrar_nav.php';

$corCogSubmitUrl = 'submit_document_cor_cog.php';
$campusFilter = trim((string) ($sheet_name ?? ''));
$docData = schogms_registrar_documents($conn, $campusFilter, 'COG');
$docRows = $docData['rows'];
$docError = $docData['error'];
?>
<!DOCTYPE html>
<html dir="ltr" lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/png" sizes="16x16" href="../../assets/images/logo.png">
    <title>COG — SchoGMS</title>
    <?php schogms_registrar_head(true); ?>
    <style>.format-sample-table{font-size:13px}.format-sample-table th{background:#f8f9fa}</style>
</head>
<body>
<?php schogms_loading_screen_once(); ?>

<?php schogms_registrar_shell_open('COG documents'); ?>
            <div class="page-breadcrumb">
                <div class="row">
                    <div class="col-7 align-self-center">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb m-0 p-0">
                                <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                                <li class="breadcrumb-item"><a href="cor-cog.php">COR &amp; COG</a></li>
                                <li class="breadcrumb-item active">COG</li>
                            </ol>
                        </nav>
                    </div>
                    <div class="col-5 align-self-center">
                        <div class="customize-input float-right">
                            <button type="button" class="btn waves-effect waves-light btn-rounded btn-success"
                                data-toggle="modal" data-target="#uploadModal">
                                Bulk upload COG
                            </button>
                            <a href="cor-cog.php#bulk-cor-cog-upload" class="btn btn-outline-success btn-rounded ml-1">COR &amp; COG bulk</a>
                        </div>
                    </div>
                </div>
            </div>
            <?php
            $corCogCategory = 'COG';
            $corCogCampus = $campusFilter;
            require __DIR__ . '/../coordinator/inc/cor_cog_upload_modal.php';
            ?>
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="table-responsive">
                                    <?php if ($docError !== ''): ?>
                                        <div class="alert alert-warning"><?= htmlspecialchars($docError) ?></div>
                                    <?php endif; ?>
                                    <table id="zero_config" class="table table-striped table-bordered no-wrap">
                                        <thead>
                                            <tr>
                                                <th>Campus</th>
                                                <th>File Group</th>
                                                <th>File Name</th>
                                                <th>Uploaded</th>
                                                <th>View</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (count($docRows) === 0): ?>
                                                <tr><td colspan="5" class="text-center text-muted">No COG documents uploaded<?= $campusFilter !== '' ? ' for ' . htmlspecialchars($campusFilter) : '' ?>.</td></tr>
                                            <?php else: ?>
                                            <?php foreach ($docRows as $row): ?>
                                                <tr>
                                                    <td><?= htmlspecialchars((string) ($row['campus'] ?? '')) ?></td>
                                                    <td><?= htmlspecialchars((string) ($row['file_group'] ?? '')) ?></td>
                                                    <td><?= htmlspecialchars((string) ($row['file_name'] ?? '')) ?></td>
                                                    <td><?= htmlspecialchars((string) ($row['uploaded_at'] ?? '')) ?></td>
                                                    <td><?php if (!empty($row['file_path'])): ?><a href="<?= htmlspecialchars((string) $row['file_path']) ?>" target="_blank" rel="noopener">Open</a><?php else: ?>—<?php endif; ?></td>
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
                <?php
                require_once __DIR__ . '/../coordinator/inc/cor_cog_format_guide.php';
                schogms_coordinator_render_cor_cog_format('COG', $campusFilter, '../coordinator/');
                ?>
            </div>
<?php
schogms_registrar_shell_close();
schogms_registrar_footer_scripts(['datatables' => true, 'sweetalert' => true]);
require __DIR__ . '/../coordinator/inc/cor_cog_upload_script.php';
?>
</body>
</html>
