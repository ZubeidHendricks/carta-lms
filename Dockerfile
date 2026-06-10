# syntax=docker/dockerfile:1
###############################################################################
# Mentor LMS — production image
#
# Runtime:  FrankenPHP + Laravel Octane (persistent workers — the framework is
#           booted once per worker instead of on every request).
# Frontend: Vite assets are compiled in an isolated Node stage and copied in.
# Speed:    OPcache + JIT, optimized autoloader, and config/route/view/event
#           caches are built at container start (once env is present).
###############################################################################

# ----------------------------------------------------------------------------
# Stage 1 — Build the React/Inertia assets with Vite
# ----------------------------------------------------------------------------
FROM node:22-alpine AS assets

WORKDIR /app

# Install JS deps first so this layer is cached unless the lockfile changes.
COPY package.json package-lock.json ./
RUN npm ci --no-audit --no-fund

# Build the production bundle (code-split, hashed, minified).
COPY . .
RUN npm run build


# ----------------------------------------------------------------------------
# Stage 2 — PHP runtime (FrankenPHP) + Composer dependencies
# ----------------------------------------------------------------------------
FROM dunglas/frankenphp:1-php8.3 AS app

# install-php-extensions ships with the FrankenPHP image.
RUN install-php-extensions \
        pdo_mysql \
        gd \
        intl \
        bcmath \
        exif \
        opcache \
        pcntl \
        zip \
        redis

# Bring in Composer from the official image.
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

ENV COMPOSER_ALLOW_SUPERUSER=1 \
    COMPOSER_NO_INTERACTION=1 \
    COMPOSER_MEMORY_LIMIT=-1

WORKDIR /app

# Performance-tuned PHP/OPcache config.
COPY docker/php/opcache.ini /usr/local/etc/php/conf.d/zz-opcache.ini
COPY docker/php/app.ini      /usr/local/etc/php/conf.d/zz-app.ini

# --- Composer install -------------------------------------------------------
# Copy the manifests first for layer caching, then add Laravel Octane on top of
# the committed lock (only octane + its deps are resolved, everything else stays
# pinned). Scripts are deferred until the full source is present.
COPY composer.json composer.lock ./
RUN composer update laravel/octane --with-dependencies \
        --no-dev --prefer-dist --no-scripts --optimize-autoloader \
    || composer update --no-dev --prefer-dist --no-scripts --optimize-autoloader

# --- Application source ------------------------------------------------------
COPY . .

# Provide a baseline .env (real App Platform env vars override these at runtime)
# and ensure an APP_KEY exists so build-time artisan calls succeed.
RUN { [ -f .env ] || cp docker.env .env; } \
    && { grep -q "APP_KEY=base64:" .env || php artisan key:generate --force; }

# Finish Composer setup now that the whole app (incl. Modules) is present.
RUN composer dump-autoload --no-dev --optimize \
    && php artisan package:discover --ansi

# Pull in the compiled frontend bundle from the Node stage.
COPY --from=assets /app/public/build ./public/build

# Storage layout + permissions for the FrankenPHP user.
RUN mkdir -p storage/framework/cache storage/framework/sessions \
        storage/framework/views storage/logs bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R ug+rw storage bootstrap/cache

COPY docker/entrypoint.sh /usr/local/bin/entrypoint
RUN chmod +x /usr/local/bin/entrypoint

# App Platform routes traffic to this port.
ENV PORT=8080 \
    OCTANE_SERVER=frankenphp \
    OCTANE_WORKERS=auto \
    OCTANE_MAX_REQUESTS=512
EXPOSE 8080

HEALTHCHECK --interval=30s --timeout=5s --start-period=40s --retries=5 \
    CMD curl -fsS "http://127.0.0.1:${PORT}/up" || exit 1

ENTRYPOINT ["entrypoint"]
