FROM php:8.2-fpm

# 1. Instalar dependencias básicas
RUN apt-get update && apt-get install -y \
    git unzip curl libzip-dev libonig-dev libxml2-dev \
    libpng-dev libjpeg-dev libfreetype6-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo_mysql zip mbstring xml gd \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# 2. Node.js
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs \
    && npm install -g npm@11.6.2

# 3. Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

# 4. Dependencias
COPY composer.json composer.lock package.json package-lock.json ./
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-scripts
RUN npm install

# 5. Copiar código
COPY . .

# 6. Build
RUN npm run build
RUN composer dump-autoload --optimize

# 7. Permisos (Vitales)
RUN chown -R www-data:www-data /var/www \
    && chmod -R 775 /var/www/storage /var/www/bootstrap/cache

# 8. Exponer puerto 80
EXPOSE 80

# --- CAMBIO RADICAL ---
# En lugar de Nginx + Supervisor, usamos el servidor de Laravel directo.
# Esto imprime logs directamente a la pantalla y elimina configuraciones complejas.
CMD php artisan config:cache && \
    php artisan route:cache && \
    php artisan serve --host=0.0.0.0 --port=80
