#!/bin/bash
set -e

# Disable extra MPMs if enabled by accident
a2dismod mpm_event mpm_worker >/dev/null 2>&1 || true
a2enmod mpm_prefork >/dev/null 2>&1 || true

PORT="${PORT:-80}"
echo "Configuring Apache to listen on port $PORT"

sed -i "s/Listen 80/Listen $PORT/g" /etc/apache2/ports.conf
sed -i "s/<VirtualHost \*:80>/<VirtualHost \*:$PORT>/g" /etc/apache2/sites-available/000-default.conf

exec apache2-foreground
