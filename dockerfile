FROM php:8.2-apache
WORKDIR /var/www/html

# Installer les dépendances système et l'extension SQLite
RUN apt-get update && apt-get install -y \
    sqlite3 \
    libsqlite3-dev \
    && docker-php-ext-install pdo pdo_sqlite \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Activer le module Apache rewrite pour les URLs propres
RUN a2enmod rewrite
# Copier les fichiers de l'application
COPY . /var/www/html/

# Créer le répertoire db avec les bonnes permissions
RUN mkdir -p /var/www/html/db && \
    chown -R www-data:www-data /var/www/html && \
    chmod -R 755 /var/www/html && \
    chmod -R 777 /var/www/html/db

# Exposer le port 80
EXPOSE 80

# Démarrer Apache en mode foreground
CMD ["apache2-foreground"]

