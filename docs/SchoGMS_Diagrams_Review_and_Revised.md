# SchoGMS — Diagram Review, Audit, and Revised Editions

**Document purpose:** Research-paper-ready diagrams verified against the SchoGMS codebase (May 2026).  
**Companion files:** [SchoGMS_System_Diagrams.md](./SchoGMS_System_Diagrams.md) (original set), [schogms-activity-diagram.html](./schogms-activity-diagram.html) (primary activity figure).

---

## Review checklist (audit results)

| Criterion | Result | Notes |
|-----------|--------|-------|
| Matches actual codebase | **Pass** (with scope notes) | PHP/MySQL primary; MongoDB legacy on some paths |
| All user roles represented | **Pass** | Admin, Chairman, Coordinator, Registrar, Director, Dean, Program Chair |
| Student/Guardian | **N/A — not implemented** | Shown only as *Recommended Enhancement* |
| 2FA in authentication diagrams | **Pass** | `verify.php`, `verify_code.php`, `verification_attempts`, `email_verified` |
| Scholarship application workflow | **Pass (mapped)** | Implemented as **CHED masterlist import**, not student portal |
| Requirement upload & verification | **Pass** | `document_uploads`, COR/COG; `validation_status` on `ched_masterlist` |
| Review / approval / rejection | **Pass** | Annex 7: Pending → Approved/Rejected; validation: Validated/Failed |
| Endorsement | **Not in codebase** | No “endorse” feature; use **campus role assignment** (Director→Dean→Chair) |
| Notifications | **Pass** | SMTP: verification, Annex approval, role assignment, some export mails |
| DB tables & relationships | **Pass** | 14 MySQL tables confirmed; `billing_table` **optional** (may not exist locally) |
| Not overcrowded | **Revised** | Paper editions simplified below |
| Formal academic labels | **Revised** | Passive, role-based wording in paper editions |
| Research-paper descriptions | **Revised** | Below per figure |

**Confirmed MySQL tables:** `admin`, `users`, `campuses`, `ched_masterlist`, `ched_masterlist_tes`, `ched_upload_log`, `registrar_master_list`, `document_uploads`, `file_submissions`, `assigned_dean`, `assigned_program_chairs`, `verification_attempts`, `schogms_colleges`, `schogms_courses`.

---

## How to use two editions

| Edition | Audience | Use in thesis |
|---------|----------|----------------|
| **Paper (simplified)** | Panel, Chapter 3–4 | Main body figures; max 8–12 figures total |
| **Technical (detailed)** | Appendix, IT documentation | Full module/table names |

**Recommended paper figures (minimum strong set):** Figure 0 (activity HTML), §2 Use Case (paper), §3 Context, §5 DFD Level 1 (paper), §6 ER (paper), §9 Login/2FA activity, §11 Requirements activity, §12 Review/approval activity, §18 Role access, §20 Deployment.

---

# Figure 0 — Overall Activity Workflow (Primary)

**Recommended paper section:** *Chapter 4 — System Process Model* or *Methodology → Operational Workflow*

### Paper edition
Use **[schogms-activity-diagram.html](./schogms-activity-diagram.html)** or Mermaid in [SchoGMS_Simple_Activity_Diagram.md](./SchoGMS_Simple_Activity_Diagram.md).

### Improved description
The SchoGMS operational model proceeds in four phases: institutional staff account provisioning and email verification; loading of beneficiary rosters and supporting documents; coordinator-led cross-validation against registrar records; and submission and executive approval of the Annex 7 campus report. Beneficiaries do not interact with the system directly.

### Suggested caption
*Figure X. Activity diagram of the SchoGMS scholarship management workflow from account verification through document validation and Annex 7 approval.*

