<?php
/**
 * Shared CHED TDP/TES masterlist upload UI + processing helpers.
 */

if (!function_exists('schogms_ched_masterlist_program_label')) {
    function schogms_ched_masterlist_program_label(string $program): string
    {
        return strtolower($program) === 'tes' ? 'TES' : 'TDP';
    }
}

if (!function_exists('schogms_ched_masterlist_format_type')) {
    function schogms_ched_masterlist_format_type(string $program): string
    {
        return strtolower($program) === 'tes' ? 'ched_tes' : 'ched_tdp';
    }
}

if (!function_exists('schogms_ched_masterlist_file_group_full')) {
    function schogms_ched_masterlist_file_group_full(string $base, string $academicYear, string $semester): string
    {
        $base = trim($base);
        $academicYear = trim($academicYear);
        $semester = trim($semester);
        if ($base === '') {
            return '';
        }
        if ($academicYear !== '' && $semester !== '') {
            return $base . ' (' . $academicYear . ', ' . $semester . ')';
        }

        return $base;
    }
}

if (!function_exists('schogms_ched_masterlist_default_file_group_base')) {
    function schogms_ched_masterlist_default_file_group_base(string $program): string
    {
        $label = schogms_ched_masterlist_program_label($program);

        return 'CHED ' . $label;
    }
}

if (!function_exists('schogms_ched_masterlist_upload_batch_status')) {
    /** Coordinator uploads → pending review; chairman → approved immediately. */
    function schogms_ched_masterlist_upload_batch_status(string $role): string
    {
        return ($role ?? '') === 'chairman' ? 'approved' : 'pending';
    }
}

if (!function_exists('schogms_ched_masterlist_upload_campus_options')) {
    /**
     * @return list<string>
     */
    function schogms_ched_masterlist_upload_campus_options(mysqli $conn): array
    {
        require_once __DIR__ . '/campus_access.php';
        $campuses = schogms_campus_catalog_names();

        foreach (['ched_masterlist' => 'sheet_name', 'ched_masterlist_tes' => 'campus'] as $table => $col) {
            $res = $conn->query("SELECT DISTINCT {$col} AS c FROM {$table} WHERE TRIM({$col}) <> '' ORDER BY c");
            if (!$res) {
                continue;
            }
            while ($row = $res->fetch_assoc()) {
                $sn = trim((string) ($row['c'] ?? ''));
                if ($sn !== '' && !in_array($sn, $campuses, true)) {
                    $campuses[] = $sn;
                }
            }
        }

        return $campuses;
    }
}

if (!function_exists('schogms_ched_masterlist_upload_button')) {
    function schogms_ched_masterlist_upload_button(): void
    {
        ?>
        <div class="customize-input float-right ml-2">
            <button type="button" class="btn waves-effect waves-light btn-rounded btn-primary"
                data-toggle="modal" data-target="#chedMasterlistUploadModal">
                <i data-feather="upload" class="feather-icon"></i> Upload masterlist
            </button>
        </div>
        <?php
    }
}

