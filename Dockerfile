FROM php:8.3-fpm-alpine

# Установка системных зависимостей и расширений PHP
RUN apk add --no-cache nginx supervisor curl libpng-dev libjpeg-turbo-dev freetype-dev zip libzip-dev git bash postgresql-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo pdo_mysql pdo_pgsql gd zip

# Установка Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Настройка рабочей директории
WORKDIR /var/www

# Копируем файлы проекта
COPY . .

# Установка зависимостей Laravel
RUN composer install --no-dev --optimize-autoloader

# Настройка прав (важно для работы Laravel)
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache

# Копируем конфигурацию Nginx (Render слушает порт 10000 по умолчанию)
RUN echo 'server { \
    listen 10000; \
    root /var/www/public; \
    index index.php index.html; \
    location / { try_files $uri $uri/ /index.php?$query_string; } \
    location ~ \.php$ { \
        try_files $uri =404; \
        fastcgi_split_path_info ^(.+\.php)(/.+)$; \
        fastcgi_pass 127.0.0.1:9000; \
        fastcgi_index index.php; \
        include fastcgi_params; \
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name; \
        fastcgi_param PATH_INFO $fastcgi_path_info; \
    } \
}' > /etc/nginx/http.d/default.conf

# Команда для одновременного запуска php-fpm и nginx
CMD php-fpm -D && nginx -g "daemon off;"
