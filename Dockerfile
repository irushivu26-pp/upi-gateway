FROM php:8.2-apache

# Install MariaDB, supervisor, curl, zip, and required PHP extensions
RUN apt-get update && apt-get install -y \
    mariadb-server \
    mariadb-client \
    supervisor \
    libpng-dev \
    libzip-dev \
    libonig-dev \
    libxml2-dev \
    curl \
    && docker-php-ext-install mysqli pdo pdo_mysql gd zip \
    && a2enmod rewrite \
    && rm -rf /var/lib/apt/lists/*

# Copy configuration files
COPY ./docker/apache.conf /etc/apache2/sites-available/000-default.conf
COPY ./docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf
COPY ./docker/entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

# Copy application source code
WORKDIR /var/www/html
COPY . /var/www/html/

# Copy database.sql from parent if needed
COPY database.sql* /var/www/html/

# Ensure proper permissions
RUN chown -R www-data:www-data /var/www/html && \
    chmod -R 755 /var/www/html

EXPOSE 80

ENTRYPOINT ["/entrypoint.sh"]
