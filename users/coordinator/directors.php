<?php
require __DIR__ . '/config/session.php';
require_once __DIR__ . '/../../inc/campus_access.php';

if (($role ?? '') !== 'coordinator') {
    header('Location: ../../index.php?ERROR=restricted');
    exit;
}

schogms_ensure_campus_access_tables($conn);
schogms_seed_campus_access_catalog($conn);

$directors = [];
$res = $conn->query(
    "SELECT user_id, name, email, campus, status, created_at
     FROM users
     WHERE role = 'director'
     ORDER BY campus ASC, name ASC
     LIMIT 200"
);
if ($res) {
    while ($row = $res->fetch_assoc()) {
        $directors[] = $row;
    }
}

$campuses = schogms_campus_catalog_names();
$coordinatorCampus = schogms_resolve_catalog_campus(trim((string) ($sheet_name ?? '')));
?>
<!DOCTYPE html>
<html dir="ltr" lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/png" href="../../assets/images/logo.png">
    <title>Campus directors — SchoGMS</title>
    <?php require_once __DIR__ . '/inc/assets.php'; schogms_coordinator_head(true); ?>
</head>
<body>
<?php require_once __DIR__ . '/inc/coordinator_nav.php'; schogms_coordinator_shell_open('Campus directors'); ?>
        <div class="container-fluid">
            <div class="page-breadcrumb">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                        <li class="breadcrumb-item active">Campus directors</li>
                    </ol>
                </nav>
            </div>

            <div class="row">
                <div class="col-lg-5">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">Assign campus director</h4>
                            <p class="text-muted small">One active director per campus. The director can assign multiple deans (one dean per college).</p>
                            <?php if ($coordinatorCampus !== ''): ?>
                                <p class="text-muted small mb-2">Your campus: <strong><?= schogms_e($coordinatorCampus) ?></strong><?= strcasecmp((string) $sheet_name, $coordinatorCampus) !== 0 && ($sheet_name ?? '') !== '' ? ' <span class="text-muted">(account: ' . schogms_e((string) $sheet_name) . ')</span>' : '' ?></p>
                            <?php endif; ?>
                            <form id="directorForm">
                                <div class="form-group">
                                    <label for="campus">Campus</label>
                                    <select class="form-control" id="campus" name="campus" required>
                                        <option value="" disabled selected>Select campus</option>
                                        <?php foreach ($campuses as $c): ?>
                                            <option value="<?= schogms_e($c) ?>"<?= ($coordinatorCampus !== '' && strcasecmp($c, $coordinatorCampus) === 0) ? ' selected' : '' ?>><?= schogms_e($c) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="director_name">Director name</label>
                                    <input type="text" class="form-control" id="director_name" name="director_name" required>
                                </div>
                                <div class="form-group">
                                    <label for="director_email">Email</label>
                                    <input type="email" class="form-control" id="director_email" name="director_email" required>
                                </div>
                                <button type="submit" class="btn btn-primary">Create director account</button>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-lg-7">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">Directors</h4>
                            <div class="table-responsive">
                                <table id="zero_config" class="table table-striped table-bordered no-wrap">
                                    <thead>
                                        <tr>
                                            <th>Status</th>
                                            <th>Campus</th>
                                            <th>Name</th>
                                            <th>Email</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if ($directors === []): ?>
                                            <tr><td colspan="5">No directors yet.</td></tr>
                                        <?php else: ?>
                                            <?php foreach ($directors as $d): ?>
                                                <tr>
                                                    <td><?= schogms_status_badge((string) ($d['status'] ?? '')) ?></td>
                                                    <td><?= schogms_e((string) ($d['campus'] ?? '')) ?></td>
                                                    <td><?= schogms_e((string) ($d['name'] ?? '')) ?></td>
                                                    <td><?= schogms_e((string) ($d['email'] ?? '')) ?></td>
                                                    <td>
                                                        <button type="button" class="btn btn-danger btn-sm delete-director"
                                                            data-id="<?= (int) $d['user_id'] ?>">Remove</button>
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

            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Campus access structure</h5>
                    <p class="text-muted small mb-3">Colleges and courses used when directors assign deans and deans assign program chairs.</p>
                    <div class="accordion" id="campusCatalog">
                        <?php $i = 0; foreach (schogms_campus_catalog() as $campusName => $colleges): $i++; ?>
                            <div class="card mb-1">
                                <div class="card-header p-2" id="h<?= $i ?>">
                                    <button class="btn btn-link btn-block text-left" type="button" data-toggle="collapse" data-target="#c<?= $i ?>">
                                        <?= schogms_e($campusName) ?>
                                    </button>
                                </div>
                                <div id="c<?= $i ?>" class="collapse" data-parent="#campusCatalog">
                                    <div class="card-body small">
                                        <?php foreach ($colleges as $college => $courses): ?>
                                            <p class="mb-1"><strong><?= schogms_e($college) ?></strong></p>
                                            <ul class="mb-2">
                                                <?php foreach ($courses as $course): ?>
                                                    <li><?= schogms_e($course) ?></li>
                                                <?php endforeach; ?>
                                            </ul>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
<?php
schogms_coordinator_shell_close();
require_once __DIR__ . '/inc/assets.php';
schogms_coordinator_footer_scripts(['datatables' => true, 'sweetalert' => true]);
?>
<script>
document.getElementById('directorForm').addEventListener('submit', async function (e) {
    e.preventDefault();
    const fd = new FormData(this);
    const confirm = await Swal.fire({
        title: 'Create director?',
        text: 'They will manage deans for the selected campus only.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Yes, create'
    });
    if (!confirm.isConfirmed) return;

    const res = await fetch('submit_director.php', { method: 'POST', body: fd });
    const data = await res.json();
    if (data.success) {
        await Swal.fire({ icon: 'success', title: 'Done', text: data.message });
        location.reload();
    } else {
        Swal.fire({ icon: 'error', title: 'Error', text: data.message || 'Failed' });
    }
});

document.querySelectorAll('.delete-director').forEach(btn => {
    btn.addEventListener('click', async function () {
        const id = this.getAttribute('data-id');
        const confirm = await Swal.fire({
            title: 'Remove director?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            confirmButtonText: 'Remove'
        });
        if (!confirm.isConfirmed) return;
        const fd = new FormData();
        fd.append('id', id);
        const res = await fetch('delete_director.php', { method: 'POST', body: fd });
        const data = await res.json();
        if (data.success) {
            await Swal.fire({ icon: 'success', title: 'Removed', timer: 1200, showConfirmButton: false });
            location.reload();
        } else {
            Swal.fire({ icon: 'error', text: data.message || 'Failed' });
        }
    });
});
</script>
</body>
</html>
