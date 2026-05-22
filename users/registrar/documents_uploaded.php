<?php
include 'config/session.php';
require_once __DIR__ . '/inc/registrar_data.php';
require_once __DIR__ . '/inc/assets.php';
require_once __DIR__ . '/inc/registrar_nav.php';

$docFetch = schogms_registrar_document_fetch(array_merge($_GET, ['per_page' => $_GET['per_page'] ?? 50]), $sheet_name ?? null);
$documentRows = $docFetch['rows'];
$totalDocuments = $docFetch['total'];
$pageTitle = 'Documents Uploaded';
?>
<!DOCTYPE html>
<html dir="ltr" lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/png" sizes="16x16" href="../../assets/images/logo.png">
    <title><?= htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8') ?> - SchoGMS</title>
    <?php schogms_registrar_head(true); ?>
</head>
<body>
<?php schogms_loading_screen_once(); ?>

<?php schogms_registrar_shell_open($pageTitle); ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Uploaded COR &amp; COG files</h4>
                    <p class="text-muted mb-3">
                        <?= number_format($totalDocuments) ?> document(s) in the database
                        <?php if (!empty($sheet_name)): ?>
                            for campus <strong><?= htmlspecialchars((string) $sheet_name, ENT_QUOTES, 'UTF-8') ?></strong>
                        <?php endif; ?>
                    </p>

                    <form method="get" class="row mb-3">
                        <div class="col-md-3">
                            <label for="category">Category</label>
                            <select name="category" id="category" class="form-control">
                                <option value="">All</option>
                                <option value="COR" <?= (($_GET['category'] ?? '') === 'COR') ? 'selected' : '' ?>>COR</option>
                                <option value="COG" <?= (($_GET['category'] ?? '') === 'COG') ? 'selected' : '' ?>>COG</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="search">Search</label>
                            <input type="text" class="form-control" id="search" name="search"
                                value="<?= htmlspecialchars((string) ($_GET['search'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"
                                placeholder="File name, campus, or file group">
                        </div>
                        <div class="col-md-3 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary mr-2">Apply</button>
                            <a href="documents_uploaded.php" class="btn btn-secondary">Clear</a>
                        </div>
                    </form>

                    <div class="table-responsive">
                        <table id="documentsTable" class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th>Category</th>
                                    <th>File name</th>
                                    <th>Campus</th>
                                    <th>File group</th>
                                    <th>Uploaded</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($documentRows)): ?>
                                    <tr>
                                        <td colspan="6" class="text-center text-muted">No documents found.</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($documentRows as $doc): ?>
                                        <?php
                                        $path = (string) ($doc['file_path'] ?? '');
                                        $diskPath = $path;
                                        if ($diskPath !== '' && !file_exists($diskPath) && str_starts_with($diskPath, '../../')) {
                                            $diskPath = __DIR__ . '/' . $diskPath;
                                        }
                                        ?>
                                        <tr>
                                            <td><?= htmlspecialchars((string) ($doc['category'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                                            <td><?= schogms_e(schogms_fix_enye_in_name((string) ($doc['original_name'] ?? ''))) ?></td>
                                            <td><?= htmlspecialchars((string) ($doc['campus'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                                            <td><?= htmlspecialchars((string) ($doc['uploaded_by'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                                            <td><?= htmlspecialchars((string) ($doc['uploaded_at'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                                            <td>
                                                <?php if ($path !== '' && ($diskPath === '' || file_exists($diskPath))): ?>
                                                    <a href="<?= htmlspecialchars($path, ENT_QUOTES, 'UTF-8') ?>" target="_blank" class="btn btn-sm btn-primary">View</a>
                                                <?php else: ?>
                                                    <span class="text-muted">Missing</span>
                                                <?php endif; ?>
                                            </td>
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
</div>
<?php
schogms_registrar_shell_close();
schogms_registrar_footer_scripts(['datatables' => true]);
?>
</body>
</html>
