FROM php:8.1-fpm

# Instalar dependencias del sistema
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libpq-dev \  
    zip \
    unzip \
    git \
    curl

# Instalar extensiones PHP incluyendo pdo_pgsql
RUN docker-php-ext-install \
    pdo_mysql \
    pdo_pgsql \ 
    mbstring \
    exif \
    pcntl \
    bcmath \
    gd \
    sockets 


# Instalar extensiones PHP
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Instalar la extensión MongoDB
RUN pecl install mongodb && docker-php-ext-enable mongodb

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Configurar directorio de trabajo
WORKDIR /var/www

# Permitir ejecutar Composer como root
ENV COMPOSER_ALLOW_SUPERUSER=1

# Copiar los archivos del proyecto
COPY . .

EXPOSE 9000

CMD ["php-fpm"]