### Screenshot / export
- Open `docs/schogms-activity-diagram.html` in browser → Print to PDF (100% zoom, A4 portrait).
- Or export Mermaid from [mermaid.live](https://mermaid.live).

---

# Diagram 1 — System Architecture

**Recommended paper section:** *Chapter 3 — System Architecture*

### Paper edition (simplified)

**Diagram code:**
```mermaid
flowchart TB
  subgraph Client["Client Tier"]
    WEB[Web Browser]
  end
  subgraph App["Application Tier"]
    PHP[PHP Web Application\nSchoGMS Modules]
  end
  subgraph Data["Data Tier"]
    SQL[(MySQL Database)]
    FILES[(File Storage)]
  end
  subgraph External["External Service"]
    MAIL[Email Server SMTP]
  end
  WEB <--> PHP
  PHP <--> SQL
  PHP <--> FILES
  PHP --> MAIL
```

**Improved description:** SchoGMS employs a three-tier architecture. Clients access server-rendered PHP modules through a web browser. The application tier processes authentication, masterlist operations, validation, and file handling. Structured data reside in MySQL; documents reside on the server filesystem. Transactional email supports verification and approval notifications.

**Suggested caption:** *Figure 1. Three-tier architecture of the SchoGMS.*

**Screenshot / export:** Recreate in draw.io or export Mermaid PNG; no live UI screenshot required.

### Technical edition (detailed)

**Diagram code:**
```mermaid
flowchart TB
  subgraph Presentation["Presentation Layer"]
    PUB[index.php / verify.php]
    ADM[admin/ portal]
    ROLE[users/coordinator | chairman | registrar | director | dean | program-chair]
  end
  subgraph Application["Application Layer"]
    AUTH[login.php + config/mysql_auth.php]
    VFY[inc/verify_account.php + verify_code.php]
    SESS[per-role config/session.php]
    BIZ[process_excel.php validate APIs submit_* upload_*]
    SMTP[config/mail.php]
  end
  subgraph Data["Data Layer"]
    MY[(MySQL)]
    MG[(MongoDB legacy file-store)]
    FS[uploads/ annex7 COR COG]
  end
  PUB --> AUTH
  ADM --> MY
  ROLE --> SESS --> BIZ
  AUTH --> MY
  AUTH --> MG
  VFY --> MY
  BIZ --> MY
  BIZ --> FS
  BIZ --> MG
  SMTP --> External[SMTP]
```

**Suggested caption:** *Figure A.1. Detailed logical architecture of the SchoGMS (technical documentation).*

---

# Diagram 2 — Use Case

**Recommended paper section:** *Chapter 3 — Functional Requirements / Use Case Model*

### Paper edition (simplified)

**Diagram code (PlantUML):**
```plantuml
@startuml SchoGMS_UseCase_Paper
left to right direction
skinparam shadowing false

actor "System Administrator" as Admin
actor "Chairman" as Chair
actor "Scholarship Coordinator" as Coord
actor "Registrar" as Reg
actor "Campus Director" as Dir
actor "College Dean" as Dean
actor "Program Chair" as PC

rectangle "SchoGMS" {
  usecase "Manage users and campuses" as UC1
  usecase "Authenticate with email verification" as UC2
  usecase "Manage CHED beneficiary lists" as UC3
  usecase "Upload and verify documents" as UC4
  usecase "Validate beneficiary records" as UC5
  usecase "Submit and approve Annex 7" as UC6
  usecase "Assign campus leadership roles" as UC7
  usecase "Generate reports" as UC8
}

Admin --> UC1
Admin --> UC2
Chair --> UC2
Chair --> UC3
Chair --> UC6
Coord --> UC2
Coord --> UC3
Coord --> UC4
Coord --> UC5
Coord --> UC6
Coord --> UC7
Reg --> UC2
Reg --> UC4
Dir --> UC2
Dir --> UC7
Dean --> UC2
Dean --> UC7
PC --> UC2
PC --> UC3
@enduml
```

**Improved description:** The use case model defines eight functional areas accessed by seven operational roles. The administrator provisions accounts. The chairman provides system-wide oversight and Annex 7 approval. The coordinator performs campus-level validation and reporting. The registrar maintains academic documents. The director, dean, and program chair support hierarchical campus governance and monitoring. Scholar self-service is outside the current system scope.

**Suggested caption:** *Figure 2. Use case diagram of the SchoGMS showing role-based functional access.*

**Screenshot / export:** PlantUML PNG; optional: composite with role list table from system documentation.

### Technical edition
Retain full use cases in [SchoGMS_System_Diagrams.md](./SchoGMS_System_Diagrams.md) §2 (includes TDP/TES split, billing import, legacy enhancement actors).

---

# Diagram 3 — Context Diagram

**Recommended paper section:** *Chapter 3 — System Context*

### Paper edition

**Diagram code:**
```mermaid
flowchart LR
  Admin(("Administrator"))
  Staff(("Scholarship\nOffices"))
  CHED(("CHED / Institutional\nData Files"))
  SYS[["SchoGMS"]]
  DB[(Records)]
  DOC[(Documents)]

  Admin <-->|configure| SYS
  Staff <-->|operate| SYS
  CHED -->|import| SYS
  SYS <-->|store| DB
  SYS <-->|store| DOC
```

**Improved description:** SchoGMS acts as the central information system between institutional users and stored scholarship records. External Excel files supply beneficiary data. The administrator configures users and campuses. Scholarship and registrar offices execute validation and reporting within the system boundary.

**Suggested caption:** *Figure 3. Context diagram of the SchoGMS and its external entities.*

**Screenshot / export:** Mermaid/draw.io only.

### Technical edition
Add SMTP, Registrar Office, Campus Leadership splits — see original §3 in System_Diagrams.md.

---

# Diagram 4 — DFD Level 0

**Recommended paper section:** *Chapter 3 — Data Flow Overview*

### Paper edition

**Diagram code:**
```mermaid
flowchart LR
  U1[Institutional Users]
  U2[Administrator]
  P0(("SchoGMS"))
  D1[(User and Campus Data)]
  D2[(Scholarship Records)]
  D3[(Uploaded Files)]

  U2 -->|configuration| P0
  U1 -->|transactions| P0
  P0 -->|reports and status| U1
  P0 <-->|read write| D1
  P0 <-->|read write| D2
  P0 <-->|read write| D3
```

**Improved description:** At context level, the system exchanges configuration data with the administrator and operational data with institutional users. Internal data stores maintain user accounts, scholarship masterlists, and uploaded documents.

**Suggested caption:** *Figure 4. Level 0 data flow diagram of the SchoGMS.*

### Technical edition
Original §4 with CHED Excel entity — unchanged structurally.

---

# Diagram 5 — DFD Level 1

**Recommended paper section:** *Chapter 3 — Process Decomposition*

### Paper edition (5 processes)

**Diagram code:**
```mermaid
flowchart TB
  U[Users]
  P1[1.0 Authenticate and Verify]
  P2[2.0 Manage Masterlists]
  P3[3.0 Manage Documents]
  P4[4.0 Validate Records]
  P5[5.0 Approve Reports and Notify]
  D1[(D1 Users)]
  D2[(D2 Masterlists)]
  D3[(D3 Documents)]
  D4[(D4 Submissions)]

  U --> P1 --> D1
  U --> P2 --> D2
  U --> P3 --> D3
  U --> P4
  P4 <--> D2
  P4 <--> D3
  U --> P5
  P5 <--> D4
  P5 -->|email| U
```

**Improved description:** Level 1 decomposition identifies five processes: authentication with email verification, masterlist management, document management, record validation, and report approval with notification. Validation consumes both masterlist and document stores.

**Suggested caption:** *Figure 5. Level 1 data flow diagram of the SchoGMS.*

### Technical edition
Seven processes (add Admin User Mgmt, separate Annex 7) — original §5.

---

# Diagram 6 — Entity-Relationship Diagram

**Recommended paper section:** *Chapter 3 — Data Model* or *Appendix*

### Paper edition (core entities only)

**Diagram code:**
```mermaid
erDiagram
  USERS ||--o{ FILE_SUBMISSIONS : submits
  CAMPUSES ||--o{ USERS : hosts
  CAMPUSES ||--o{ CHED_MASTERLIST : contains
  CAMPUSES ||--o{ REGISTRAR_MASTER_LIST : contains
  CAMPUSES ||--o{ DOCUMENT_UPLOADS : stores
  CHED_MASTERLIST ||--o| DOCUMENT_UPLOADS : "matched by name"
  USERS {
    int user_id PK
    string role
    string campus
    enum status
  }
  CHED_MASTERLIST {
    int id PK
    string sheet_name
    string validation_status
  }
  REGISTRAR_MASTER_LIST {
    int id PK
    string campus
  }
  DOCUMENT_UPLOADS {
    int id PK
    enum category
  }
  FILE_SUBMISSIONS {
    int id PK
    enum status
  }
```

**Improved description:** The conceptual data model links operational users to campus-scoped scholarship records, registrar reference data, uploaded documents, and formal submissions. Logical matching between beneficiary names and document filenames supports validation; formal foreign keys are not enforced on all relationships.

**Suggested caption:** *Figure 6. Conceptual entity-relationship diagram of core SchoGMS entities.*

### Technical edition
Add `assigned_dean`, `assigned_program_chairs`, `ched_masterlist_tes`, `verification_attempts`, `schogms_colleges/courses` — original §6.

---

# Diagram 7 — Database Schema

**Recommended paper section:** *Appendix — Database Design*

### Paper edition
Use a **table list figure** in Word instead of full schema diagram:

| Table | Purpose |
|-------|---------|
| `users` | Staff accounts, roles, verification |
| `ched_masterlist` / `ched_masterlist_tes` | TDP/TES beneficiaries |
| `registrar_master_list` | Registrar enrollment data |
| `document_uploads` | COR/COG index |
| `file_submissions` | Annex 7 tracking |
| `assigned_dean` / `assigned_program_chairs` | College/program leadership logins |

**Suggested caption:** *Table X. Primary database tables of the SchoGMS.*

### Technical edition
Class diagram of all 14 tables — original §7; note `billing_table` only if `SHOW TABLES` confirms it on deployment DB.

---

# Diagram 8 — Class Diagram

**Recommended paper section:** *Appendix only* (optional)

**Note:** SchoGMS uses procedural PHP; class diagram is **logical modules only**.

**Paper edition:** Omit from main chapter to avoid overcrowding.

**Technical edition:** Retain original §8.

**Suggested caption:** *Figure A.2. Logical module diagram of SchoGMS PHP components.*

---

# Diagram 9 — Activity: Login and 2FA

**Recommended paper section:** *Chapter 4 — Security / Authentication*

### Paper edition

**Diagram code:**
```mermaid
flowchart TD
  A([Start]) --> B[User opens login page]
  B --> C[Submit email and password]
  C --> D{Credentials valid?}
  D -->|No| E[Display error message]
  E --> Z([End])
  D -->|Yes| F{Account verified?}
  F -->|No| G[Open email verification page]
  G --> H[Enter six-digit code]
  H --> I{Code valid?}
  I -->|No| J[Record attempt and show error]
  J --> G
  I -->|Yes| K[Activate account]
  F -->|Yes| K
  K --> L[Create session and open role dashboard]
  L --> Z
```

**Improved description:** Authentication requires valid credentials and, for newly provisioned accounts, successful email verification using a time-limited numeric code. Failed verification attempts are logged. Upon success, the system establishes a session and routes the user to a role-specific dashboard.

**Suggested caption:** *Figure 7. Activity diagram of user login and email-based two-factor verification in the SchoGMS.*

**Screenshot / export:** `index.php`, `verify.php` (pending + success states), `users/coordinator/index.php` after login.

### Technical edition
Include `login.php`, `verify_code.php`, `verification_attempts`, chairman pre-activated exception — original §9.

---

# Diagram 10 — Activity: Beneficiary Registration (Scholarship Intake)

**Recommended paper section:** *Chapter 4 — Beneficiary Management*

**Terminology:** Not a student application portal; **institutional masterlist import**.

### Paper edition

**Diagram code:**
```mermaid
flowchart TD
  A([Start]) --> B[Authorized staff opens upload module]
  B --> C[Select campus and upload Excel file]
  C --> D{File valid?}
  D -->|No| E[Report errors to user]
  E --> Z([End])
  D -->|Yes| F[Store beneficiary records]
  F --> G[Display masterlist for campus]
  G --> Z
```

**Improved description:** Beneficiary registration is performed by authorized staff through bulk import of CHED-formatted masterlists. The system validates file structure, persists records to the campus masterlist, and presents the data for subsequent validation. A scholar-facing application module is not part of the current implementation.

**Suggested caption:** *Figure 8. Activity diagram of beneficiary registration through CHED masterlist import in the SchoGMS.*

**Screenshot / export:** `users/coordinator/ched_masterlist.php` upload form; table with data after seed.

### Technical edition
`process_excel.php`, `ched_upload_log`, TDP vs TES tables — original §10.

---

# Diagram 11 — Activity: Requirements Upload and Verification

**Recommended paper section:** *Chapter 4 — Document Compliance*

### Paper edition

**Diagram code:**
```mermaid
flowchart TD
  A([Start]) --> B[Registrar uploads COR and COG]
  B --> C[System stores document metadata]
  C --> D[Coordinator runs validation]
  D --> E{Records and documents consistent?}
  E -->|Yes| F[Mark beneficiaries as validated]
  E -->|No| G[Mark beneficiaries as failed]
  F --> H[Generate validation report]
  G --> H
  H --> Z([End])
```

**Improved description:** Documentary requirements are fulfilled when the registrar uploads Certificates of Registration and Grades. The coordinator compares beneficiary masterlist entries, registrar records, and uploaded documents. Each record receives a validation outcome used in compliance reporting.

**Suggested caption:** *Figure 9. Activity diagram of document upload and beneficiary validation in the SchoGMS.*

**Screenshot / export:** `users/registrar/cor-cog.php`, `users/coordinator/validate.php` with filters and results.

### Technical edition
`document_uploads`, `auto_validate_tdp.php`, `validation_status` — original §11.

---

# Diagram 12 — Activity: Review, Approval, Rejection, and Notification

**Recommended paper section:** *Chapter 4 — Approval Workflow*

**Note:** The term **endorsement** does not appear in the codebase. Campus **role assignment** (Director assigns Dean, Dean assigns Program Chair) is a separate administrative flow with email notification. **Annex 7** is the primary approval/rejection workflow for the paper.

### Paper edition

**Diagram code:**
```mermaid
flowchart TD
  A([Start]) --> B[Coordinator submits Annex 7 report]
  B --> C[System sets status to Pending]
  C --> D[Chairman reviews submission]
  D --> E{Decision}
  E -->|Approve| F[Update status to Approved]
  F --> G[Send email notification to coordinator]
  E -->|Reject| H[Update status to Rejected]
  G --> I[Record retained for reporting]
  H --> I
  I --> Z([End])
```

**Improved description:** Campus reporting concludes with Annex 7 submission by the coordinator. The chairman examines the submission and records an approval or rejection decision. Approved submissions trigger email notification to the submitting coordinator. All outcomes are retained for audit and reporting.

**Suggested caption:** *Figure 10. Activity diagram of Annex 7 review, approval, rejection, and notification in the SchoGMS.*

**Screenshot / export:** `users/coordinator/submit_form.php`, `users/chairman/anex-form2.php` with Approve/Reject and preview modal.

### Technical edition
Add optional billing import, Pending hold state — original §12.

---

# Diagram 13 — Sequence: Login and 2FA

**Recommended paper section:** *Appendix — Interaction Model*

### Paper edition (fewer participants)

**Diagram code:**
```mermaid
sequenceDiagram
  actor User
  participant UI as Web Interface
  participant Auth as Authentication Module
  participant DB as Database

  User->>UI: Enter credentials
  UI->>Auth: Validate login
  Auth->>DB: Query user account
  alt not verified
    Auth-->>UI: Redirect to verification
    User->>UI: Submit verification code
    UI->>Auth: Verify code
    Auth->>DB: Update account status
  end
  Auth-->>UI: Create session
  UI-->>User: Show role dashboard
```

**Suggested caption:** *Figure A.3. Sequence diagram of authentication and email verification.*

**Screenshot / export:** Optional sequence overlay; login/verify screenshots sufficient for paper.

### Technical edition
Full participant list — original §13.

---

# Diagram 14 — Sequence: Masterlist Import

**Recommended paper section:** *Appendix*

### Paper edition
Same as Diagram 10 sequence — Chairman/Coordinator → Upload → Database.

**Suggested caption:** *Figure A.4. Sequence diagram of CHED masterlist import.*

**Screenshot / export:** Network tab optional; upload UI screenshot preferred.

---

# Diagram 15 — Sequence: Requirements Review

**Recommended paper section:** *Appendix*

### Paper edition

**Diagram code:**
```mermaid
sequenceDiagram
  actor Coordinator
  participant UI as Validation Module
  participant DB as Database

  Coordinator->>UI: Request campus validation
  UI->>DB: Load masterlist and documents
  UI->>UI: Compare records
  UI->>DB: Update validation status
  UI-->>Coordinator: Display results
```

**Suggested caption:** *Figure A.5. Sequence diagram of coordinator-led record validation.*

---

# Diagram 16 — Sequence: Annex Approval

**Recommended paper section:** *Appendix*

### Paper edition

**Diagram code:**
```mermaid
sequenceDiagram
  actor Chairman
  actor Coordinator
  participant UI as Review Module
  participant DB as Database
  participant Mail as Email Service

  Chairman->>UI: Approve submission
  UI->>DB: Update status to Approved
  UI->>Mail: Send notification
  Mail-->>Coordinator: Approval email
  UI-->>Chairman: Confirm success
```

**Suggested caption:** *Figure A.6. Sequence diagram of Annex 7 approval and notification.*

---

# Diagram 17 — State Diagram

**Recommended paper section:** *Chapter 4 — Status Model*

### Paper edition (two parallel state machines)

**Diagram code:**
```mermaid
stateDiagram-v2
  state "User Account" as UA {
    [*] --> Pending
    Pending --> Active: Email verified
    Pending --> Restricted: Admin action
    Active --> [*]
  }
  state "Annex 7 Submission" as AN {
    [*] --> Pending
    Pending --> Approved: Chairman approves
    Pending --> Rejected: Chairman rejects
    Approved --> [*]
    Rejected --> [*]
  }
  state "TDP Validation" as VAL {
    [*] --> Unvalidated
    Unvalidated --> Validated: Coordinator validation
    Unvalidated --> Failed: Coordinator validation
    Validated --> [*]
    Failed --> [*]
  }
```

**Improved description:** SchoGMS maintains distinct status models for user accounts, formal campus submissions, and beneficiary validation outcomes. These states are independent but collectively describe scholarship processing progress.

**Suggested caption:** *Figure 11. State diagram of user account, Annex 7 submission, and beneficiary validation statuses.*

### Technical edition
Original §17 composite diagram.

---

# Diagram 18 — User Role Access

**Recommended paper section:** *Chapter 3 — Access Control*

### Paper edition

**Diagram code:**
```mermaid
flowchart TB
  subgraph Roles["Operational Roles"]
    ADM[System Administrator]
    CH[Chairman]
    CO[Scholarship Coordinator]
    RG[Registrar]
    DR[Campus Director]
    DN[College Dean]
    PC[Program Chair]
  end
  subgraph Modules["Major Modules"]
    M1[User and Campus Administration]
    M2[CHED Masterlists]
    M3[Validation and Reports]
    M4[Documents COR COG]
    M5[Annex 7 Workflow]
    M6[Leadership Assignment]
    M7[Monitoring Dashboards]
  end
  ADM --> M1
  CH --> M2
  CH --> M5
  CO --> M2
  CO --> M3
  CO --> M4
  CO --> M5
  CO --> M6
  RG --> M4
  DR --> M6
  DN --> M6
  DN --> M7
  PC --> M7
```

**Improved description:** Access is organized by institutional role. The administrator manages configuration. The chairman and coordinator perform scholarship operations. The registrar supplies documents. The director, dean, and program chair support hierarchical governance and monitoring within assigned scope.

**Suggested caption:** *Figure 12. Role-based access map of SchoGMS functional modules.*

**Screenshot / export:** One sidebar per role (coordinator, chairman, registrar) showing menu differences.

---

# Diagram 19 — Sitemap

**Recommended paper section:** *Appendix — User Interface Structure*

### Paper edition
Limit to **three role trees** (Coordinator, Chairman, Registrar) — original §19 trimmed to 15 nodes max.

**Suggested caption:** *Figure A.7. Navigation structure for primary SchoGMS roles.*

**Screenshot / export:** Full-window captures per role dashboard with sidebar visible.

---

# Diagram 20 — Deployment

**Recommended paper section:** *Chapter 3 — Deployment Environment*

### Paper edition
Retain simplified §20 from original (Browser → Apache/PHP → MySQL + Files + SMTP).

**Suggested caption:** *Figure 13. Deployment diagram of the SchoGMS on Apache, PHP, and MySQL.*

**Screenshot / export:** XAMPP control panel optional; architecture diagram sufficient.

---

# Diagram 21 — Component

**Recommended paper section:** *Appendix only*

**Paper edition:** Omit or use simplified 4-component version (UI, Auth, Business Logic, Data Access).

**Technical edition:** Original §21.

---

# Diagram 22 — Report Generation

**Recommended paper section:** *Chapter 4 — Reporting*

### Paper edition

**Diagram code:**
```mermaid
flowchart LR
  A[User applies filters] --> B[System queries database]
  B --> C{Output type}
  C -->|Screen| D[Data table or dashboard]
  C -->|File| E[Excel or CSV export]
```

**Improved description:** Reporting is generated on demand from filtered database queries. Results are viewed in the application or exported to spreadsheet formats for institutional use.

**Suggested caption:** *Figure 14. Report generation flow in the SchoGMS.*

**Screenshot / export:** Validation export download; chairman dashboard charts.

---

# Diagram 23 — Notification Flow

**Recommended paper section:** *Chapter 4 — Communication*

### Paper edition

**Diagram code:**
```mermaid
flowchart TD
  T1[Account created] --> N1[Verification email]
  T2[Annex 7 approved] --> N2[Approval email to coordinator]
  T3[Leadership role assigned] --> N3[Assignment email]
  T4[Validation export optional] --> N4[Email with attachment]
  N1 --> SMTP[Institutional SMTP]
  N2 --> SMTP
  N3 --> SMTP
  N4 --> SMTP
```

**Improved description:** System notifications are delivered primarily through email using institutional SMTP settings. Triggers include account verification, Annex 7 approval, leadership role assignment, and selected validation exports.

**Suggested caption:** *Figure 15. Notification flow for email-based alerts in the SchoGMS.*

**Note:** There is no in-application notification inbox (**Recommended Enhancement**).

---

# Diagram 24 — Audit Trail / Logging

**Recommended paper section:** *Chapter 4 — Accountability* (with limitation statement)

### Paper edition

**Diagram code:**
```mermaid
flowchart TD
  E1[Verification attempts] --> L1[(verification_attempts)]
  E2[Bulk upload summary] --> L2[(ched_upload_log)]
  E3[System errors] --> L3[Server error log]
  E4[Admin activity UI] --> L4[Placeholder sample data]
  L4 -.->|Recommended Enhancement| L5[Unified audit_log table]
```

**Improved description:** Accountability is supported through verification attempt logging, upload transaction logs, and server-side error logging. A consolidated administrative audit viewer is not fully implemented; the admin logs interface currently displays illustrative sample entries and should not be cited as live audit data without further development.

**Suggested caption:** *Figure 16. Audit and logging mechanisms in the SchoGMS (with noted limitations).*

**Screenshot / export:** Do **not** use `admin/logs.php` as evidence unless replaced with real logging; cite `verification_attempts` table structure instead.

---

## Summary: What changed in this revision

1. **Formal academic labels** on all paper editions (role titles, process names).  
2. **Simplified paper editions** for overcrowded diagrams (1, 2, 5, 6, 18, 22–24).  
3. **2FA explicitly** in diagrams 9 and 13.  
4. **Application workflow** renamed to beneficiary registration (import).  
5. **Endorsement** replaced with accurate **approval/rejection** and **role assignment** notes.  
6. **Schema** aligned to 14 confirmed tables; billing marked optional.  
7. **Figure 0 / HTML activity** designated as the primary process figure.  
8. **Screenshot guide** per critical figure for thesis evidence.

---

## Rendering checklist for thesis submission

| Priority | Figure | Export method |
|:--------:|--------|---------------|
| 1 | Overall activity | PDF from `schogms-activity-diagram.html` |
| 2 | Use case (paper) | PlantUML PNG |
| 3 | Architecture (paper) | Mermaid PNG |
| 4 | DFD Level 1 (paper) | Mermaid PNG |
| 5 | ER (paper) | Mermaid PNG |
| 6 | Login/2FA activity | Mermaid PNG + UI screenshots |
| 7 | Validation activity | Mermaid PNG + `validate.php` screenshot |
| 8 | Annex approval activity | Mermaid PNG + `anex-form2.php` screenshot |
| 9 | Role access | Mermaid PNG |
| 10 | Deployment | Mermaid PNG |

*End of review and revised editions.*
