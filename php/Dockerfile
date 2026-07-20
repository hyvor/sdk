FROM composer:2 AS composer

FROM php:8.4-cli AS test

RUN apt-get update && apt-get install -y --no-install-recommends unzip git \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer /usr/bin/composer /usr/bin/composer

WORKDIR /app

COPY composer.json ./
RUN composer install --prefer-dist --no-progress --no-interaction

COPY . .

CMD ["tail", "-f", "/dev/null"]
