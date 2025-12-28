# Utiliser l'image PHP 8.2 FPM
FROM php:8.2-fpm

# Installer les dépendances système
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    libsqlite3-dev

# Nettoyer le cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Installer les extensions PHP
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd pdo_sqlite

# Installer Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Définir le répertoire de travail
WORKDIR /var/www

# Copier les fichiers du projet
COPY . .

# Installer les dépendances PHP
RUN composer install --no-interaction --optimize-autoloader

# Donner les permissions au dossier storage et bootstrap/cache
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache

# Exposer le port 9000 pour PHP-FPM
EXPOSE 9000

# Copier le script d'entrée
COPY entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]

CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]
