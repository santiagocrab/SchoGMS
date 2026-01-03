#!/bin/bash
# Script to import schogms.sql into XAMPP MySQL

MYSQL_BIN="/Applications/XAMPP/xamppfiles/bin/mysql"
SQL_FILE="/Users/jamesremegio/Downloads/SchoGMS/schogms.sql"

echo "Importing schogms.sql into XAMPP MySQL..."
echo "Database: schogms"
echo "User: root"
echo ""

# Check if MySQL is running
if ! $MYSQL_BIN -u root -e "SELECT 1;" > /dev/null 2>&1; then
    echo "ERROR: MySQL server is not running!"
    echo "Please start MySQL from XAMPP Control Panel or run:"
    echo "sudo /Applications/XAMPP/xamppfiles/bin/mysql.server start"
    exit 1
fi

# Create database if it doesn't exist
echo "Creating database 'schogms' if it doesn't exist..."
$MYSQL_BIN -u root -e "CREATE DATABASE IF NOT EXISTS schogms;" || {
    echo "ERROR: Failed to create database"
    exit 1
}

# Import the SQL file
echo "Importing SQL file..."
$MYSQL_BIN -u root schogms < "$SQL_FILE" || {
    echo "ERROR: Failed to import SQL file"
    exit 1
}

echo ""
echo "SUCCESS: Database 'schogms' has been imported successfully!"
echo "You can now access it via phpMyAdmin at http://localhost/phpmyadmin"


