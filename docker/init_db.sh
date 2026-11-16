#!/bin/bash
# Wait for MariaDB to become ready
echo "Waiting for MariaDB to start..."
for i in {1..60}; do
    if mysqladmin ping --silent > /dev/null 2>&1; then
        echo "MariaDB is ready."
        break
    fi
    sleep 1
done

# Setup database and user
mysql -e "CREATE DATABASE IF NOT EXISTS upi_gateway CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;" 2>/dev/null || true
mysql -e "GRANT ALL PRIVILEGES ON *.* TO 'root'@'localhost' IDENTIFIED BY '' WITH GRANT OPTION; FLUSH PRIVILEGES;" 2>/dev/null || true

# Check if tables exist, if not import database.sql
TABLE_COUNT=$(mysql -N -s -e "SELECT COUNT(*) FROM information_schema.tables WHERE table_schema='upi_gateway';" 2>/dev/null || echo 0)
if [ "$TABLE_COUNT" -eq 0 ]; then
    echo "Importing database schema..."
    if [ -f "/var/www/html/database.sql" ]; then
        mysql upi_gateway < /var/www/html/database.sql
        echo "Database schema imported successfully."
    fi
fi
