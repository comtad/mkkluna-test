FROM php:8.3-fpm

# Установка зависимостей
RUN apt-get update && apt-get install -y \
    build-essential \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    locales \
    zip \
    jpegoptim optipng pngquant gifsicle \
    vim \
    unzip \
    git \
    curl \
    libpq-dev \
    libzip-dev

# Очистка кэша
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Установка расширений PHP
RUN docker-php-ext-install pdo_pgsql pgsql zip exif pcntl
RUN docker-php-ext-configure gd --with-freetype --with-jpeg
RUN docker-php-ext-install gd

# Установка Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Добавление пользователя для приложения Laravel с UID=1000 и GID=1000
RUN groupadd -g 1000 www
RUN useradd -u 1000 -ms /bin/bash -g www www

# Рабочая директория
WORKDIR /var/www/html

# Копирование кода приложения из корня проекта
COPY . /var/www/html

# Установка зависимостей Composer (теперь composer.json должен быть в корне)
RUN composer install --no-interaction --optimize-autoloader

# Изменение прав доступа
RUN chown -R www:www /var/www/html

# Переключение на пользователя www
USER www

# Экспорт порта и запуск PHP-FPM
EXPOSE 9000
CMD ["php-fpm"]