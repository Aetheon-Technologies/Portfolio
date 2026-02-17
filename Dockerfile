FROM php:8.3-apache

# Enable required PHP extensions
RUN docker-php-ext-install pdo pdo_mysql mysqli mbstring fileinfo

# Enable Apache mod_rewrite (for .htaccess)
RUN a2enmod rewrite

# Copy app files
COPY . /var/www/html/

# Set correct permissions
RUN chown -R www-data:www-data /var/www/html/uploads \
    && chmod -R 755 /var/www/html/uploads

# Allow .htaccess overrides
RUN sed -i 's/AllowOverride None/AllowOverride All/g' /etc/apache2/apache2.conf

EXPOSE 80
