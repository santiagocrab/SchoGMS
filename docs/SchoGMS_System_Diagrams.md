# SchoGMS — System Diagrams for Research Documentation

> **Revised editions (paper + technical, audit checklist, captions, screenshot guide):**  
> See **[SchoGMS_Diagrams_Review_and_Revised.md](./SchoGMS_Diagrams_Review_and_Revised.md)** — use that file for your thesis figures.  
> This file retains the original detailed diagram set for reference.

**System:** Scholarship and Grants Management System (SchoGMS)  
**Basis:** Codebase review (`users/`, `admin/`, `config/`, `inc/`, MySQL schema, `docs/SchoGMS_System_Documentation.md`)  
**Stack:** PHP (server-rendered pages), MySQL (primary), MongoDB file-store (legacy path), SMTP email, local/XAMPP deployment  

**Scope notes (read before using figures):**
- **Student/Scholar** and **Guardian** login portals are **not implemented**; scholars exist as rows in `ched_masterlist`, `ched_masterlist_tes`, and `registrar_master_list`.
- **Self-service scholarship application** (student fills a web form) is **not implemented**; workflows use **Excel/masterlist import**, **registrar uploads**, and **staff validation**.
- Diagrams 10, 14, and 17 that refer to “student application” are **mapped to the implemented CHED masterlist + validation workflow** and labeled accordingly.
- `admin/logs.php` shows **sample/placeholder rows** — not a live audit table (**Needs Verification**).
- `billing_table` is **optional** (used when present on chairman/coordinator verified-scholars pages).

---

## Simple Activity Diagram — How SchoGMS Works (Easy to Read)

**Diagram Type:** Activity Diagram (premium / reader-friendly)  

**Purpose:** The clearest single figure for your paper: four colored phases, plain language, role legend, no technical jargon.

**Best formats (pick one):**
- **HTML (recommended for screenshots):** [schogms-activity-diagram.html](./schogms-activity-diagram.html) — open in browser, print to PDF.
- **Interactive:** `canvases/schogms-activity-workflow.canvas.tsx` in Cursor.
- **Full write-up:** [SchoGMS_Simple_Activity_Diagram.md](./SchoGMS_Simple_Activity_Diagram.md)

**Description / Meaning:** SchoGMS runs in four phases: (1) staff get access through admin-created accounts and email verification; (2) scholar names and COR/COG documents are uploaded; (3) the coordinator checks whether records match; (4) Annex 7 is submitted and the chairman approves or rejects. Scholars never log in—their names exist only on uploaded lists.

**Diagram Code:**
```mermaid
flowchart TB
    START([START])

    subgraph P1["PHASE 1 — GET ACCESS"]
        direction TB
        A1["Administrator creates staff account"]
        A2["Staff receives 6-digit code by email"]
        A3["Staff enters code on Verify page"]
        A4{"Code correct?"}
        A5["Try again"]
        A6["Account is active"]
        A7["Staff logs in → opens dashboard"]
        A1 --> A2 --> A3 --> A4
        A4 -->|No| A5 --> A3
        A4 -->|Yes| A6 --> A7
    end

    subgraph P2["PHASE 2 — LOAD SCHOLAR DATA"]
        direction TB
        B1["Chairman or coordinator uploads CHED scholar list Excel"]
        B2["Registrar uploads COR and COG files"]
        B1 --> B2
    end

    subgraph P3["PHASE 3 — CHECK EVERYTHING"]
        direction TB
        C1["Coordinator compares lists and documents"]
        C2{"Records and documents match?"}
        C3["Mark scholars VALIDATED"]
        C4["Mark scholars FAILED"]
        C1 --> C2
        C2 -->|Yes| C3
        C2 -->|No| C4
    end

    subgraph P4["PHASE 4 — APPROVE CAMPUS REPORT"]
        direction TB
        D1["Coordinator uploads Annex 7 report"]
        D2["Chairman reviews the file"]
        D3{"Approve or reject?"}
        D4["Email sent to coordinator Approved"]
        D5["Status recorded Rejected"]
        D6["System saves all results for reports"]
        D1 --> D2 --> D3
        D3 -->|Approve| D4 --> D6
        D3 -->|Reject| D5 --> D6
    end

    END([END])

    START --> A1
    A7 --> B1
    B2 --> C1
    C3 --> D1
    C4 --> D1
    D6 --> END
```

**One-line summary:** Admin → verify → login → upload lists & documents → validate → Annex 7 → chairman approves → saved for reports.

**Note:** No student or guardian login in the current system.

**Suggested Figure Caption:** *Figure 0. Activity diagram of the SchoGMS in four phases: access, data loading, validation, and Annex 7 approval.*

---

## 1. System Architecture Diagram

**Diagram Type:** System Architecture Diagram  

**Purpose:** Shows major layers, modules, and data stores of SchoGMS as implemented.

**Description / Meaning:** SchoGMS follows a classic three-tier web architecture. The presentation layer consists of role-specific PHP pages under `users/{role}/` and a separate `admin/` portal. The application layer handles authentication (`login.php`, `config/mysql_auth.php`), email verification (`inc/verify_account.php`), masterlist processing, validation, and file uploads. Persistent data is stored primarily in **MySQL**. A **legacy MongoDB** file-based store (`conn_mongodb.php`) remains for some historical paths. Uploaded documents (COR, COG, Annex 7, Excel) are stored on the **filesystem** under `uploads/` and role-specific folders. Outbound **SMTP** supports verification and approval notifications.

