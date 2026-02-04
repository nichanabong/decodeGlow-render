FROM php:8.2-apache

RUN a2enmod rewrite

# Install database drivers
RUN docker-php-ext-install pdo pdo_mysql mysqli

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Copy project files
COPY . /var/www/html/

# Install Composer dependencies
RUN composer install --no-dev --optimize-autoloader

# Set Apache DocumentRoot to /public
RUN sed -i 's|/var/www/html|/var/www/html/public|g' /etc/apache2/sites-available/000-default.conf

WORKDIR /var/www/html/public

EXPOSE 80
