#!/bin/sh
set -e

# S'assurer que le fichier .env existe
if [ ! -f .env ]; then
    echo "Creating .env from .env.example..."
    cp .env.example .env
    php artisan key:generate --force
fi

# S'assurer que la base de données SQLite existe
if [ ! -f database/database.sqlite ]; then
    echo "Creating database.sqlite..."
    touch database/database.sqlite
    chown www-data:www-data database/database.sqlite
fi

# S'assurer que les permissions sont correctes avant les migrations
chown -R www-data:www-data storage bootstrap/cache database

# Lancer les migrations
echo "Running migrations..."
php artisan migrate --force

# Lancer la commande principale
echo "Starting application..."
exec "$@"