if (!function_exists('schogms_ched_masterlist_upload_modal')) {
    /**
     * @param array{
     *   program: string,
     *   role: string,
     *   base_path?: string,
     *   campus?: string,
     *   campus_editable?: bool,
     *   campuses?: list<string>,
     *   submit_url: string,
     *   guide_url?: string|null
     * } $opts
     */
    function schogms_ched_masterlist_upload_modal(array $opts): void
    {
        require_once __DIR__ . '/schogms_upload_format.php';

        $program = strtolower(trim((string) ($opts['program'] ?? 'tdp')));
        if (!in_array($program, ['tdp', 'tes'], true)) {
            $program = 'tdp';
        }
        $role = strtolower(trim((string) ($opts['role'] ?? 'coordinator')));
        $basePath = rtrim((string) ($opts['base_path'] ?? '../../'), '/') . '/';
        $campus = trim((string) ($opts['campus'] ?? ''));
        $campusEditable = (bool) ($opts['campus_editable'] ?? ($role === 'chairman'));
        $campuses = $opts['campuses'] ?? [];
        $submitUrl = (string) ($opts['submit_url'] ?? '');
        $guideUrl = $opts['guide_url'] ?? null;
        $formatType = schogms_ched_masterlist_format_type($program);
        $progLabel = schogms_ched_masterlist_program_label($program);
        $defaultGroup = schogms_ched_masterlist_default_file_group_base($program);
        $modalId = 'chedMasterlistUploadModal';
        if ($role === 'chairman') {
            $pendingNote = 'Chairman uploads are <strong>approved immediately</strong> and appear on <a href="file_groups.php">File groups</a>.';
        } else {
            $pendingNote = 'Coordinator uploads are <strong>pending</strong> until the chairman approves them on File groups.';
        }
        ?>
        <div class="modal fade" id="<?= htmlspecialchars($modalId, ENT_QUOTES, 'UTF-8') ?>" tabindex="-1" role="dialog"
            aria-labelledby="chedMasterlistUploadModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="chedMasterlistUploadModalLabel">Upload CHED <?= htmlspecialchars($progLabel, ENT_QUOTES, 'UTF-8') ?> masterlist</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <?php schogms_upload_format_modal_hint($formatType, $basePath, $guideUrl); ?>
                        <p class="small text-muted"><?= $pendingNote ?></p>
                        <form id="chedMasterlistUploadForm" data-program="<?= htmlspecialchars($program, ENT_QUOTES, 'UTF-8') ?>"
                            data-submit-url="<?= htmlspecialchars($submitUrl, ENT_QUOTES, 'UTF-8') ?>">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="ched_upload_campus" class="form-label">Campus</label>
                                    <?php if ($campusEditable): ?>
                                        <select class="form-control" id="ched_upload_campus" name="campus" required>
                                            <option value="">Select campus</option>
                                            <?php foreach ($campuses as $c): ?>
                                                <option value="<?= htmlspecialchars($c, ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($c, ENT_QUOTES, 'UTF-8') ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    <?php else: ?>
                                        <input type="text" class="form-control" id="ched_upload_campus" name="campus" readonly
                                            value="<?= htmlspecialchars($campus, ENT_QUOTES, 'UTF-8') ?>">
                                        <small class="text-muted">From your coordinator account</small>
                                    <?php endif; ?>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="ched_upload_academic_year" class="form-label">Academic year</label>
                                    <select class="form-control" id="ched_upload_academic_year" name="academic_year" required>
                                        <option value="">Select year</option>
                                        <?php foreach (['2026-2027', '2025-2026', '2024-2025', '2023-2024'] as $ay): ?>
                                            <option value="<?= htmlspecialchars($ay, ENT_QUOTES, 'UTF-8') ?>"<?= $ay === '2024-2025' ? ' selected' : '' ?>><?= htmlspecialchars($ay, ENT_QUOTES, 'UTF-8') ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="ched_upload_semester" class="form-label">Semester</label>
                                    <select class="form-control" id="ched_upload_semester" name="semester" required>
                                        <option value="">Select semester</option>
                                        <option value="1st Semester" selected>1st Semester</option>
                                        <option value="2nd Semester">2nd Semester</option>
                                        <option value="Summer">Summer</option>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="ched_upload_file_group" class="form-label">File group / batch name</label>
                                    <input type="text" class="form-control" id="ched_upload_file_group" name="file_group"
                                        value="<?= htmlspecialchars($defaultGroup, ENT_QUOTES, 'UTF-8') ?>"
                                        placeholder="e.g. CHED <?= htmlspecialchars($progLabel, ENT_QUOTES, 'UTF-8') ?> 2024-2025 1st Sem" required>
                                    <small class="text-muted">Saved as: <span id="ched_upload_file_group_preview" class="font-weight-medium"></span></small>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="ched_upload_excel" class="form-label">Excel or CSV file</label>
                                <input type="file" class="form-control" id="ched_upload_excel" name="excelFile"
                                    accept=".xls,.xlsx,.csv" required>
                            </div>
                            <button type="submit" class="btn btn-primary">Upload</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }
}

