#!/bin/bash
# Use Railway's PORT env var, default to 80
PORT=${PORT:-80}

# Update Apache to listen on the correct port
sed -i "s/Listen 80/Listen $PORT/g" /etc/apache2/ports.conf
sed -i "s/<VirtualHost \*:80>/<VirtualHost *:$PORT>/g" /etc/apache2/sites-enabled/000-default.conf

echo "Starting Apache on port $PORT"
exec apache2-foreground
