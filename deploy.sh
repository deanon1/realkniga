#!/bin/bash

# Деплой скрипт для Render
# Создаем SQLite базу если её нет
if [ ! -f database/database.sqlite ]; then
    touch database/database.sqlite
    chmod 666 database/database.sqlite
fi

# Устанавливаем права на папки storage
chmod -R 775 storage bootstrap/cache

# Запускаем миграции
php artisan migrate --force

# Очищаем кэш
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
