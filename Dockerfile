FROM php:8.3-apache

# Installer les dépendances système nécessaires à SQLite
RUN apt-get update && apt-get install -y \
    sqlite3 \
    libsqlite3-dev \
    && docker-php-ext-install pdo pdo_sqlite \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Activer mod_rewrite
RUN a2enmod rewrite

# Copier le projet
COPY . /var/www/html/

# Permissions (IMPORTANT pour SQLite)
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 /var/www/html

# --- Force rebuild (chaque commit modifie ce hash)
ARG CACHEBUST=1

WORKDIR /var/www/html

EXPOSE 80