**Diagram Code:**
```mermaid
flowchart TB
  subgraph Presentation["Presentation Layer (Browser)"]
    PUB["index.php / verify.php"]
    ADM_UI["admin/*.php"]
    ROLE_UI["users/{coordinator,chairman,registrar,director,dean,program-chair}/"]
  end

  subgraph Application["Application Layer (PHP on Apache/XAMPP)"]
    LOGIN["login.php"]
    MYSQL_AUTH["config/mysql_auth.php"]
    VERIFY["inc/verify_account.php / verify_code.php"]
    SESS["Role session guards config/session.php"]
    PROC["process_excel.php, submit_*, upload_*, validate APIs"]
    MAIL["config/mail.php + email_templates.php"]
    HELP["config/schogms_helpers.php"]
  end

  subgraph Data["Data Layer"]
    MYSQL[(MySQL — users, masterlists, documents metadata, file_submissions)]
    MONGO[(MongoDB file-store — legacy conn_mongodb.php)]
    FS[(Filesystem — uploads/COR, COG, annex7, Excel)]
  end

  subgraph External["External Services"]
    SMTP[SMTP Server]
  end

  PUB --> LOGIN
  ADM_UI --> MYSQL
  ROLE_UI --> SESS
  LOGIN --> MYSQL_AUTH
  LOGIN --> MONGO
  VERIFY --> MYSQL
  MYSQL_AUTH --> MYSQL
  PROC --> MYSQL
  PROC --> FS
  PROC --> MONGO
  MAIL --> SMTP
  SESS --> PROC
```

**Suggested Figure Caption:** *Figure 1. Layered system architecture of the SchoGMS showing PHP application modules, MySQL persistence, filesystem storage, and SMTP integration.*

---

## 2. Use Case Diagram

**Diagram Type:** Use Case Diagram  

**Purpose:** Summarizes actors and major use cases grounded in implemented modules.

**Description / Meaning:** Actors include **System Administrator**, **Chairman**, **Coordinator** (scholarship staff), **Registrar**, **Director**, **Dean**, and **Program Chair**. Use cases reflect actual pages: user provisioning, CHED TDP/TES masterlist management, registrar masterlist and COR/COG uploads, cross-validation, Annex 7 submission and approval, campus hierarchy assignment, and reporting/exports. **Student** and **Guardian** actors are shown dashed as **Recommended Enhancement** because no login module exists.

**Diagram Code (PlantUML):**
```plantuml
@startuml SchoGMS_UseCase
left to right direction
skinparam actorStyle awesome

actor "System Administrator" as Admin
actor "Chairman" as Chairman
actor "Coordinator\n(Scholarship Staff)" as Coord
actor "Registrar" as Reg
actor "Director" as Dir
actor "Dean" as Dean
actor "Program Chair" as PC

actor "Student/Scholar" as Student <<Recommended Enhancement>>
actor "Guardian" as Guardian <<Recommended Enhancement>>

rectangle "SchoGMS" {
  usecase "Manage users & campuses" as UC1
  usecase "Email verification (2FA)" as UC2
  usecase "Login" as UC3
  usecase "Upload/view CHED TDP masterlist" as UC4
  usecase "Upload/view CHED TES masterlist" as UC5
  usecase "Validate TDP vs registrar data" as UC6
  usecase "Manage registrar masterlist" as UC7
  usecase "Upload/view COR & COG" as UC8
  usecase "Submit Annex 7 report" as UC9
  usecase "Approve/reject Annex 7" as UC10
  usecase "Assign campus directors" as UC11
  usecase "Assign college deans" as UC12
  usecase "Assign program chairs" as UC13
  usecase "Monitor TDP/TES (dean/chair)" as UC14
  usecase "Import verified scholars/billing" as UC15
  usecase "Export validation reports" as UC16
  usecase "View admin dashboard stats" as UC17
  usecase "Self-service scholarship application" as UC18 <<Recommended Enhancement>>
}

Admin --> UC1
Admin --> UC17
Admin --> UC2
Chairman --> UC3
Chairman --> UC4
Chairman --> UC5
Chairman --> UC10
Chairman --> UC15
Coord --> UC3
Coord --> UC4
Coord --> UC5
Coord --> UC6
Coord --> UC8
Coord --> UC9
Coord --> UC11
Coord --> UC15
Coord --> UC16
Reg --> UC3
Reg --> UC7
Reg --> UC8
Dir --> UC3
Dir --> UC12
Dean --> UC3
Dean --> UC13
Dean --> UC14
PC --> UC3
PC --> UC14
Student ..> UC18
Guardian ..> UC18
UC2 .> UC3 : <<include>>
@enduml
```

**Suggested Figure Caption:** *Figure 2. Use case diagram of the SchoGMS illustrating role-based functions; student self-service application is marked as a recommended enhancement.*

---

## 3. Context Diagram

**Diagram Type:** Context Diagram (Level 0 external view)  

**Purpose:** Defines SchoGMS boundary and external entities.

**Description / Meaning:** SchoGMS operates as a central system interacting with institutional staff through web browsers, the **MySQL** database for structured records, the **file system** for documents, and **email** for verification and notifications. External entities include CHED-related data sources (imported via Excel), campus offices (coordinator, registrar, leadership roles), and the system administrator.

