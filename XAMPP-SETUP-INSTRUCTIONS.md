# XAMPP Setup Instructions for SchoGMS

## Step 1: Start XAMPP MySQL Server

You need to start MySQL before importing the database. Choose one of these methods:

### Method 1: Using XAMPP Control Panel (Recommended)
1. Open XAMPP Control Panel
2. Click "Start" next to MySQL
3. Wait until MySQL shows as "Running" (green)

### Method 2: Using Terminal
Run this command in Terminal (you'll be prompted for your password):
```bash
sudo /Applications/XAMPP/xamppfiles/bin/mysql.server start
```

## Step 2: Import the Database

Once MySQL is running, execute the import script:

```bash
cd /Users/jamesremegio/Downloads/SchoGMS
./import-database.sh
```

Or manually import using MySQL command line:
```bash
/Applications/XAMPP/xamppfiles/bin/mysql -u root -e "CREATE DATABASE IF NOT EXISTS schogms;"
/Applications/XAMPP/xamppfiles/bin/mysql -u root schogms < schogms.sql
```

### Alternative: Using phpMyAdmin
1. Start Apache and MySQL in XAMPP Control Panel
2. Open http://localhost/phpmyadmin in your browser
3. Click "New" to create a database
4. Name it: `schogms`
5. Select the `schogms` database
6. Click "Import" tab
7. Choose file: `schogms.sql`
8. Click "Go"

## Step 3: Verify Configuration

All database connection files have been updated to use:
- Server: `localhost`
- Username: `root`
- Password: (empty)
- Database: `schogms`

## Step 4: Access the Application

1. Make sure Apache is running in XAMPP Control Panel
2. Place the project in XAMPP's htdocs directory, OR
3. Access it directly from: `/Users/jamesremegio/Downloads/SchoGMS`

If accessing from Downloads folder, you may need to configure Apache virtual host or access via:
- http://localhost/admin-12-02/ (for admin panel)
- http://localhost/ (for main application if index.php is in root)

## Troubleshooting

### MySQL won't start
- Check if port 3306 is already in use
- Check XAMPP error logs: `/Applications/XAMPP/xamppfiles/logs/`

### Connection errors
- Verify MySQL is running
- Check that database `schogms` exists
- Verify config files use correct credentials