if (!function_exists('schogms_ched_masterlist_upload_scripts')) {
    function schogms_ched_masterlist_upload_scripts(): void
    {
        ?>
        <script>
        (function () {
            function updateFileGroupPreview() {
                var base = (document.getElementById('ched_upload_file_group') || {}).value || '';
                var ay = (document.getElementById('ched_upload_academic_year') || {}).value || '';
                var sem = (document.getElementById('ched_upload_semester') || {}).value || '';
                var el = document.getElementById('ched_upload_file_group_preview');
                if (!el) return;
                base = base.trim();
                if (base && ay && sem) {
                    el.textContent = base + ' (' + ay + ', ' + sem + ')';
                } else {
                    el.textContent = base || '—';
                }
            }
            ['ched_upload_file_group', 'ched_upload_academic_year', 'ched_upload_semester'].forEach(function (id) {
                var el = document.getElementById(id);
                if (el) {
                    el.addEventListener('input', updateFileGroupPreview);
                    el.addEventListener('change', updateFileGroupPreview);
                }
            });
            updateFileGroupPreview();

            var form = document.getElementById('chedMasterlistUploadForm');
            if (!form) return;

            form.addEventListener('submit', function (event) {
                event.preventDefault();
                var submitUrl = form.getAttribute('data-submit-url') || '';
                var campusEl = document.getElementById('ched_upload_campus');
                var campus = campusEl ? campusEl.value.trim() : '';
                var fileGroup = (document.getElementById('ched_upload_file_group') || {}).value.trim();
                var academicYear = (document.getElementById('ched_upload_academic_year') || {}).value.trim();
                var semester = (document.getElementById('ched_upload_semester') || {}).value.trim();
                var fileInput = document.getElementById('ched_upload_excel');
                var file = fileInput && fileInput.files[0];

                if (!campus) {
                    if (typeof showToast === 'function') showToast('Please select or confirm campus.', 'error');
                    return;
                }
                if (!fileGroup || !academicYear || !semester) {
                    if (typeof showToast === 'function') showToast('Please complete file group, academic year, and semester.', 'error');
                    return;
                }
                if (!file) {
                    if (typeof showToast === 'function') showToast('Please select a file.', 'error');
                    return;
                }
                var ext = file.name.split('.').pop().toLowerCase();
                if (['xls', 'xlsx', 'csv'].indexOf(ext) === -1) {
                    if (typeof showToast === 'function') showToast('Please upload .xlsx, .xls, or .csv.', 'error');
                    return;
                }

                Swal.fire({
                    title: 'Upload masterlist?',
                    text: 'Campus: ' + campus + '. This may take a moment for large files.',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, upload',
                    cancelButtonText: 'Cancel'
                }).then(function (result) {
                    if (!result.isConfirmed) return;

                    var fd = new FormData();
                    fd.append('file_group', fileGroup);
                    fd.append('academic_year', academicYear);
                    fd.append('semester', semester);
                    fd.append('campus', campus);
                    fd.append('sheet_name', campus);
                    fd.append('excelFile', file);

                    Swal.fire({
                        title: 'Uploading…',
                        allowOutsideClick: false,
                        showConfirmButton: false,
                        didOpen: function () { Swal.showLoading(); }
                    });

                    fetch(submitUrl, { method: 'POST', body: fd })
                        .then(function (r) { return r.json(); })
                        .then(function (data) {
                            if (data.success) {
                                Swal.fire({
                                    title: 'Success',
                                    text: data.message || 'Upload complete.',
                                    icon: 'success',
                                    timer: 2500
                                }).then(function () {
                                    if (typeof jQuery !== 'undefined' && jQuery.fn.modal) {
                                        jQuery('#chedMasterlistUploadModal').modal('hide');
                                    }
                                    location.reload();
                                });
                            } else {
                                Swal.fire({
                                    title: 'Upload failed',
                                    text: data.error || 'Could not process the file.',
                                    icon: 'error'
                                });
                            }
                        })
                        .catch(function () {
                            Swal.fire({
                                title: 'Upload failed',
                                text: 'Network or server error.',
                                icon: 'error'
                            });
                        });
                });
            });
        })();
        </script>
        <?php
    }
}

