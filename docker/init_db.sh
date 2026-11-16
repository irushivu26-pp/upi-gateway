#!/bin/bash
echo "Waiting for MariaDB to start..."
for i in {1..60}; do
    if mysqladmin ping --silent > /dev/null 2>&1; then
        echo "MariaDB is ready."
        break
    fi
    sleep 1
done

echo "Setting up MariaDB database and user permissions..."
mysql -e "CREATE DATABASE IF NOT EXISTS upi_gateway CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
mysql -e "CREATE USER IF NOT EXISTS 'gateway_user'@'localhost' IDENTIFIED BY 'gateway_pass123';"
mysql -e "CREATE USER IF NOT EXISTS 'gateway_user'@'127.0.0.1' IDENTIFIED BY 'gateway_pass123';"
mysql -e "GRANT ALL PRIVILEGES ON upi_gateway.* TO 'gateway_user'@'localhost';"
mysql -e "GRANT ALL PRIVILEGES ON upi_gateway.* TO 'gateway_user'@'127.0.0.1';"
mysql -e "FLUSH PRIVILEGES;"

# Check if tables exist, if not import database.sql
TABLE_COUNT=$(mysql -N -s -e "SELECT COUNT(*) FROM information_schema.tables WHERE table_schema='upi_gateway';" 2>/dev/null || echo 0)
if [ "$TABLE_COUNT" -eq 0 ]; then
    echo "Importing database schema..."
    if [ -f "/var/www/html/database.sql" ]; then
        mysql upi_gateway < /var/www/html/database.sql
        echo "Database schema imported successfully."
    fi
fi
