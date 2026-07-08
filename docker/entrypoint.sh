#!/bin/bash
set -e

# ── Crear subdirectorios de log que KLogger necesita ────────────────────────
# Se hace en runtime porque el volumen ./http se monta sobre /var/www/html
# tapando cualquier directorio pre-creado en build time.
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

# Dar permisos de ejecución a los scripts shell del cron
chmod +x /var/www/cron/*.sh 2>/dev/null || true

# ── Configurar puerto de Apache (default 80, VPS puede usar 8081) ────────────
APACHE_PORT=${APACHE_PORT:-80}
if [ "$APACHE_PORT" != "80" ]; then
    sed -i -E "s/^Listen [0-9]+$/Listen ${APACHE_PORT}/" /etc/apache2/ports.conf
    sed -i "s/<VirtualHost \*:80>/<VirtualHost *:${APACHE_PORT}>/" /etc/apache2/sites-enabled/*.conf 2>/dev/null || true
    echo "[entrypoint] Apache configured to listen on port ${APACHE_PORT}"
fi

# ── Arrancar cron en segundo plano ──────────────────────────────────────────
service cron start
echo "[entrypoint] cron started"

# ── Arrancar Apache en primer plano (proceso principal del contenedor) ───────
exec "$@"
