FROM php:8.5-apache

# Installer SQLite + PDO
RUN apt-get update \
	&& apt-get install -y --no-install-recommends libsqlite3-dev \
	&& docker-php-ext-install pdo pdo_sqlite \
	&& a2enmod rewrite \
	&& rm -rf /var/lib/apt/lists/*

WORKDIR /var/www/html

COPY . /var/www/html

RUN mkdir -p /var/www/html/db \
    && chown -R www-data:www-data /var/www/html/db \
    && chmod -R 775 /var/www/html/db


EXPOSE 80
