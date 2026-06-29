# Stage 1: Node - build frontend assets
FROM node:22-alpine AS frontend

WORKDIR /app

COPY monev-app/package*.json ./
RUN npm ci

COPY monev-app/ .
RUN npm run build

# Stage 2: PHP - production image
FROM php:8.4-cli-alpine AS app

# Install system deps + PHP extensions
RUN apk add --no-cache \
        curl \
        unzip \
        libpng-dev \
        libjpeg-turbo-dev \
        freetype-dev \
        oniguruma-dev \
        libxml2-dev \
        sqlite \
        sqlite-dev \
    && docker-php-ext-install \
        pdo \
        pdo_sqlite \
        mbstring \
        xml \
        bcmath \
        fileinfo

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Copy app source
COPY monev-app/ .

# Install PHP dependencies (no dev)
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Copy built frontend assets from stage 1
COPY --from=frontend /app/public/build ./public/build

# Setup storage permissions
RUN mkdir -p storage/framework/{sessions,views,cache} \
    && mkdir -p storage/logs \
    && mkdir -p bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

# Copy entrypoint
COPY docker-entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

EXPOSE 8000

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
