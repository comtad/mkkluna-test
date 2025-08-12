FROM php:8.2-fpm

# Получаем аргументы сборки
ARG USER_UID
ARG USER_GID

# Установка зависимостей
RUN apt-get update && apt-get install -y \
    libpq-dev \
    libzip-dev \
    zip \
    unzip \
    git \
    && docker-php-ext-install pdo pdo_pgsql zip

# Создаем пользователя с заданными UID/GID
RUN groupadd -g ${USER_GID} www && \
    useradd -u ${USER_UID} -ms /bin/bash -g www www

# Установка Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Рабочая директория
WORKDIR /var/www/html

# Копируем ВЕСЬ проект
COPY --chown=www:www . .

# Переключаемся на пользователя www
USER www

# Установка зависимостей Composer
RUN composer install --no-interaction --optimize-autoloader

# Настройка приложения
RUN test -f .env || cp .env.example .env && \
    php artisan key:generate && \
    chmod -R 775 storage bootstrap/cache && \
    php artisan optimize

# Точка входа
CMD ["php-fpm"]