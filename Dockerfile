FROM php:8.2-apache

# Installer les extensions PHP nécessaires
RUN docker-php-ext-install pdo pdo_mysql

# Activer le module Apache rewrite
RUN a2enmod rewrite

# Copier tous les fichiers du projet dans le conteneur
COPY . /var/www/html/

# Définir les permissions
RUN chown -R www-data:www-data /var/www/html

EXPOSE 80
