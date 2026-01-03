#!/bin/bash
# Script to fix Apache access - either restart Apache or copy files

echo "=========================================="
echo "Fixing Apache Access for SchoGMS"
echo "=========================================="
echo ""

HTDOCS="/Applications/XAMPP/xamppfiles/htdocs"
PROJECT_DIR="/Users/jamesremegio/Downloads/SchoGMS"
SYMLINK_NAME="SchoGMS"

echo "Option 1: Restart Apache (requires password)"
echo "  Run: sudo /Applications/XAMPP/xamppfiles/bin/httpd -k restart"
echo "  OR use XAMPP Control Panel to restart Apache"
echo ""
echo "Option 2: Copy project to htdocs (no restart needed)"
read -p "Copy project to htdocs instead of symlink? (y/n): " -n 1 -r
echo

if [[ $REPLY =~ ^[Yy]$ ]]; then
    echo "Removing symlink..."
    rm -f "$HTDOCS/$SYMLINK_NAME"
    
    echo "Copying project to htdocs (this may take a moment)..."
    # Use rsync to copy efficiently, excluding large directories if needed
    rsync -av --exclude='.git' --exclude='node_modules' --exclude='mongodb_data' \
        "$PROJECT_DIR/" "$HTDOCS/$SYMLINK_NAME/"
    
    if [ $? -eq 0 ]; then
        echo "✅ Project copied successfully!"
        echo ""
        echo "You can now access:"
        echo "  Main: http://localhost/SchoGMS/"
        echo "  Admin: http://localhost/SchoGMS/admin-12-02/"
    else
        echo "❌ Copy failed"
        exit 1
    fi
else
    echo ""
    echo "Please restart Apache using one of these methods:"
    echo "1. XAMPP Control Panel: Stop and Start Apache"
    echo "2. Terminal: sudo /Applications/XAMPP/xamppfiles/bin/httpd -k restart"
    echo ""
    echo "Then try accessing: http://localhost/SchoGMS/"
fi

