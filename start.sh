#!/bin/sh

# Salir si hay errores
set -e

# 1. Ajustar permisos 
chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache

# 2. Caché de configuración y rutas
echo "Caching config..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 3. Enlace simbólico para imágenes (Storage)
echo "Linking storage..."
php artisan storage:link || true

# 4. Correr migraciones
echo "Running migrations..."
php artisan migrate --force

# 5. Iniciar Supervisor (que a su vez inicia Nginx y PHP)
echo "Starting Supervisor..."
/usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
