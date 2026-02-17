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
# Ensure only mpm_prefork is loaded — rm directly since a2dismod can silently fail
RUN rm -f /etc/apache2/mods-enabled/mpm_event.conf \
          /etc/apache2/mods-enabled/mpm_event.load \
          /etc/apache2/mods-enabled/mpm_worker.conf \
          /etc/apache2/mods-enabled/mpm_worker.load && \
    a2enmod mpm_prefork

# Allow .htaccess overrides in document root
RUN sed -i 's/AllowOverride None/AllowOverride All/g' /etc/apache2/apache2.conf

# Copy app files
COPY . /var/www/html/

# Set upload directory permissions
RUN chown -R www-data:www-data /var/www/html/uploads \
    && chmod -R 755 /var/www/html/uploads

EXPOSE 8080
# Configure Apache port from Railway's PORT env var at container startup (inline to avoid CRLF issues)
CMD ["/bin/bash", "-c", "PORT=${PORT:-8080} && sed -i \"s/Listen 80/Listen $PORT/g\" /etc/apache2/ports.conf && sed -i \"s/<VirtualHost \\*:80>/<VirtualHost *:$PORT>/g\" /etc/apache2/sites-enabled/000-default.conf && exec apache2-foreground"]