if (!function_exists('schogms_ched_tdp_process_upload_file')) {
    /**
     * @return array{success: bool, inserted?: int, error?: string, message?: string}
     */
    function schogms_ched_tdp_process_upload_file(
        mysqli $conn,
        string $targetFilePath,
        string $uploadedFileName,
        string $campus,
        string $fileGroupFull
    ): array {
        require_once dirname(__DIR__) . '/users/coordinator/inc/ched_masterlist_import.php';

        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($targetFilePath);
        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray(null, true, true, true);

        if (count($rows) < 2) {
            return ['success' => false, 'error' => 'The file does not contain enough rows.'];
        }

        $layout = schogms_ched_tdp_import_layout($rows);
        $dataRows = array_slice($rows, $layout['data_start']);

        $insertQuery = '
            INSERT INTO ched_masterlist (
                sheet_name, seq, app_no, award_no, lastname, firstname, extname, middlename,
                sex, birthdate, course_program_enrolled, year_level,
                total_units_enrolled, status_of_enrollment, remarks,
                filename, file_group
            ) VALUES (
                ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?
            )';

        $stmt = $conn->prepare($insertQuery);
        if (!$stmt) {
            return ['success' => false, 'error' => 'Failed to prepare database statement.'];
        }

        $conn->begin_transaction();
        $inserted = 0;

        foreach ($dataRows as $row) {
            if (!is_array($row)) {
                continue;
            }
            $seq = schogms_ched_tdp_row_cell($row, 'A');
            if ($seq === '') {
                break;
            }
            if (!array_filter($row, static fn($v) => trim((string) $v) !== '')) {
                continue;
            }

            $stmt->bind_param(
                'sssssssssssssssss',
                $campus,
                $seq,
                schogms_ched_tdp_row_cell($row, 'B'),
                schogms_ched_tdp_row_cell($row, 'C'),
                schogms_ched_tdp_row_cell($row, 'D'),
                schogms_ched_tdp_row_cell($row, 'E'),
                schogms_ched_tdp_row_cell($row, 'F'),
                schogms_ched_tdp_row_cell($row, 'G'),
                schogms_ched_tdp_row_cell($row, 'H'),
                schogms_ched_tdp_row_cell($row, 'I'),
                schogms_ched_tdp_row_cell($row, 'J'),
                schogms_ched_tdp_row_cell($row, 'K'),
                schogms_ched_tdp_row_cell($row, $layout['units_col']),
                schogms_ched_tdp_row_cell($row, $layout['status_col']),
                schogms_ched_tdp_row_cell($row, $layout['remarks_col']),
                $uploadedFileName,
                $fileGroupFull
            );

            if (!$stmt->execute()) {
                $conn->rollback();
                $stmt->close();

                return ['success' => false, 'error' => 'Error saving masterlist row.'];
            }
            $inserted++;
        }

        $conn->commit();
        $stmt->close();

        return [
            'success' => true,
            'inserted' => $inserted,
            'message' => "Uploaded {$inserted} record(s) for campus {$campus}.",
        ];
    }
}

if (!function_exists('schogms_ched_tes_process_upload_file')) {
    /**
     * @return array{success: bool, inserted?: int, error?: string, message?: string}
     */
    function schogms_ched_tes_process_upload_file(
        mysqli $conn,
        string $targetFilePath,
        string $uploadedFileName,
        string $campus,
        string $fileGroupFull
    ): array {
        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($targetFilePath);
        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray(null, true, true, true);

        if (count($rows) < 2) {
            return ['success' => false, 'error' => 'The file does not contain enough rows.'];
        }

        $dataRows = array_slice($rows, 1);
        $insertQuery = '
            INSERT INTO ched_masterlist_tes (
                seq, app_no, lastname, firstname, ext, middlename, sex, course_program_enrolled, year_level,
                street, town_city, contact, batch_no, campus, filename, file_group
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)';

        $stmt = $conn->prepare($insertQuery);
        if (!$stmt) {
            return ['success' => false, 'error' => 'Failed to prepare database statement.'];
        }

        $conn->begin_transaction();
        $inserted = 0;

        foreach ($dataRows as $row) {
            if (!is_array($row)) {
                continue;
            }
            $seq = trim((string) ($row['A'] ?? ''));
            if ($seq === '') {
                break;
            }
            if (!array_filter($row, static fn($v) => trim((string) $v) !== '')) {
                continue;
            }

            $stmt->bind_param(
                'ssssssssssssssss',
                $seq,
                (string) ($row['B'] ?? ''),
                (string) ($row['C'] ?? ''),
                (string) ($row['D'] ?? ''),
                (string) ($row['E'] ?? ''),
                (string) ($row['F'] ?? ''),
                (string) ($row['G'] ?? ''),
                (string) ($row['H'] ?? ''),
                (string) ($row['I'] ?? ''),
                (string) ($row['J'] ?? ''),
                (string) ($row['K'] ?? ''),
                (string) ($row['L'] ?? ''),
                (string) ($row['M'] ?? ''),
                $campus,
                $uploadedFileName,
                $fileGroupFull
            );

            if (!$stmt->execute()) {
                $conn->rollback();
                $stmt->close();

                return ['success' => false, 'error' => 'Error saving TES masterlist row.'];
            }
            $inserted++;
        }

        $conn->commit();
        $stmt->close();

        return [
            'success' => true,
            'inserted' => $inserted,
            'message' => "Uploaded {$inserted} TES record(s) for campus {$campus}.",
        ];
    }
}

