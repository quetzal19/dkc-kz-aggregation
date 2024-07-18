.PHONY: all

SHELL=/bin/bash -e

##################
# Variables
##################
include .env

ENV ?= local

$(info ENV="$(ENV)")

help: ## Справка
	@awk 'BEGIN {FS = ":.*?## "} /^[a-zA-Z_-]+:.*?## / {printf "\033[36m%-30s\033[0m %s\n", $$1, $$2}' $(MAKEFILE_LIST)

info: ## Шпаргалка по установки из README.md
	@sed '/git/,/```/!d;/```/q' README.md | grep -v '```'


##################
# Docker compose
##################
ps:
	docker ps | grep --color dkc-kz-agg

rebuild: ## Сборка контейнеров без запуска проекта
	docker compose -f docker/docker-compose.$(ENV).yml build --no-cache

up: ## Запуск проекта
	docker compose -f docker/docker-compose.$(ENV).yml up -d

down: ## Остановка всех контейнеров проекта
	docker compose -f docker/docker-compose.$(ENV).yml down

bash-php: ## Зайти в bash контейнера с php
	docker compose -f docker/docker-compose.$(ENV).yml exec php-fpm /bin/bash

bash-mongo: ## Зайти в bash контейнера с mongodb
	docker compose -f docker/docker-compose.$(ENV).yml exec mongodb sh

bash-rabbit: ## Зайти в bash контейнера с mongodb
	docker compose -f docker/docker-compose.$(ENV).yml exec rabbit sh