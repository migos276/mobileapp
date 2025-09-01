FROM php:8.2-apache

# Installer les extensions PHP nécessaires
RUN apt-get update && apt-get install -y \
    sqlite3 \
    libsqlite3-dev \
    && docker-php-ext-install pdo pdo_sqlite

# Activer mod_rewrite pour les URLs propres
RUN a2enmod rewrite

# Copier la configuration Apache
COPY docker/apache.conf /etc/apache2/sites-available/000-default.conf

# Copier le code de l'application
COPY . /var/www/html/

# Définir les permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html \
    && chmod -R 777 /var/www/html/database

EXPOSE 80

CMD ["apache2-foreground"]