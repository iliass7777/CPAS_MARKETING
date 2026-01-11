FROM php:8.3-apache

# Activer SQLite
RUN docker-php-ext-install pdo pdo_sqlite

# Activer rewrite
RUN a2enmod rewrite

# Copier le projet
COPY . /var/www/html/

# Donner les droits à SQLite
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 /var/www/html

WORKDIR /var/www/html

EXPOSE 80
