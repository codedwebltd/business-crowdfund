#!/bin/bash

# Auto Backup Database + Git Push
# Usage: ./backup-and-push.sh "Your commit message"

PROJECT_PATH="/home/qiviotalk/business.qiviotalk.online"
BACKUP_DIR="$PROJECT_PATH/storage/backups"
DATE=$(date +%Y%m%d_%H%M%S)

# Get commit message from argument or use default
COMMIT_MSG="${1:-Auto backup and push - $DATE}"

echo "🔄 Starting backup and push process..."

# Create backup directory if not exists
mkdir -p "$BACKUP_DIR"

# Backup database
echo "📦 Backing up database..."
cd "$PROJECT_PATH"
php artisan db:backup --filename="backup_${DATE}.sql"

if [ $? -eq 0 ]; then
    echo "✅ Database backup created: backup_${DATE}.sql"
else
    echo "❌ Database backup failed!"
    exit 1
fi

# Git operations
echo "📤 Pushing to Git..."
git add .
git commit -m "$COMMIT_MSG"
git push origin main

if [ $? -eq 0 ]; then
    echo "✅ Git push successful!"
    echo "📝 Commit: $COMMIT_MSG"
else
    echo "❌ Git push failed!"
    exit 1
fi

echo "🎉 Done! Database backed up and code pushed."
