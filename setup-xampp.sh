#!/bin/bash
# Master setup script for SchoGMS in XAMPP

MYSQL_BIN="/Applications/XAMPP/xamppfiles/bin/mysql"
MYSQL_SERVER="/Applications/XAMPP/xamppfiles/bin/mysql.server"
SQL_FILE="/Users/jamesremegio/Downloads/SchoGMS/schogms.sql"

echo "=========================================="
echo "SchoGMS XAMPP Setup Script"
echo "=========================================="
echo ""

# Check if MySQL is running
echo "Checking MySQL status..."
if $MYSQL_BIN -u root -e "SELECT 1;" > /dev/null 2>&1; then
    echo "✅ MySQL is already running"
else
    echo "⚠️  MySQL is not running"
    echo ""
    echo "Attempting to start MySQL..."
    echo "NOTE: You may be prompted for your password"
    
    if sudo $MYSQL_SERVER start 2>/dev/null; then
        echo "✅ MySQL started successfully"
        sleep 2  # Give MySQL time to fully start
    else
        echo ""
        echo "❌ Could not start MySQL automatically"
        echo ""
        echo "Please start MySQL manually using one of these methods:"
        echo "1. Open XAMPP Control Panel and click 'Start' next to MySQL"
        echo "2. Run: sudo $MYSQL_SERVER start"
        echo ""
        echo "Then run this script again: ./setup-xampp.sh"
        exit 1
    fi
fi

echo ""
echo "Creating database 'schogms'..."
$MYSQL_BIN -u root -e "CREATE DATABASE IF NOT EXISTS schogms;" || {
    echo "❌ Failed to create database"
    exit 1
}
echo "✅ Database created/verified"

echo ""
echo "Importing schogms.sql (this may take a minute)..."
if $MYSQL_BIN -u root schogms < "$SQL_FILE"; then
    echo "✅ Database imported successfully!"
else
    echo "❌ Failed to import database"
    exit 1
fi

echo ""
echo "=========================================="
echo "✅ Setup Complete!"
echo "=========================================="
echo ""
echo "Database: schogms"
echo "Username: root"
echo "Password: (empty)"
echo ""
echo "You can now:"
echo "1. Access phpMyAdmin at: http://localhost/phpmyadmin"
echo "2. Test connection: php test-connection.php"
echo "3. Access the application via your web browser"
echo ""


