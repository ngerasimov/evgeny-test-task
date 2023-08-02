build:
	docker-compose up -d --build

up:
	docker-compose up -d --remove-orphans

down:
	docker-compose down --remove-orphans

wipe:
	docker-compose down -v --remove-orphans

shell:
	docker-compose exec  --user=www-data php sh

xshell:
	docker-compose exec  --user=www-data php-xdebug sh

root:
	docker-compose exec  --user=root php sh

init-db:
	docker-compose exec --user=www-data php chmod +x deploy/*.sh
	docker-compose exec --user=www-data php sh -c deploy/init.db.sh

init-db-test:
	docker-compose exec --user=www-data php chmod +x deploy/*.sh
	docker-compose exec --user=www-data php sh -c deploy/init.db_test.sh

phpunit:
	docker-compose exec --user=www-data php chmod +x deploy/*.sh
	docker-compose exec --user=www-data php sh -c deploy/test.sh

psalm:
	docker-compose exec --user=www-data php vendor/bin/psalm

csfix:
	docker-compose exec --user=www-data php vendor/bin/ecs --fix

composer:
	docker-compose exec --user=www-data php composer install

install: build composer init-db

test: init-db-test phpunit