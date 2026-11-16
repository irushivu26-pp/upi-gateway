#!/bin/bash
set -e

# Support Render custom PORT
if [ -n "$PORT" ]; then
    sed -i "s/80/$PORT/g" /etc/apache2/ports.conf /etc/apache2/sites-available/000-default.conf
fi

# Ensure supervisor log dir exists
mkdir -p /var/log/supervisor

# If local MariaDB
if [ -z "$DB_HOST" ] || [ "$DB_HOST" = "localhost" ] || [ "$DB_HOST" = "127.0.0.1" ]; then
    if [ ! -d "/var/lib/mysql/mysql" ]; then
        echo "Initializing MariaDB..."
        mysql_install_db --user=mysql --datadir=/var/lib/mysql > /dev/null 2>&1
    fi

    # Start MariaDB temporarily for initialization
    /usr/bin/mysqld_safe --datadir=/var/lib/mysql &
    MYSQL_PID=$!

    # Wait for MariaDB to respond
    for i in {1..30}; do
        if mysqladmin ping --silent > /dev/null 2>&1; then
            break
        fi
        sleep 1
    done

    echo "Configuring database..."
    mysql -e "CREATE DATABASE IF NOT EXISTS upi_gateway CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
    mysql -e "GRANT ALL PRIVILEGES ON *.* TO 'root'@'localhost' IDENTIFIED BY '' WITH GRANT OPTION; FLUSH PRIVILEGES;"

    TABLE_COUNT=$(mysql -N -s -e "SELECT COUNT(*) FROM information_schema.tables WHERE table_schema='upi_gateway';" 2>/dev/null || echo 0)
    if [ "$TABLE_COUNT" -eq 0 ]; then
        if [ -f "/var/www/html/database.sql" ]; then
            echo "Importing database schema..."
            mysql upi_gateway < /var/www/html/database.sql
        fi
    fi

    # Stop temporary MariaDB process so supervisor can manage it
    kill -TERM "$MYSQL_PID"
    wait "$MYSQL_PID" 2>/dev/null || true
fi

echo "Starting services via supervisor..."
exec /usr/bin/supervisord -n -c /etc/supervisor/conf.d/supervisord.conf
