#!/bin/sh

set -e

echo "🔴 --- INICIO DE DEBUG ---"

# 1. Verificar que los archivos de config existen
echo "🔍 Verificando archivos de configuración..."
ls -la /etc/supervisor/conf.d/supervisord.conf
ls -la /etc/nginx/sites-available/default

# 2. Probar la configuración de Nginx (sin arrancarlo)
echo "🧪 Probando configuración de Nginx..."
nginx -t

# 3. Ajustar permisos
echo "🔧 Ajustando permisos..."
chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache
chmod -R 775 /var/www/storage /var/www/bootstrap/cache

# 4. Tareas de Laravel
echo "🧹 Cacheando configuración..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "🔗 Linkeando storage..."
php artisan storage:link || true

echo "🗄️ Ejecutando migraciones..."
php artisan migrate --force

echo "🟢 --- FIN DE DEBUG (Iniciando Supervisor) ---"

# 5. Arrancar Supervisor (SIN 'exec' temporalmente para ver si escupe error al salir)
/usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
