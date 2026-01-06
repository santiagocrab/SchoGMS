# Login Pages Quick Reference

> 🚀 **Quick Access:** Open [`login-dashboard.html`](login-dashboard.html) in your browser for a visual dashboard with clickable links to all login pages!

## 🎯 Primary Login Entry Points

### Main User Login (MongoDB)
```
📄 index.php          → Form page
📄 login.php          → Handler (MongoDB)
```

### Admin Login (MySQL)
```
📄 admin/index.html    → Form page
📄 admin/login.php    → Handler (MySQL)
```

### Role-Specific Logins
```
📄 login-chair.php    → Chair login form
📄 login_chair.php    → Chair handler
📄 login-dean.php     → Dean login form  
📄 login_dean.php     → Dean handler
```

## 📁 Admin Version Directories
All have `index.html` + `login.php`:
- `admin/` (main)
- `admin-12-02/`
- `admin-14-2/`
- `admin-2-13/`
- `admin-31-01/`

## 🧪 Test/Debug Files (24 files)
- `test_login_*.php` (3 files)
- `debug_login*.php` (2 files)
- `fix_*login*.php` (2 files)
- `*_FIX*.php` (5 files)
- `create_test_login.php`
- `new_login.php`
- `TEST_LOGIN_WORKING.php`
- `LOGIN-GUIDE.php`

## 📊 Database Usage

| Database | Used By |
|----------|---------|
| **MongoDB** | Main users (coordinator, dean, chair, etc.) |
| **MySQL** | Admin users only |

## 🔗 Quick Links

**Production Logins:**
- Main: `/index.php`
- Admin: `/admin/index.html`
- Chair: `/login-chair.php`
- Dean: `/login-dean.php`

**Config Files:**
- MongoDB: `conn_mongodb.php`
- MySQL: `admin/config/conn.php`

---
*See LOGIN_PAGES_TRACKER.md for detailed information*

