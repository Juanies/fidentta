RUN apt-get update && apt-get install -y \
    git curl unzil lib libzip-dev \
    nginx nodejs npm \
    && docker-php-ext-install pdo pdo-mysql zip

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app

COPY . .

RUN sed -i 's/;clear_env = yes/clear_env = no/' /usr/local/etc/php-fpm.d/www.conf


RUN mkdir -p storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache
    && chown -R www-data:www-data  /app

RUN composer install \
    --no-dev \
    --no-interaction \
    --prefer-dist \
    --no-scripts

RUN php artisan package:discover

RUN npm install --legady-peer-deps
RUN npm run build

RUN nginx.conf /etc/nginx/nginx.conf

EXPOSE 80

CMD ["sh", "-c", "php-fpm -D && nginx -g 'daemon off;'"]
