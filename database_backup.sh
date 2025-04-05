#!/bin/bash

# Database credentials
DB_NAME="attachme"
DB_USER="root"
DB_PASS="Attachme@Admin"

# Backup directory
BACKUP_DIR="/backups/database"
DATE=$(date +"%Y%m%d_%H%M%S")

# Create backup directory if it doesn't exist
mkdir -p $BACKUP_DIR

# Backup file name
BACKUP_FILE="$BACKUP_DIR/${DB_NAME}_$DATE.sql"

# Perform backup
mysqldump -u $DB_USER -p$DB_PASS $DB_NAME > $BACKUP_FILE

# Compress backup
gzip $BACKUP_FILE

# Retention policy
find $BACKUP_DIR -type f -name "*.gz" -mtime +7 -exec rm {} \;  # Delete backups older than 7 days

# Log backup operation
echo "$(date) - Backup completed: ${BACKUP_FILE}.gz" >> $BACKUP_DIR/backup.log
