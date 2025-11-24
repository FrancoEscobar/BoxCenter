FROM php:8.2-cli

WORKDIR /var/www

# Creamos un archivo index.php tonto. Sin Laravel. Sin Nginx. Sin Composer.
RUN echo "<?php echo '<h1>SI VES ESTO, RAILWAY FUNCIONA</h1><p>El problema está en el código de Laravel.</p>'; ?>" > index.php

# Exponemos puerto 80
EXPOSE 80

# Arrancamos PHP puro en el puerto 80 forzado
CMD echo "--- ARRANCANDO PRUEBA DE DIAGNOSTICO ---" && \
    php -S 0.0.0.0:80
