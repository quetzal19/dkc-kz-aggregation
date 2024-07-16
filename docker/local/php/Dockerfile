FROM php:8.3-fpm

ARG UID
ARG GID

RUN apt-get update && apt-get install -y git libzip-dev procps vim

COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/
RUN install-php-extensions amqp bcmath intl mongodb opcache zip

# Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

RUN groupmod -o -g ${GID} www-data && usermod -o -u ${UID} -g www-data www-data

USER www-data

WORKDIR /var/www

CMD COMPOSER_MEMORY_LIMIT=-1 composer install ; php-fpm
