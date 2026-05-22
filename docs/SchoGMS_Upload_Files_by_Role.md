# SchoGMS — Files to Upload by User Role

Based on the implemented PHP modules under `users/` and `admin/`.  
**Students and guardians do not log in** — there is no scholar-facing upload.

---

## Quick summary by file type

| File type | Used for | Typical roles |
|-----------|----------|---------------|
| **.xlsx / .xls / .csv** | CHED TDP/TES masterlists, registrar masterlist, Annex 7, billing | Coordinator, chairman, registrar (some pages) |
| **.pdf / .jpg / .jpeg / .png** | COR and COG (one file per scholar) | Registrar, coordinator |
| **.rar / .zip** | COR bulk (registrar `cor-cog.php` only) | Registrar |
| **None** | Admin, director, dean, program chair (forms only, no document upload) | See below |

---

## 1. System Administrator (`admin/`)

| Upload? | Details |
|---------|---------|
| **No file uploads** | Creates users and campuses via forms only (`user-management.php`, `submit_user.php`). Verification email is sent automatically; admin does not upload spreadsheets or documents. |

---

## 2. Chairman (`users/chairman/`)

| # | What to upload | File types | Page (menu) | Handler | Stored in |
|---|----------------|------------|-------------|---------|----------|
| 1 | **CHED TDP masterlist** | `.xlsx`, `.xls`, `.csv` (max ~10MB on dedicated page) | Upload TDP → `upload_ched_tdp.php` | `submit_ched_tdp_upload.php` | MySQL `ched_masterlist` + `users/chairman/uploads/` |
| 2 | **CHED TDP masterlist** (alternate UI) | `.xlsx`, `.xls`, `.csv` | CHED TDP Masterlist → `ched_masterlist.php` | `submit_ched_masterlist.php` / `process_excel.php` | `ched_masterlist` |
| 3 | **CHED TES masterlist** | `.xlsx`, `.xls`, `.csv` | TES Masterlist → `ched_masterlist_tes.php` | `submit_ched_masterlist.php` | `ched_masterlist_tes` |
| 4 | **Registrar masterlist** (enrollment reference) | `.xlsx`, `.xls`, `.csv` | Masterlist → `masterlist.php` | `submit_master_list.php` | `registrar_master_list` |
| 5 | **Program / student data** (program list context) | `.xlsx`, `.xls`, `.csv` | Program List → `program_list.php` | AJAX upload on page | Varies (program reference) |
| 6 | **Annex 7** (optional on chairman UI) | `.xlsx`, `.xls`, `.csv` | Annex 7 → `anex-form2.php` (upload modal) | Page script | `file_submissions` / `forms/` |
| 7 | **Verified scholars — billing / payments** | `.xlsx`, `.xls` | Verified Scholars → `verified-scholars.php` | `process_excel.php` | `billing_table` *(if table exists)* |

**Chairman does not upload COR/COG** in the main sidebar flow (that is registrar/coordinator work).

**Download templates:** `download_verified_scholars_template.php` (billing Excel layout).

---

## 3. Scholarship Coordinator (`users/coordinator/`)

Campus-scoped (one campus per coordinator account).

| # | What to upload | File types | Page (menu) | Handler | Stored in |
|---|----------------|------------|-------------|---------|----------|
| 1 | **CHED TDP masterlist** | `.xlsx`, `.xls`, `.csv` | TDP masterlist → `ched_masterlist.php` | `submit_ched_masterlist.php` | `ched_masterlist` |
| 2 | **CHED TES masterlist** | `.xlsx`, `.xls`, `.csv` | TES masterlist → `ched_masterlist_tes.php` | `submit_ched_masterlist.php` | `ched_masterlist_tes` |
| 3 | **Registrar masterlist** | `.xlsx`, `.xls`, `.csv` | Registrar Masterlist → `masterlist.php` | `submit_master_list.php` | `registrar_master_list` |
| 4 | **Registrar masterlist** (from validate screens) | `.xlsx`, `.xls`, `.csv` | Validate TDP → `validate.php`; Validate Remarks → `validate_remarks.php` | `submit_master_list.php` | `registrar_master_list` |
| 5 | **CHED masterlist** (enrollment status flow) | `.xlsx`, `.xls`, `.csv` | Enrollment Status → `enrollment_status.php` | `submit_ched_masterlist.php` | `ched_masterlist` |
| 6 | **COR documents** | `.pdf`, `.jpg`, `.jpeg`, `.png` (multiple files) | COR & COG → `cor.php` or `cor-cog.php` | `submit_document_cor_cog.php` | `document_uploads` + disk |
| 7 | **COG documents** | `.pdf`, `.jpg`, `.jpeg`, `.png` (multiple files) | COR & COG → `cog.php` or `cor-cog.php` | `submit_document_cor_cog.php` | `document_uploads` + disk |
| 8 | **COR / COG per scholar** (optional) | `.pdf`, `.jpg`, `.jpeg`, `.png` | Edit student on masterlist (modal) | `update_masterlist_student.php` | `document_uploads` |
| 9 | **Annex 7 — Scholarship Grant Utilization** | `.xlsx`, `.xls`, `.csv` | Submit form → `submit_form.php` | `upload_file.php` | `file_submissions` + `users/coordinator/forms/` |
| 10 | **Verified scholars — billing** | `.xlsx`, `.xls` | Verified scholars → `verified-scholars.php` | `process_excel.php` | `billing_table` *(if table exists)* |

