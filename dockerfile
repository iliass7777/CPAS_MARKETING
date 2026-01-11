FROM php:8.5-apache

RUN apt-get update && apt-get install -y \
    sqlite3 \
    libsqlite3-dev \
    && docker-php-ext-install pdo pdo_sqlite \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Enable Apache modules
RUN a2enmod rewrite headers

# Set working directory
WORKDIR /var/www/html

# Copy application files
COPY . /var/www/html

# Create db directory and set permissions
RUN mkdir -p /var/www/html/db && \
    chown -R www-data:www-data /var/www/html && \
    chmod -R 755 /var/www/html && \
    chmod -R 775 /var/www/html/db

# Configure Apache to allow .htaccess overrides
RUN echo '<Directory /var/www/html>' > /etc/apache2/conf-available/php-app.conf && \
    echo '    Options Indexes FollowSymLinks' >> /etc/apache2/conf-available/php-app.conf && \
    echo '    AllowOverride All' >> /etc/apache2/conf-available/php-app.conf && \
    echo '    Require all granted' >> /etc/apache2/conf-available/php-app.conf && \
    echo '</Directory>' >> /etc/apache2/conf-available/php-app.conf && \
    a2enconf php-app



EXPOSE 80

