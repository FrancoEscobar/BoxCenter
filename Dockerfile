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

# 4. Dependencias (Caché)
COPY composer.json composer.lock ./
COPY package.json package-lock.json ./

# 5. Instalar libs
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-scripts
RUN npm install

# 6. Copiar código
COPY . .

# 7. Build Frontend
RUN npm run build

# 8. Autoload PHP
RUN composer dump-autoload --optimize

# 9. Permisos
RUN chown -R www-data:www-data /var/www \
    && chmod -R 775 /var/www/storage /var/www/bootstrap/cache

# 10. Configuración y ARREGLO DE WINDOWS (CRLF)
COPY ./docker/nginx.conf /etc/nginx/sites-available/default
COPY ./docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf
COPY ./start.sh /usr/local/bin/start.sh

RUN sed -i 's/\r$//' /usr/local/bin/start.sh

RUN chmod +x /usr/local/bin/start.sh

EXPOSE 80

CMD ["/usr/local/bin/start.sh"]
