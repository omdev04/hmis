# ====================================================
# InfyHMS v14.0 — Render.com Optimized Dockerfile
# PHP 8.1 + Apache + PlanetScale MySQL
# ====================================================

FROM php:8.1-apache

LABEL maintainer="InfyHMS"
LABEL version="14.0"
LABEL description="InfyHMS HMS — Render.com Deployment"

# ── System Dependencies ─────────────────────────────
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libwebp-dev \
    zip \
    unzip \
    supervisor \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# ── Node.js 18 (Laravel Mix / asset build) ─────────
RUN curl -fsSL https://deb.nodesource.com/setup_18.x | bash - \
    && apt-get install -y nodejs \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# ── PHP Extensions ──────────────────────────────────
RUN docker-php-ext-configure gd \
        --with-freetype \
        --with-jpeg \
        --with-webp \
    && docker-php-ext-install \
        pdo \
        pdo_mysql \
        mbstring \
        exif \
        pcntl \
        bcmath \
        gd \
        zip \
        opcache \
        intl

# ── Composer ────────────────────────────────────────
COPY --from=composer:2.6 /usr/bin/composer /usr/bin/composer

# ── Apache Setup ────────────────────────────────────
RUN a2enmod rewrite headers deflate expires

# ── PHP Configuration ───────────────────────────────
RUN cp "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"
RUN echo "upload_max_filesize = 100M"  >> "$PHP_INI_DIR/php.ini" && \
    echo "post_max_size = 100M"        >> "$PHP_INI_DIR/php.ini" && \
    echo "memory_limit = 512M"         >> "$PHP_INI_DIR/php.ini" && \
    echo "max_execution_time = 300"    >> "$PHP_INI_DIR/php.ini" && \
    echo "max_input_time = 300"        >> "$PHP_INI_DIR/php.ini" && \
    echo "max_input_vars = 5000"       >> "$PHP_INI_DIR/php.ini"

# OPcache (performance)
RUN echo "opcache.enable=1"                    >> "$PHP_INI_DIR/conf.d/opcache.ini" && \
    echo "opcache.validate_timestamps=0"       >> "$PHP_INI_DIR/conf.d/opcache.ini" && \
    echo "opcache.max_accelerated_files=10000" >> "$PHP_INI_DIR/conf.d/opcache.ini" && \
    echo "opcache.memory_consumption=256"      >> "$PHP_INI_DIR/conf.d/opcache.ini" && \
    echo "opcache.interned_strings_buffer=16"  >> "$PHP_INI_DIR/conf.d/opcache.ini" && \
    echo "opcache.fast_shutdown=1"             >> "$PHP_INI_DIR/conf.d/opcache.ini"

# ── Apache VirtualHost ──────────────────────────────
COPY docker/apache/000-default.conf /etc/apache2/sites-available/000-default.conf

# ── Supervisor Config ───────────────────────────────
COPY docker/supervisor/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# ── Working Directory ───────────────────────────────
WORKDIR /var/www/html

# ── Copy Application Code ───────────────────────────
COPY . .

# ── Permissions ─────────────────────────────────────
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/storage \
    && chmod -R 755 /var/www/html/bootstrap/cache

# ── Install PHP Dependencies ────────────────────────
RUN composer install \
    --no-dev \
    --optimize-autoloader \
    --no-interaction \
    --prefer-dist

# ── Build Frontend Assets ───────────────────────────
RUN npm ci && npm run prod && rm -rf node_modules

# ── Entrypoint ──────────────────────────────────────
COPY docker/scripts/render-start.sh /render-start.sh
RUN chmod +x /render-start.sh

# NOTE: Render sets PORT automatically — Apache listens on $PORT
EXPOSE 80

CMD ["/render-start.sh"]
