FROM php:8.2-cli

# 1. Instalar dependencias mínimas
RUN apt-get update && apt-get install -y \
    git unzip curl libzip-dev libonig-dev libxml2-dev \
    libpng-dev libjpeg-dev libfreetype6-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo_mysql zip mbstring xml gd \
    && apt-get clean

# 2. Node.js (Solo para compilar assets)
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs \
    && npm install -g npm@11.6.2

# 3. Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

# 4. Instalar librerías
COPY composer.json composer.lock package.json package-lock.json ./
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-scripts
RUN npm install

# 5. Copiar código
COPY . .

# 6. Compilar Frontend
RUN npm run build
RUN composer dump-autoload --optimize

# 7. Permisos (Esto es vital para que Laravel no falle al escribir logs)
RUN chown -R www-data:www-data /var/www \
    && chmod -R 775 /var/www/storage /var/www/bootstrap/cache

# 8. Exponer puerto 80
EXPOSE 80

# --- COMANDO DE ARRANQUE MANUAL ---
# 1. Mostramos variables y archivos para debug
# 2. Forzamos el puerto 80 en todas las interfaces (0.0.0.0)
# 3. Apuntamos directamente a la carpeta public
CMD echo "--- INICIANDO DEBUGGER ---" && \
    echo "Archivos en public:" && \
    ls -la public/ && \
    echo "Iniciando servidor PHP en 0.0.0.0:80..." && \
    php -S 0.0.0.0:80 -t public/
