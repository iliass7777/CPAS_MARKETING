FROM php:8.5-apache


# FOR SQLITE EXTENTIONS
RUN apt-get update && apt-get install -y \
    sqlite3 \
    libsqlite3-dev \
    && docker-php-ext-install pdo pdo_sqlite \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*
    

# Enable Apache
RUN a2enmod rewrite
WORKDIR /var/www/html

COPY . /var/www/html




