<?php
/** @var string $mlProgram tdp|tes */
/** @var string $mlCampus */
$mlProgram = $mlProgram ?? 'tdp';
$mlCampus = $mlCampus ?? '';
$isTes = ($mlProgram === 'tes');
?>
<div class="modal fade" id="editStudentModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit scholar &amp; upload COR / COG</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <form id="editStudentForm" enctype="multipart/form-data">
                <div class="modal-body">
                    <input type="hidden" name="program" value="<?= htmlspecialchars($mlProgram) ?>">
                    <input type="hidden" name="campus" value="<?= htmlspecialchars($mlCampus) ?>">
                    <input type="hidden" name="id" id="edit_student_id" value="">

                    <div id="edit_validation_guide" class="alert alert-warning d-none mb-3" role="alert">
                        <strong>What to fix (from validation)</strong>
                        <ul id="edit_validation_guide_list" class="mb-2 pl-3"></ul>
                        <p class="small mb-0 text-muted">Masterlist values are compared to registrar records. After saving, click <strong>Re-validate all scholars</strong> to refresh status.</p>
                    </div>

                    <p class="small text-muted mb-3">
                        Document filename should match: <strong>LASTNAME, FIRSTNAME MIDDLENAME.pdf</strong>
                    </p>

                    <div class="row">
                        <div class="col-md-4 form-group">
                            <label>Last name</label>
                            <input type="text" class="form-control" name="lastname" id="edit_lastname" required>
                        </div>
                        <div class="col-md-4 form-group">
                            <label>First name</label>
                            <input type="text" class="form-control" name="firstname" id="edit_firstname" required>
                        </div>
                        <div class="col-md-4 form-group">
                            <label><?= $isTes ? 'Ext' : 'Ext name' ?></label>
                            <input type="text" class="form-control" name="<?= $isTes ? 'ext' : 'extname' ?>" id="edit_ext">
                        </div>
                        <div class="col-md-4 form-group">
                            <label>Middle name</label>
                            <input type="text" class="form-control" name="middlename" id="edit_middlename">
                        </div>
                        <div class="col-md-4 form-group">
                            <label>App no.</label>
                            <input type="text" class="form-control" name="app_no" id="edit_app_no">
                        </div>
                        <div class="col-md-4 form-group">
                            <label>SEQ</label>
                            <input type="text" class="form-control" name="seq" id="edit_seq">
                        </div>
                        <?php if (!$isTes): ?>
                        <div class="col-md-4 form-group">
                            <label>Award no.</label>
                            <input type="text" class="form-control" name="award_no" id="edit_award_no">
                        </div>
                        <div class="col-md-4 form-group">
                            <label>Birthdate</label>
                            <input type="text" class="form-control" name="birthdate" id="edit_birthdate">
                        </div>
                        <?php endif; ?>
                        <div class="col-md-4 form-group">
                            <label>Sex</label>
                            <input type="text" class="form-control" name="sex" id="edit_sex">
                        </div>
                        <div class="col-md-8 form-group">
                            <label>Course</label>
                            <input type="text" class="form-control" name="course_program_enrolled" id="edit_course">
                        </div>
                        <div class="col-md-4 form-group">
                            <label>Year level</label>
                            <input type="text" class="form-control" name="year_level" id="edit_year_level">
                        </div>
                        <?php if ($isTes): ?>
                        <div class="col-md-4 form-group">
                            <label>Street</label>
                            <input type="text" class="form-control" name="street" id="edit_street">
                        </div>
                        <div class="col-md-4 form-group">
                            <label>Town / city</label>
                            <input type="text" class="form-control" name="town_city" id="edit_town_city">
                        </div>
                        <div class="col-md-4 form-group">
                            <label>Contact</label>
                            <input type="text" class="form-control" name="contact" id="edit_contact">
                        </div>
                        <div class="col-md-4 form-group">
                            <label>Batch no.</label>
                            <input type="text" class="form-control" name="batch_no" id="edit_batch_no">
                        </div>
                        <?php else: ?>
                        <div class="col-md-4 form-group">
                            <label>Units enrolled</label>
                            <input type="text" class="form-control" name="total_units_enrolled" id="edit_units">
                        </div>
                        <div class="col-md-4 form-group">
                            <label>Enrollment status</label>
                            <input type="text" class="form-control" name="status_of_enrollment" id="edit_status">
                        </div>
                        <div class="col-md-12 form-group">
                            <label>Remarks</label>
                            <input type="text" class="form-control" name="remarks" id="edit_remarks">
                        </div>
                        <?php endif; ?>
                    </div>

                    <hr>
                    <div class="row">
                        <div class="col-md-6">
                            <label>Upload COR (PDF / image)</label>
                            <input type="file" class="form-control-file" name="cor_file" accept=".pdf,.jpg,.jpeg,.png">
                            <small class="text-muted" id="edit_cor_status"></small>
                        </div>
                        <div class="col-md-6">
                            <label>Upload COG (PDF / image)</label>
                            <input type="file" class="form-control-file" name="cog_file" accept=".pdf,.jpg,.jpeg,.png">
                            <small class="text-muted" id="edit_cog_status"></small>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save &amp; sync CSV</button>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
