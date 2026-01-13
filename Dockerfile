# Gunakan PHP 8.2 dengan Apache
FROM php:8.2-apache

# Set working directory
WORKDIR /var/www/html

# Install dependencies sistem yang diperlukan
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    libzip-dev \
    nodejs \
    npm \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Enable Apache mod_rewrite untuk Laravel
RUN a2enmod rewrite

# Konfigurasi Apache untuk Laravel
RUN sed -i 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/000-default.conf
RUN sed -i 's/AllowOverride None/AllowOverride All/g' /etc/apache2/apache2.conf

# Copy project files
COPY . /var/www/html

# Copy .env.example ke .env
RUN cp .env.example .env

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Install Node dependencies dan build assets
RUN npm install && npm run build

# Set permissions untuk Laravel
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/storage \
    && chmod -R 755 /var/www/html/bootstrap/cache

# Buat entrypoint script untuk handle environment variables
RUN echo '#!/bin/bash\n\
    set -e\n\
    \n\
    # Generate APP_KEY jika belum ada\n\
    if [ -z "$APP_KEY" ] || [ "$APP_KEY" == "base64:XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX" ]; then\n\
    php artisan key:generate --force\n\
    else\n\
    # Set APP_KEY dari environment variable\n\
    sed -i "s|APP_KEY=.*|APP_KEY=$APP_KEY|g" .env\n\
    fi\n\
    \n\
    # Update .env dengan environment variables jika ada\n\
    [ ! -z "$APP_ENV" ] && sed -i "s|APP_ENV=.*|APP_ENV=$APP_ENV|g" .env\n\
    [ ! -z "$APP_DEBUG" ] && sed -i "s|APP_DEBUG=.*|APP_DEBUG=$APP_DEBUG|g" .env\n\
    [ ! -z "$DB_CONNECTION" ] && sed -i "s|DB_CONNECTION=.*|DB_CONNECTION=$DB_CONNECTION|g" .env\n\
    [ ! -z "$DB_HOST" ] && sed -i "s|DB_HOST=.*|DB_HOST=$DB_HOST|g" .env\n\
    [ ! -z "$DB_PORT" ] && sed -i "s|DB_PORT=.*|DB_PORT=$DB_PORT|g" .env\n\
    [ ! -z "$DB_DATABASE" ] && sed -i "s|DB_DATABASE=.*|DB_DATABASE=$DB_DATABASE|g" .env\n\
    [ ! -z "$DB_USERNAME" ] && sed -i "s|DB_USERNAME=.*|DB_USERNAME=$DB_USERNAME|g" .env\n\
    [ ! -z "$DB_PASSWORD" ] && sed -i "s|DB_PASSWORD=.*|DB_PASSWORD=$DB_PASSWORD|g" .env\n\
    \n\
    # Cache config\n\
    php artisan config:cache\n\
    \n\
    # Run Apache\n\
    exec apache2-foreground\n\
    ' > /usr/local/bin/docker-entrypoint.sh \
    && chmod +x /usr/local/bin/docker-entrypoint.sh

# Expose port 80
EXPOSE 80

# Gunakan entrypoint script
ENTRYPOINT ["/usr/local/bin/docker-entrypoint.sh"]
