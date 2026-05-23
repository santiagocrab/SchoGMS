# SchoGMS — Full demo guide (all accounts)

Use this folder for **live presentations** and **screenshots**. All uploadable files live under **`demo/files/`** after you run the builder once.

**How uploads work (step-by-step, what happens next):** see **[`docs/SchoGMS_Upload_Workflow.md`](../docs/SchoGMS_Upload_Workflow.md)**.

## Build the file package

```bash
/Applications/XAMPP/xamppfiles/bin/php tools/build_demo_package.php
```

Or in the browser:  
`http://localhost/SchoGMS/tools/build_demo_package.php?key=schogms_demo`

That creates PDFs, Excel samples, and `demo/files/manifest.json`.

## Optional: auto-fill the database (large dataset)

For dashboards full of numbers (not manual upload):

```bash
/Applications/XAMPP/xamppfiles/bin/php tools/seed_demo_data.php --light
```

Filter in the UI: file groups starting with **`DEMO |`**.  
The **presentation kit** below uses prefix **`DEMO PRESENT |`** so it does not clash with the seed script.

---

## Test accounts (login at `index.php`)

| # | Role | Username / email (examples) | Password | Dashboard |
|---|------|-----------------------------|----------|-----------|
| 1 | **Coordinator (ACCESS)** | `access`, `Coordinator`, `test@mail` | `password123` | `/users/coordinator/` |
| 2 | **Registrar (ACCESS)** | `registrar access`, `registrar` | `password123` | `/users/registrar/` |
| 3 | **Chairman** | `chairman`, `james.remegio@wvsu.edu.ph` | `password123` (reset if needed) | `/users/chairman/` |
| 4 | **Director** | `Campus Director Isulan` (or your campus director) | `password123` | `/users/director/` |
| 5 | **Dean** | `DemoDean` (or dean from Director → Assign deans) | `schogms123` | `/users/dean/` |
| 6 | **Program chair** | `ProgramChairDemo` or `programchair.schogms.demo@local` | `schogms123` | `/users/program-chair/` |
| 7 | **Admin** | `admin` | `admin123` | `/admin-12-02/` |

**Before demo:** run `reset-mongodb-passwords.php` or `FINAL_FIX_PASSWORDS.php` if MySQL logins fail.  
**Program chair / dean:** create with `tools/add_one_program_chair.php` and `tools/add_one_assigned_dean.php` if missing.

Accounts **5–6** mostly **view** data (assignments, counts) — they do not use the files in `demo/files/` unless you extend the demo.

---

## Presentation campus & file groups

| Purpose | File group name (copy into form) |
|---------|----------------------------------|
| Registrar masterlist | `DEMO PRESENT \| Registrar ACCESS SY 2024-2025` |
| CHED TDP masterlist | `DEMO PRESENT \| TDP ACCESS SY 2024-2025` |
| CHED TES masterlist | `DEMO PRESENT \| TES ACCESS SY 2024-2025` |
| COR documents | `DEMO PRESENT \| COR ACCESS SY 2024-2025` |
| COG documents | `DEMO PRESENT \| COG ACCESS SY 2024-2025` |
| Annex 7 submit form | `DEMO PRESENT \| Annex7 ACCESS` |
| Billing import (chairman) | `DEMO PRESENT \| Billing ACCESS SY 2024-2025` |

**Scholars in this kit (5):** REYES, MARIA; DELA CRUZ, JUAN; GARCIA, SOPHIA; BAUTISTA, CARLO; FERNANDEZ, ANALYN — COR/COG PDF names must stay **`Lastname, Firstname Middlename.pdf`**.

---

## When to upload what (recommended order)

