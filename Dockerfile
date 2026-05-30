FROM php:8.2-apache

# Install system dependencies
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libzip-dev \
    zip \
    unzip \
    git \
    curl \
    gnupg \
    && rm -rf /var/lib/apt/lists/*

# Install Node.js (needed to compile assets with Vite)
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs \
    && rm -rf /var/lib/apt/lists/*

# Configure and install PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo_mysql bcmath zip gd

# Increase PHP upload and memory limits
RUN echo "upload_max_filesize = 120M" > /usr/local/etc/php/conf.d/uploads.ini \
    && echo "post_max_size = 125M" >> /usr/local/etc/php/conf.d/uploads.ini \
    && echo "memory_limit = 512M" >> /usr/local/etc/php/conf.d/uploads.ini \
    && echo "max_execution_time = 300" >> /usr/local/etc/php/conf.d/uploads.ini \
    && echo "max_input_time = 300" >> /usr/local/etc/php/conf.d/uploads.ini

# Enable Apache mod_rewrite and disable conflicting MPMs (ensure only prefork is active)
RUN a2enmod rewrite && (a2dismod mpm_event mpm_worker || true) && a2enmod mpm_prefork

# Copy composer from official image to /usr/local/bin
COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer
RUN chmod +x /usr/local/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy application source code
COPY . .

# Install PHP dependencies using composer
RUN php /usr/local/bin/composer install --no-interaction --optimize-autoloader --no-dev

# Install NPM dependencies and compile frontend assets using Vite
RUN npm ci || npm install
RUN npm run build

# Adjust Apache configuration to point to Laravel's public directory
RUN sed -ri -e 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!/var/www/html/public!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Set permissions for storage and bootstrap/cache
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Setup entrypoint script
COPY docker-entrypoint.sh /usr/local/bin/
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

# Expose port (Railway overrides this via dynamic port mapping, but it is good practice to expose a default)
EXPOSE 8080

ENTRYPOINT ["docker-entrypoint.sh"]
