# Campus access structure (college → course)

## Hierarchy

| Level | Who manages | Scope |
|-------|-------------|--------|
| **Coordinator** | Creates **directors** | One active director per campus |
| **Director** | Assigns **deans** | One dean per **college** on their campus |
| **Dean** | Assigns **program chairs** | One chair per **course** in their college |
| **Program chair** | Views scholars | Single **course** only |

Masterlist uploads and validation remain with the **coordinator** (by campus / `file_group`). Deans and chairs see scholars whose `course_program_enrolled` matches their assigned course (fuzzy match supported).

## Install / update catalog

```bash
/Applications/XAMPP/xamppfiles/bin/php tools/install_campus_access.php
```

This creates `schogms_colleges` and `schogms_courses`, adds `college_name` columns to assignment tables, and seeds data from `config/campus_access_catalog.php`.

## Edit colleges or courses

Edit `config/campus_access_catalog.php`, then re-run the install script (uses `INSERT IGNORE` — safe to repeat).

## Coordinator UI

**Campus directors** — `users/coordinator/directors.php`

## Legacy assignments

Older `assigned_dean` rows may still use `course_program` as a **file group** name. Re-assign deans by **college** from the director screen. Display uses `college_name` when set, otherwise falls back to `course_program`.

## Campuses in catalog

- Isulan Campus  
- Tacurong Campus  
- Kalamansig  
- Lutayan  
- Palimbang Campus  
- Bagumbayan  
