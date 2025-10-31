#!/bin/bash

echo "🟢 Iniciando entorno de desarrollo BoxCenter..."

# Esperar a que MySQL esté listo
echo "⏳ Esperando a que la base de datos esté lista..."
sleep 10

# Instalar dependencias PHP si faltan
if [ ! -d "vendor" ]; then
  echo "🎼 Instalando dependencias con Composer..."
  composer install --no-interaction --prefer-dist --optimize-autoloader
fi

# Generar APP_KEY si no existe
if [ ! -f "artisan" ]; then
  echo "❌ Archivo artisan no encontrado, verifica que estés en la raíz del proyecto."
  exit 1
fi

if [ -z "$(php artisan key:generate --show)" ]; then
  echo "🔑 Generando APP_KEY..."
  php artisan key:generate
fi

# Ejecutar migraciones y seeders solo si la tabla 'users' no existe
php artisan migrate:fresh --seed --force

# Instalar dependencias Node si faltan
if [ ! -d "node_modules" ]; then
  echo "📦 Instalando dependencias de Node..."
  npm install
fi

# # Iniciar Vite en segundo plano en todas las interfaces
# echo "💡 Iniciando servidor de Vite (desarrollo)..."
# npm run dev

# Iniciar Nginx y PHP-FPM con Supervisor
echo "🚀 Iniciando Nginx y PHP-FPM..."
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
