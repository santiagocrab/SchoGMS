# SchoGMS — System Architecture & Functional Design

**Best visual version:** Open **[schogms-architecture-functional.html](./schogms-architecture-functional.html)** in your browser (print to PDF for your paper).

**Chapter 3 integration:** Paste Section 1–2 below into [Chapter3_System_Design.md](./Chapter3_System_Design.md) §3.3.1 or use as replacement figures.

---

## 1. System Architecture

### Description

The SchoGMS architecture follows a **three-tier, multi-portal web model**. Institutional staff access the system through a web browser. The **presentation tier** includes a public authentication entry (`index.php`, `verify.php`), a separate **administrator portal** (`admin/`), and **seven role-specific modules** under `users/` (coordinator, chairman, registrar, director, dean, program-chair). The **application tier** is implemented in PHP: authentication and two-factor email verification, campus-scoped session control, Excel masterlist import, document upload, TDP/TES validation, Annex 7 approval workflow, and SMTP notifications. The **data tier** consists of a **MySQL** relational database (fourteen core tables) and **filesystem storage** for COR, COG, Annex 7, and import files. **SMTP** is an external service for verification codes and approval emails.

### Meaning of the diagram

Readers can see **where each role lives** in the structure (not one generic dashboard) and **which cross-cutting services** (auth, mail, validation) sit between the UI and the database. This matches the actual XAMPP/LAMP deployment: Apache executes PHP; there is no separate API gateway or microservice layer in the current build.

### Figure caption

*Figure 3-1. Layered system architecture of the SchoGMS showing presentation portals, application modules, data stores, and external email service.*

### Diagram code (paper — layered)

```mermaid
flowchart TB
  subgraph L1["Layer 1 — Presentation"]
    BR[Web Browser]
    PUB[Public Login and Verify]
    ADM[Admin Portal]
    R1[Coordinator Portal]
    R2[Chairman Portal]
    R3[Registrar Portal]
    R4[Director / Dean / Program Chair]
  end

  subgraph L2["Layer 2 — Application — PHP"]
    AUTH[Authentication and 2FA]
    SESS[Session and Campus Access]
    ML[Masterlist Import]
    DOC[Document Management]
    VAL[Validation Engine]
    ANX[Annex 7 Workflow]
    MAIL[Email Service]
  end

  subgraph L3["Layer 3 — Data"]
    MY[(MySQL)]
    FS[(File Storage)]
  end

  SMTP[SMTP Email Server]

  BR --> PUB & ADM & R1 & R2 & R3 & R4
  PUB & ADM & R1 & R2 & R3 & R4 --> AUTH
  AUTH --> SESS
  SESS --> ML & DOC & VAL & ANX
  ML & DOC & VAL & ANX --> MY
  ML & DOC & ANX --> FS
  AUTH & ANX --> MAIL
  MAIL --> SMTP
```

### Diagram code (technical — deployment style)

```mermaid
flowchart TB
  subgraph Client["Client"]
    Browser[Browser]
  end
  subgraph Apache["Apache + PHP"]
    login[login.php / verify_code.php]
    admin[admin/*]
    coord[users/coordinator/*]
    chair[users/chairman/*]
    reg[users/registrar/*]
    hier[users/director | dean | program-chair]
    inc[inc/ + config/]
  end
  subgraph Persist["Persistence"]
    MySQL[(MySQL 14 tables)]
    Disk[uploads/ + annex7]
  end
  Browser --> login & admin & coord & chair & reg & hier
  login & admin & coord & chair & reg & hier --> inc
  inc --> MySQL & Disk
  inc --> SMTP[config/mail.php → SMTP]
```

### Screenshot / export

- **Primary:** PDF from `schogms-architecture-functional.html` Section 1.
- **Alternative:** Mermaid PNG from codes above.

---

## 2. Functional Design

### Description

Functional design decomposes SchoGMS into **eight cohesive functions** (A–H), each mapped to implemented pages and database artifacts. Together they cover the full scholarship lifecycle: provisioning access, loading beneficiaries, attaching compliance documents, validating records, obtaining executive approval on Annex 7, delegating campus leadership, and producing reports.

### Meaning of the diagram

Where the architecture diagram shows **layers and technology**, the functional design shows **what the system does** for users. The pipeline diagram (B → C → D → E → F → H) is the main operational path; function G operates in parallel for organizational structure.

### Figure caption