**Diagram Code:**
```mermaid
flowchart LR
  Admin[("System Administrator")]
  Staff[("Scholarship Staff\n(Coordinator, Chairman)")]
  RegOf[("Registrar Office")]
  Lead[("Campus Leadership\n(Director, Dean, Program Chair)")]
  CHED[("CHED / Institutional\nExcel Masterlists")]

  SchoGMS[["SchoGMS\n(Web Application)"]]

  DB[(MySQL Database)]
  Files[(Document Storage)]
  Email[SMTP / Email]

  Admin <-->|manage users, campuses| SchoGMS
  Staff <-->|masterlists, validation, Annex 7| SchoGMS
  RegOf <-->|masterlist, COR/COG| SchoGMS
  Lead <-->|assignments, monitoring| SchoGMS
  CHED -->|Excel import| SchoGMS
  SchoGMS <-->|CRUD| DB
  SchoGMS <-->|store/retrieve files| Files
  SchoGMS -->|verification & approval mail| Email
  Email --> Staff
  Email --> RegOf
```

**Suggested Figure Caption:** *Figure 3. Context diagram of the SchoGMS and its interaction with institutional actors and external data sources.*

---

## 4. Level 0 Data Flow Diagram

**Diagram Type:** DFD Level 0  

**Purpose:** High-level data flows between external entities and the system.

**Description / Meaning:** At Level 0, data enters SchoGMS as **login credentials**, **verification codes**, **Excel masterlists**, **registrar lists**, **COR/COG files**, and **Annex 7 submissions**. The system outputs **dashboards**, **validation results**, **approval status**, **reports/exports**, and **email notifications**.

**Diagram Code:**
```mermaid
flowchart LR
  E1[Staff Users]
  E2[Administrator]
  E3[Registrar]
  E4[Excel Files]

  P0(("0\nSchoGMS"))

  D1[(D1: User & Role Data)]
  D2[(D2: Scholarship Masterlists)]
  D3[(D3: Documents & Submissions)]

  E2 -->|user/campus config| P0
  E1 -->|credentials, validation actions| P0
  E3 -->|registrar data, COR/COG| P0
  E4 -->|TDP/TES imports| P0
  P0 -->|dashboards, reports| E1
  P0 -->|approval email| E1
  P0 <-->|read/write| D1
  P0 <-->|read/write| D2
  P0 <-->|read/write| D3
```

**Suggested Figure Caption:** *Figure 4. Level 0 data flow diagram of the SchoGMS.*

---

## 5. Level 1 Data Flow Diagram

**Diagram Type:** DFD Level 1  

**Purpose:** Decomposes SchoGMS into major processes aligned with code modules.

**Description / Meaning:** Level 1 expands the system into **P1 Authentication & Verification**, **P2 User & Campus Administration**, **P3 Masterlist Management**, **P4 Document Management**, **P5 Validation & Cross-Matching**, **P6 Annex 7 Workflow**, and **P7 Reporting & Export**. Data stores match MySQL tables and upload directories.

**Diagram Code:**
```mermaid
flowchart TB
  E1[Staff / Registrar]
  E2[Administrator]

  P1["P1: Authenticate &\nVerify Email"]
  P2["P2: Admin User &\nCampus Mgmt"]
  P3["P3: CHED/Registrar\nMasterlist Import"]
  P4["P4: COR/COG &\nFile Storage"]
  P5["P5: TDP/TES\nValidation"]
  P6["P6: Annex 7\nSubmit/Approve"]
  P7["P7: Reports &\nExports"]

  D1[(users, admin,\nverification_attempts)]
  D2[(ched_masterlist,\nregistrar_master_list)]
  D3[(document_uploads,\nuploads/*)]
  D4[(file_submissions)]

  E2 --> P2
  E1 -->|login| P1
  E1 -->|upload excel| P3
  E1 -->|upload docs| P4
  E1 -->|validate| P5
  E1 -->|submit annex| P6
  E1 -->|export| P7

  P1 <--> D1
  P2 <--> D1
  P3 --> D2
  P4 --> D3
  P5 <--> D2
  P5 <--> D3
  P6 <--> D4
  P7 --> D2
  P7 --> D4
  P6 -->|email| E1
```

**Suggested Figure Caption:** *Figure 5. Level 1 data flow diagram decomposing SchoGMS into authentication, administration, masterlist, document, validation, Annex 7, and reporting processes.*

---

## 6. Entity-Relationship Diagram

**Diagram Type:** ER Diagram (conceptual)  

**Purpose:** Shows principal entities and relationships in the MySQL schema.

**Description / Meaning:** The ER model centers on **users** (operational accounts), **campuses**, role-assignment tables (**assigned_dean**, **assigned_program_chairs**), scholarship rosters (**ched_masterlist**, **ched_masterlist_tes**), registrar records (**registrar_master_list**), uploaded documents (**document_uploads**), and compliance submissions (**file_submissions**). Relationships are predominantly logical (campus name matching) rather than strict foreign keys in all cases.

**Diagram Code:**
```mermaid
erDiagram
  CAMPUSES ||--o{ USERS : "assigned_to"
  USERS ||--o{ FILE_SUBMISSIONS : "submits"
  CAMPUSES ||--o{ CHED_MASTERLIST : "sheet_name"
  CAMPUSES ||--o{ CHED_MASTERLIST_TES : "campus"
  CAMPUSES ||--o{ REGISTRAR_MASTER_LIST : "campus"
  CAMPUSES ||--o{ DOCUMENT_UPLOADS : "campus"
  SCHOGMS_COLLEGES ||--o{ SCHOGMS_COURSES : "has"
  CAMPUSES ||--o{ ASSIGNED_DEAN : "campus"
  CAMPUSES ||--o{ ASSIGNED_PROGRAM_CHAIRS : "campus"
  USERS ||--o{ VERIFICATION_ATTEMPTS : "logs"

  USERS {
    int user_id PK
    string name
    string email
    string role
    string campus
    enum status
    tinyint email_verified
  }
  CAMPUSES {
    int id PK
    string campus_name
  }
  CHED_MASTERLIST {
    int id PK
    string sheet_name
    string app_no
    string lastname
    string validation_status
  }
  REGISTRAR_MASTER_LIST {
    int id PK
    string campus
    string last_name
    string course
  }
  DOCUMENT_UPLOADS {
    int id PK
    enum category
    string file_path
  }
  FILE_SUBMISSIONS {
    int id PK
    enum status
    string file_path
  }
  ASSIGNED_DEAN {
    int id PK
    string college_name
    string email
  }
  ASSIGNED_PROGRAM_CHAIRS {
    int id PK
    string course_program
    string email
  }
```

