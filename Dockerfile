# Use PHP 8.3 with Nginx
FROM php:8.3-fpm

# Install dependencies
RUN apt-get update && apt-get install -y \
    nginx \
    zip unzip \
    curl libpng-dev libonig-dev libxml2-dev \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Set working directory
WORKDIR /var/www

# Copy Laravel files
COPY . .

# Install composer dependencies
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
RUN composer install --no-dev --optimize-autoloader

# Nginx config
COPY ./nginx/nginx.conf /etc/nginx/nginx.conf

EXPOSE 9000
CMD ["php-fpm"]
