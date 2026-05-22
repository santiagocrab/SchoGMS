# SchoGMS — System Pages, Purpose, and Features Documentation

**Document type:** System implementation / Chapter 4 documentation  
**System:** Scholarship and Grants Management System (SchoGMS)  
**Version note:** Based on codebase review (roles, navigation, and primary PHP modules under `users/` and `admin/`).

---

## Table of Contents

1. [Overview of SchoGMS User Accounts](#a-overview-of-schogms-user-accounts)
2. [Purpose of Each Account Type](#b-purpose-of-each-account-type)
3. [Page Documentation Per Account Type](#c-page-documentation-per-account-type)
4. [Screenshot Checklist for the Paper](#e-screenshot-checklist-for-the-paper)
5. [User Access and Feature Matrix](#f-user-access-and-feature-matrix)
6. [Summary of System Functionality](#g-summary-of-system-functionality)
7. [Global Screenshot Instructions](#h-global-screenshot-instructions)
8. [Two-Factor Authentication (Email Verification)](#two-factor-authentication-email-verification)

---

## A. Overview of SchoGMS User Accounts

SchoGMS (Scholarship and Grants Management System) supports institutional scholarship operations for SKSU campuses: CHED TDP/TES masterlists, registrar records, Certificate of Registration (COR) and Certificate of Grades (COG), Annex 7 utilization reports, billing/verified scholars, and a campus hierarchy (Coordinator → Director → Dean → Program Chair).

| Account type | Authentication | Scope |
|--------------|----------------|--------|
| **System Administrator** | Separate `admin/` login (`admin` table) | System-wide user and campus configuration |
| **Chairman** | Main login (`users` table, MySQL) | System-wide review and masterlist oversight |
| **Scholarship Coordinator** | Main login (`users` or MongoDB legacy) | Single assigned campus |
| **Campus Director** | Main login (`users` table) | One campus; assigns deans by college |
| **College Dean** | Main login (`assigned_dean` table) | One college within a campus; assigns program chairs |
| **Program Chair** | Main login (`assigned_program_chairs` table) | One course/program; monitors TDP/TES enrollees |
| **Registrar** | Main login (`users` table) | Campus registrar operations (masterlist, documents) |
| **Student/Scholar** | *Not implemented* | Scholars are data records, not portal users |
| **Guardian** | *Not implemented* | — |

**Hierarchy (campus access model):** Coordinator creates Directors per campus → Director assigns Deans per college → Dean assigns Program Chairs per course.

---

## B. Purpose of Each Account Type

| Role | Purpose |
|------|---------|
| **Administrator** | Creates and maintains user accounts, campuses, and system-level visibility (dashboard statistics). |
| **Chairman** | Final oversight: approves Annex 7 submissions, views/uploads CHED masterlists system-wide, manages verified scholars/billing imports. |
| **Coordinator** | Campus scholarship operations: masterlists, validation against registrar data, COR/COG tracking, director assignment, Annex 7 submission to chairman, verified scholars. |
| **Director** | Campus leadership: assigns college deans under the director’s campus. |
| **Dean** | College-level oversight: assigns program chairs and monitors TDP/TES at college scope. |
| **Program Chair** | Course-level monitoring of TDP/TES scholar counts and enrollment-related views. |
| **Registrar** | Maintains registrar masterlist and uploads/views COR and COG documents. |

---

## C. Page Documentation Per Account Type

*Convention: **UI pages** are listed for screenshots. **Backend/API pages** (e.g. `process_excel.php`, `submit_*`, `fetch_chart.php`) are described under their parent page and are not separate figures unless noted.*

---

### Public (Unauthenticated)

#### Account Type: Guest / All Users (Pre-Login)

**Brief Description:** Entry point for authentication and email verification before role dashboards are accessible.

**Accessible Pages:** `index.php` (Login), `verify.php` (Email verification / 2FA), `404.php` (error page).

---

#### Page Name: Login Page (`index.php`)

| Field | Description |
|-------|-------------|
| **Page Purpose** | Authenticates users via email or username and password, then routes them to the correct role dashboard. |
| **Main Features** | Email/username and password form; error messages for restricted, pending, inactive, session expired, and invalid credentials; link to verification when account is pending. |
| **User Actions** | Enter credentials and submit (POST to `login.php`); follow “Click here to verify” when `ERROR=pending`. |
| **Displayed Data** | SchoGMS branding, error alerts. |
| **System Benefit** | Centralizes secure access and enforces account status before module use. |
| **Screenshot to Capture** | Full login screen with logo, form fields, and Sign In button (optional: pending-account error with verify link). |
| **Suggested Figure Caption** | *Figure X. Login interface of the SchoGMS showing email-based authentication and account status messaging.* |

**Screenshot instructions:** Open `http://[host]/SchoGMS/index.php` (logged out). Capture entire auth card.

---

#### Page Name: Email Verification / Two-Factor Authentication (`verify.php`)

| Field | Description |
|-------|-------------|
| **Page Purpose** | Completes account activation using a one-time verification code sent to the user’s email (referred to in the UI as 2FA). Required for new MySQL users created as `pending` until `email_verified = 1`. |
| **Main Features** | Email + 6-digit verification code form; AJAX submission to `verify_code.php`; success redirect to role home; failure logging in `verification_attempts`. |
| **User Actions** | Enter registered email and code; submit Verify; on success, automatic redirect to role dashboard. |
| **Displayed Data** | Verification form; SweetAlert success/error messages. |
| **System Benefit** | Reduces unauthorized account use and confirms email ownership before access to scholarship data. |
| **Screenshot to Capture** | (1) Verification form empty; (2) success dialog after valid code; (3) login page showing pending message with verify link. |
| **Suggested Figure Caption** | *Figure X. Two-factor email verification module of the SchoGMS used to activate newly created user accounts.* |

**Needs verification:** Email delivery depends on SMTP (`config/smtp.local.php`); if mail fails, use DB `verification_code` for demo screenshots.

---

### System Administrator

#### Account Type: System Administrator

**Brief Description:** Manages institutional users, campuses, and high-level system statistics. Uses a **separate** admin portal (`admin/`), not the main `index.php` role login.

**Accessible Pages (primary):** `admin/index.html` → `admin/login.php`, `admin/dashboard.php`, `admin/user-management.php`, `admin/data-management.php`, `admin/logs.php`, `admin/logout.php`.

**Backend/supporting:** `submit_user.php`, `submit_campus.php`, `delete_user.php`, `update_user_status.php`, `get_user_data.php`, `update_user_name.php`, `delete_campus.php`.

---

#### Page Name: Admin Login (`admin/index.html` / `admin/login.php`)

| Field | Description |
|-------|-------------|
| **Page Purpose** | Authenticates system administrators against the `admin` table. |
| **Main Features** | Username/password authentication; session creation. |
| **User Actions** | Submit admin credentials. |
| **Displayed Data** | Admin login form. |
| **System Benefit** | Separates super-user access from scholarship operational accounts. |
| **Screenshot to Capture** | Admin login page before dashboard access. |
| **Suggested Figure Caption** | *Figure X. Administrator login portal of the SchoGMS.* |

---

#### Page Name: Admin Dashboard (`admin/dashboard.php`)

| Field | Description |
|-------|-------------|
| **Page Purpose** | Presents system-wide statistics: user counts by role, MongoDB/MySQL summaries, and operational overview. |
| **Main Features** | Summary cards (coordinators, chairman, directors, registrars, deans, program chairs, active/restricted users); charts and data tables (DataTables); navigation to user management. |
| **User Actions** | View statistics; navigate via sidebar. |
| **Displayed Data** | Role-based user counts, system metrics. |
| **System Benefit** | Supports monitoring and reporting for system administrators. |
| **Screenshot to Capture** | Full dashboard with summary cards, charts, and sidebar (Dashboard, User Management). |
| **Suggested Figure Caption** | *Figure X. Administrator dashboard of the SchoGMS displaying user role statistics and system summaries.* |

---

#### Page Name: User Management (`admin/user-management.php`)

| Field | Description |
|-------|-------------|
| **Page Purpose** | Creates, lists, updates, and restricts scholarship system users and campus assignments. |
| **Main Features** | User table (name, email, role, campus, status, created date); create user modal (roles: Coordinator, Chairman, Registrar, Director); campus selection from `campuses` table; update user modal; campus management modal; triggers welcome/verification email on creation (except chairman auto-active). |
| **User Actions** | Add user; assign role and campus; edit user; change status (active/pending/restricted); add/delete campuses; delete users. |
| **Displayed Data** | User list; campus list; role and status fields. |
| **System Benefit** | Centralized provisioning aligned with campus-based scholarship workflow. |
| **Screenshot to Capture** | User management table with Create User modal open showing role and campus fields. |
| **Suggested Figure Caption** | *Figure X. User management module of the SchoGMS for creating coordinator, chairman, registrar, and director accounts.* |

---

#### Page Name: Data Management (`admin/data-management.php`)

| Field | Description |
|-------|-------------|
| **Page Purpose** | Administrative data maintenance area (linked from sidebar on some admin pages). |
| **Main Features** | Data management utilities (layout consistent with admin theme). |
| **User Actions** | Navigate sub-links (e.g. logs). |
| **Displayed Data** | Administrative data tools. |
| **System Benefit** | Supports database/content maintenance. |
| **Screenshot to Capture** | Data management page with sidebar. |
| **Suggested Figure Caption** | *Figure X. Data management interface of the SchoGMS administrator module.* |

**Needs verification:** Main dashboard sidebar may hide this link (commented); access via direct URL or submenu if enabled.

---

#### Page Name: System Logs (`admin/logs.php`)

| Field | Description |
|-------|-------------|
| **Page Purpose** | Displays system or activity logs for audit and troubleshooting. |
| **Main Features** | Log listing/table. |
| **User Actions** | Browse logs. |
| **Displayed Data** | Timestamped log entries. |
| **System Benefit** | Supports accountability and error diagnosis. |
| **Screenshot to Capture** | Logs page with visible log entries. |
| **Suggested Figure Caption** | *Figure X. System logs view of the SchoGMS for administrative audit.* |

---

#### Page Name: Admin Logout (`admin/logout.php`)

| Field | Description |
|-------|-------------|
| **Page Purpose** | Ends administrator session. |
| **User Actions** | Click Logout from user menu. |
| **Screenshot to Capture** | Optional—dropdown showing Logout. Usually omitted from paper unless demonstrating session security. |

---

### Chairman (Scholarship Chairman)

#### Account Type: Chairman

**Brief Description:** System-wide authority for Annex 7 approval, CHED masterlist oversight, TDP uploads, program lists, and verified scholars/billing at all campuses.

**Accessible Pages (sidebar):** `index.php`, `anex-form2.php`, `ched_masterlist.php`, `ched_masterlist_tes.php`, `upload_ched_tdp.php`, `program_list.php`, `verified-scholars.php`, `change_password.php`, `logout.php`.

**Supporting:** `view_annex_file.php` (in-browser preview), `submit_ched_masterlist.php`, `submit_ched_tdp_upload.php`, `process_excel.php`, `download_verified_scholars_template.php`, `update_status.php`.

---

#### Page Name: Chairman Dashboard (`users/chairman/index.php`)

| Field | Description |
|-------|-------------|
| **Page Purpose** | Summary of TDP/TES scholar counts (all campuses) and pending Annex 7 reviews. |
| **Main Features** | Stat cards; quick links to review and masterlists. |
| **User Actions** | Open Annex 7 review or masterlists from cards/links. |
| **Displayed Data** | Total TDP, TES, pending Annex 7 counts. |
| **System Benefit** | Immediate visibility of workload requiring chairman action. |
| **Screenshot to Capture** | Dashboard with three summary cards and quick links; sidebar visible. |
| **Suggested Figure Caption** | *Figure X. Chairman dashboard of the SchoGMS showing system-wide TDP, TES, and pending Annex 7 counts.* |

---

#### Page Name: Annex 7 Review (`users/chairman/anex-form2.php`)

| Field | Description |
|-------|-------------|
| **Page Purpose** | Reviews coordinator-submitted Annex 7 (Scholarship Grant Utilization) files; approves or declines submissions. |
| **Main Features** | Submission table (campus, file, status, dates); **View** — in-page scrollable preview (Excel via SheetJS); Approve / Decline for pending items; Save actions for pending records. |
| **User Actions** | View file; approve; decline; filter/browse submissions. |
| **Displayed Data** | File metadata, status badges, preview table. |
| **System Benefit** | Formal approval gate before utilization reports are accepted system-wide. |
| **Screenshot to Capture** | Review table plus open preview modal showing spreadsheet content. |
| **Suggested Figure Caption** | *Figure X. Annex 7 review interface of the SchoGMS chairman module with in-page document preview.* |

---

#### Page Name: TDP Masterlist — Chairman (`users/chairman/ched_masterlist.php`)

| Field | Description |
|-------|-------------|
| **Page Purpose** | Read-only/system-wide browse of CHED TDP masterlist records uploaded across campuses. |
| **Main Features** | Searchable/sortable masterlist table (DataTables). |
| **User Actions** | Search, sort, paginate; view scholar rows. |
| **Displayed Data** | Names, course, year, units, campus (`sheet_name`), file group, enrollment status, upload time. |
| **System Benefit** | Cross-campus transparency for TDP beneficiaries. |
| **Screenshot to Capture** | Masterlist table with multiple campuses visible. |
| **Suggested Figure Caption** | *Figure X. System-wide CHED TDP masterlist view accessible to the SchoGMS chairman.* |

---

#### Page Name: TES Masterlist — Chairman (`users/chairman/ched_masterlist_tes.php`)

| Field | Description |
|-------|-------------|
| **Page Purpose** | Same as TDP masterlist for TES program data. |
| **Main Features** | Parallel to TDP masterlist for `ched_masterlist_tes`. |
| **Screenshot to Capture** | TES masterlist table with sample rows. |
| **Suggested Figure Caption** | *Figure X. CHED TES masterlist overview for chairman-level monitoring in the SchoGMS.* |

---

#### Page Name: Upload TDP (`users/chairman/upload_ched_tdp.php`)

| Field | Description |
|-------|-------------|
| **Page Purpose** | Allows chairman to upload CHED TDP masterlist Excel files into MySQL `ched_masterlist`. |
| **Main Features** | Campus selector; file upload; recent uploads list. |
| **User Actions** | Select campus; upload `.xlsx`/`.xls`; submit. |
| **Displayed Data** | Upload form; recent upload history. |
| **System Benefit** | Centralized national/regional masterlist ingestion when chairman receives files from CHED. |
| **Screenshot to Capture** | Upload form and recent uploads section. |
| **Suggested Figure Caption** | *Figure X. CHED TDP masterlist upload interface of the SchoGMS chairman account.* |

---

#### Page Name: Program List (`users/chairman/program_list.php`)

| Field | Description |
|-------|-------------|
| **Page Purpose** | Displays program/course catalog used for scholarship file grouping and reporting. |
| **Main Features** | Program listing table. |
| **User Actions** | Browse programs. |
| **Displayed Data** | Program names and related metadata. |
| **System Benefit** | Standardizes program reference data across campuses. |
| **Screenshot to Capture** | Program list table. |
| **Suggested Figure Caption** | *Figure X. Program list reference page of the SchoGMS chairman module.* |

---

#### Page Name: Verified Scholars (`users/chairman/verified-scholars.php`)

| Field | Description |
|-------|-------------|
| **Page Purpose** | System-wide view of CHED masterlist scholars and billing/payment records; imports billing Excel. |
| **Main Features** | Tabs: **CHED masterlist** \| **Billing / payments**; upload billing Excel modal with format guide; campus filter on billing tab; template download. |
| **User Actions** | Upload billing file; switch tabs; filter by campus. |
| **Displayed Data** | Scholar masterlist rows; billing amounts, OR numbers, semester payment fields. |
| **System Benefit** | Consolidates verification and financial tracking for chairman reporting. |
| **Screenshot to Capture** | Page with both tabs visible (or billing tab with upload modal). |
| **Suggested Figure Caption** | *Figure X. Verified scholars and billing management page of the SchoGMS chairman showing masterlist and payment records.* |

**Needs verification:** Confirm upload success on your XAMPP instance before final paper screenshots.

---

#### Page Name: Change Password — Chairman (`users/chairman/change_password.php`)

| Field | Description |
|-------|-------------|
| **Page Purpose** | Allows chairman to update account password. |
| **User Actions** | Enter current/new password; submit. |
| **Screenshot to Capture** | Change password form (user menu → Change Password). |
| **Suggested Figure Caption** | *Figure X. Account password change screen of the SchoGMS chairman user.* |

---

### Scholarship Coordinator

#### Account Type: Scholarship Coordinator (Staff)

**Brief Description:** Primary campus scholarship operator: masterlists, validation, documents, director management, Annex 7 submission, verified scholars.

**Accessible Pages (main navigation):**  
`index.php`, `ched_masterlist.php`, `ched_masterlist_tes.php`, `validate.php`, `validate_tes.php`, `cor-cog.php`, `directors.php`, `submit_form.php`, `requirements.php`, `verified-scholars.php`, `change_password.php`, `logout.php`.

**Workflow / secondary pages:**  
`validated_masterlist.php`, `validated_masterlist_tes.php`, `validated_remarks.php`, `validated_remarks_tes.php`, `validate_remarks.php`, `enrollment_status.php`, `bulk_validate.php`, `bulk_validate_tdp.php`, `auto_validate_tdp.php`, `cor.php`, `cog.php`, `update_masterlist_student.php`, `export_success.php`, download guides, `submit_director.php`, `delete_director.php`, `process_excel.php`, `submit_ched_masterlist.php`, `submit_document_cor_cog.php`.

---

#### Page Name: Coordinator Dashboard (`users/coordinator/index.php`)

| Field | Description |
|-------|-------------|
| **Page Purpose** | Campus-scoped analytics: TDP/TES counts, file groups, charts. |
| **Main Features** | Stat cards; file group filter; charts via `fetch_dashboard_charts.php`. |
| **User Actions** | Filter by file group; navigate to modules. |
| **Displayed Data** | Campus name, record counts, chart data. |
| **System Benefit** | Data-driven campus scholarship monitoring. |
| **Screenshot to Capture** | Dashboard with stats and at least one chart; sidebar visible. |
| **Suggested Figure Caption** | *Figure X. Coordinator dashboard of the SchoGMS showing campus-level TDP and TES statistics.* |

---

#### Page Name: TDP Masterlist — Coordinator (`users/coordinator/ched_masterlist.php`)

| Field | Description |
|-------|-------------|
| **Page Purpose** | Upload and manage campus TDP masterlist; view/edit scholar rows for assigned campus. |
| **Main Features** | Excel upload; masterlist table; edit student (via `update_masterlist_student.php`). |
| **User Actions** | Upload masterlist; search; edit records. |
| **Displayed Data** | CHED scholar fields (name, course, units, enrollment status, etc.). |
| **System Benefit** | Maintains authoritative campus TDP beneficiary list. |
| **Screenshot to Capture** | Masterlist table with upload control visible. |
| **Suggested Figure Caption** | *Figure X. Campus CHED TDP masterlist management page of the SchoGMS coordinator.* |

---

#### Page Name: TES Masterlist — Coordinator (`users/coordinator/ched_masterlist_tes.php`)

| Field | Description |
|-------|-------------|
| **Page Purpose** | Campus TES masterlist upload and management (parallel to TDP). |
| **Screenshot to Capture** | TES masterlist table with sample rows. |
| **Suggested Figure Caption** | *Figure X. Campus CHED TES masterlist management page of the SchoGMS coordinator.* |

---

#### Page Name: Validate TDP (`users/coordinator/validate.php`)

| Field | Description |
|-------|-------------|
| **Page Purpose** | Compares CHED TDP masterlist against registrar data; supports bulk validation and remark updates. |
| **Main Features** | Validation workspace; bulk mode (`?bulk=1`); links to validated exports and remarks. |
| **User Actions** | Run validation; add remarks; export validated lists; open bulk validate. |
| **Displayed Data** | Matched/unmatched scholars; validation status; registrar cross-fields. |
| **System Benefit** | Automates eligibility checking and reduces manual spreadsheet work. |
| **Screenshot to Capture** | Validate TDP screen with comparison results or bulk validation panel. |
| **Suggested Figure Caption** | *Figure X. TDP validation module of the SchoGMS comparing CHED masterlist and registrar records.* |

---

#### Page Name: Validate TES (`users/coordinator/validate_tes.php`)

| Field | Description |
|-------|-------------|
| **Page Purpose** | TES-specific validation (same workflow as TDP for TES table). |
| **Screenshot to Capture** | TES validation interface. |
| **Suggested Figure Caption** | *Figure X. TES validation module of the SchoGMS.* |

---

#### Page Name: Validated Masterlist Export — TDP/TES (`validated_masterlist.php`, `validated_masterlist_tes.php`)

| Field | Description |
|-------|-------------|
| **Page Purpose** | Generates/export views of validated scholar lists after validation run. |
| **User Actions** | Export/download validated data (often Excel-oriented). |
| **Screenshot to Capture** | Export result or validated list view. |

**Needs verification:** May trigger download rather than static page—capture browser download dialog or opened export if applicable.

---

#### Page Name: COR & COG — Coordinator (`users/coordinator/cor-cog.php`)

| Field | Description |
|-------|-------------|
| **Page Purpose** | Tracks Certificate of Registration and Certificate of Grades uploads per campus/file group. |
| **Main Features** | Document listing; upload integration; naming guide download. |
| **User Actions** | Upload/view COR/COG; download naming guide. |
| **Displayed Data** | Campus, file group, category, file name, upload date. |
| **System Benefit** | Centralizes documentary requirements for scholarship compliance. |
| **Screenshot to Capture** | COR & COG table with sample uploaded documents. |
| **Suggested Figure Caption** | *Figure X. COR and COG document management page of the SchoGMS coordinator.* |

---

#### Page Name: Campus Directors (`users/coordinator/directors.php`)

| Field | Description |
|-------|-------------|
| **Page Purpose** | Creates and manages one active **Director** account per campus (campus access hierarchy). |
| **Main Features** | Director list; assign director to campus; uses `campus_access_catalog`. |
| **User Actions** | Add director; assign campus; delete/deactivate (`delete_director.php`, `submit_director.php`). |
| **Displayed Data** | Director name, email, campus, status. |
| **System Benefit** | Delegates campus leadership while coordinator retains oversight. |
| **Screenshot to Capture** | Directors table with “add director” form/modal. |
| **Suggested Figure Caption** | *Figure X. Campus director assignment interface of the SchoGMS coordinator module.* |

---

#### Page Name: Submit Form — Annex 7 (`users/coordinator/submit_form.php`)

| Field | Description |
|-------|-------------|
| **Page Purpose** | Coordinators upload Annex 7 utilization Excel for **chairman approval**. |
| **Main Features** | Format guide (row 3+ data); upload; submission status tracking. |
| **User Actions** | Upload Annex 7 file; view submission history/status. |
| **Displayed Data** | Submitted files, pending/approved/declined status. |
| **System Benefit** | Formal submission pipeline to chairman. |
| **Screenshot to Capture** | Submit form page with format instructions and upload button. |
| **Suggested Figure Caption** | *Figure X. Annex 7 submission page of the SchoGMS coordinator for chairman review.* |

---

#### Page Name: Requirements (`users/coordinator/requirements.php`)

| Field | Description |
|-------|-------------|
| **Page Purpose** | Overview of uploaded COR/COG requirement documents for the coordinator’s campus. |
| **Main Features** | Combined COR/COG listing from `document_uploads`. |
| **User Actions** | Browse uploaded requirements. |
| **Displayed Data** | Document metadata by category. |
| **System Benefit** | Quick compliance check without opening COR/COG upload UI. |
| **Screenshot to Capture** | Requirements table for one campus. |
| **Suggested Figure Caption** | *Figure X. Scholarship requirements summary page of the SchoGMS coordinator.* |

---

#### Page Name: Verified Scholars — Coordinator (`users/coordinator/verified-scholars.php`)

| Field | Description |
|-------|-------------|
| **Page Purpose** | Campus-scoped CHED masterlist view plus billing Excel import to `billing_table`. |
| **Main Features** | Upload guide; billing upload modal; masterlist table; template download. |
| **User Actions** | Upload billing Excel; view scholars. |
| **Displayed Data** | Scholar list; billing/payment columns after import. |
| **System Benefit** | Links academic masterlist to financial verification at campus level. |
| **Screenshot to Capture** | Verified scholars page with upload guide and table. |
| **Suggested Figure Caption** | *Figure X. Verified scholars page of the SchoGMS coordinator showing campus masterlist and billing import.* |

---

#### Page Name: Change Password — Coordinator (`users/coordinator/change_password.php`)

| Field | Description |
|-------|-------------|
| **Page Purpose** | Password update for coordinator account. |
| **Screenshot to Capture** | Standard change-password form. |
| **Suggested Figure Caption** | *Figure X. Account password change screen of the SchoGMS coordinator.* |

---

### Campus Director

#### Account Type: Campus Director

**Brief Description:** Manages college deans for the director’s assigned campus (college-level assignments).

**Accessible Pages:** `index.php`, `dean.php`, `change_password.php`, `logout.php`. Supporting: `submit_chair.php`, `delete_file.php`, `fetch_chart.php`, `fetch_access.php`.

---

#### Page Name: Director Dashboard (`users/director/index.php`)

| Field | Description |
|-------|-------------|
| **Page Purpose** | Campus director home with charts/statistics for assigned campus scope. |
| **Main Features** | Dashboard charts; campus context. |
| **User Actions** | Navigate to dean management. |
| **Displayed Data** | Campus-level metrics. |
| **System Benefit** | Supports director oversight before dean assignments. |
| **Screenshot to Capture** | Director dashboard with sidebar (Dashboard, College Dean). |
| **Suggested Figure Caption** | *Figure X. Campus director dashboard of the SchoGMS.* |

---

#### Page Name: College Dean Management (`users/director/dean.php`)

| Field | Description |
|-------|-------------|
| **Page Purpose** | Assigns **deans** to **colleges** within the director’s campus (`assigned_dean`). |
| **Main Features** | Dean list; create assignment by college; credentials/email notification. |
| **User Actions** | Add dean; assign college; manage status. |
| **Displayed Data** | Dean name, email, college, campus, status. |
| **System Benefit** | Implements college-level layer of campus access hierarchy. |
| **Screenshot to Capture** | Dean assignment table with add-dean form. |
| **Suggested Figure Caption** | *Figure X. College dean assignment page of the SchoGMS campus director module.* |

---

#### Page Name: Change Password — Director (`users/director/change_password.php`)

| Field | Description |
|-------|-------------|
| **Page Purpose** | Password update for director. |
| **Screenshot to Capture** | Change password screen. |
| **Suggested Figure Caption** | *Figure X. Account password change screen of the SchoGMS campus director.* |

---

### College Dean (Assigned Dean)

#### Account Type: College Dean

**Brief Description:** Assigns program chairs by **course** within the dean’s college; monitors TDP/TES at college scope.

**Accessible Pages:** `index.php`, `tdp.php`, `tes.php`, `program-chair.php`, `change_password.php`, `logout.php`. Supporting: `submit_chair.php`, `fetch_students.php`, `fetch_courses.php`, charts.

---

#### Page Name: Dean Dashboard (`users/dean/index.php`)

| Field | Description |
|-------|-------------|
| **Page Purpose** | College-level dashboard with TDP/TES summaries and charts. |
| **Screenshot to Capture** | Dean dashboard with stats/charts. |
| **Suggested Figure Caption** | *Figure X. College dean dashboard of the SchoGMS showing TDP and TES overview.* |

---

#### Page Name: TDP — Dean (`users/dean/tdp.php`)

| Field | Description |
|-------|-------------|
| **Page Purpose** | View TDP scholars/enrollment data scoped to dean’s college/courses. |
| **User Actions** | Filter/browse students; view charts. |
| **Displayed Data** | Student counts, course breakdowns. |
| **Screenshot to Capture** | TDP monitoring page with data table or chart. |
| **Suggested Figure Caption** | *Figure X. TDP scholar monitoring view of the SchoGMS college dean.* |

---

#### Page Name: TES — Dean (`users/dean/tes.php`)

| Field | Description |
|-------|-------------|
| **Page Purpose** | TES equivalent of dean TDP page. |
| **Screenshot to Capture** | TES monitoring for dean. |
| **Suggested Figure Caption** | *Figure X. TES scholar monitoring view of the SchoGMS college dean.* |

---

#### Page Name: Program Chair Management (`users/dean/program-chair.php`)

| Field | Description |
|-------|-------------|
| **Page Purpose** | Assigns program chairs to specific **courses** within the college (`assigned_program_chairs`). |
| **User Actions** | Create chair account; assign course; set active status. |
| **Displayed Data** | Chair name, email, course, college, campus. |
| **System Benefit** | Completes course-level delegation in hierarchy. |
| **Screenshot to Capture** | Program chair table with assignment form. |
| **Suggested Figure Caption** | *Figure X. Program chair assignment interface of the SchoGMS college dean.* |

---

#### Page Name: Change Password — Dean (`users/dean/change_password.php`)

| Field | Description |
|-------|-------------|
| **Page Purpose** | Password update for dean. |
| **Screenshot to Capture** | Change password form. |

---

### Program Chair

#### Account Type: Program Chair

**Brief Description:** Monitors TDP and TES scholars for an assigned course/program; read-only analytics at course scope.

**Accessible Pages:** `index.php`, `tdp.php`, `tes.php`, `change_password.php`, `logout.php`. Supporting: `fetch_students.php`, `fetch_courses.php`, charts.

*Note: Login uses `assigned_program_chairs` table (email or chair name). Role slug: `program-chair`.*

---

#### Page Name: Program Chair Dashboard (`users/program-chair/index.php`)

| Field | Description |
|-------|-------------|
| **Page Purpose** | Course-scoped dashboard with TDP/TES charts and enrollment statistics. |
| **Screenshot to Capture** | Dashboard with charts and sidebar (Dashboard, TDP, TES). |
| **Suggested Figure Caption** | *Figure X. Program chair dashboard of the SchoGMS displaying course-level scholarship statistics.* |

---

#### Page Name: TDP — Program Chair (`users/program-chair/tdp.php`)

| Field | Description |
|-------|-------------|
| **Page Purpose** | Lists/monitors TDP students for assigned course. |
| **Displayed Data** | Distinct student counts, course-filtered masterlist joins. |
| **Screenshot to Capture** | TDP table filtered to one course. |
| **Suggested Figure Caption** | *Figure X. TDP scholar list view of the SchoGMS program chair.* |

---

#### Page Name: TES — Program Chair (`users/program-chair/tes.php`)

| Field | Description |
|-------|-------------|
| **Page Purpose** | TES monitoring for assigned course. |
| **Screenshot to Capture** | TES program chair view. |
| **Suggested Figure Caption** | *Figure X. TES scholar list view of the SchoGMS program chair.* |

---

#### Page Name: Change Password — Program Chair (`users/program-chair/change_password.php`)

| Field | Description |
|-------|-------------|
| **Page Purpose** | Password update. |
| **Screenshot to Capture** | Change password form. |

---

### Registrar

#### Account Type: Registrar

**Brief Description:** Maintains registrar masterlist and manages COR/COG document uploads for scholarship validation.

**Accessible Pages (sidebar):** `index.php`, `masterlist.php`, `cor-cog.php`, `documents_uploaded.php`, `logout.php`.

**Additional pages (not in main sidebar — legacy/direct URL):** `ched_masterlist.php`, `verified-scholars.php`, `requirements.php`, `program_list.php`, `view_document.php`, debug/test scripts (exclude from paper).

---

#### Page Name: Registrar Dashboard (`users/registrar/index.php`)

| Field | Description |
|-------|-------------|
| **Page Purpose** | Registrar home with file/document counts and quick navigation. |
| **Screenshot to Capture** | Registrar dashboard with sidebar. |
| **Suggested Figure Caption** | *Figure X. Registrar dashboard of the SchoGMS.* |

---

#### Page Name: Registrar Masterlist (`users/registrar/masterlist.php`)

| Field | Description |
|-------|-------------|
| **Page Purpose** | Upload and maintain `registrar_master_list`; link scholars to COR/COG; search and filter. |
| **Main Features** | Masterlist upload; search; COR/COG badges linking to `view_document.php`; utilities (e.g. check COR status). |
| **User Actions** | Upload list; search students; open COR/COG; clear filters. |
| **Displayed Data** | ID number, names, contact, enrollment, document availability. |
| **System Benefit** | Supplies registrar truth data for coordinator validation. |
| **Screenshot to Capture** | Masterlist with COR/COG action badges visible. |
| **Suggested Figure Caption** | *Figure X. Registrar masterlist page of the SchoGMS with COR and COG document links.* |

---

#### Page Name: COR & COG — Registrar (`users/registrar/cor-cog.php`)

| Field | Description |
|-------|-------------|
| **Page Purpose** | Upload and manage COR/COG files (batch/single); integrates with document storage. |
| **User Actions** | Upload documents; view linked files. |
| **Displayed Data** | Upload forms; file lists. |
| **Screenshot to Capture** | COR/COG upload interface. |
| **Suggested Figure Caption** | *Figure X. COR and COG upload module of the SchoGMS registrar.* |

---

#### Page Name: Documents Uploaded (`users/registrar/documents_uploaded.php`)

| Field | Description |
|-------|-------------|
| **Page Purpose** | Lists all uploaded registrar documents for review and management. |
| **User Actions** | Browse; open documents; delete/clean (where enabled). |
| **Displayed Data** | File names, types, dates, paths. |
| **System Benefit** | Audit trail of submitted requirement files. |
| **Screenshot to Capture** | Uploaded documents table. |
| **Suggested Figure Caption** | *Figure X. Uploaded documents registry of the SchoGMS registrar module.* |

---

#### Page Name: Document Viewer (`users/registrar/view_document.php`)

| Field | Description |
|-------|-------------|
| **Page Purpose** | Streams/displays individual COR or COG files. |
| **Screenshot to Capture** | Open COR or COG from masterlist—browser showing document viewer. |
| **Suggested Figure Caption** | *Figure X. In-system preview of a Certificate of Registration document in the SchoGMS.* |

---

## Two-Factor Authentication (Email Verification)

SchoGMS uses **email-based verification** as a second factor before first login for MySQL users created by the administrator (UI labels this as 2FA on the login page when status is `pending`).

### Process flow

1. **Admin creates user** (`admin/user-management.php` → `inc/admin_user_create.php`).
   - Most roles: `status = pending`, `email_verified = 0`, 6-digit `verification_code`, 60-minute `verification_expires`.
   - **Chairman:** may be created as `active` with `email_verified = 1` (skips verification).
2. **Email sent** (if SMTP configured in `config/smtp.local.php`) with verification code and login URL.
3. **User attempts login** → redirected to `index.php?ERROR=pending` with link to `verify.php`.
4. **User submits** email + code on `verify.php` → POST to `verify_code.php`.
5. **On success:** account set to `active`, code cleared, session started, redirect to role home (`config/schogms_helpers.php` → `schogms_role_home()`).
6. **On failure:** error message; attempt logged in `verification_attempts`.

### Related files

| File | Role |
|------|------|
| `index.php` | Login UI; pending error with verify link |
| `verify.php` | Verification form UI |
| `verify_code.php` | Validates code and activates account |
| `login.php` | Primary authentication router |
| `config/mysql_auth.php` | Blocks login if `email_verified !== 1` (except coordinator legacy rules) |
| `inc/admin_user_create.php` | Generates verification code on user create |

### Screenshot guide (2FA)

| Step | Account | Page | What to show |
|------|---------|------|--------------|
| 1 | Pending user | `index.php` | Pending error + “Click here to verify” |
| 2 | Pending user | `verify.php` | Email and verification code fields |
| 3 | Pending user | `verify.php` | Success alert before redirect |
| 4 | Active user | Role dashboard | Post-verification landing page |

---

## E. Screenshot Checklist for the Paper

| Screenshot No. | Account Type | Page Name | Screenshot Description | Suggested Figure Caption |
|----------------|--------------|-----------|------------------------|---------------------------|
| 1 | Public | Login | Full login page with branding and form | Figure 1. Login interface of the SchoGMS |
| 2 | Public | Login (pending) | Error state with link to verify | Figure 2. Pending account notification on the SchoGMS login page |
| 3 | Public | Email verification (2FA) | `verify.php` with email and code fields | Figure 3. Email verification (2FA) module of the SchoGMS |
| 4 | Public | 2FA success | SweetAlert success before redirect | Figure 4. Successful account verification in the SchoGMS |
| 5 | Admin | Admin login | `admin/index.html` login form | Figure 5. Administrator login portal of the SchoGMS |
| 6 | Admin | Dashboard | Summary cards and charts | Figure 6. Administrator dashboard of the SchoGMS |
| 7 | Admin | User management | User table + create user modal | Figure 7. User management module of the SchoGMS |
| 8 | Admin | Logs | System logs table | Figure 8. System logs view of the SchoGMS *(optional)* |
| 9 | Chairman | Dashboard | TDP/TES/pending Annex cards | Figure 9. Chairman dashboard of the SchoGMS |
| 10 | Chairman | Annex 7 review | Table + preview modal | Figure 10. Annex 7 review with document preview |
| 11 | Chairman | TDP masterlist | System-wide masterlist table | Figure 11. Chairman TDP masterlist view |
| 12 | Chairman | Upload TDP | Upload form + recent uploads | Figure 12. CHED TDP upload interface (chairman) |
| 13 | Chairman | Verified scholars | Tabs: masterlist + billing | Figure 13. Verified scholars and billing (chairman) |
| 14 | Coordinator | Dashboard | Campus stats and charts | Figure 14. Coordinator dashboard of the SchoGMS |
| 15 | Coordinator | TDP masterlist | Campus masterlist + upload | Figure 15. Campus TDP masterlist (coordinator) |
| 16 | Coordinator | Validate TDP | Validation comparison view | Figure 16. TDP validation module |
| 17 | Coordinator | COR & COG | Document listing | Figure 17. COR and COG management (coordinator) |
| 18 | Coordinator | Campus directors | Director assignment table | Figure 18. Campus director assignment |
| 19 | Coordinator | Submit Annex 7 | Upload form + format guide | Figure 19. Annex 7 submission (coordinator) |
| 20 | Coordinator | Verified scholars | Campus billing upload | Figure 20. Verified scholars (coordinator) |
| 21 | Director | Dashboard | Director home | Figure 21. Campus director dashboard |
| 22 | Director | Dean management | Dean/college assignment | Figure 22. College dean assignment (director) |
| 23 | Dean | Dashboard | Dean home | Figure 23. College dean dashboard |
| 24 | Dean | Program chairs | Chair/course assignment | Figure 24. Program chair assignment (dean) |
| 25 | Dean | TDP | College TDP view | Figure 25. TDP monitoring (dean) |
| 26 | Program Chair | Dashboard | Course-level charts | Figure 26. Program chair dashboard |
| 27 | Program Chair | TDP | Course-filtered list | Figure 27. TDP view (program chair) |
| 28 | Registrar | Dashboard | Registrar home | Figure 28. Registrar dashboard |
| 29 | Registrar | Masterlist | Table with COR/COG links | Figure 29. Registrar masterlist with document links |
| 30 | Registrar | COR & COG upload | Upload interface | Figure 30. COR/COG upload (registrar) |
| 31 | Registrar | Documents uploaded | Uploaded files list | Figure 31. Uploaded documents registry |
| 32 | Registrar | Document viewer | COR/COG preview | Figure 32. Document preview in the SchoGMS |

*Renumber figures to match your paper’s chapter sequence.*

---

## F. User Access and Feature Matrix

Legend: **✓** = Can access · **—** = Cannot access · **△** = Partial / indirect (data only, not login role)

| Feature / Page | Admin | Coordinator (Staff) | Chairman | Director | Dean | Program Chair | Registrar | Student/Scholar | Guardian |
|----------------|:-----:|:-------------------:|:--------:|:--------:|:----:|:-------------:|:---------:|:---------------:|:--------:|
| Login (`index.php`) | △ Admin portal | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | — | — |
| Email verification / 2FA (`verify.php`) | — | ✓ (new users) | △ Pre-activated | ✓ | ✓ | — | ✓ | — | — |
| Admin dashboard | ✓ | — | — | — | — | — | — | — | — |
| User management | ✓ | — | — | — | — | — | — | — | — |
| System logs | ✓ | — | — | — | — | — | — | — | — |
| Role dashboard | — | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | — | — |
| CHED TDP masterlist (upload/view) | — | ✓ (campus) | ✓ (all) | — | △ View | △ View | △ Legacy | △ Data only | — |
| CHED TES masterlist | — | ✓ | ✓ | — | △ View | △ View | △ Legacy | △ Data only | — |
| TDP/TES validation | — | ✓ | — | — | — | — | — | — | — |
| Registrar masterlist | — | △ Used in validation | — | — | — | — | ✓ | △ Data only | — |
| COR & COG upload/manage | — | ✓ View/track | — | — | — | — | ✓ | — | — |
| Campus director assignment | — | ✓ | — | — | — | — | — | — | — |
| Dean assignment | — | — | — | ✓ | — | — | — | — | — |
| Program chair assignment | — | — | — | — | ✓ | — | — | — | — |
| Annex 7 submit | — | ✓ | — | — | — | — | — | — | — |
| Annex 7 approve/decline | — | — | ✓ | — | — | — | — | — | — |
| Verified scholars / billing | — | ✓ (campus) | ✓ (all) | — | — | — | △ Legacy page | — | — |
| Program list reference | — | — | ✓ | — | — | — | △ Legacy | — | — |
| Change password | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | △ | — | — |
| Application portal (self-service) | — | — | — | — | — | — | — | — | — |

---

## G. Summary of System Functionality

SchoGMS implements a **multi-role scholarship operations platform** centered on CHED TDP/TES masterlists, registrar cross-validation, documentary requirements (COR/COG), financial/billing verification, and a **hierarchical campus governance model** (Coordinator → Director → Dean → Program Chair). The **Chairman** provides system-wide approval (Annex 7) and oversight. The **Administrator** provisions accounts with **email-based two-factor verification** for most roles. Scholars are managed as **records** within masterlists rather than through a separate student portal—an important scope note for research documentation.

### Implementation notes for evaluators

- Dual storage: MySQL (primary for chairman/coordinator/registrar) and MongoDB (legacy login paths for some roles).
- Chairman and verified-scholars pages were refactored—verify on deployment before final screenshots.
- Admin **Data Management** link may be commented out on dashboard but pages exist.
- Registrar **verified-scholars** and **ched_masterlist** exist but are not in the registrar sidebar (use coordinator/chairman for paper flows unless testing legacy URLs).
- No dedicated **Student/Scholar** or **Guardian** login module was found in the production `users/` tree.

### Related project documentation

- `tools/README_DEMO_SEED.md` — demo data for screenshots
- `tools/README_CAMPUS_ACCESS.md` — campus → college → course hierarchy
- `config/campus_access_catalog.php` — campus/college/course catalog

---

## H. Global Screenshot Instructions

1. Use a consistent browser zoom (100%) and window size (e.g. 1440×900).
2. Hide personal emails in figures if required by ethics review—blur or use demo accounts.
3. Seed demo data (`tools/seed_demo_data.php` per `tools/README_DEMO_SEED.md`) so tables are not empty.
4. Capture **sidebar + page title** in every role screenshot for clarity.
5. For workflow figures (Annex 7, validation), use **two-panel** captures: list view + modal/action.
6. Login credentials:
   - **Admin:** `admin/` portal (not main index).
   - **Coordinator/Chairman/Registrar/Director:** main `index.php`.
   - **Dean / Program Chair:** accounts from `assigned_dean` / `assigned_program_chairs` (created by director/dean).

---

*End of document*