if (!function_exists('schogms_ched_masterlist_handle_json_upload')) {
    /**
     * @param list<string> $allowedRoles
     * @return never
     */
    function schogms_ched_masterlist_handle_json_upload(
        mysqli $conn,
        string $program,
        array $allowedRoles
    ): void {
        header('Content-Type: application/json');

        $role = strtolower(trim((string) ($_SESSION['role'] ?? '')));
        if (!in_array($role, $allowedRoles, true)) {
            echo json_encode(['success' => false, 'error' => 'Access denied.']);
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'error' => 'Invalid request method.']);
            exit;
        }

        $program = strtolower(trim($program));
        $fileGroupBase = trim((string) ($_POST['file_group'] ?? ''));
        $academicYear = trim((string) ($_POST['academic_year'] ?? ''));
        $semester = trim((string) ($_POST['semester'] ?? ''));
        $campus = trim((string) ($_POST['campus'] ?? $_POST['sheet_name'] ?? ''));

        if ($fileGroupBase === '') {
            echo json_encode(['success' => false, 'error' => 'File group is required.']);
            exit;
        }
        if ($academicYear === '' || $semester === '') {
            echo json_encode(['success' => false, 'error' => 'Academic year and semester are required.']);
            exit;
        }
        if ($campus === '') {
            echo json_encode(['success' => false, 'error' => 'Campus is required.']);
            exit;
        }

        if ($role === 'coordinator') {
            $sessionCampus = trim((string) ($_SESSION['sheet_name'] ?? ''));
            if ($sessionCampus !== '' && strcasecmp($sessionCampus, $campus) !== 0) {
                echo json_encode(['success' => false, 'error' => 'Campus must match your assigned campus.']);
                exit;
            }
            if ($sessionCampus !== '') {
                $campus = $sessionCampus;
            }
        }

        $fileGroupFull = schogms_ched_masterlist_file_group_full($fileGroupBase, $academicYear, $semester);

        if (!isset($_FILES['excelFile']) || $_FILES['excelFile']['error'] !== UPLOAD_ERR_OK) {
            echo json_encode(['success' => false, 'error' => 'File upload failed.']);
            exit;
        }

        $uploadsDir = dirname(__DIR__) . '/users/' . ($role === 'chairman' ? 'chairman' : 'coordinator') . '/uploads/';
        if (!is_dir($uploadsDir) && !mkdir($uploadsDir, 0777, true)) {
            echo json_encode(['success' => false, 'error' => 'Failed to create uploads directory.']);
            exit;
        }

        $uploadedFileName = basename((string) $_FILES['excelFile']['name']);
        $targetFilePath = $uploadsDir . $uploadedFileName;

        if (!move_uploaded_file($_FILES['excelFile']['tmp_name'], $targetFilePath)) {
            echo json_encode(['success' => false, 'error' => 'Failed to save the uploaded file.']);
            exit;
        }

        require dirname(__DIR__) . '/vendor/autoload.php';

        try {
            $result = $program === 'tes'
                ? schogms_ched_tes_process_upload_file($conn, $targetFilePath, $uploadedFileName, $campus, $fileGroupFull)
                : schogms_ched_tdp_process_upload_file($conn, $targetFilePath, $uploadedFileName, $campus, $fileGroupFull);

            if (is_file($targetFilePath)) {
                @unlink($targetFilePath);
            }

            if (!$result['success']) {
                echo json_encode($result);
                exit;
            }

            require_once __DIR__ . '/schogms_file_group_meta.php';
            $batchStatus = schogms_ched_masterlist_upload_batch_status($role);
            schogms_file_group_meta_register(
                $conn,
                $program,
                $campus,
                $fileGroupFull,
                $batchStatus,
                schogms_file_group_meta_uploader_from_session()
            );

            echo json_encode([
                'success' => true,
                'message' => $result['message'] ?? 'Upload complete.',
                'inserted' => $result['inserted'] ?? 0,
            ]);
        } catch (Throwable $e) {
            if ($conn instanceof mysqli) {
                $conn->rollback();
            }
            if (isset($targetFilePath) && is_file($targetFilePath)) {
                @unlink($targetFilePath);
            }
            echo json_encode(['success' => false, 'error' => 'Error processing file: ' . $e->getMessage()]);
        }
        exit;
    }
}
