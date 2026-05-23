# SchoGMS — Full demonstration script (presenter copy)

Use this document as your **talk track** for a live demo. Pair it with the sample files in [`demo/files/`](../demo/files/) and the technical walkthrough in [`SchoGMS_Upload_Workflow.md`](SchoGMS_Upload_Workflow.md).

**Campus for this kit:** `ACCESS`  
**File group prefix:** `DEMO PRESENT | …` (copy exactly from tables below)

---

## Before you start (15 minutes)

### 1. Build the upload file kit

```bash
/Applications/XAMPP/xamppfiles/bin/php tools/build_demo_package.php
```

Or browser: `http://localhost/SchoGMS/tools/build_demo_package.php?key=schogms_demo`

This creates Excel/PDF samples under `demo/files/` and updates `demo/files/manifest.json`.

### 2. Run the all-in-one prep script (optional)

```bash
/Applications/XAMPP/xamppfiles/bin/php tools/prepare_demo_presentation.php
```

Prints account checklist + upload order in the terminal.

### 3. Open these browser tabs

| Tab | URL |
|-----|-----|
| Login | `http://localhost/SchoGMS/index.php` |
| Admin | `http://localhost/SchoGMS/admin/user-management.php` (or your admin panel URL) |
| Verify (for new users) | `http://localhost/SchoGMS/verify.php` |
| Demo files folder | Open `demo/files/` in Finder / Explorer |

### 4. Clear old service worker (if uploads fail)

In browser DevTools → Application → Service Workers → **Unregister**, then hard refresh (Cmd+Shift+R).

---

## Part A — Admin: create accounts (your live setup)

**Say:** *“SchoGMS is role-based. The admin creates campus accounts; each role sees only what they need.”*

**Where:** Admin → **User management** → **Add user**

**Default password for admin-created users:** `schogms123` (shown in the success message / email template)

| Order | Full name (example) | Email (example) | Role | Campus | After save |
|-------|---------------------|-----------------|------|--------|------------|
| 1 | Demo Chairman | `chairman.demo@schogms.local` | **Chairman** | *(none)* | **Active immediately** — only one chairman allowed |
| 2 | Demo Registrar ACCESS | `registrar.demo@schogms.local` | **Registrar** | **ACCESS** | **Pending** → verify on `verify.php` |
| 3 | Demo Coordinator ACCESS | `coordinator.demo@schogms.local` | **Coordinator** | **ACCESS** | **Pending** → verify |
| 4 | Demo Director Isulan | `director.demo@schogms.local` | **Director** | **ISULAN** (or your campus) | **Pending** → verify |

**Say while creating:** *“Registrar and coordinator are scoped to one campus. Chairman sees all campuses. Director manages deans for a campus.”*

### Verify pending accounts

**Say:** *“New users verify email before first login.”*

1. Open `verify.php`
2. Enter email + **6-digit code** from admin success popup (or email if SMTP works)
3. Login at `index.php` with **email or name** + `schogms123`

**Shortcut (skip admin if time is short):**

```bash
/Applications/XAMPP/xamppfiles/bin/php tools/fix_demo_logins.php
```

Uses password `password123` for coordinator/registrar/chairman/director — see [`demo/ACCOUNTS.md`](../demo/ACCOUNTS.md).

### Dean & program chair (no file uploads — show hierarchy)

| Step | Account | Where | Say |
|------|---------|-------|-----|
| A5 | **Director** logs in | Director → Assign deans | *“Director picks deans per college.”* |
| A6 | Create **Dean** | Director UI or `tools/add_one_assigned_dean.php` | Password often `schogms123` |
| A7 | **Dean** logs in | Dean → Program chair | *“Dean assigns program chairs per course.”* |
| A8 | Create **Program chair** | Dean UI or `tools/add_one_program_chair.php` | *“Program chair views TDP/TES counts for one program.”* |

---

## Part B — Upload file groups (copy/paste labels)

| Purpose | File group label |
|---------|------------------|
| Registrar masterlist | `DEMO PRESENT \| Registrar ACCESS SY 2024-2025` |
| CHED TDP | `DEMO PRESENT \| TDP ACCESS SY 2024-2025` |
| CHED TES | `DEMO PRESENT \| TES ACCESS SY 2024-2025` |
| COR documents | `DEMO PRESENT \| COR ACCESS SY 2024-2025` |
| COG documents | `DEMO PRESENT \| COG ACCESS SY 2024-2025` |
| Annex 7 | `DEMO PRESENT \| Annex7 ACCESS` |
| Billing (chairman) | `DEMO PRESENT \| Billing ACCESS SY 2024-2025` |

**Academic year / semester on CHED upload modal:** `2024-2025` · `1st Semester`

**Five demo scholars (names must match across files):**