**Suggested Figure Caption:** *Figure 6. Entity-relationship diagram of core SchoGMS database entities.*

---

## 7. Database Schema Diagram

**Diagram Type:** Database Schema Diagram  

**Purpose:** Lists implemented MySQL tables and key columns for documentation.

**Description / Meaning:** The physical schema supports multi-campus scholarship operations. The `users` table stores coordinator, chairman, registrar, and director accounts with verification fields. `assigned_dean` and `assigned_program_chairs` store college/program leadership credentials. Masterlist tables hold beneficiary records. `document_uploads` indexes COR/COG files. `file_submissions` tracks Annex 7. Optional `billing_table` (**Needs Verification** on deployment) supports verified-scholar payment imports.

**Diagram Code:**
```mermaid
classDiagram
  class admin {
    admin_id PK
    username
    password
  }
  class users {
    user_id PK
    email
    role
    campus
    status
    verification_code
    email_verified
  }
  class campuses {
    id PK
    campus_name
  }
  class ched_masterlist {
    id PK
    sheet_name
    app_no
    lastname
    validation_status
  }
  class ched_masterlist_tes {
    id PK
    campus
    app_no
  }
  class registrar_master_list {
    id PK
    campus
    last_name
    course
  }
  class document_uploads {
    id PK
    category COR/COG
    file_path
  }
  class file_submissions {
    id PK
    status Pending/Approved/Rejected
  }
  class assigned_dean {
    id PK
    college_name
    email
  }
  class assigned_program_chairs {
    id PK
    course_program
    email
  }
  class verification_attempts {
    id PK
    email
    attempt_time
  }
  class schogms_colleges {
    id PK
    campus
  }
  class schogms_courses {
    id PK
    college_id FK
  }
  class billing_table {
    <<optional>>
    Needs Verification
  }
```

**Suggested Figure Caption:** *Figure 7. Database schema overview of the SchoGMS MySQL tables.*

---

## 8. Class Diagram

**Diagram Type:** Class Diagram (logical / module-level)  

**Purpose:** Describes major PHP modules as logical classes (SchoGMS is procedural PHP, not OOP MVC).

**Description / Meaning:** SchoGMS does not use a framework-style class model for controllers; instead, **include files** and **namespaced functions** encapsulate behavior. The diagram documents this **logical structure** for academic completeness. **Needs Verification:** some legacy pages call MongoDB wrappers directly.

**Diagram Code:**
```mermaid
classDiagram
  class LoginRouter {
    +login.php
    +routeByRole()
  }
  class MySQLAuth {
    +schogms_mysql_users_login()
    +schogms_load_mysql_session_user()
  }
  class VerifyAccount {
    +schogms_verify_user_account()
    +schogms_log_verification_attempt()
  }
  class SessionGuard {
    +config/session.php per role
  }
  class ValidationService {
    +schogms_validation_build_sql_where()
    +tdp_bulk_validate()
    +compare registrar vs CHED
  }
  class MasterlistImport {
    +process_excel.php
    +submit_ched_masterlist.php
  }
  class DocumentService {
    +document_uploads CRUD
    +filesystem store
  }
  class Annex7Workflow {
    +upload_file.php
    +update_status.php
  }
  class MailService {
    +schogms_send_mail()
  }
  LoginRouter --> MySQLAuth
  LoginRouter --> SessionGuard
  VerifyAccount --> MySQLAuth
  ValidationService --> MasterlistImport
  Annex7Workflow --> MailService
  DocumentService --> ValidationService
```

**Suggested Figure Caption:** *Figure 8. Logical class diagram of major SchoGMS PHP modules and their dependencies.*

---

## 9. Activity Diagram — Login and 2FA Verification

**Diagram Type:** Activity Diagram  

**Purpose:** Documents authentication and email verification as implemented.

**Description / Meaning:** Users authenticate at `index.php` → `login.php`. MySQL users with `email_verified = 0` are redirected to `verify.php`. Successful verification via `verify_code.php` activates the account and starts a session. Failed attempts may be recorded in `verification_attempts`. Chairman accounts may be created pre-verified by the administrator.

**Diagram Code:**
```mermaid
flowchart TD
  Start([Start]) --> A[Open index.php]
  A --> B[Submit credentials to login.php]
  B --> C{Valid credentials?}
  C -->|No| D[Show error on index.php]
  D --> End1([End])
  C -->|Yes| E{Account status?}
  E -->|restricted/inactive| D
  E -->|pending or not verified| F[Redirect to verify.php]
  F --> G[Enter email + 6-digit code]
  G --> H[POST verify_code.php]
  H --> I{Code valid and not expired?}
  I -->|No| J[Log verification_attempts]
  J --> F
  I -->|Yes| K[Set users.status=active, email_verified=1]
  K --> L[Create PHP session]
  L --> M[Redirect to role dashboard]
  E -->|active & verified| L
  M --> End2([End])
```

