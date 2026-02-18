FROM php:8.3-apache-bookworm

# Prevent apache2 from being upgraded (which would reset MPM config to mpm_event)
RUN apt-mark hold apache2 apache2-bin apache2-data

# Install system dependencies required by PHP extensions
RUN apt-get update && apt-get install -y \
    libonig-dev \
    libzip-dev \
    && rm -rf /var/lib/apt/lists/*

# Enable required PHP extensions
RUN docker-php-ext-install pdo pdo_mysql mysqli mbstring

# Enable mod_rewrite AND clean MPM conflicts in one atomic step
RUN a2enmod rewrite && \
    rm -f /etc/apache2/mods-enabled/mpm_event.conf \
          /etc/apache2/mods-enabled/mpm_event.load \
          /etc/apache2/mods-enabled/mpm_worker.conf \
          /etc/apache2/mods-enabled/mpm_worker.load && \
    a2enmod mpm_prefork

# Allow .htaccess overrides in document root
RUN sed -i 's/AllowOverride None/AllowOverride All/g' /etc/apache2/apache2.conf

# Configure port 8080 at build time (Railway consistently uses port 8080)
RUN sed -i 's/Listen 80/Listen 8080/g' /etc/apache2/ports.conf && \
    find /etc/apache2/sites-enabled -name '*.conf' \
         -exec sed -i 's/<VirtualHost \*:80>/<VirtualHost *:8080>/g' {} +

# Verify Apache config is valid — build fails here with clear output if broken
RUN apache2ctl -t

# Copy app files
COPY . /var/www/html/

# Set upload directory permissions
RUN chown -R www-data:www-data /var/www/html/uploads \
    && chmod -R 755 /var/www/html/uploads

EXPOSE 8080
CMD ["bash", "-c", "rm -f /etc/apache2/mods-enabled/mpm_event.* /etc/apache2/mods-enabled/mpm_worker.* && exec apache2-foreground"]