(function () {
    var program = <?= json_encode($mlProgram) ?>;
    var campus = <?= json_encode($mlCampus) ?>;

    function clearGuideHighlights() {
        ['edit_course', 'edit_year_level'].forEach(function (id) {
            var el = document.getElementById(id);
            if (el) {
                el.classList.remove('border-warning');
            }
        });
        var form = document.getElementById('editStudentForm');
        if (form) {
            form.querySelectorAll('.guide-highlight-file').forEach(function (w) {
                w.classList.remove('border', 'border-warning', 'rounded', 'p-2');
            });
        }
    }

    function applyValidationGuide(guide) {
        var panel = document.getElementById('edit_validation_guide');
        var list = document.getElementById('edit_validation_guide_list');
        clearGuideHighlights();
        if (!panel || !list) {
            return;
        }
        list.innerHTML = '';
        if (!guide || !guide.items || guide.items.length === 0) {
            panel.classList.add('d-none');
            if (guide && guide.passed) {
                panel.classList.remove('d-none', 'alert-warning');
                panel.classList.add('alert-success');
                list.innerHTML = '<li>This scholar passed validation. You can still edit details or replace COR/COG.</li>';
            }
            return;
        }
        panel.classList.remove('d-none', 'alert-success');
        panel.classList.add('alert-warning');
        guide.items.forEach(function (item) {
            var li = document.createElement('li');
            li.className = 'mb-2';
            var strong = document.createElement('strong');
            strong.textContent = item.issue;
            li.appendChild(strong);
            li.appendChild(document.createElement('br'));

            if (item.field === 'course' || item.field === 'year_level') {
                var cmp = document.createElement('span');
                cmp.innerHTML = 'Masterlist: <code></code>';
                cmp.querySelector('code').textContent = item.masterlist || '—';
                li.appendChild(cmp);
                if (item.registrar && item.registrar !== '—') {
                    var reg = document.createElement('span');
                    reg.innerHTML = ' · Registrar: <code></code>';
                    reg.querySelector('code').textContent = item.registrar;
                    li.appendChild(reg);
                }
                li.appendChild(document.createElement('br'));
                var act = document.createElement('span');
                act.className = 'text-muted';
                act.textContent = item.action;
                li.appendChild(act);
                if (item.registrar && item.registrar !== '—') {
                    var useBtn = document.createElement('button');
                    useBtn.type = 'button';
                    useBtn.className = 'btn btn-sm btn-outline-primary ml-1';
                    useBtn.textContent = 'Use registrar value';
                    useBtn.addEventListener('click', function () {
                        if (item.field === 'course') {
                            document.getElementById('edit_course').value = item.registrar;
                        }
                        if (item.field === 'year_level') {
                            document.getElementById('edit_year_level').value = item.registrar;
                        }
                    });
                    li.appendChild(document.createElement('br'));
                    li.appendChild(useBtn);
                }
            } else {
                var docAct = document.createElement('span');
                docAct.className = 'text-muted';
                docAct.textContent = item.action;
                li.appendChild(docAct);
            }
            list.appendChild(li);

            if (item.field === 'course') {
                document.getElementById('edit_course').classList.add('border-warning');
            }
            if (item.field === 'year_level') {
                document.getElementById('edit_year_level').classList.add('border-warning');
            }
            if (item.field === 'cor') {
                var corIn = document.querySelector('#editStudentForm input[name="cor_file"]');
                if (corIn && corIn.parentElement) {
                    corIn.parentElement.classList.add('guide-highlight-file', 'border', 'border-warning', 'rounded', 'p-2');
                }
            }
            if (item.field === 'cog') {
                var cogIn = document.querySelector('#editStudentForm input[name="cog_file"]');
                if (cogIn && cogIn.parentElement) {
                    cogIn.parentElement.classList.add('guide-highlight-file', 'border', 'border-warning', 'rounded', 'p-2');
                }
            }
        });
    }

    function parseGuide(btn) {
        var raw = btn.getAttribute('data-guide');
        if (!raw) {
            return null;
        }
        try {
            return JSON.parse(raw);
        } catch (e) {
            return null;
        }
    }

    document.querySelectorAll('.btn-edit-student').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var id = btn.getAttribute('data-id');
            var guide = parseGuide(btn);
            fetch('get_masterlist_student.php?id=' + encodeURIComponent(id) + '&program=' + encodeURIComponent(program) + '&campus=' + encodeURIComponent(campus))
                .then(function (r) { return r.json(); })
                .then(function (data) {
                    if (!data.success) {
                        Swal.fire('Error', data.message || 'Could not load student', 'error');
                        return;
                    }
                    var row = data.row;
                    document.getElementById('edit_student_id').value = id;
                    document.getElementById('edit_lastname').value = row.lastname || '';
                    document.getElementById('edit_firstname').value = row.firstname || '';
                    document.getElementById('edit_middlename').value = row.middlename || '';
                    document.getElementById('edit_app_no').value = row.app_no || '';
                    document.getElementById('edit_seq').value = row.seq || '';
                    document.getElementById('edit_ext').value = row.ext || row.extname || '';
                    document.getElementById('edit_sex').value = row.sex || '';
                    document.getElementById('edit_course').value = row.course_program_enrolled || '';
                    document.getElementById('edit_year_level').value = row.year_level || '';
                    if (program === 'tes') {
                        document.getElementById('edit_street').value = row.street || '';
                        document.getElementById('edit_town_city').value = row.town_city || '';
                        document.getElementById('edit_contact').value = row.contact || '';
                        document.getElementById('edit_batch_no').value = row.batch_no || '';
                    } else {
                        document.getElementById('edit_award_no').value = row.award_no || '';
                        document.getElementById('edit_birthdate').value = row.birthdate || '';
                        document.getElementById('edit_units').value = row.total_units_enrolled || '';
                        document.getElementById('edit_status').value = row.status_of_enrollment || '';
                        document.getElementById('edit_remarks').value = row.remarks || '';
                    }
                    document.getElementById('edit_cor_status').textContent = data.has_cor ? 'COR on file' : 'No COR yet — upload below';
                    document.getElementById('edit_cog_status').textContent = data.has_cog ? 'COG on file' : 'No COG yet — upload below';
                    applyValidationGuide(guide);
                    $('#editStudentModal').modal('show');
                });
        });
    });

    $('#editStudentModal').on('hidden.bs.modal', function () {
        clearGuideHighlights();
        document.getElementById('edit_validation_guide').classList.add('d-none');
    });

    document.getElementById('editStudentForm').addEventListener('submit', function (e) {
        e.preventDefault();
        var fd = new FormData(this);
        Swal.fire({ title: 'Saving…', allowOutsideClick: false, didOpen: function () { Swal.showLoading(); } });
        fetch('update_masterlist_student.php', { method: 'POST', body: fd })
            .then(function (r) { return r.json(); })
            .then(function (data) {
                if (data.success) {
                    Swal.fire('Saved', data.message, 'success').then(function () { location.reload(); });
                } else {
                    Swal.fire('Error', data.message || 'Save failed', 'error');
                }
            })
            .catch(function () { Swal.fire('Error', 'Request failed', 'error'); });
    });
})();
</script>