**Suggested Figure Caption:** *Figure 9. Activity diagram of login and email-based two-factor verification in the SchoGMS.*

---

## 10. Activity Diagram — Student Scholarship Application

**Diagram Type:** Activity Diagram  

**Purpose:** Documents scholarship beneficiary intake as actually implemented.

**Description / Meaning:** **Recommended Enhancement:** a student self-service application portal. **Implemented equivalent:** CHED TDP/TES beneficiaries are onboarded through **Excel upload** by Chairman or Coordinator (`submit_ched_masterlist.php`, `process_excel.php`), creating rows in `ched_masterlist` / `ched_masterlist_tes`. Coordinators may also work with registrar-sourced lists. This activity diagram therefore reflects **institutional import**, not a student-facing form.

**Diagram Code:**
```mermaid
flowchart TD
  Start([Start]) --> A{Actor?}
  A -->|Chairman / Coordinator| B[Open CHED masterlist upload page]
  B --> C[Select campus, file group, Excel file]
  C --> D[Submit to process_excel / submit handler]
  D --> E{Parse & validate rows?}
  E -->|Errors| F[Show error count / log ched_upload_log]
  F --> End1([End])
  E -->|Success| G[Insert into ched_masterlist or ched_masterlist_tes]
  G --> H[Set validation_status empty or Pending]
  H --> I[Display masterlist table]
  I --> End2([End])

  A -.->|Recommended Enhancement| X[Student logs in and fills application form]
  X -.-> Y[Not implemented in codebase]
```

**Suggested Figure Caption:** *Figure 10. Activity diagram of scholarship beneficiary intake via CHED masterlist import (implemented); student self-service application is a recommended enhancement.*

---

## 11. Activity Diagram — Requirements Upload and Verification

**Diagram Type:** Activity Diagram  

**Purpose:** Covers documentary requirements (COR, COG) and validation.

**Description / Meaning:** The **Registrar** uploads COR/COG files into `document_uploads` and the filesystem. **Coordinators** track documents and run **TDP validation** (`validate.php`, `auto_validate_tdp.php`) comparing CHED masterlist fields against `registrar_master_list` and document presence. Validation updates `ched_masterlist.validation_status` to **Validated** or **Failed**. Enrollment/document completeness may be exported via validation remark reports.

**Diagram Code:**
```mermaid
flowchart TD
  Start([Start]) --> R[Registrar opens cor-cog.php]
  R --> U[Upload COR/COG files]
  U --> V[Store file_path in document_uploads]
  V --> W[Files saved under uploads/]

  Start --> C[Coordinator opens validate.php]
  C --> X[Load campus TDP rows + filters]
  X --> Y[Compare with registrar_master_list]
  Y --> Z{COR & COG match rules?}
  Z -->|Yes| AA[Mark validation_status Validated]
  Z -->|No| AB[Mark validation_status Failed]
  AA --> AC[Export / view validated remarks]
  AB --> AC
  AC --> End([End])
```

**Suggested Figure Caption:** *Figure 11. Activity diagram of requirements upload by the registrar and verification by the scholarship coordinator.*

---

## 12. Activity Diagram — Application Review and Approval

**Diagram Type:** Activity Diagram  

**Purpose:** Documents review/approval workflows present in the system.

**Description / Meaning:** **Implemented review/approval** includes: (1) **TDP validation** by coordinator; (2) **Annex 7** submission by coordinator (`submit_form.php` / `upload_file.php`) with chairman **approve/reject** (`anex-form2.php`, `update_status.php`); (3) optional **billing** import on verified scholars pages. There is no separate “scholarship application committee” module beyond these flows.

**Diagram Code:**
```mermaid
flowchart TD
  Start([Start]) --> A[Coordinator completes validation & reports]
  A --> B[Coordinator uploads Annex 7 Excel]
  B --> C[Insert file_submissions status=Pending]
  C --> D[Chairman opens Annex 7 review]
  D --> E{Review file preview}
  E --> F{Decision}
  F -->|Approve| G[update_status.php → Approved]
  G --> H[Send approval email via SMTP]
  F -->|Reject| I[status=Rejected]
  F -->|Hold| J[status=Pending]
  H --> End([End])
  I --> End
  J --> End
```

**Suggested Figure Caption:** *Figure 12. Activity diagram of Annex 7 submission and chairman approval in the SchoGMS.*

---

## 13. Sequence Diagram — Login and 2FA

**Diagram Type:** Sequence Diagram  

**Purpose:** Shows temporal interaction for login and verification.

**Description / Meaning:** The sequence involves the browser, PHP login/verify endpoints, MySQL `users` table, and optional SMTP (on account creation, not on every login). Session variables (`user_id`, `role`, `auth_type`) are set before redirecting to the role folder from `schogms_role_home()`.

**Diagram Code:**
```mermaid
sequenceDiagram
  actor User
  participant Browser
  participant index.php
  participant login.php
  participant mysql_auth
  participant MySQL
  participant verify.php
  participant verify_code.php

  User->>Browser: Enter email/password
  Browser->>login.php: POST credentials
  login.php->>mysql_auth: schogms_mysql_users_login()
  mysql_auth->>MySQL: SELECT users
  alt invalid password
    MySQL-->>login.php: no match
    login.php-->>Browser: redirect index.php?ERROR=1
  else not verified
    MySQL-->>login.php: email_verified=0
    login.php-->>Browser: redirect verify.php
    User->>Browser: Enter 6-digit code
    Browser->>verify_code.php: POST email+code
    verify_code.php->>MySQL: verify & UPDATE users
    verify_code.php-->>Browser: JSON success + redirect URL
  else success
    login.php->>Browser: Set session, redirect users/{role}/
  end
```

