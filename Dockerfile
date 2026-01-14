FROM php:8.5-apache

# Installer sqlite dev lib puis activer pdo_sqlite
RUN apt-get update \
	&& apt-get install -y --no-install-recommends libsqlite3-dev \
	&& docker-php-ext-install pdo pdo_sqlite \
	&& a2enmod rewrite \
	&& rm -rf /var/lib/apt/lists/*

WORKDIR /var/www/html

COPY . /var/www/html

EXPOSE 80

# Apache est lancé automatiquement dans l'image officielle
