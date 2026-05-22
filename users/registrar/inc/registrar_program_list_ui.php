<?php
/**
 * Render helpers for registrar program list summaries.
 */

if (!function_exists('schogms_registrar_render_program_list_stats')) {
    /** @param array{file_groups: int, files: int, scholars: int, programs: int} $totals */
    function schogms_registrar_render_program_list_stats(array $totals): void
    {
        ?>
        <div class="row pl-stat-grid mb-3">
            <div class="col-6 col-md-3 mb-2">
                <div class="pl-stat-card">
                    <span class="pl-stat-card-label">File groups</span>
                    <span class="pl-stat-card-value"><?= number_format((int) ($totals['file_groups'] ?? 0)) ?></span>
                </div>
            </div>
            <div class="col-6 col-md-3 mb-2">
                <div class="pl-stat-card">
                    <span class="pl-stat-card-label">Upload files</span>
                    <span class="pl-stat-card-value"><?= number_format((int) ($totals['files'] ?? 0)) ?></span>
                </div>
            </div>
            <div class="col-6 col-md-3 mb-2">
                <div class="pl-stat-card">
                    <span class="pl-stat-card-label">Scholars</span>
                    <span class="pl-stat-card-value"><?= number_format((int) ($totals['scholars'] ?? 0)) ?></span>
                </div>
            </div>
            <div class="col-6 col-md-3 mb-2">
                <div class="pl-stat-card">
                    <span class="pl-stat-card-label">Programs / courses</span>
                    <span class="pl-stat-card-value"><?= number_format((int) ($totals['programs'] ?? 0)) ?></span>
                </div>
            </div>
        </div>
        <?php
    }
}

if (!function_exists('schogms_registrar_render_program_list_programs_table')) {
    /**
     * @param list<array<string, mixed>> $programs
     */
    function schogms_registrar_render_program_list_programs_table(array $programs, string $tableId): void
    {
        ?>
        <h6 class="font-weight-bold text-secondary mb-2">Programs summary</h6>
        <p class="small text-muted mb-2">
            Each row is a <strong>course/program enrolled</strong> from the CHED masterlist (e.g. BSIT, BSED English).
        </p>
        <div class="table-responsive mb-4">
            <table id="<?= htmlspecialchars($tableId, ENT_QUOTES, 'UTF-8') ?>" class="table table-sm table-bordered table-hover w-100 pl-summary-table">
                <thead>
                    <tr>
                        <th>Program / course</th>
                        <th class="text-right">Scholars</th>
                    </tr>
                </thead>
                <tbody>
                <?php if ($programs === []): ?>
                    <tr class="pl-empty-row"><td colspan="2" class="text-muted text-center py-3">No programs recorded.</td></tr>
                <?php else: ?>
                    <?php foreach ($programs as $row): ?>
                        <tr>
                            <td><?= schogms_e((string) ($row['program_name'] ?? '')) ?></td>
                            <td class="text-right"><?= number_format((int) ($row['scholar_count'] ?? 0)) ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php
    }
}

if (!function_exists('schogms_registrar_render_program_list_file_groups_table')) {
    /**
     * @param list<array<string, mixed>> $fileGroups
     */
    function schogms_registrar_render_program_list_file_groups_table(array $fileGroups, string $tableId): void
    {
        ?>
        <h6 class="font-weight-bold text-secondary mb-2">File group summary</h6>
        <p class="small text-muted mb-2">
            A <strong>file group</strong> is the batch label used when uploading (often academic year + semester).
            Multiple Excel files can share one file group.
        </p>
        <div class="table-responsive mb-4">
            <table id="<?= htmlspecialchars($tableId, ENT_QUOTES, 'UTF-8') ?>" class="table table-sm table-bordered table-hover w-100 pl-summary-table">
                <thead>
                    <tr>
                        <th>File group</th>
                        <th class="text-right">Files</th>
                        <th class="text-right">Scholars</th>
                        <th class="text-right">Programs</th>
                        <th>Programs included</th>
                    </tr>
                </thead>
                <tbody>
                <?php if ($fileGroups === []): ?>
                    <tr class="pl-empty-row"><td colspan="5" class="text-muted text-center py-3">No file groups found.</td></tr>
                <?php else: ?>
                    <?php foreach ($fileGroups as $row): ?>
                        <tr>
                            <td class="font-weight-medium"><?= schogms_e((string) ($row['file_group'] ?? '')) ?></td>
                            <td class="text-right"><?= number_format((int) ($row['file_count'] ?? 0)) ?></td>
                            <td class="text-right"><?= number_format((int) ($row['total_entries'] ?? 0)) ?></td>
                            <td class="text-right"><?= number_format((int) ($row['program_count'] ?? 0)) ?></td>
                            <td class="pl-programs-cell" title="<?= schogms_e((string) ($row['programs_summary'] ?? '')) ?>">
                                <?php
                                $progs = $row['programs'] ?? [];
                                if (is_array($progs) && $progs !== []) {
                                    foreach (array_slice($progs, 0, 6) as $p) {
                                        echo '<span class="pl-program-tag">' . schogms_e((string) $p) . '</span>';
                                    }
                                    if (count($progs) > 6) {
                                        echo '<span class="pl-program-more">+' . (count($progs) - 6) . ' more</span>';
                                    }
                                } else {
                                    echo '<span class="text-muted">—</span>';
                                }
                                ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php
    }
}

if (!function_exists('schogms_registrar_render_program_list_batches_table')) {
    /**
     * @param list<array<string, mixed>> $batches
     */
    function schogms_registrar_render_program_list_batches_table(array $batches, string $tableId): void
    {
        ?>
        <h6 class="font-weight-bold text-secondary mb-2">Upload batches (by file)</h6>
        <div class="table-responsive">
            <table id="<?= htmlspecialchars($tableId, ENT_QUOTES, 'UTF-8') ?>" class="table table-striped table-bordered table-sm w-100">
                <thead>
                    <tr>
                        <th>File group</th>
                        <th>Filename</th>
                        <th class="text-right">Scholars</th>
                        <th class="text-right">Programs</th>
                        <th>Programs in this file</th>
                    </tr>
                </thead>
                <tbody>
                <?php if ($batches === []): ?>
                    <tr class="pl-empty-row">
                        <td colspan="5" class="text-center text-muted py-4">No upload files for this section yet.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($batches as $row): ?>
                        <tr>
                            <td><?= schogms_e((string) ($row['file_group'] ?? '')) ?></td>
                            <td><?= schogms_e((string) ($row['filename'] ?? '')) ?></td>
                            <td class="text-right"><?= number_format((int) ($row['total_entries'] ?? 0)) ?></td>
                            <td class="text-right"><?= number_format((int) ($row['program_count'] ?? 0)) ?></td>
                            <td class="pl-programs-cell" title="<?= schogms_e((string) ($row['programs_summary'] ?? '')) ?>">
                                <?php
                                $progs = $row['programs'] ?? [];
                                if (is_array($progs) && $progs !== []) {
                                    echo '<span class="small">' . schogms_e((string) ($row['programs_summary'] ?? '')) . '</span>';
                                } else {
                                    echo '<span class="text-muted">—</span>';
                                }
                                ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php
    }
}
