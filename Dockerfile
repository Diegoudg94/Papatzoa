FROM php:8.3-cli

RUN apt-get update && apt-get install -y \
    unzip \
    git \
    curl \
    sqlite3 \
    libsqlite3-dev

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /app

COPY . .

RUN composer install

RUN touch database/database.sqlite

RUN cp .env.example .env

RUN php artisan key:generate

RUN php artisan migrate --force

EXPOSE 10000

CMD php -S 0.0.0.0:10000 -t public
