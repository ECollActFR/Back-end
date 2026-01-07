#!/bin/bash
# Script to fix Docker permissions issue
# Run this script once to fix permissions for files created by Docker containers

echo "🔧 Fixing permissions for Docker files..."

# Get current user info
USER_ID=$(id -u)
GROUP_ID=$(id -g)
USER_NAME=$(id -un)
GROUP_NAME=$(id -gn)

echo "Current user: $USER_NAME (UID: $USER_ID, GID: $GROUP_ID)"

# Fix permissions on api directory
echo "Fixing permissions on api/ directory..."
sudo chown -R $USER_NAME:$GROUP_NAME /home/alstazya/ecollact/Back-end/api/

# Fix directory permissions
echo "Fixing directory permissions..."
find /home/alstazya/ecollact/Back-end/api/ -type d -exec chmod 755 {} \;

# Fix file permissions
echo "Fixing file permissions..."
find /home/alstazya/ecollact/Back-end/api/ -type f -exec chmod 644 {} \;

# Make scripts executable
echo "Making scripts executable..."
find /home/alstazya/ecollact/Back-end/api/ -name "*.sh" -exec chmod +x {} \;

echo "✅ Permissions fixed!"
echo ""
echo "Next steps:"
echo "1. docker-compose down"
echo "2. docker-compose up --build"
echo ""
echo "Your new containers will run with your user permissions."