| # | Name on masterlist | COR/COG filename |
|---|-------------------|------------------|
| 1 | REYES, MARIA SANTOS | `REYES, MARIA SANTOS.pdf` |
| 2 | DELA CRUZ, JUAN TORRES | `DELA CRUZ, JUAN TORRES.pdf` |
| 3 | GARCIA, SOPHIA MENDOZA | `GARCIA, SOPHIA MENDOZA.pdf` |
| 4 | BAUTISTA, CARLO AQUINO | `BAUTISTA, CARLO AQUINO.pdf` |
| 5 | FERNANDEZ, ANALYN CASTILLO | `FERNANDEZ, ANALYN CASTILLO.pdf` |

---

## Part C — Live demo flow (45–60 minutes)

### Step 0 — Login & roles overview (~3 min)

**Account:** Coordinator (ACCESS)  
**Say:** *“This is the campus coordinator dashboard — TDP, TES, validation, COR/COG, and Annex 7.”*  
**Show:** Sidebar, notification bell (empty until uploads), profile menu.

---

### Step 1 — Registrar masterlist (~5 min)

| | |
|--|--|
| **Login** | Registrar (ACCESS) |
| **Page** | Registrar → **Masterlist** → **Upload** |
| **File** | `demo/files/01_registrar/registrar_masterlist_ACCESS_demo.xlsx` |
| **File group** | `DEMO PRESENT \| Registrar ACCESS SY 2024-2025` |
| **AY / Sem** | 2024-2025 · 1st Semester |

**Say:** *“The registrar loads official enrolment first. Validation later compares CHED grantees to this list.”*

**Show:** Table with 5 scholars; note campus = ACCESS.

**Next:** Log out → Coordinator.

---

### Step 2 — Coordinator CHED TDP upload (~5 min)

| | |
|--|--|
| **Login** | Coordinator (ACCESS) |
| **Page** | **TDP Masterlist** → **Upload masterlist** |
| **File** | `demo/files/02_coordinator/ched_tdp_ACCESS_SY2024-2025_demo.xlsx` |
| **File group** | `DEMO PRESENT \| TDP ACCESS SY 2024-2025` |
| **Campus** | ACCESS (auto from account) |
| **AY / Sem** | 2024-2025 · 1st Semester |

**Say:** *“Coordinator uploads the CHED grantee list. This creates a **pending file group** for chairman approval.”*

**Show:** Success toast; rows on TDP masterlist; **Edit** on one row (optional).

**Next:** Log out → Chairman.

---

### Step 3 — Chairman approves file group (~4 min)

| | |
|--|--|
| **Login** | Chairman |
| **Page** | **Review → File groups** → tab **TDP** → filter **Pending** |
| **Action** | **Approve** batch `DEMO PRESENT \| TDP ACCESS SY 2024-2025` |
| **File** | *(none — UI only)* |

**Say:** *“Chairman got a notification when the coordinator uploaded. Approving unlocks the workflow for this campus batch.”*

**Show:** Notification bell; uploader name; status → Approved.

**Next:** Log out → Coordinator.

---

### Step 4 — COR bulk upload (~5 min)

| | |
|--|--|
| **Login** | Coordinator |
| **Page** | **COR** → **Bulk upload COR** |
| **Files** | All PDFs in `demo/files/03_cor_cog/COR/` (select all 5) |
| **File group** | `DEMO PRESENT \| COR ACCESS SY 2024-2025` |

**Say:** *“COR filenames must match masterlist names: Lastname, Firstname Middlename.pdf”*

**Show:** Accepted count = 5; COR links on TDP masterlist.

---

### Step 5 — COG bulk upload (~3 min)

| | |
|--|--|
| **Page** | **COG** → **Bulk upload COG** |
| **Files** | All PDFs in `demo/files/03_cor_cog/COG/` |
| **File group** | `DEMO PRESENT \| COG ACCESS SY 2024-2025` |

**Say:** *“Same naming rule as COR. Both documents drive enrollment status on the masterlist.”*

---

### Step 6 — Validate TDP (~5 min)

| | |
|--|--|
| **Page** | **Validate TDP** (or Validate TDP with bulk=1) |
| **Upload** | **None** — comparison only |

**Say:** *“System matches CHED rows to registrar: name, course, year level, and COR/COG presence.”*

**Show:** Validated vs failed counts; open one scholar **Edit** if validation failed (fix course/year).

---

### Step 7 — Annex 7 submit (~4 min)

| | |
|--|--|
| **Page** | **Submit Form** → **Upload** |
| **File** | `demo/files/02_coordinator/annex7_ACCESS_demo.xlsx` |
| **File group** | `DEMO PRESENT \| Annex7 ACCESS` |

