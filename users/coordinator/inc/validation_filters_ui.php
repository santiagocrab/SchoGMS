<?php
require_once __DIR__ . '/validation_filters.php';
/**
 * Multi-select filter form for validate.php / validate_tes.php
 *
 * @var string $vfProgram  tdp|tes
 * @var string $vfCampus   campus / sheet_name
 * @var array  $vfGet      $_GET
 * @var array  $vfOptions  from schogms_validation_filter_options()
 * @var string $vfPage     validate.php | validate_tes.php
 */
$vfProgram = $vfProgram ?? 'tdp';
$vfCampus = $vfCampus ?? '';
$vfGet = $vfGet ?? [];
$vfOptions = $vfOptions ?? [];
$vfPage = $vfPage ?? 'validate.php';

$multiKeys = [
    'sex' => 'Sex',
    'course' => 'Course',
    'year_level' => 'Year level',
    'status' => 'Enrollment status',
    'validation' => 'Validation',
];
if ($vfProgram === 'tdp') {
    $multiKeys = ['batch' => 'Batch (file group)', 'batch_no' => 'Batch no.'] + $multiKeys;
} else {
    $multiKeys = ['batch' => 'File group', 'batch_no' => 'Batch no.'] + $multiKeys;
}

$statusChoices = ['Enrolled', 'Not Enrolled'];
$validationChoices = ['Validated', 'Failed', 'Pending'];

function vf_selected_multi(array $get, string $key, string $value): bool
{
    return in_array($value, schogms_filter_get_array($get, $key), true);
}
?>
<div class="card border-secondary mb-3">
    <div class="card-body py-3">
        <h6 class="card-title mb-2">Filters <small class="text-muted">(hold Ctrl / Cmd to select multiple)</small></h6>
        <form method="get" action="<?php echo htmlspecialchars($vfPage); ?>" id="validationFilterForm">
            <input type="hidden" name="bulk" value="1">
            <input type="hidden" name="sheet_name" value="<?php echo htmlspecialchars($vfCampus); ?>">
            <?php if ($vfPage === 'validate_remarks.php'): ?>
            <input type="hidden" name="program" value="<?php echo htmlspecialchars($vfProgram); ?>">
            <?php endif; ?>

            <div class="row">
                <?php foreach ($multiKeys as $key => $label):
                    if ($key === 'status') {
                        $choices = $statusChoices;
                    } elseif ($key === 'validation') {
                        $choices = $validationChoices;
                    } else {
                        $choices = $vfOptions[$key] ?? [];
                    }
                    ?>
                <div class="col-md-4 col-lg-3 mb-2">
                    <label class="small font-weight-bold d-block"><?php echo htmlspecialchars($label); ?></label>
                    <select name="<?php echo htmlspecialchars($key); ?>[]" class="form-control form-control-sm" multiple size="4">
                        <?php foreach ($choices as $choice): ?>
                        <option value="<?php echo htmlspecialchars($choice); ?>"
                            <?php echo vf_selected_multi($vfGet, $key, $choice) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($choice); ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <?php endforeach; ?>

                <div class="col-md-6 col-lg-4 mb-2">
                    <label class="small font-weight-bold d-block">Search</label>
                    <input type="text" name="search" class="form-control form-control-sm"
                           placeholder="Name, app no., course…"
                           value="<?php echo htmlspecialchars((string) ($vfGet['search'] ?? '')); ?>">
                </div>
            </div>

            <div class="mt-2">
                <button type="submit" class="btn btn-primary btn-sm">Apply filters</button>
                <a href="<?php echo htmlspecialchars(
                    $vfPage . '?bulk=1&sheet_name=' . rawurlencode($vfCampus)
                    . ($vfPage === 'validate_remarks.php' ? '&program=' . rawurlencode($vfProgram) : '')
                ); ?>"
                   class="btn btn-secondary btn-sm">Clear all</a>
            </div>
        </form>
    </div>
</div>

<?php
$vfExportQs = schogms_validation_export_query($vfProgram, $vfCampus, $vfGet);
$vfExports = $vfProgram === 'tes'
    ? [
        ['validated_masterlist_tes.php', 'Export TES Form', 'exportTesForm'],
        ['validated_masterlist_tes_delisting.php', 'Export TES Delisting', 'exportTesDelist'],
        ['validated_remarks_tes.php', 'Export Remarks', 'exportTesRemarks'],
    ]
    : [
        ['validated_masterlist.php', 'Export Annex Form 2', 'exportTdpAnnex'],
        ['validated_masterlist_delisting.php', 'Export Delisting Form', 'exportTdpDelist'],
        ['validated_remarks.php', 'Export Remarks', 'exportTdpRemarks'],
    ];
?>
<div class="row mb-3" id="validationExportRow">
    <?php foreach ($vfExports as $ex): ?>
    <div class="col-md-4 mb-2">
        <a class="btn btn-success btn-rounded btn-block"
           href="<?php echo htmlspecialchars($ex[0] . '?' . $vfExportQs); ?>"
           target="_blank" rel="noopener"
           id="<?php echo htmlspecialchars($ex[2]); ?>">
            <?php echo htmlspecialchars($ex[1]); ?>
        </a>
    </div>
    <?php endforeach; ?>
</div>
<p class="small text-muted mb-3">Exports include only scholars matching the filters above.</p>