**Naming guide for COR/COG:** `download_cor_cog_naming_guide.php` — e.g. `LASTNAME, FIRSTNAME MIDDLENAME.pdf`.

**Coordinator does not approve Annex 7** — only submits; chairman approves.

---

## 4. Registrar (`users/registrar/`)

| # | What to upload | File types | Page (menu) | Handler | Stored in |
|---|----------------|------------|-------------|---------|----------|
| 1 | **Registrar masterlist** | `.xlsx`, `.xls`, `.csv` | Registrar Masterlist → `masterlist.php` | `submit_master_list.php` | `registrar_master_list` |
| 2 | **COR documents** | `.pdf`, `.jpg`, `.jpeg`, `.png`, `.rar`, `.zip` (multiple) | COR & COG → `cor-cog.php` | `submit_document_mongodb.php` *(MySQL `document_uploads` in current build)* | `document_uploads` + `uploads/` |
| 3 | **CHED masterlist** (legacy/extra page) | `.xlsx`, `.xls`, `.csv` | `ched_masterlist.php` *(not always in sidebar)* | `submit_ched_masterlist.php` | `ched_masterlist` |
| 4 | **Verified scholars — billing** (legacy page) | `.xlsx`, `.xls` | `verified-scholars.php` *(legacy)* | `process_excel.php` | `billing_table` |

**Bulk COR tool (power users):** `upload_all_cor.php` → batch upload to `submit_document_mongodb_batch.php`.

**Test/dev only (not for production paper):** `upload_test.php`, `upload_standalone.php`, `test_upload_simple.php`.

---

## 5. Campus Director (`users/director/`)

| Upload? | Details |
|---------|---------|
| **No document/spreadsheet uploads** | Assigns **college deans** via form on `dean.php` (`submit_chair.php` / `submit_document.php` — user account fields, not COR/COG files). |

---

## 6. College Dean (`users/dean/`)

| Upload? | Details |
|---------|---------|
| **No document/spreadsheet uploads** | Assigns **program chairs** via `program-chair.php` (form: name, email, college, course — no file attachment). |

---

## 7. Program Chair (`users/program-chair/`)

| Upload? | Details |
|---------|---------|
| **No uploads** | Views TDP/TES lists (`tdp.php`, `tes.php`) — read-only monitoring. |

---

## 8. Public / Guest (before login)

| Upload? | Details |
|---------|---------|
| **No uploads** | Login (`index.php`) and email verification (`verify.php`) only accept text (email, password, 6-digit code). |

---

## What each upload needs (metadata)

Users usually must also provide **campus**, **academic year**, **semester**, and/or **file group** (batch name) on the upload form — not only the file.

| Upload category | Typical metadata on the form |
|-----------------|------------------------------|
| CHED TDP/TES | Campus / sheet name, file group, sometimes filename tag |
| Registrar masterlist | Campus (session), academic year, semester, file group |
| COR / COG | Campus, category (COR or COG), file group, academic year, semester |
| Annex 7 | Campus, coordinator user id & email (from session) |
| Billing Excel | Rows per scholar; campus in column F (see verified scholars guide) |

---

## Where files are saved on the server (disk)

| Content | Typical folder |
|---------|----------------|
| Annex 7 (coordinator) | `users/coordinator/forms/` |
| Chairman TDP uploads | `users/chairman/uploads/` |
| COR / COG / documents | `uploads/COR/`, `uploads/COG/`, `uploads/documents/{campus}/...`, `users/registrar/uploads/` |
| Demo seed Annex 7 | `users/coordinator/uploads/annex7/` |

Database table **`document_uploads`** stores path, campus, category, file name, file group.  
Database table **`file_submissions`** stores Annex 7 metadata and status (Pending / Approved / Rejected).

---

## Roles × upload matrix (at a glance)

| Role | Excel/CSV | COR/COG PDF/images | Annex 7 | Billing Excel |
|------|:---------:|:------------------:|:---------:|:-------------:|
| Administrator | — | — | — | — |
| Chairman | Yes (all campuses) | — | Optional UI | Yes |
| Coordinator | Yes (one campus) | Yes | Yes | Yes |
| Registrar | Yes | Yes (+ RAR/ZIP on cor-cog) | — | Legacy page |
| Director | — | — | — | — |
| Dean | — | — | — | — |
| Program Chair | — | — | — | — |

---

## For your thesis / documentation

- **Primary operational uploads:** CHED masterlists (Excel), registrar masterlist (Excel), COR/COG (PDF/images), Annex 7 (Excel), optional billing (Excel).
- **No self-service scholar uploads** — beneficiaries are rows in imported lists, not portal users.

See also: [SchoGMS_System_Documentation.md](./SchoGMS_System_Documentation.md), [Chapter3_System_Design.md](./Chapter3_System_Design.md).
