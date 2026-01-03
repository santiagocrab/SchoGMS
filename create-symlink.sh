#!/bin/bash
# Script to create symlink in XAMPP htdocs

HTDOCS="/Applications/XAMPP/xamppfiles/htdocs"
PROJECT_DIR="/Users/jamesremegio/Downloads/SchoGMS"
SYMLINK_NAME="SchoGMS"

echo "Creating symlink for SchoGMS in XAMPP htdocs..."
echo "This requires administrator privileges."
echo ""

# Check if symlink already exists
if [ -L "$HTDOCS/$SYMLINK_NAME" ] || [ -d "$HTDOCS/$SYMLINK_NAME" ]; then
    echo "⚠️  SchoGMS already exists in htdocs"
    read -p "Remove existing and create new symlink? (y/n): " -n 1 -r
    echo
    if [[ $REPLY =~ ^[Yy]$ ]]; then
        sudo rm -rf "$HTDOCS/$SYMLINK_NAME"
    else
        echo "Aborted."
        exit 1
    fi
fi

# Create symlink
sudo ln -sf "$PROJECT_DIR" "$HTDOCS/$SYMLINK_NAME"

if [ $? -eq 0 ]; then
    echo "✅ Symlink created successfully!"
    echo ""
    echo "You can now access the application at:"
    echo "  Main: http://localhost/SchoGMS/"
    echo "  Admin: http://localhost/SchoGMS/admin-12-02/"
else
    echo "❌ Failed to create symlink"
    exit 1
fi