| Step | When (in your talk) | Account | Page | File(s) from `demo/files/` | File group |
|------|---------------------|---------|------|----------------------------|------------|
| **1** | *“Registrar loads official enrolment data first.”* | Registrar | Masterlist → Upload | `01_registrar/registrar_masterlist_ACCESS_demo.xlsx` | Registrar row above |
| **2** | *“Coordinator uploads CHED TDP grantees.”* | Coordinator (ACCESS) | CHED TDP Masterlist → Upload | `02_coordinator/ched_tdp_ACCESS_SY2024-2025_demo.xlsx` | TDP row above |
| **3** | *“Chairman gets a notification — file group pending.”* | Chairman | Review → **File groups** → Pending | *(no file — approve step 2 upload)* | TDP file group |
| **4** | *“Coordinator uploads proof of registration.”* | Coordinator | COR → Bulk upload | All PDFs in `03_cor_cog/COR/` | COR row above |
| **5** | *“Then grades (COG).”* | Coordinator | COG → Bulk upload | All PDFs in `03_cor_cog/COG/` | COG row above |
| **6** | *“Validate against registrar.”* | Coordinator | Validate TDP | *(no upload)* | Filter `DEMO PRESENT \| TDP…` |
| **7** | *“Coordinator submits utilization report.”* | Coordinator | Submit Form | `02_coordinator/annex7_ACCESS_demo.xlsx` | Annex7 row above |
| **8** | *“Chairman approves Annex / reviews program list.”* | Chairman | File groups / Program list | *(review only)* | — |
| **9** | *“Optional TES track.”* | Coordinator | CHED TES Masterlist | `02_coordinator/ched_tes_ACCESS_demo.xlsx` | TES row above |
| **10** | *“Verified scholars & billing.”* | Chairman | Verified scholars → import | `04_chairman/billing_verified_scholars_ACCESS_demo.xlsx` | Billing row above |

**Timing tip:** Steps **1 → 2 → 3** show the full **file group approval** workflow (registrar → coordinator → chairman). Steps **4–5** show **COR/COG** matching scholar names. Step **6** is the “validation” screenshot.

---

## Per-account cheat sheet (what to show, no upload)

| Role | What to demo | Upload? |
|------|----------------|---------|
| **Coordinator** | Dashboard, masterlists, validate, COR/COG, directors, submit form | Yes — steps 2, 4–5, 7, 9 |
| **Registrar** | Masterlist, COR/COG status | Yes — step 1 |
| **Chairman** | File groups (pending/approved), program list tabs, notifications bell, verified scholars | Approve step 3; optional step 10 |
| **Director** | Assign deans by college | No files in kit |
| **Dean** | Assign program chairs | No files in kit |
| **Program chair** | TDP/TES counts for one course | No files in kit |
| **Admin** | User management, campuses | No files in kit |

---

## Folder map (`demo/files/`)

```
demo/files/
├── manifest.json              ← machine-readable upload order
├── 01_registrar/              ← Step 1
├── 02_coordinator/            ← Steps 2, 7, 9 (TDP, TES, Annex 7)
├── 03_cor_cog/
│   ├── COR/                   ← Step 4 (5 PDFs)
│   └── COG/                   ← Step 5 (5 PDFs)
└── 04_chairman/               ← Step 10 billing sample
```

---

## Troubleshooting

| Problem | Fix |
|---------|-----|
| COR upload says “does not match masterlist” | Upload **step 2** first; filenames must match `Lastname, Firstname Middlename.pdf` |
| “File group too long” | Use the exact labels in the table above (under 512 characters) |
| Chairman login fails | `php tools/activate_chairman.php your@email` or reset password scripts |
| Empty dashboard | Run `seed_demo_data.php --light` **or** complete steps 1–2 manually |

---

## Related tools

| Tool | Purpose |
|------|---------|
| `tools/seed_demo_data.php` | Thousands of `DEMO \|` rows + PDFs (screenshots) |
| `tools/seed_pending_file_group.php` | One pending file group for chairman UI only |
| `tools/build_demo_package.php` | Regenerates this `demo/files/` kit |
| `LOGIN-GUIDE.php` | Browser login reference |
