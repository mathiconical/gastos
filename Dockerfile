# Use multi-stage build to reduce final image size
FROM php:8.4.4-fpm-alpine3.21 as builder

# Install build dependencies
RUN apk add --no-cache \
  build-base \
  libpng-dev \
  libjpeg-turbo-dev \
  libwebp-dev \
  libxpm-dev \
  freetype-dev \
  libzip-dev \
  libmcrypt-dev \
  oniguruma-dev \
  icu-dev \
  mariadb-dev

# Configure and install PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp && \
  docker-php-ext-configure intl && \
  docker-php-ext-install -j$(nproc) gd pdo pdo_mysql mysqli mbstring zip exif pcntl bcmath opcache intl

# Cleanup build dependencies
RUN apk del --no-cache \
  build-base \
  libpng-dev \
  libjpeg-turbo-dev \
  libwebp-dev \
  libxpm-dev \
  freetype-dev \
  libzip-dev \
  libmcrypt-dev \
  mariadb-dev \
  icu-dev \
  oniguruma-dev && \
  rm -rf /var/cache/apk/*

# Final stage
FROM php:8.4.4-fpm-alpine3.21

# Copy only necessary files from builder
COPY --from=builder /usr/local/etc/php/conf.d/ /usr/local/etc/php/conf.d/
COPY --from=builder /usr/local/lib/php/extensions/ /usr/local/lib/php/extensions/

# Install runtime dependencies
RUN apk add --no-cache \
  libpng \
  libjpeg-turbo \
  libwebp \
  libxpm \
  freetype \
  libzip \
  icu \
  mysql-client \
  oniguruma && \
  rm -rf /var/cache/apk/*

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Create working directory and set permissions
RUN mkdir -p /usr/app && \
  chown www-data:www-data /usr/app

WORKDIR /usr/app

# Copy application files
COPY --chown=www-data:www-data ./app/ /usr/app/

EXPOSE 9090

CMD ["php-fpm"]
