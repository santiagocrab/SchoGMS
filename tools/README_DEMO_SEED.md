# Demo test data (SchoGMS)

## Presentation kit (manual upload — all accounts)

For a **step-by-step demo** with real files to upload in order:

1. Build files: `/Applications/XAMPP/xamppfiles/bin/php tools/build_demo_package.php`
2. Read: **`demo/DEMO_GUIDE.md`** (when to upload each file per role)
3. Files live in: **`demo/files/`** (`01_registrar`, `02_coordinator`, `03_cor_cog`, `04_chairman`)
4. Logins: **`demo/ACCOUNTS.md`**

Uses file group prefix **`DEMO PRESENT |`** (5 scholars, ACCESS campus).

---

## Run the seed (auto-fill database)

**Massive (default)** — thousands of rows + hundreds of files:

```bash
/Applications/XAMPP/xamppfiles/bin/php tools/seed_demo_data.php
```

**Light** — quick smoke test (~12 TDP / campus):

```bash
/Applications/XAMPP/xamppfiles/bin/php tools/seed_demo_data.php --light
```

**Browser** (local only):

- Full: `http://localhost/SchoGMS/tools/seed_demo_data.php?key=schogms_demo&confirm=1`
- Light: `http://localhost/SchoGMS/tools/seed_demo_data.php?key=schogms_demo&confirm=1&light=1`

Running again **removes old DEMO rows** and inserts fresh data.

## What gets created (massive mode)

| Data | Count (approx.) | Notes |
|------|-----------------|--------|
| TDP masterlist | **~1,050+** | 150 per campus × 7 (split across 4 school years / file groups) |
| TES masterlist | **~490** | 70 per campus |
| Registrar | **~1,050+** | Matches TDP scholars; ~11% year mismatch for validation testing |
| COR / COG | **~2,500+ PDF files** | Under `users/coordinator/uploads/COR` and `COG` |
| Billing | **~700** | If `billing_table` exists; OR prefix `DEMO-OR-` |
| Annex 7 | **35** | 5 per campus; Pending / Approved / Rejected + files in `uploads/annex7/` |
| CSV exports | **~40+** | `DEMO_TDP_[campus]_[SY].csv`, `DEMO_TES_[campus].csv` |

**Light mode:** ~12 TDP, ~8 TES, ~10 billing per campus.

## Campuses

ACCESS, ISULAN, KALAMANSIG, BAGUMBAYAN, PALIMBANG, TACURONG, LUTAYAN

## File groups (look for this prefix)

`DEMO | TDP ACCESS SY 2024-2025`  
`DEMO | TDP ACCESS SY 2023-2024`  
`DEMO | TES ISULAN SY 2024-2025`  
`DEMO | COR ACCESS SY 2024-2025`  
etc.

## Test logins

Use your existing **coordinator** accounts (campus must match, e.g. ACCESS, ISULAN).

Generated names use pools like **REYES**, **DELA CRUZ**, **GARCIA**, **BAUTISTA** — thousands of unique combinations.

## Where to click

1. **Dashboard** — large counts per campus  
2. **TDP / TES Masterlist** — filter `DEMO | ...`  
3. **Validate TDP / TES** — pass/fail/pending mix  
4. **Requirements** — long COR/COG list  
5. **Verified scholars** — billing tab (if table exists)  
6. **Chairman → Annex 7** — many pending/approved/rejected rows  
7. **Chairman → Verified scholars** — system-wide masterlist + billing  

## Disk space

Massive mode creates **1,500+** small PDF files (~1 KB each). Re-running the seed replaces DB rows; old PDFs with new seq suffixes may accumulate — safe to delete `users/coordinator/uploads/COR/*_D*.pdf` and `COG/*` if needed.
