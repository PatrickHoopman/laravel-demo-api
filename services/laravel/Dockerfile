FROM php:8.0.3-cli-alpine3.12

RUN apk add --no-cache bash zip unzip curl postgresql-dev

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN docker-php-ext-install pdo_pgsql

WORKDIR /workspace
COPY . /workspace

RUN composer install

# RUN ./docker-init.sh

EXPOSE 8000
CMD ./laravel-init.sh