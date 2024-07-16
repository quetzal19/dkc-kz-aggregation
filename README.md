# DKC KZ. Сервис агрегации

## Версии ПО
1. PHP 8.3
2. [MongoDB 4.4](https://www.mongodb.com/docs/v4.4/) (используется эта версия, потому что в ней остался sh, начиная с 5-й версии нужен отдельный клиент)
3. Symfony 7.0.*

# Как развернуть проект локально:

```
git clone git@github.com:quetzal19/dkc-kz-aggregation.git
cd dkc-kz-aggregation
cp .env.example .env
cp docker/.env.example docker/.env
```

Проверить настройки .env файлов, изменить их при необходимости

## Поднятие контейнеров:

```
make up
```

## Зайти в bash контейнера php:

```
make bash-php
```

При первом запуске нужно выполнить `composer install`.
