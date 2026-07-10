#!/bin/bash
set -e

# ── Create the log subdirectories that KLogger needs ────────────────────────
# Done at runtime because the ./http volume is mounted over /var/www/html,
# hiding any directory pre-created at build time.
LOG_BASE="/var/www/html/log"
for subdir in perf login dc dc-games cron report bounces insults; do
    dir="$LOG_BASE/$subdir"
    if [ ! -d "$dir" ]; then
        mkdir -p "$dir"
        echo "[entrypoint] Created $dir"
    fi
    chown -R www-data:www-data "$dir"
    chmod -R 775 "$dir"
done
chown www-data:www-data "$LOG_BASE"
chmod 775 "$LOG_BASE"

# Give execute permissions to the cron shell scripts
chmod +x /var/www/cron/*.sh 2>/dev/null || true

# ── Configure Apache port (default 80, the VPS may use 8081) ─────────────────
APACHE_PORT=${APACHE_PORT:-80}
if [ "$APACHE_PORT" != "80" ]; then
    sed -i -E "s/^Listen [0-9]+$/Listen ${APACHE_PORT}/" /etc/apache2/ports.conf
    sed -i "s/<VirtualHost \*:80>/<VirtualHost *:${APACHE_PORT}>/" /etc/apache2/sites-enabled/*.conf 2>/dev/null || true
    echo "[entrypoint] Apache configured to listen on port ${APACHE_PORT}"
fi

# ── Start cron in the background ─────────────────────────────────────────────
service cron start
echo "[entrypoint] cron started"

# ── Start Apache in the foreground (main process of the container) ───────────
exec "$@"
