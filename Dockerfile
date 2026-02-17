FROM php:8.3-apache

# Install system dependencies required by PHP extensions
RUN apt-get update && apt-get install -y \
    libonig-dev \
    libzip-dev \
    && rm -rf /var/lib/apt/lists/*

# Enable required PHP extensions
RUN docker-php-ext-install pdo pdo_mysql mysqli mbstring

# Enable Apache mod_rewrite (for .htaccess)
RUN a2enmod rewrite

# Allow .htaccess overrides in document root
RUN sed -i 's/AllowOverride None/AllowOverride All/g' /etc/apache2/apache2.conf

# Copy app files
COPY . /var/www/html/

# Set upload directory permissions
RUN chown -R www-data:www-data /var/www/html/uploads \
    && chmod -R 755 /var/www/html/uploads

EXPOSE 80
