FROM php:5.6-apache

# Debian Stretch quedó archivado (EOL); apuntar apt al archivo histórico
RUN sed -i \
        -e 's|deb.debian.org/debian|archive.debian.org/debian|g' \
        -e 's|security.debian.org/debian-security|archive.debian.org/debian-security|g' \
        -e '/stretch-updates/d' \
        /etc/apt/sources.list \
    && printf 'Acquire::Check-Valid-Until "false";\n' > /etc/apt/apt.conf.d/99no-check-valid-until

RUN apt-get update && apt-get install -y --no-install-recommends --allow-unauthenticated \
        libpng-dev \
        libjpeg62-turbo-dev \
        libzip-dev \
    && docker-php-ext-configure gd --with-png-dir=/usr --with-jpeg-dir=/usr \
    && docker-php-ext-install mysql mysqli pdo_mysql gd zip \
    && a2enmod rewrite headers \
    && rm -rf /var/lib/apt/lists/*

# Kohana usa .htaccess para el rewrite de rutas
RUN sed -ri -e 's!AllowOverride None!AllowOverride All!g' /etc/apache2/apache2.conf

WORKDIR /var/www/html
