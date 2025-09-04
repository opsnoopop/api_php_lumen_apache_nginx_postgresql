# --- Stage 1: composer dependencies ---
FROM composer:2 AS vendor
WORKDIR /app
ARG APP_ENV=prod

# โค้ดจริงอยู่ที่ ./api (มี composer.json / composer.lock)
COPY ./api/composer.json ./api/composer.lock /app/

RUN if [ "$APP_ENV" = "prod" ]; then \
        composer install --no-dev --prefer-dist --no-progress --no-interaction; \
    else \
        composer install --prefer-dist --no-progress --no-interaction; \
    fi

# --- Stage 2: php-apache runtime ---
FROM php:8.4-apache

# ต้องมี oniguruma (libonig-dev) และเครื่องมือ build สำหรับ mbstring + intl
RUN apt-get update && apt-get install -y --no-install-recommends \
      git curl zip unzip \
      libicu-dev libzip-dev libpng-dev \
      libonig-dev \
      build-essential autoconf pkg-config \
      libpq-dev \
    && rm -rf /var/lib/apt/lists/*

# เปิด Apache modules ที่จำเป็น
RUN a2enmod rewrite headers expires

# ติดตั้ง PHP extensions
RUN docker-php-ext-install -j"$(nproc)" intl pdo pdo_pgsql opcache

# ตั้ง DocumentRoot และ AllowOverride
ENV APACHE_DOCUMENT_ROOT=/var/www/public
RUN sed -ri "s!DocumentRoot /var/www/html!DocumentRoot ${APACHE_DOCUMENT_ROOT}!g" /etc/apache2/sites-available/000-default.conf && \
    sed -ri "s/AllowOverride None/AllowOverride All/g" /etc/apache2/apache2.conf && \
    if ! grep -q "${APACHE_DOCUMENT_ROOT}" /etc/apache2/apache2.conf; then \
        printf "\n<Directory ${APACHE_DOCUMENT_ROOT}>\n\tAllowOverride All\n</Directory>\n" >> /etc/apache2/apache2.conf; \
    fi

WORKDIR /var/www
COPY ./php/php.ini /usr/local/etc/php/php.ini

# คัดลอกโค้ดและ vendor
COPY ./api/ /var/www/
COPY --from=vendor /app/vendor /var/www/vendor

# สิทธิ์โฟลเดอร์ที่ต้องเขียนได้
RUN mkdir -p /var/www/storage /var/www/bootstrap/cache && \
    chown -R www-data:www-data /var/www && \
    find /var/www/storage /var/www/bootstrap/cache -type d -exec chmod 775 {} \; || true

EXPOSE 80
CMD ["apache2-foreground"]