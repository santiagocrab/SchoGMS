<?php
/**
 * Read-only TDP/TES scholar masterlist for campus director (scoped to assigned campus).
 */

if (!function_exists('schogms_director_scholar_program_label')) {
    function schogms_director_scholar_program_label(string $type): string
    {
        return $type === 'tes' ? 'TES' : 'TDP';
    }
}

if (!function_exists('schogms_director_scholar_count')) {
    function schogms_director_scholar_count(mysqli $conn, string $campus, string $type, string $courseFilter = ''): int
    {
        $campus = trim($campus);
        if ($campus === '') {
            return 0;
        }
        $isTes = ($type === 'tes');
        if ($isTes) {
            $sql = 'SELECT COUNT(*) AS n FROM ched_masterlist_tes WHERE campus = ?';
        } else {
            $sql = 'SELECT COUNT(*) AS n FROM ched_masterlist WHERE sheet_name = ?';
        }
        if ($courseFilter !== '') {
            $sql .= ' AND course_program_enrolled = ?';
        }
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            return 0;
        }
        if ($courseFilter !== '') {
            $stmt->bind_param('ss', $campus, $courseFilter);
        } else {
            $stmt->bind_param('s', $campus);
        }
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        return (int) ($row['n'] ?? 0);
    }
}

if (!function_exists('schogms_director_scholar_course_options')) {
    /** @return list<string> */
    function schogms_director_scholar_course_options(mysqli $conn, string $campus, string $type): array
    {
        $campus = trim($campus);
        if ($campus === '') {
            return [];
        }
        $isTes = ($type === 'tes');
        if ($isTes) {
            $stmt = $conn->prepare(
                'SELECT DISTINCT course_program_enrolled AS c FROM ched_masterlist_tes
                 WHERE campus = ? AND course_program_enrolled IS NOT NULL AND TRIM(course_program_enrolled) <> ""
                 ORDER BY c ASC'
            );
        } else {
            $stmt = $conn->prepare(
                'SELECT DISTINCT course_program_enrolled AS c FROM ched_masterlist
                 WHERE sheet_name = ? AND course_program_enrolled IS NOT NULL AND TRIM(course_program_enrolled) <> ""
                 ORDER BY c ASC'
            );
        }
        if (!$stmt) {
            return [];
        }
        $stmt->bind_param('s', $campus);
        $stmt->execute();
        $res = $stmt->get_result();
        $out = [];
        while ($row = $res->fetch_assoc()) {
            $out[] = (string) $row['c'];
        }
        $stmt->close();

        return $out;
    }
}