**Say:** *“Annex 7 is the utilization report — separate from CHED file groups. Chairman reviews on Annex 7 page.”*

**Next:** Chairman → **Annex 7** → approve/decline.

---

### Step 8 — Optional TES track (~4 min)

| | |
|--|--|
| **Page** | **TES Masterlist** → **Upload masterlist** |
| **File** | `demo/files/02_coordinator/ched_tes_ACCESS_demo.xlsx` |
| **File group** | `DEMO PRESENT \| TES ACCESS SY 2024-2025` |

**Say:** *“TES uses the same approval flow on the TES tab in File groups.”*

---

### Step 9 — Chairman billing / verified scholars (~4 min)

| | |
|--|--|
| **Login** | Chairman |
| **Page** | **Verified scholars** → **Upload** (billing import) |
| **File** | `demo/files/04_chairman/billing_verified_scholars_ACCESS_demo.xlsx` |
| **File group** | `DEMO PRESENT \| Billing ACCESS SY 2024-2025` |

**Say:** *“Billing data is separate from masterlists — OR numbers and payment amounts for verified scholars.”*

---

### Step 10 — Wrap-up: hierarchy roles (~5 min)

| Role | Show | Say |
|------|------|-----|
| **Director** | TDP/TES campus view, assign deans | Campus oversight |
| **Dean** | Program chair assignment | College level |
| **Program chair** | TDP/TES for one course | Program level |
| **Admin** | User list, campuses | Account governance |

---

## Part D — Complete upload file index

| Step | Role | Page | File path (under `demo/files/`) | DB / table |
|------|------|------|----------------------------------|------------|
| 1 | Registrar | Masterlist upload | `01_registrar/registrar_masterlist_ACCESS_demo.xlsx` | `registrar_master_list` |
| 2 | Coordinator | TDP Masterlist upload | `02_coordinator/ched_tdp_ACCESS_SY2024-2025_demo.xlsx` | `ched_masterlist` + file group batch |
| 3 | Chairman | File groups approve | — | `schogms_file_group_batches` |
| 4 | Coordinator | COR bulk | `03_cor_cog/COR/*.pdf` (5 files) | `document_uploads` |
| 5 | Coordinator | COG bulk | `03_cor_cog/COG/*.pdf` (5 files) | `document_uploads` |
| 6 | Coordinator | Validate TDP | — | updates validation on `ched_masterlist` |
| 7 | Coordinator | Submit Form | `02_coordinator/annex7_ACCESS_demo.xlsx` | `file_submissions` |
| 8 | Coordinator | TES Masterlist | `02_coordinator/ched_tes_ACCESS_demo.xlsx` | `ched_masterlist_tes` |
| 9 | Chairman | Verified scholars | `04_chairman/billing_verified_scholars_ACCESS_demo.xlsx` | `billing_table` |

**Download templates (empty format):** Coordinator/Chairman upload modals → **Download template**, or `inc/download_upload_template.php`.

---

## Part E — Troubleshooting (during demo)

| Problem | Quick fix |
|---------|-----------|
| Login fails | Run `tools/fix_demo_logins.php` |
| Pending user cannot login | Complete `verify.php` with admin’s 6-digit code |
| COR rejected | Upload TDP first; check PDF names |
| Edit save HTTP 500 | Hard refresh; unregister service worker |
| Chairman no pending | Wrong tab (TDP vs TES) or batch already approved |
| Empty dashboard | Complete steps 1–2 or run `tools/seed_demo_data.php --light` |

---

## One-page cheat sheet (print this)

```
PREP:  php tools/build_demo_package.php
       php tools/prepare_demo_presentation.php

ADMIN: User management → Add user (default pass: schogms123)
       Verify at verify.php (except chairman)

ORDER:
  1 Registrar  → registrar_masterlist_ACCESS_demo.xlsx
  2 Coordinator → ched_tdp_ACCESS_SY2024-2025_demo.xlsx
  3 Chairman   → File groups → Approve TDP
  4 Coordinator → COR/*.pdf
  5 Coordinator → COG/*.pdf
  6 Coordinator → Validate TDP
  7 Coordinator → annex7_ACCESS_demo.xlsx
  8 Chairman   → Annex 7 review
  9 (opt) Coordinator → ched_tes_ACCESS_demo.xlsx
 10 (opt) Chairman → billing_verified_scholars_ACCESS_demo.xlsx
```

---

## Related docs

- Upload details: [`SchoGMS_Upload_Workflow.md`](SchoGMS_Upload_Workflow.md)
- Demo files & accounts: [`demo/DEMO_GUIDE.md`](../demo/DEMO_GUIDE.md), [`demo/ACCOUNTS.md`](../demo/ACCOUNTS.md)
- Machine-readable order: [`demo/files/manifest.json`](../demo/files/manifest.json)
