#!/bin/bash
# Quick Git Push with Auto Backup
# Usage:
#   ./push.sh "commit message"
#   ./push.sh   (uses auto message)

cd /home/qiviotalk/business.qiviotalk.online

# Get commit message or use default
MSG="${1:-Update: $(date +%Y-%m-%d\ %H:%M:%S)}"

echo "📦 Backing up database..."
php artisan db:backup --filename="backup_$(date +%Y%m%d_%H%M%S).sql"

echo "📤 Git push: $MSG"
git add .
git commit -m "$MSG"
git push origin main

echo "✅ Done!"
