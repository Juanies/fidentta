FROM php:8.5-apache-bookworm AS php-base

RUN apt-get update && apt-get install -y --no-install-recommends \
    libcurl4-openssl-dev \
    libicu-dev \
    libzip-dev \
    && docker-php-ext-install -j"$(nproc)" pdo_mysql bcmath exif intl zip curl \
    && a2enmod rewrite headers \
    && printf '<Directory /var/www/html/public>\n    AllowOverride All\n    Require all granted\n</Directory>\n' > /etc/apache2/conf-available/laravel.conf \
    && a2enconf laravel \
    && rm -rf /var/lib/apt/lists/*

RUN php --ri mysqlnd | grep -q 'auth_plugin_caching_sha2_password'

RUN sed -ri 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/*.conf

FROM php-base AS vendor

COPY --from=composer:2 /usr/bin/composer /usr/local/bin/composer

WORKDIR /var/www/html

COPY composer.json composer.lock ./

RUN composer install \
    --no-dev \
    --prefer-dist \
    --no-interaction \
    --no-progress \
    --no-scripts

COPY . .

RUN composer dump-autoload --no-dev --optimize --no-scripts \
    && php artisan package:discover --ansi

FROM node:24-bookworm-slim AS assets

WORKDIR /app

COPY package.json package-lock.json .npmrc ./

RUN npm ci --no-audit

COPY . .

COPY --from=vendor /var/www/html/vendor ./vendor

RUN npm run build

FROM php-base AS app

WORKDIR /var/www/html

COPY --from=vendor --chown=www-data:www-data /var/www/html /var/www/html

COPY --from=assets --chown=www-data:www-data /app/public/build /var/www/html/public/build

RUN mkdir -p storage/framework/cache storage/framework/sessions storage/framework/views storage/logs bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R ug+rwX storage bootstrap/cache

EXPOSE 80

CMD ["apache2-foreground"]
