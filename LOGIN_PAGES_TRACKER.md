# Login Pages Tracker - SchoGMS

This document tracks all login pages and authentication entry points in the SchoGMS system.

> 🚀 **Quick Access:** Open [`login-dashboard.html`](login-dashboard.html) in your browser for a visual dashboard with clickable links to all login pages!

## Main Login Pages

### 1. Root Level Login Pages

| File Path | Purpose | Form Action | Database | User Type |
|-----------|---------|-------------|----------|-----------|
| `login.php` | Main user login (MongoDB) | `login.php` | MongoDB | All users (coordinator, dean, chairman, etc.) |
| `index.php` | Main login page (HTML form) | `login.php` | MongoDB | All users |
| `login-chair.php` | Program Chair login page | `login_chair.php` | MongoDB | Program Chair |
| `login_chair.php` | Program Chair login handler | - | MongoDB | Program Chair |
| `login-dean.php` | Dean login page | `login_dean.php` | MongoDB | Dean |
| `login_dean.php` | Dean login handler | - | MongoDB | Dean |
| `login-chairman-confirm.php` | Chairman confirmation page | - | - | Chairman (verification) |
| `login-dean-confirm.php` | Dean confirmation page | - | - | Dean (verification) |
| `new_login.php` | Alternative/new login implementation | - | MongoDB | All users |

## Admin Login Pages

### 2. Main Admin Directory (`admin/`)

| File Path | Purpose | Form Action | Database | User Type |
|-----------|---------|-------------|----------|-----------|
| `admin/login.php` | Admin login handler | - | MySQL | Admin users |
| `admin/index.html` | Admin login form | `login.php` | MySQL | Admin users |
| `admin/test_login.php` | Admin login testing | - | MySQL | Admin (testing) |
| `admin/debug_login.php` | Admin login debugging | - | MySQL | Admin (debugging) |

### 3. Admin Version Directories

All admin version directories have similar structure with `index.html` (login form) and `login.php` (handler):

| Directory | Login Form | Login Handler | Database |
|-----------|------------|---------------|----------|
| `admin-12-02/` | `index.html` | `login.php` | MySQL |
| `admin-14-2/` | `index.html` | `login.php` | MySQL |
| `admin-2-13/` | `index.html` | `login.php` | MySQL |
| `admin-31-01/` | `index.html` | `login.php` | MySQL |

**Note:** Each admin version directory contains:
- `index.html` - Login form (submits to `login.php`)
- `login.php` - Login handler (MySQL database)

## User Role-Specific Login Pages

### 4. Users Directory Login Pages

| File Path | Purpose | Notes |
|-----------|---------|-------|
| `users/login-chair.php` | Redirect to main chair login | Redirects to `../login-chair.php` |

### 5. User Role Dashboards (Post-Login)

These pages handle authentication checks but are not login pages themselves:

- `users/chairman/index.php` - Chairman dashboard
- `users/coordinator/index.php` - Coordinator dashboard
- `users/dean/index.php` - Dean dashboard
- `users/director/index.php` - Director dashboard
- `users/program-chair/index.php` - Program Chair dashboard
- `users/registrar/` - Registrar area (multiple files)

## Testing & Debug Login Pages

### 6. Test Login Files

| File Path | Purpose |
|-----------|---------|
| `test_login_simple.php` | Simple login test |
| `test_login_direct.php` | Direct login test |
| `test_mongodb_login.php` | MongoDB login testing |
| `create_test_login.php` | Create test login credentials |
| `TEST_LOGIN_WORKING.php` | Working login test verification |

### 7. Debug Login Files

| File Path | Purpose |
|-----------|---------|
| `debug_login_mongodb.php` | Debug MongoDB login issues |
| `admin/debug_login.php` | Debug admin login issues |

## Utility & Fix Login Files

### 8. Login Fix/Utility Scripts

| File Path | Purpose |
|-----------|---------|
| `fix_all_logins.php` | Fix login issues across system |
| `force_login_fix.php` | Force fix login problems |
| `LOGIN-GUIDE.php` | Login troubleshooting guide |
| `FINAL_FIX_PASSWORDS.php` | Fix password issues |
| `REAL_TIME_FIX.php` | Real-time login fixes |
| `DIRECT_JSON_FIX.php` | Fix JSON-related login issues |
| `FIX_NOW.php` | Quick login fix utility |

## Database Connections

### Login Pages Using MongoDB:
- `login.php` (root)
- `login_chair.php`
- `login_dean.php`
- `new_login.php`
- All test/debug MongoDB files

### Login Pages Using MySQL:
- `admin/login.php`
- `admin-12-02/login.php`
- `admin-14-2/login.php`
- `admin-2-13/login.php`
- `admin-31-01/login.php`

## Login Flow Summary

### Main User Login Flow:
1. User visits `index.php` (root)
2. Form submits to `login.php`
3. `login.php` authenticates against MongoDB
4. Redirects based on user role

### Admin Login Flow:
1. Admin visits `admin/index.html` (or version-specific)
2. Form submits to `admin/login.php`
3. `login.php` authenticates against MySQL `admin` table
4. Redirects to `dashboard.php`

### Role-Specific Login Flows:
- **Chair**: `login-chair.php` → `login_chair.php` → MongoDB
- **Dean**: `login-dean.php` → `login_dean.php` → MongoDB
- **Confirmation Pages**: `login-chairman-confirm.php`, `login-dean-confirm.php`

## Key Files to Monitor

### Primary Entry Points:
1. `index.php` - Main user login
2. `admin/index.html` - Admin login
3. `login-chair.php` - Chair login
4. `login-dean.php` - Dean login

### Authentication Handlers:
1. `login.php` - Main user authentication (MongoDB)
2. `admin/login.php` - Admin authentication (MySQL)
3. `login_chair.php` - Chair authentication
4. `login_dean.php` - Dean authentication

## Notes

- **MongoDB** is used for regular users (coordinators, deans, chairs, etc.)
- **MySQL** is used for admin users
- Multiple admin version directories exist (likely for different deployment versions)
- Test and debug files should not be used in production
- Confirmation pages are for email verification flows

## Quick Reference

**Main Login URLs:**
- Main: `/index.php` or `/login.php`
- Admin: `/admin/index.html` or `/admin/login.php`
- Chair: `/login-chair.php`
- Dean: `/login-dean.php`

**Database Config Files:**
- MongoDB: `conn_mongodb.php`
- MySQL (Admin): `admin/config/conn.php`

---

*Last Updated: After pulling from GitHub repository*
*Total Login-Related Files: 25+*

