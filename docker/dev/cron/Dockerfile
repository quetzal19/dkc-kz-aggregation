FROM php:8.3

ARG UID
ARG GID

RUN apt-get update && apt-get install -y libzip-dev procps vim cron

COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/
RUN install-php-extensions amqp bcmath intl mongodb opcache zip

RUN groupmod -o -g ${GID} www-data && usermod -o -u ${UID} -g www-data www-data

WORKDIR /var/www

CMD crontab -u www-data /var/crontab/www-data && cron -f
