#!/bin/bash
set -e

DATE=$(date +%Y%m%d_%H%M%S)
BACKUP_DIR="/var/backups/lanzataxi"
APP_DIR="/var/www/html/lanzataxi.lan"

if [ -f "$APP_DIR/.env" ]; then
  set -a
  . "$APP_DIR/.env"
  set +a
fi

DB_NAME="${DB_DATABASE:-lanzataxi_db}"
DB_USER="${DB_USERNAME:-claudia_autoelectro}"
DB_PASS="${DB_PASSWORD:-}"

mkdir -p "$BACKUP_DIR"

if [ -n "$DB_PASS" ]; then
  mysqldump -u "$DB_USER" -p"$DB_PASS" "$DB_NAME" > "$BACKUP_DIR/db_$DATE.sql"
else
  mysqldump -u "$DB_USER" "$DB_NAME" > "$BACKUP_DIR/db_$DATE.sql"
fi

tar -czf "$BACKUP_DIR/files_$DATE.tar.gz" "$APP_DIR/storage" "$APP_DIR/.env"

find "$BACKUP_DIR" -type f -mtime +7 -delete
