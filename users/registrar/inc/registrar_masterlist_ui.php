<?php
/**
 * UI helpers for registrar masterlist page.
 */

if (!function_exists('schogms_registrar_masterlist_active_chips')) {
    /**
     * @param array<string, string> $filters
     * @return list<array{label: string, href: string}>
     */
    function schogms_registrar_masterlist_active_chips(array $filters): array
    {
        $labels = [
            'category' => 'Source file',
            'file_group' => 'File group',
            'academic_year' => 'Academic year',
            'semester' => 'Semester',
            'search' => 'Search',
        ];
        $chips = [];
        foreach ($filters as $key => $value) {
            $value = trim((string) $value);
            if ($value === '' || !isset($labels[$key])) {
                continue;
            }
            $next = $filters;
            unset($next[$key]);
            $next['page'] = 1;
            if (isset($filters['limit']) && $filters['limit'] !== '') {
                $next['limit'] = $filters['limit'];
            }
            $chips[] = [
                'label' => $labels[$key] . ': ' . $value,
                'href' => schogms_registrar_masterlist_build_url($next, null),
            ];
        }

        return $chips;
    }
}

if (!function_exists('schogms_registrar_masterlist_render_cor_cog')) {
    /** @param array{has_cor: bool, has_cog: bool, cor_path: string, cog_path: string} $docs */
    function schogms_registrar_masterlist_render_cor_cog(array $docs): void
    {
        $hasCor = !empty($docs['has_cor']);
        $hasCog = !empty($docs['has_cog']);
        $corPath = (string) ($docs['cor_path'] ?? '');
        $cogPath = (string) ($docs['cog_path'] ?? '');

        if ($hasCor && $corPath !== '') {
            echo '<a href="view_document.php?file=' . htmlspecialchars(urlencode($corPath), ENT_QUOTES, 'UTF-8')
                . '&type=COR" target="_blank" class="badge badge-success rml-doc-badge" title="View COR">COR</a> ';
        } elseif ($hasCor) {
            echo '<span class="badge badge-success rml-doc-badge">COR</span> ';
        }

        if ($hasCog && $cogPath !== '') {
            echo '<a href="view_document.php?file=' . htmlspecialchars(urlencode($cogPath), ENT_QUOTES, 'UTF-8')
                . '&type=COG" target="_blank" class="badge badge-primary rml-doc-badge" title="View COG">COG</a>';
        } elseif ($hasCog) {
            echo '<span class="badge badge-primary rml-doc-badge">COG</span>';
        }

        if (!$hasCor && !$hasCog) {
            echo '<span class="badge badge-secondary rml-doc-badge">No COR/COG</span>';
        } elseif ($hasCor && !$hasCog) {
            echo '<span class="badge badge-light border rml-doc-badge text-muted">No COG</span>';
        } elseif (!$hasCor && $hasCog) {
            echo '<span class="badge badge-light border rml-doc-badge text-muted">No COR</span>';
        }
    }
}

if (!function_exists('schogms_registrar_masterlist_render_pagination')) {
    /**
     * @param array<string, scalar|null> $baseParams
     */
    function schogms_registrar_masterlist_render_pagination(int $page, int $totalPages, array $baseParams): void
    {
        if ($totalPages <= 1) {
            return;
        }
        echo '<nav class="mb-2" aria-label="Masterlist pages"><ul class="pagination pagination-sm mb-0">';
        if ($page > 1) {
            echo '<li class="page-item"><a class="page-link" href="'
                . htmlspecialchars(schogms_registrar_masterlist_build_url($baseParams, $page - 1), ENT_QUOTES, 'UTF-8')
                . '">Previous</a></li>';
        }
        for ($p = max(1, $page - 2); $p <= min($totalPages, $page + 2); $p++) {
            $active = $p === $page ? ' active' : '';
            echo '<li class="page-item' . $active . '"><a class="page-link" href="'
                . htmlspecialchars(schogms_registrar_masterlist_build_url($baseParams, $p), ENT_QUOTES, 'UTF-8')
                . '">' . $p . '</a></li>';
        }
        if ($page < $totalPages) {
            echo '<li class="page-item"><a class="page-link" href="'
                . htmlspecialchars(schogms_registrar_masterlist_build_url($baseParams, $page + 1), ENT_QUOTES, 'UTF-8')
                . '">Next</a></li>';
        }
        echo '</ul></nav>';
    }
}
