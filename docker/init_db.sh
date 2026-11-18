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

echo "Ensuring active gateway token and Paytm settings..."
mysql upi_gateway -e "
UPDATE users SET user_token='dcb789d237d4f44cb5e4532abaf92866', paytm_connected='Yes', upi_id='paytmqr5gfmzt@ptys', company='payprintsgateway' WHERE id=1;
INSERT INTO paytm_tokens (id, user_token, phoneNumber, MID, Upiid, status, user_id) VALUES (1, 'dcb789d237d4f44cb5e4532abaf92866', '9999999999', 'PAYTM_MID_101', 'paytmqr5gfmzt@ptys', 'Active', 1) ON DUPLICATE KEY UPDATE user_token='dcb789d237d4f44cb5e4532abaf92866', Upiid='paytmqr5gfmzt@ptys', status='Active';
" 2>/dev/null || true
