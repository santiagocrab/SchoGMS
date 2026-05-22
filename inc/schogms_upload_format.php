<?php
/**
 * Downloadable upload templates + on-page instructions for SchoGMS masterlist uploads.
 */

if (!function_exists('schogms_upload_format_types')) {
    /** @return array<string, array{title: string, filename: string, description: string}> */
    function schogms_upload_format_types(): array
    {
        return [
            'ched_tdp' => [
                'title' => 'CHED TDP masterlist',
                'filename' => 'SchoGMS_TDP_Masterlist_Template.csv',
                'description' => 'Tulong-Dunong Program (ched_masterlist)',
            ],
            'ched_tes' => [
                'title' => 'CHED TES masterlist',
                'filename' => 'SchoGMS_TES_Masterlist_Template.csv',
                'description' => 'Tertiary Education Subsidy (ched_masterlist_tes)',
            ],
            'registrar_masterlist' => [
                'title' => 'Registrar masterlist',
                'filename' => 'SchoGMS_Registrar_Masterlist_Template.csv',
                'description' => 'Registrar student export (registrar_master_list)',
            ],
        ];
    }
}

if (!function_exists('schogms_upload_format_download_href')) {
    function schogms_upload_format_download_href(string $type, string $basePath = '../../'): string
    {
        $basePath = rtrim($basePath, '/') . '/';
        $type = strtolower(trim($type));

        return htmlspecialchars($basePath . 'inc/download_upload_template.php?type=' . rawurlencode($type), ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('schogms_upload_format_template_csv')) {
    /** @return array{content: string, filename: string}|null */
    function schogms_upload_format_template_csv(string $type): ?array
    {
        $types = schogms_upload_format_types();
        $type = strtolower(trim($type));
        if (!isset($types[$type])) {
            return null;
        }

        $lines = [];
        if ($type === 'ched_tdp') {
            $lines = [
                ['(Row 1–3: leave column A blank — do not type in column A on these rows.)'],
                [''],
                [''],
                ['SEQ', 'APP NO', 'AWARD NO', 'LASTNAME', 'FIRSTNAME', 'EXTNAME', 'MIDDLENAME', 'SEX', 'BIRTHDATE', 'COURSE/PROGRAM ENROLLED', 'YEAR LEVEL', 'TOTAL UNITS ENROLLED', 'STATUS OF ENROLLMENT', 'REMARKS'],
                ['1', 'APP-0001', 'AW-0001', 'DELA CRUZ', 'JUAN', '', 'CARLOS', 'M', '2001-05-15', 'Bachelor of Science in Information Technology', '1', '21', 'ENROLLED', ''],
            ];
        } elseif ($type === 'ched_tes') {
            $lines = [
                ['SEQ', 'APP NO', 'LASTNAME', 'FIRSTNAME', 'EXT', 'MIDDLENAME', 'SEX', 'COURSE/PROGRAM ENROLLED', 'YEAR LEVEL', 'STREET', 'TOWN/CITY', 'CONTACT', 'BATCH NO'],
                ['1', 'TES-0001', 'SANTOS', 'MARIA', '', 'REYES', 'F', 'Bachelor of Science in Education', '2', 'Purok 1', 'Isulan', '09171234567', 'BATCH-01'],
            ];
        } else {
            $lines = [
                ['last_name', 'first_name', 'middle_name', 'ext_name', 'id_number', 'gender', 'student_type', 'year_level', 'attended', 'course', 'scholarship', 'gpa', 'enrolled', 'email_address', 'mobile_number'],
                ['DELA CRUZ', 'JUAN', 'CARLOS', '', '2024-00001', 'M', 'REGULAR', '1', 'YES', 'BS Information Technology', 'TDP', '1.75', 'YES', 'juan@example.edu', '09171234567'],
            ];
        }

        $out = fopen('php://temp', 'r+');
        if ($out === false) {
            return null;
        }
        fwrite($out, "\xEF\xBB\xBF");
        foreach ($lines as $row) {
            fputcsv($out, $row);
        }
        rewind($out);
        $content = stream_get_contents($out);
        fclose($out);

        return [
            'content' => $content !== false ? $content : '',
            'filename' => $types[$type]['filename'],
        ];
    }
}

if (!function_exists('schogms_upload_format_render_guide')) {
    /**
     * @param string $type ched_tdp|ched_tes|registrar_masterlist
     * @param string $basePath relative path to project root from current page (e.g. ../../)
     */
    function schogms_upload_format_render_guide(string $type, string $basePath = '../../', bool $compact = false): void
    {
        $types = schogms_upload_format_types();
        $type = strtolower(trim($type));
        if (!isset($types[$type])) {
            return;
        }
        $meta = $types[$type];
        $href = schogms_upload_format_download_href($type, $basePath);
        $cardClass = $compact ? 'border-info mb-3' : 'border-primary mb-4';
        ?>
        <div class="card <?= $cardClass ?>" id="upload-format-guide">
            <div class="card-body">
                <h5 class="card-title text-primary mb-2">Download format file &amp; how to prepare it</h5>
                <p class="text-muted small mb-2">
                    Use the template for <strong><?= htmlspecialchars($meta['title'], ENT_QUOTES, 'UTF-8') ?></strong>
                    (<?= htmlspecialchars($meta['description'], ENT_QUOTES, 'UTF-8') ?>).
                    Save as <strong>.xlsx</strong> or <strong>.csv</strong> before uploading.
                </p>
                <p class="mb-3">
                    <a href="<?= $href ?>" class="btn btn-outline-primary btn-sm" download>
                        <i data-feather="download" class="feather-icon"></i>
                        Download <?= htmlspecialchars($meta['filename'], ENT_QUOTES, 'UTF-8') ?>
                    </a>
                </p>
                <?php if ($type === 'ched_tdp'): ?>
                    <h6 class="font-weight-medium">Steps (TDP)</h6>
                    <ol class="small text-muted mb-2 pl-3">
                        <li>Download the template and open it in Excel or LibreOffice.</li>
                        <li><strong>Rows 1–3:</strong> leave <strong>column A empty</strong> (notes may go in other columns only).</li>
                        <li><strong>Row 4 onward:</strong> enter one scholar per row. Column <strong>A = SEQ</strong> (required on every data row).</li>
                        <li>Columns <strong>A–N</strong>: SEQ, App no., Award no., names, sex, birthdate, course/program, year level, units, enrollment status, remarks.</li>
                        <li>On the upload form, choose <strong>campus</strong>, <strong>academic year</strong>, <strong>semester</strong>, and a <strong>file group</strong> label (e.g. <code>CHED TDP 2024-2025 1st Sem</code>).</li>
                        <li>Upload the saved file. The system reads data from row 4+ (or auto-detects if row 3 contains a <code>SEQ</code> header).</li>
                    </ol>
                <?php elseif ($type === 'ched_tes'): ?>
                    <h6 class="font-weight-medium">Steps (TES)</h6>
                    <ol class="small text-muted mb-2 pl-3">
                        <li>Download the template and open it in Excel or LibreOffice.</li>
                        <li><strong>Row 1</strong> must be the header row (SEQ, APP NO, names, etc.).</li>
                        <li><strong>Row 2 onward:</strong> one scholar per row; do not leave SEQ (column A) blank until you are done.</li>
                        <li>Columns <strong>A–M</strong>: SEQ through Batch no. (see template header).</li>
                        <li>Select <strong>campus</strong> on the upload form — it is stored with every row (not taken from the Excel sheet).</li>
                        <li>Set <strong>file group</strong> to identify this batch (academic year + semester recommended).</li>
                    </ol>
                <?php else: ?>
                    <h6 class="font-weight-medium">Steps (Registrar masterlist)</h6>
                    <ol class="small text-muted mb-2 pl-3">
                        <li>Download the template — it shows the <strong>first columns</strong> of the registrar export.</li>
                        <li><strong>Row 1</strong> = headers, <strong>row 2+</strong> = scholar rows (same as your registrar system export).</li>
                        <li>If your export has more columns (through column BA), keep the same order as the registrar file; the template is a shortened sample.</li>
                        <li>Use a clear <strong>file group</strong> name on upload so you can filter and review batches on the masterlist page.</li>
                        <li>Names with <strong>ñ</strong>: use proper encoding in Excel; the system fixes common <code>?</code> issues on display.</li>
                    </ol>
                <?php endif; ?>
                <p class="small text-muted mb-0">
                    <strong>Tip:</strong> Do not delete empty rows in the middle of the list. End the list with a completely blank row (empty SEQ) if your tool adds trailing rows.
                </p>
            </div>
        </div>
        <?php
    }
}

if (!function_exists('schogms_upload_format_modal_hint')) {
    /**
     * Short hint for upload modals (masterlist pages).
     *
     * @param string|null $fullPageUrl e.g. upload_ched_tdp.php for chairman dedicated upload tab
     */
    function schogms_upload_format_modal_hint(string $type, string $basePath = '../../', ?string $fullPageUrl = null): void
    {
        $types = schogms_upload_format_types();
        $type = strtolower(trim($type));
        if (!isset($types[$type])) {
            return;
        }
        $href = schogms_upload_format_download_href($type, $basePath);
        ?>
        <div class="alert alert-light border small mb-3">
            <strong>Format file:</strong>
            <a href="<?= $href ?>" class="btn btn-outline-primary btn-sm ml-1" download>Download template</a>
            <?php if ($fullPageUrl !== null && $fullPageUrl !== ''): ?>
                <a href="<?= htmlspecialchars($fullPageUrl, ENT_QUOTES, 'UTF-8') ?>" class="btn btn-link btn-sm">Full upload guide</a>
            <?php endif; ?>
            <?php if ($type === 'ched_tdp'): ?>
                <p class="mb-0 mt-2">Leave <strong>column A blank on rows 1–3</strong>, then enter scholars from row 4 (A = SEQ). Or use a <code>SEQ</code> header row — the system auto-detects. Columns A–N.</p>
            <?php elseif ($type === 'ched_tes'): ?>
                <p class="mb-0 mt-2"><strong>Row 1</strong> = headers, <strong>row 2+</strong> = data. Columns A–M. Pick campus on the form below.</p>
            <?php else: ?>
                <p class="mb-0 mt-2"><strong>Row 1</strong> = headers, <strong>row 2+</strong> = scholar rows. Match your registrar export column order.</p>
            <?php endif; ?>
        </div>
        <?php
    }
}