**Suggested Figure Caption:** *Figure 13. Sequence diagram of user login and email verification in the SchoGMS.*

---

## 14. Sequence Diagram — Scholarship Application Submission

**Diagram Type:** Sequence Diagram  

**Purpose:** Shows beneficiary data submission path as implemented.

**Description / Meaning:** **Mapped to CHED masterlist import** (not student portal). Chairman or Coordinator uploads Excel; server parses and inserts rows; upload metadata may be written to `ched_upload_log`.

**Diagram Code:**
```mermaid
sequenceDiagram
  actor Staff as Chairman/Coordinator
  participant UI as ched_masterlist.php
  participant API as process_excel.php
  participant MySQL
  participant FS as Filesystem

  Staff->>UI: Select Excel + metadata
  UI->>API: POST file
  API->>FS: Save upload temporarily
  API->>API: Parse spreadsheet
  loop each row
    API->>MySQL: INSERT ched_masterlist
  end
  API->>MySQL: INSERT ched_upload_log
  API-->>UI: success/error counts
  UI-->>Staff: Display masterlist
```

**Suggested Figure Caption:** *Figure 14. Sequence diagram of CHED masterlist import representing scholarship beneficiary registration in the SchoGMS.*

---

## 15. Sequence Diagram — Requirements Review

**Diagram Type:** Sequence Diagram  

**Purpose:** Shows coordinator validation against registrar data and documents.

**Description / Meaning:** Validation requests load filtered TDP rows, join or compare with `registrar_master_list`, check `document_uploads` for name/campus matches, and persist `validation_status`. Bulk validation may use `auto_validate_tdp.php` / `tdp_bulk_validate.php`.

**Diagram Code:**
```mermaid
sequenceDiagram
  actor Coord as Coordinator
  participant validate.php
  participant API as auto_validate_tdp.php
  participant MySQL

  Coord->>validate.php: Open validation UI + filters
  validate.php->>MySQL: SELECT ched_masterlist (campus)
  validate.php->>MySQL: SELECT registrar_master_list
  validate.php->>MySQL: SELECT document_uploads
  Coord->>API: Trigger bulk validate
  loop each scholar row
    API->>API: Compare names/enrollment/docs
    API->>MySQL: UPDATE validation_status
  end
  API-->>Coord: JSON counts Validated/Failed
```

**Suggested Figure Caption:** *Figure 15. Sequence diagram of TDP requirements review and validation by the campus coordinator.*

---

## 16. Sequence Diagram — Application Approval

**Diagram Type:** Sequence Diagram  

**Purpose:** Shows chairman approval of coordinator submissions (Annex 7).

**Description / Meaning:** Chairman updates `file_submissions.status` through `update_status.php`. On **Approved**, the system sends email to the coordinator’s `user_email` using `schogms_send_mail()` and templates from `config/email_templates.php`.

**Diagram Code:**
```mermaid
sequenceDiagram
  actor Chairman
  participant UI as anex-form2.php
  participant API as update_status.php
  participant MySQL
  participant Mail as config/mail.php
  actor Coord as Coordinator

  Chairman->>UI: Preview Annex file
  Chairman->>API: POST file_id, status=Approved
  API->>MySQL: SELECT file_submissions
  API->>MySQL: UPDATE status
  API->>Mail: schogms_send_mail(coordinator)
  Mail-->>Coord: Annex 7 Approved email
  API-->>UI: JSON success
```

**Suggested Figure Caption:** *Figure 16. Sequence diagram of Annex 7 approval and coordinator notification in the SchoGMS.*

---

## 17. State Diagram — Scholarship Application Status

**Diagram Type:** State Diagram  

**Purpose:** Models status lifecycles for records in SchoGMS.

**Description / Meaning:** Multiple parallel state machines exist: **user accounts** (`pending` → `active`), **Annex 7 files** (`Pending` → `Approved` / `Rejected`), and **TDP validation** (`validation_status`: empty/Pending → `Validated` / `Failed`). A unified “scholarship application” entity does not exist as a single table.

**Diagram Code:**
```mermaid
stateDiagram-v2
  [*] --> Imported: Excel import to ched_masterlist
  Imported --> PendingValidation: validation_status empty
  PendingValidation --> Validated: Coordinator validation pass
  PendingValidation --> Failed: Validation fail
  Validated --> [*]

  state "Annex 7 (file_submissions)" as Annex {
    [*] --> AnnexPending: Coordinator upload
    AnnexPending --> AnnexApproved: Chairman approves
    AnnexPending --> AnnexRejected: Chairman rejects
    AnnexApproved --> [*]
    AnnexRejected --> [*]
  }

  state "User account (users)" as Acct {
    [*] --> AcctPending: Admin creates user
    AcctPending --> AcctActive: Email verified
    AcctPending --> AcctRestricted: Admin restricts
    AcctActive --> [*]
  }
```

**Suggested Figure Caption:** *Figure 17. State diagram of TDP validation, Annex 7 submission, and user account statuses in the SchoGMS.*

---

## 18. User Role Access Diagram

**Diagram Type:** Role-Based Access Diagram  

**Purpose:** Visualizes which roles access which module groups.

**Description / Meaning:** Access is enforced by **separate portals** (`admin/` vs `users/{role}/`), **session role checks** in `config/session.php`, and **campus/college scoping** (`inc/campus_access.php`). Deans and program chairs authenticate via `assigned_dean` / `assigned_program_chairs` tables in addition to or instead of `users`.

