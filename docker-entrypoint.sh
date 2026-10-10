#!/bin/bash
set -e

# Default to 80 if PORT is not set by Railway
PORT="${PORT:-80}"
echo "Configuring Apache to listen on port $PORT"

sed -i "s/Listen 80/Listen $PORT/g" /etc/apache2/ports.conf
sed -i "s/:80/:$PORT/g" /etc/apache2/sites-available/000-default.conf

# Run Apache in foreground
exec apache2-foreground