if (!function_exists('schogms_director_render_scholar_list')) {
    function schogms_director_render_scholar_list(mysqli $conn, string $campus, string $type): void
    {
        $campus = trim($campus);
        $type = $type === 'tes' ? 'tes' : 'tdp';
        $label = schogms_director_scholar_program_label($type);
        $listPage = $type === 'tes' ? 'tes.php' : 'tdp.php';
        $courseFilter = trim((string) ($_GET['course'] ?? ''));

        if ($campus === '') {
            echo '<div class="alert alert-warning">No campus is assigned to your director account. '
                . 'Ask the coordinator to assign your campus before viewing scholar lists.</div>';

            return;
        }

        $total = schogms_director_scholar_count($conn, $campus, $type, $courseFilter);
        $courses = schogms_director_scholar_course_options($conn, $campus, $type);
        $campusEsc = htmlspecialchars($campus, ENT_QUOTES, 'UTF-8');
        ?>
        <div class="row mb-3">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-body py-3 d-flex flex-wrap align-items-center justify-content-between">
                        <div>
                            <h4 class="card-title mb-1"><?= htmlspecialchars($label, ENT_QUOTES, 'UTF-8') ?> scholars — <?= $campusEsc ?></h4>
                            <p class="text-muted mb-0 small">Read-only campus masterlist for monitoring enrollment.</p>
                        </div>
                        <div class="text-right">
                            <span class="badge badge-primary badge-pill px-3 py-2" style="font-size:1rem;"><?= number_format($total) ?> total</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <form method="get" action="<?= htmlspecialchars($listPage, ENT_QUOTES, 'UTF-8') ?>" class="form-inline mb-3">
                            <label class="mr-2 font-weight-medium" for="course">Program / course</label>
                            <select name="course" id="course" class="form-control mr-2" style="min-width:280px;max-width:100%;">
                                <option value="">All programs</option>
                                <?php foreach ($courses as $c): ?>
                                    <option value="<?= htmlspecialchars($c, ENT_QUOTES, 'UTF-8') ?>"
                                        <?= $courseFilter === $c ? 'selected' : '' ?>><?= htmlspecialchars($c, ENT_QUOTES, 'UTF-8') ?></option>
                                <?php endforeach; ?>
                            </select>
                            <button type="submit" class="btn btn-primary">Apply filter</button>
                            <?php if ($courseFilter !== ''): ?>
                                <a href="<?= htmlspecialchars($listPage, ENT_QUOTES, 'UTF-8') ?>" class="btn btn-light ml-2">Clear</a>
                            <?php endif; ?>
                        </form>
                        <div class="table-responsive">
                        <?php
                        $isTes = ($type === 'tes');
                        if ($isTes) {
                            $sql = 'SELECT id, campus, seq, app_no, award_no, lastname, firstname, extname, middlename, sex, birthdate, course_program_enrolled, year_level
                                    FROM ched_masterlist_tes WHERE campus = ?';
                        } else {
                            $sql = 'SELECT id, sheet_name, seq, app_no, award_no, lastname, firstname, extname, middlename, sex, birthdate, course_program_enrolled, year_level
                                    FROM ched_masterlist WHERE sheet_name = ?';
                        }
                        if ($courseFilter !== '') {
                            $sql .= ' AND course_program_enrolled = ?';
                        }
                        $sql .= ' ORDER BY lastname ASC, firstname ASC LIMIT 50000';
                        $stmt = $conn->prepare($sql);
                        if (!$stmt) {
                            echo '<div class="alert alert-danger">Could not load scholar list.</div>';

                            return;
                        }
                        if ($courseFilter !== '') {
                            $stmt->bind_param('ss', $campus, $courseFilter);
                        } else {
                            $stmt->bind_param('s', $campus);
                        }
                        $stmt->execute();
                        $result = $stmt->get_result();
                        ?>
                            <table id="director_scholar_table" class="table table-striped table-bordered nowrap" style="width:100%;">
                                <thead>
                                    <tr>
                                        <th>Campus</th>
                                        <th>Seq</th>
                                        <th>App no.</th>
                                        <th>Award no.</th>
                                        <th>Last name</th>
                                        <th>First name</th>
                                        <th>Ext.</th>
                                        <th>Middle name</th>
                                        <th>Sex</th>
                                        <th>Birthdate</th>
                                        <th>Course / program</th>
                                        <th>Year level</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php while ($row = $result->fetch_assoc()): ?>
                                    <?php
                                    $campusCell = $isTes ? ($row['campus'] ?? '') : ($row['sheet_name'] ?? '');
                                    ?>
                                    <tr>
                                        <td><?= htmlspecialchars((string) $campusCell, ENT_QUOTES, 'UTF-8') ?></td>
                                        <td><?= htmlspecialchars((string) ($row['seq'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                                        <td><?= htmlspecialchars((string) ($row['app_no'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                                        <td><?= htmlspecialchars((string) ($row['award_no'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                                        <td><?= htmlspecialchars((string) ($row['lastname'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                                        <td><?= htmlspecialchars((string) ($row['firstname'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                                        <td><?= htmlspecialchars((string) ($row['extname'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                                        <td><?= htmlspecialchars((string) ($row['middlename'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                                        <td><?= htmlspecialchars((string) ($row['sex'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                                        <td><?= htmlspecialchars((string) ($row['birthdate'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                                        <td><?= htmlspecialchars((string) ($row['course_program_enrolled'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                                        <td><?= htmlspecialchars((string) ($row['year_level'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                                    </tr>
                                <?php endwhile; ?>
                                </tbody>
                            </table>
                        <?php $stmt->close(); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }
}
