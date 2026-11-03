#!/bin/bash
set -e

# Disable conflicting MPM modules and enable only mpm_prefork
a2dismod mpm_event 2>/dev/null || true
a2dismod mpm_worker 2>/dev/null || true
a2enmod mpm_prefork

# If PORT environment variable is passed by Railway/Cloud, configure Apache to listen on that port
if [ -n "$PORT" ]; then
    sed -i "s/Listen 80/Listen $PORT/g" /etc/apache2/ports.conf
    sed -i "s/<VirtualHost \*:80>/<VirtualHost \*:$PORT>/g" /etc/apache2/sites-available/000-default.conf
fi

exec apache2-foreground

