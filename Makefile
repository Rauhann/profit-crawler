#!make
include .env
export $(shell sed 's/=.*//' .env)

t=

up:
	docker-compose up
upd:
	docker-compose up -d
down:
	docker-compose down
downs:
	docker-compose down --remove-orphans
build:
	docker-compose up --build
buildd:
	docker-compose up --build -d
sh:
	docker exec -it -u dev profit-crawler-app /bin/bash
server:
	docker exec -it profit-crawler-nginx /bin/bash
db:
	docker exec -it profit-crawler-db bash -c "mysql -u ${DB_USERNAME} -p'${DB_PASSWORD}' ${DB_DATABASE}"
redis:
	docker exec -it profit-crawler-redis sh
migrate:
	php artisan migrate
reset:
	docker-compose down --remove-orphans && docker system prune -a -f && docker-compose up --build
prune:
	docker-compose down --remove-orphans && docker system prune -a -f
