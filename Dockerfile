# Use an official PHP image with Apache
FROM php:8.2-apache

# Install dependencies for Laravel and GD extension
RUN apt-get update && apt-get install -y \
    libzip-dev \
    libpng-dev \
    libjpeg-dev \
    zip \
    unzip \
    && docker-php-ext-install zip pdo_mysql gd \
    && a2enmod rewrite \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set the working directory in the container
WORKDIR /var/www/html

# Copy application files
COPY . .

# Copy example .env and rename to .env
COPY .env.example .env

# Install Laravel dependencies
RUN composer install --optimize-autoloader --no-dev

# Set proper permissions for Laravel
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 /var/www/html/storage \
    && chmod -R 775 /var/www/html/bootstrap/cache

# Update Apache to use Laravel's public directory
RUN sed -i 's|/var/www/html|/var/www/html/public|' /etc/apache2/sites-available/000-default.conf

# Generate Laravel's application key
RUN php artisan key:generate

# Expose port 80 for the web server
EXPOSE 80

# Start Apache server
CMD ["apache2-foreground"]