*Figure 3-2. Functional design of the SchoGMS showing eight modules and the scholarship operations pipeline.*

### Diagram code (functional modules + pipeline)

```mermaid
flowchart LR
  subgraph Functions["SchoGMS Functional Modules"]
    direction TB
    FA["A Authentication\nand 2FA"]
    FB["B User and Campus\nAdmin"]
    FC["C CHED Masterlists\nTDP / TES"]
    FD["D Registrar Data\nCOR / COG"]
    FE["E Validation\nand Compliance"]
    FF["F Annex 7\nApprove / Reject"]
    FG["G Campus Hierarchy\nAssignments"]
    FH["H Reporting\nDashboards"]
  end

  subgraph Pipeline["Main Operations Pipeline"]
    direction LR
    P1[B + A] --> P2[C] --> P3[D] --> P4[E] --> P5[F] --> P6[H]
  end

  FG -.->|parallel monitoring| P2
  FG -.->|parallel monitoring| P4
```

### Function reference table

| ID | Function | Purpose | Primary roles | Key tables / paths |
|----|----------|---------|---------------|-------------------|
| **A** | Authentication & security | Login, email verification (2FA), session | All staff | `users`, `verification_attempts`, `login.php`, `verify.php` |
| **B** | User & campus administration | Create accounts, campuses, stats | Administrator | `admin/`, `users`, `campuses` |
| **C** | CHED beneficiary masterlists | Import and view TDP/TES rosters | Chairman, coordinator | `ched_masterlist`, `ched_masterlist_tes` |
| **D** | Registrar data & documents | Masterlist, COR/COG upload | Registrar | `registrar_master_list`, `document_uploads` |
| **E** | Validation & compliance | Cross-check lists and documents | Coordinator | `validation_status`, `validate.php` |
| **F** | Annex 7 workflow | Submit, review, approve, reject, notify | Coordinator, chairman | `file_submissions`, `update_status.php` |
| **G** | Campus hierarchy | Directors, deans, program chairs | Coordinator, director, dean | `assigned_dean`, `assigned_program_chairs` |
| **H** | Reporting & dashboards | Charts, tables, exports | All roles (scoped) | Dashboard `index.php`, exports |

### Combined architecture + functional (single figure for thesis)

```mermaid
flowchart TB
  subgraph Users["Users"]
    ADM2[Administrator]
    STAFF[Chairman Coordinator Registrar]
    LEAD[Director Dean Program Chair]
  end

  subgraph Presentation["Presentation Layer"]
    UI[Role-based Web Portals]
  end

  subgraph Core["Application Functions"]
    F_A[A Auth 2FA]
    F_B[B Admin]
    F_C[C Masterlists]
    F_D[D Documents]
    F_E[E Validation]
    F_F[F Annex 7]
    F_G[G Hierarchy]
    F_H[H Reports]
  end

  subgraph Data["Data Layer"]
    DB[(MySQL)]
    FILES[(Files)]
  end

  ADM2 --> UI
  STAFF --> UI
  LEAD --> UI
  UI --> F_A --> F_B & F_C & F_D & F_E & F_F & F_G & F_H
  F_B & F_C & F_E & F_F --> DB
  F_D & F_F --> FILES
  F_A & F_F --> EXT[SMTP]
```

### Screenshot / export

- **Primary:** PDF from `schogms-architecture-functional.html` Sections 2–3.
- Capture **one sidebar each** for coordinator, chairman, registrar to show functional modules in the UI (optional composite figure).

---

## 3. Recommended paper section

| Content | Section |
|---------|---------|
| System Architecture | Chapter 3 — §3.3.1 System Architecture |
| Functional Design | Chapter 3 — new §3.3.1b Functional Design (or merge with 3.3.1) |
| Combined figure | Figure 3-1a (architecture) and Figure 3-1b (functional) |

---

## 4. One-paragraph summary (copy for thesis)

The SchoGMS is architected as a three-tier web application in which role-specific PHP portals communicate with a shared application core that implements authentication with email-based verification, CHED masterlist management, registrar document handling, record validation, Annex 7 approval, campus hierarchy assignment, and reporting. Persistent data are stored in MySQL and on the server filesystem; notifications are delivered through SMTP. Functionally, the system comprises eight modules that align with institutional roles at Sultan Kudarat State University, supporting campus-scoped scholarship operations from account provisioning through chairman approval of utilization reports.

---

*End of architecture and functional design document.*
