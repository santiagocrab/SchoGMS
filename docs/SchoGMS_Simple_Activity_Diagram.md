# SchoGMS — Activity Diagram (Best Overview)

**The easiest way to understand SchoGMS:** open the visual page in your browser, or use the diagram below.

| Format | File | Best for |
|--------|------|----------|
| **Visual (recommended)** | [schogms-activity-diagram.html](./schogms-activity-diagram.html) | Paper screenshots, panel presentation, printing |
| **Interactive** | `canvases/schogms-activity-workflow.canvas.tsx` | Explore phases in Cursor (open beside chat) |
| **Mermaid** | Code below | GitHub, Word via mermaid.live export |

---

## The whole story in one sentence

**Staff get accounts → verify email → upload scholar lists and COR/COG → coordinator checks matches → coordinator sends Annex 7 → chairman approves → done.**

Scholars **do not log in** — their names come from uploaded Excel lists.

---

## Diagram (4 phases — top to bottom)

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

    END([END — Scholarship cycle complete])

    START --> A1
    A7 --> B1
    B2 --> C1
    C3 --> D1
    C4 --> D1
    D6 --> END

    classDef phase fill:#f8fafc,stroke:#94a3b8,stroke-width:2px
    classDef action fill:#ffffff,stroke:#334155,stroke-width:1.5px
    classDef choice fill:#e0f2fe,stroke:#0369a1,stroke-width:2px
    class P1,P2,P3,P4 phase
    class A1,A2,A3,A6,A7,B1,B2,C1,C3,C4,D1,D2,D4,D5,D6 action
    class A4,C2,D3 choice
```

---

## Who does what (quick reference)

| Phase | Who | What |
|:-----:|-----|------|
| 1 | **Administrator** | Creates user accounts |
| 1 | **Staff** | Verifies email, logs in |
| 2 | **Chairman / Coordinator** | Uploads official scholar list |
| 2 | **Registrar** | Uploads COR & COG |
| 3 | **Coordinator** | Validates data, marks pass/fail |
| 4 | **Coordinator** | Submits Annex 7 |
| 4 | **Chairman** | Approves or rejects |
| 4 | **System** | Stores data for dashboards and exports |

---

## Figure caption (copy for your paper)

*Figure X. Activity diagram of the SchoGMS organized in four phases: staff access and verification, loading of scholar data and documents, coordinator validation, and chairman approval of the Annex 7 campus report.*

---

## Export as PNG for Word / PDF

1. Open [schogms-activity-diagram.html](./schogms-activity-diagram.html) in Chrome → **Print → Save as PDF** (best layout), or screenshot.  
2. Or paste the Mermaid block into [mermaid.live](https://mermaid.live) → **Export PNG/SVG**.
