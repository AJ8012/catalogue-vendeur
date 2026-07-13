FROM php:8.2-apache

# 1. Installer les dépendances système nécessaires (git, zip, etc.)
RUN apt-get update && apt-get install -y \
    git \
    zip \
    unzip

# 2. Installer Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 3. Installer les extensions PHP nécessaires
RUN docker-php-ext-install pdo pdo_mysql

# 4. Activer le module Apache rewrite
RUN a2enmod rewrite

# 5. Copier tous les fichiers du projet
COPY . /var/www/html/

# 6. Installer les dépendances PHP avec Composer
WORKDIR /var/www/html
RUN composer install --no-interaction --prefer-dist --no-dev

# 7. Permissions
RUN chown -R www-data:www-data /var/www/html

EXPOSE 80