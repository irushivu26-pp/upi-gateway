#!/bin/bash
set -e

# Support Render dynamic PORT (Render binds to $PORT e.g. 10000 or 80)
HTTP_PORT=${PORT:-80}
echo "Configuring Apache to listen on 0.0.0.0:$HTTP_PORT..."
sed -i "s/Listen 80/Listen $HTTP_PORT/g" /etc/apache2/ports.conf
sed -i "s/<VirtualHost \*:80>/<VirtualHost \*:$HTTP_PORT>/g" /etc/apache2/sites-available/000-default.conf

# Ensure required directories exist
mkdir -p /var/log/supervisor /var/run/mysqld
chown -R mysql:mysql /var/run/mysqld /var/lib/mysql

# Initialize MariaDB directory if not initialized
if [ ! -d "/var/lib/mysql/mysql" ]; then
    echo "Initializing MariaDB system tables..."
    mysql_install_db --user=mysql --datadir=/var/lib/mysql > /dev/null 2>&1
fi

# Launch DB initializer in background so Apache starts immediately
/usr/local/bin/init_db.sh &

echo "Starting services via supervisord..."
exec /usr/bin/supervisord -n -c /etc/supervisor/conf.d/supervisord.conf
