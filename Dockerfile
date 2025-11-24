FROM php:8.2-fpm

# 1. Instalar dependencias
RUN apt-get update && apt-get install -y \
    git unzip curl libzip-dev libonig-dev libxml2-dev \
    libpng-dev libjpeg-dev libfreetype6-dev nginx supervisor procps \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo_mysql zip mbstring xml gd \
    && apt-get clean && rm -rf /var/lib/apt/lists/* \
    && mkdir -p /var/log/nginx /var/log/php

# 2. Node.js
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs \
    && npm install -g npm@11.6.2

# 3. Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

# 4. Dependencias
COPY composer.json composer.lock ./
COPY package.json package-lock.json ./

RUN composer install --no-dev --optimize-autoloader --no-interaction --no-scripts
RUN npm install

# 5. Copiar código
COPY . .

# 6. Build y Autoload
RUN npm run build
RUN composer dump-autoload --optimize

# 7. Permisos
RUN chown -R www-data:www-data /var/www \
    && chmod -R 775 /var/www/storage /var/www/bootstrap/cache

# 8. Configuración de Nginx (Copiamos la tuya, que parecía correcta para IPv4)
COPY ./docker/nginx.conf /etc/nginx/sites-available/default

# --- AQUI ESTA LA MAGIA: GENERAMOS LOS ARCHIVOS CRÍTICOS EN LINUX ---

# Generar supervisord.conf correcto al vuelo
RUN echo '[supervisord]' > /etc/supervisor/conf.d/supervisord.conf \
    && echo 'nodaemon=true' >> /etc/supervisor/conf.d/supervisord.conf \
    && echo 'logfile=/dev/stdout' >> /etc/supervisor/conf.d/supervisord.conf \
    && echo 'logfile_maxbytes=0' >> /etc/supervisor/conf.d/supervisord.conf \
    && echo 'pidfile=/var/run/supervisord.pid' >> /etc/supervisor/conf.d/supervisord.conf \
    && echo '' >> /etc/supervisor/conf.d/supervisord.conf \
    && echo '[program:php-fpm]' >> /etc/supervisor/conf.d/supervisord.conf \
    && echo 'command=php-fpm -F' >> /etc/supervisor/conf.d/supervisord.conf \
    && echo 'autostart=true' >> /etc/supervisor/conf.d/supervisord.conf \
    && echo 'autorestart=true' >> /etc/supervisor/conf.d/supervisord.conf \
    && echo 'stdout_logfile=/dev/stdout' >> /etc/supervisor/conf.d/supervisord.conf \
    && echo 'stdout_logfile_maxbytes=0' >> /etc/supervisor/conf.d/supervisord.conf \
    && echo 'stderr_logfile=/dev/stderr' >> /etc/supervisor/conf.d/supervisord.conf \
    && echo 'stderr_logfile_maxbytes=0' >> /etc/supervisor/conf.d/supervisord.conf \
    && echo '' >> /etc/supervisor/conf.d/supervisord.conf \
    && echo '[program:nginx]' >> /etc/supervisor/conf.d/supervisord.conf \
    && echo 'command=nginx -g "daemon off;"' >> /etc/supervisor/conf.d/supervisord.conf \
    && echo 'autostart=true' >> /etc/supervisor/conf.d/supervisord.conf \
    && echo 'autorestart=true' >> /etc/supervisor/conf.d/supervisord.conf \
    && echo 'stdout_logfile=/dev/stdout' >> /etc/supervisor/conf.d/supervisord.conf \
    && echo 'stdout_logfile_maxbytes=0' >> /etc/supervisor/conf.d/supervisord.conf \
    && echo 'stderr_logfile=/dev/stderr' >> /etc/supervisor/conf.d/supervisord.conf \
    && echo 'stderr_logfile_maxbytes=0' >> /etc/supervisor/conf.d/supervisord.conf

# Generar start.sh correcto al vuelo
RUN echo '#!/bin/sh' > /usr/local/bin/start.sh \
    && echo 'set -e' >> /usr/local/bin/start.sh \
    && echo 'echo "🚀 Iniciando contenedor..."' >> /usr/local/bin/start.sh \
    && echo 'chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache' >> /usr/local/bin/start.sh \
    && echo 'php artisan config:cache' >> /usr/local/bin/start.sh \
    && echo 'php artisan route:cache' >> /usr/local/bin/start.sh \
    && echo 'php artisan view:cache' >> /usr/local/bin/start.sh \
    && echo 'php artisan storage:link || true' >> /usr/local/bin/start.sh \
    && echo 'php artisan migrate --force' >> /usr/local/bin/start.sh \
    && echo 'echo "🔥 Iniciando Supervisor..."' >> /usr/local/bin/start.sh \
    && echo 'exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf' >> /usr/local/bin/start.sh \
    && chmod +x /usr/local/bin/start.sh

# --------------------------------------------------------------------

EXPOSE 80

CMD ["/usr/local/bin/start.sh"]