**Diagram Code:**
```mermaid
flowchart TB
  subgraph AdminPortal["admin/ — System Administrator"]
    A1[Dashboard]
    A2[User Management]
    A3[Logs - placeholder]
  end

  subgraph ChairmanMod["Chairman — system-wide"]
    C1[TDP/TES masterlist]
    C2[Annex 7 approve]
    C3[Verified scholars/billing]
  end

  subgraph CoordMod["Coordinator — one campus"]
    O1[TDP/TES + validate]
    O2[COR/COG view]
    O3[Annex 7 submit]
    O4[Director assign]
    O5[Verified scholars]
  end

  subgraph RegMod["Registrar — campus"]
    R1[Registrar masterlist]
    R2[COR/COG upload]
  end

  subgraph HierMod["Campus hierarchy"]
    D1[Director → assign deans]
    D2[Dean → assign chairs]
    D3[Program chair → TDP/TES view]
  end

  Admin --> AdminPortal
  Chairman --> ChairmanMod
  Coordinator --> CoordMod
  Registrar --> RegMod
  Director --> HierMod
  Dean --> HierMod
  ProgramChair --> HierMod
```

**Suggested Figure Caption:** *Figure 18. Role-based access map of SchoGMS functional modules by user type.*

---

## 19. Sitemap / Navigation Structure Diagram

**Diagram Type:** Sitemap  

**Purpose:** Documents navigable pages per role for screenshots and usability documentation.

**Description / Meaning:** The sitemap follows folder structure under `users/` and `admin/`. Sidebars are defined in role `index.php` and shared `inc/*_nav.php` files (coordinator, registrar). Some legacy PHP pages exist but are omitted from sidebars.

**Diagram Code:**
```mermaid
flowchart TD
  Root[index.php / verify.php]
  Root --> Admin[admin/]
  Admin --> A1[dashboard.php]
  Admin --> A2[user-management.php]
  Admin --> A3[logs.php]

  Root --> Coord[users/coordinator/]
  Coord --> C1[index.php]
  Coord --> C2[ched_masterlist.php]
  Coord --> C3[ched_masterlist_tes.php]
  Coord --> C4[validate.php]
  Coord --> C5[cor-cog.php]
  Coord --> C6[directors.php]
  Coord --> C7[submit_form.php]
  Coord --> C8[verified-scholars.php]

  Root --> Chair[users/chairman/]
  Chair --> H1[index.php]
  Chair --> H2[ched_masterlist.php]
  Chair --> H3[anex-form2.php]
  Chair --> H4[verified-scholars.php]

  Root --> Reg[users/registrar/]
  Reg --> R1[index.php]
  Reg --> R2[masterlist.php]
  Reg --> R3[cor-cog.php]
  Reg --> R4[documents_uploaded.php]

  Root --> Dir[users/director/]
  Dir --> D1[index.php + deans.php]

  Root --> Dean[users/dean/]
  Dean --> DN1[index.php + program chairs]

  Root --> PC[users/program-chair/]
  PC --> P1[tdp.php / tes.php]
```

**Suggested Figure Caption:** *Figure 19. Sitemap of primary SchoGMS navigation paths by role.*

---

## 20. Deployment Diagram

**Diagram Type:** Deployment Diagram  

**Purpose:** Shows physical/logical deployment typical for XAMPP development and institutional hosting.

**Description / Meaning:** SchoGMS is deployed as files under the web root (e.g. `htdocs/SchoGMS`). **Apache** serves PHP. **MySQL** runs locally or on a network server. Uploaded files reside on disk. SMTP may be institutional or a provider configured in `config/smtp.local.php`.

**Diagram Code:**
```mermaid
flowchart TB
  subgraph Client["Client Tier"]
    Browser[Web Browser]
  end

  subgraph Server["Application Server — XAMPP / LAMP"]
    Apache[Apache HTTP Server]
    PHP[PHP Runtime]
    App[SchoGMS PHP Pages]
  end

  subgraph DataTier["Data Tier"]
    MySQL[(MySQL Server)]
    Disk[(File Storage\nuploads/)]
  end

  subgraph Net["Network"]
    SMTP[SMTP Server]
  end

  Browser --> Apache
  Apache --> PHP
  PHP --> App
  App --> MySQL
  App --> Disk
  App --> SMTP
```

**Suggested Figure Caption:** *Figure 20. Deployment diagram of the SchoGMS on an Apache/PHP/MySQL stack.*

---

## 21. Component Diagram

**Diagram Type:** Component Diagram  

**Purpose:** Groups related PHP components and shared libraries.

**Description / Meaning:** Components are **coarse-grained PHP modules** rather than Composer packages (except PhpSpreadsheet for Excel). Shared configuration lives in `config/`; cross-role logic in `inc/`.

**Diagram Code:**
```mermaid
flowchart TB
  subgraph WebUI["Web UI Components"]
    AdminUI[admin module]
    CoordUI[coordinator module]
    ChairUI[chairman module]
    RegUI[registrar module]
    DirUI[director module]
    DeanUI[dean module]
    PCUI[program-chair module]
  end

  subgraph Core["Core Services"]
    Auth[login.php + mysql_auth.php]
    Verify[inc/verify_account.php]
    Mail[config/mail.php]
    Helpers[schogms_helpers.php]
    CampusAccess[inc/campus_access.php]
  end

  subgraph DataAccess["Data Access"]
    MySQLConn[config/schogms_mysql.php]
    MongoLegacy[conn_mongodb.php]
  end

  subgraph Integrations["Integrations"]
    PhpSpreadsheet[PhpSpreadsheet — Excel]
    DataTables[DataTables / Chart.js — UI]
  end

  AdminUI --> Auth
  CoordUI --> Auth
  ChairUI --> Auth
  RegUI --> Auth
  CoordUI --> CampusAccess
  CoordUI --> PhpSpreadsheet
  ChairUI --> PhpSpreadsheet
  Auth --> MySQLConn
  Verify --> MySQLConn
  CoordUI --> MongoLegacy
  Mail --> SMTPExt[SMTP]
```

