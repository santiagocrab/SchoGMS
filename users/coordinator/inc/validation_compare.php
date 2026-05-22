<?php
/**
 * Case-insensitive comparison helpers for TDP/TES validation.
 */

if (!function_exists('schogms_normalize_text')) {
    function schogms_normalize_text(string $value): string
    {
        $value = trim($value);
        if ($value === '') {
            return '';
        }
        if (function_exists('mb_strtolower')) {
            $value = mb_strtolower($value, 'UTF-8');
        } else {
            $value = strtolower($value);
        }
        $value = preg_replace('/\s+/u', ' ', $value);
        $value = str_replace(['.', ',', '-', '_'], ' ', $value);
        $value = preg_replace('/\s+/u', ' ', trim($value));

        return $value;
    }
}

if (!function_exists('schogms_courses_match')) {
    function schogms_courses_match(string $chedCourse, string $registrarCourse): bool
    {
        $a = schogms_normalize_text($chedCourse);
        $b = schogms_normalize_text($registrarCourse);
        if ($a === '' || $b === '') {
            return false;
        }
        if ($a === $b) {
            return true;
        }

        return str_contains($a, $b) || str_contains($b, $a);
    }
}

if (!function_exists('schogms_canonical_year_level')) {
    function schogms_canonical_year_level(string $value): string
    {
        $v = schogms_normalize_text($value);
        if ($v === '') {
            return '';
        }

        $ordinals = [
            1 => '1st year',
            2 => '2nd year',
            3 => '3rd year',
            4 => '4th year',
            5 => '5th year',
        ];

        if (preg_match('/^(\d)$/', $v, $m)) {
            $n = (int) $m[1];
            return $ordinals[$n] ?? $v;
        }

        if (preg_match('/^(\d+)\s*(st|nd|rd|th)\s*year?$/', $v, $m)) {
            $n = (int) $m[1];
            return $ordinals[$n] ?? ($m[1] . $m[2] . ' year');
        }

        if (preg_match('/^year\s*(\d+)$/', $v, $m)) {
            $n = (int) $m[1];
            return $ordinals[$n] ?? $v;
        }

        $wordMap = [
            'first' => '1st year',
            'second' => '2nd year',
            'third' => '3rd year',
            'fourth' => '4th year',
            'fifth' => '5th year',
        ];
        foreach ($wordMap as $word => $canonical) {
            if (str_contains($v, $word)) {
                return $canonical;
            }
        }

        return $v;
    }
}

if (!function_exists('schogms_year_levels_match')) {
    function schogms_year_levels_match(string $chedYear, string $registrarYear): bool
    {
        $a = schogms_canonical_year_level($chedYear);
        $b = schogms_canonical_year_level($registrarYear);
        if ($a === '' || $b === '') {
            return false;
        }
        if ($a === $b) {
            return true;
        }

        if (preg_match('/^(\d+)/', $a, $ma) && preg_match('/^(\d+)/', $b, $mb)) {
            return $ma[1] === $mb[1];
        }

        return str_contains($a, $b) || str_contains($b, $a);
    }
}
