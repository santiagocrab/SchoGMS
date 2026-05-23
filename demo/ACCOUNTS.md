# Demo accounts — quick reference

**Login URL:** http://localhost/SchoGMS/index.php  
**Admin URL:** http://localhost/SchoGMS/admin-12-02/

If login fails, run once:

```bash
/Applications/XAMPP/xamppfiles/bin/php tools/fix_demo_logins.php
```

Or open: `http://localhost/SchoGMS/tools/fix_demo_logins.php?key=schogms_demo`

| Role | Username | Password |
|------|----------|----------|
| Coordinator (ACCESS) | `access` | `password123` |
| Registrar (ACCESS) | `registrar access` | `password123` |
| Chairman | `chairman` | `password123` |
| Director (Isulan) | `Campus Director Isulan` | `password123` |
| Dean (demo) | `DemoDean` | `schogms123` |
| Program chair | `ProgramChairDemo` | `schogms123` |
| Admin | `admin` | `admin123` |

**Notes**

- Dean and program chair use **`schogms123`**, not `password123`.
- Dean account **`DemoDean`** is created/reset by the fix script (campus ISULAN).
- Chairman also works as MySQL user `chairman` (not only MongoDB).

Upload walkthrough: [`DEMO_GUIDE.md`](DEMO_GUIDE.md) · [`docs/SchoGMS_Upload_Workflow.md`](../docs/SchoGMS_Upload_Workflow.md)