**Suggested Figure Caption:** *Figure 21. Component diagram of SchoGMS PHP modules and shared services.*

---

## 22. Report Generation Flow Diagram

**Diagram Type:** Process Flow / Data Flow  

**Purpose:** Describes how reports and exports are produced.

**Description / Meaning:** Reports are generated by **exporting SQL result sets** to Excel/CSV via PhpSpreadsheet or dedicated export scripts (`validated_remarks.php`, `validated_remarks_tes.php`, coordinator `data/masterlist_exports/`, chairman download templates). Charts on dashboards use `fetch_chart.php` endpoints. There is no separate reporting server.

**Diagram Code:**
```mermaid
flowchart LR
  A[User opens validation or masterlist page]
  B[Apply filters — campus, category, status]
  C[SQL query ched_masterlist + joins]
  D{Output format?}
  E[On-screen DataTable]
  F[PhpSpreadsheet Excel export]
  G[CSV masterlist_exports]
  H[Dashboard chart JSON fetch_chart.php]

  A --> B --> C
  C --> D
  D --> E
  D --> F
  D --> G
  C --> H
```

**Suggested Figure Caption:** *Figure 22. Report generation flow from filtered queries to on-screen tables, Excel exports, and dashboard charts in the SchoGMS.*

---

## 23. Notification Flow Diagram

**Diagram Type:** Process Flow  

**Purpose:** Shows when the system sends email notifications.

**Description / Meaning:** Email is used for **account verification** (new user), **Annex 7 approval** (chairman → coordinator), and **role assignment** templates in `config/email_templates.php`. Delivery depends on SMTP configuration; failures are logged via `schogms_log_error()`. There is **no in-app notification center** (**Recommended Enhancement**).

**Diagram Code:**
```mermaid
flowchart TD
  T1[Admin creates user] --> M1[Generate verification_code]
  M1 --> E1{SMTP configured?}
  E1 -->|Yes| Mail1[Send welcome/verification email]
  E1 -->|No| N1[Needs Verification — manual code]

  T2[Chairman approves Annex 7] --> M2[update_status.php]
  M2 --> Mail2[schogms_send_mail to coordinator]

  T3[Dean/Director assigns role] --> M3[Assignment email templates]
  M3 --> Mail3[Optional SMTP send]

  Mail1 --> Log[error_log / schogms_log_error on failure]
  Mail2 --> Log
  Mail3 --> Log
```

**Suggested Figure Caption:** *Figure 23. Notification flow for email-based alerts in the SchoGMS.*

---

## 24. Audit Trail / Logging Flow Diagram

**Diagram Type:** Process Flow  

**Purpose:** Documents logging and audit mechanisms.

**Description / Meaning:** Implemented logging includes **`verification_attempts`** (failed/successful verification tries), **`ched_upload_log`** (bulk upload statistics), and **PHP `error_log`** via `schogms_log_error()` and `config/error_handler.php`. The **admin logs UI** currently displays **static sample rows** — a dedicated `audit_log` table was **not found** (**Needs Verification** / **Recommended Enhancement**).

**Diagram Code:**
```mermaid
flowchart TD
  subgraph Events["Auditable Events"]
    E1[Login failure]
    E2[Verification attempt]
    E3[Excel masterlist upload]
    E4[Annex status change]
    E5[PHP/runtime errors]
  end

  subgraph Stores["Implemented Stores"]
    V1[(verification_attempts)]
    V2[(ched_upload_log)]
    V3[(PHP error_log file)]
  end

  subgraph UI["Admin UI"]
    L1[admin/logs.php — placeholder sample data]
  end

  E2 --> V1
  E3 --> V2
  E5 --> V3
  E1 --> V3
  E4 --> V3

  V1 -.->|Recommended Enhancement| L2[Unified audit_log table + UI]
  L1 -.-> NeedsVerification[Needs Verification]
```

**Suggested Figure Caption:** *Figure 24. Audit and logging flow in the SchoGMS, including verification attempts, upload logs, and error logging.*

---

## Appendix A — Mapping Paper Terminology to Implementation

| Paper / generic term | SchoGMS implementation |
|----------------------|-------------------------|
| Scholarship application | CHED TDP/TES masterlist row + validation |
| Application submission | Excel import / registrar masterlist upload |
| Requirements upload | COR/COG → `document_uploads` |
| Application review | `validate.php`, remarks exports |
| Application approval | Annex 7 `file_submissions` + chairman `update_status.php` |
| Student portal | **Not implemented** |
| Profile management | Limited; `change_password.php` on some roles |
| Audit logs | Partial; admin logs UI is placeholder |

---

## Appendix B — Rendering Diagrams

- **Mermaid:** VS Code, GitHub, [mermaid.live](https://mermaid.live), or Pandoc with mermaid filter.
- **PlantUML:** Use for Figure 2 (Use Case); [plantuml.com](https://www.plantuml.com/plantuml).

---

*End of diagram set — aligned with SchoGMS codebase as of documentation review.*
