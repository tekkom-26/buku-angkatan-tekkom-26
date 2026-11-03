# Stage 1: Build frontend assets
FROM node:22-bookworm-slim AS frontend
WORKDIR /app
COPY package.json package-lock.json ./
RUN npm ci
COPY . .
RUN npm run build

# Stage 2: Composer dependencies
FROM dunglas/frankenphp:1-php8.4-bookworm AS dependencies
RUN install-php-extensions pdo_pgsql pgsql intl zip bcmath opcache pcntl
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install \
    --no-dev \
    --no-interaction \
    --no-progress \
    --no-scripts \
    --optimize-autoloader \
    --prefer-dist

# Stage 3: Runtime image
FROM dunglas/frankenphp:1-php8.4-bookworm AS runtime
RUN install-php-extensions pdo_pgsql pgsql intl zip bcmath opcache pcntl
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app

# Enable production PHP configuration
RUN cp "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"

# Copy dependencies and application source
COPY --from=dependencies /app/vendor /app/vendor
COPY . /app
COPY --from=frontend /app/public/build /app/public/build

# Publish Filament assets and finalize classmap
RUN composer dump-autoload --no-dev --classmap-authoritative \
    && php artisan filament:assets

# Ensure writable storage and cache directories
RUN mkdir -p \
    storage/framework/cache/data \
    storage/framework/sessions \
    storage/framework/views \
    storage/app/private \
    storage/app/public \
    storage/logs \
    bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

# Copy FrankenPHP/Caddy configuration
COPY Caddyfile /etc/caddy/Caddyfile

ENV PORT=80
ENV SERVER_NAME=:80

EXPOSE 80

CMD ["frankenphp", "run", "--config", "/etc/caddy/Caddyfile"]
