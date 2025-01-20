#!/bin/sh
set -e

# Esperar a que MongoDB esté disponible
until php -r "
try {
    \$manager = new MongoDB\Driver\Manager('mongodb://mongodb:27017');
    \$manager->selectServer(new MongoDB\Driver\ReadPreference('primary'));
    echo 'MongoDB connection successful\n';
    exit(0);
} catch (Exception \$e) {
    echo 'Waiting for MongoDB...\n';
    sleep(1);
}
" > /dev/null 2>&1; do
    echo "Waiting for MongoDB..."
    sleep 1
done

# Ejecutar comandos post-instalación
php artisan package:discover
php artisan optimize

# Ejecutar el comando original
exec php-fpm