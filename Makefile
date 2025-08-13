CURRENT_UID := $(shell id -u)
CURRENT_GID := $(shell id -g)
.PHONY: build up down restart

build:
	docker-compose build --build-arg USER_UID=$(CURRENT_UID) --build-arg USER_GID=$(CURRENT_GID)

up:
	docker-compose up -d

down:
	docker-compose down

restart: down up

logs:
	docker-compose logs -f

shell:
	docker-compose exec app bash

migrate:
	docker-compose exec app php artisan migrate

optimize:
	docker-compose exec app php artisan optimize

keygen:
	docker-compose exec app php artisan key:generate