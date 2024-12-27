DOCKER_IMAGE=cart-api-php

.PHONY: docker-bash
docker-bash:
	docker exec -it $(DOCKER_IMAGE) bash

.PHONY: create-migrations
create-migrations:
	docker exec -it $(DOCKER_IMAGE) php bin/console doctrine:migrations:diff

.PHONY: create-migrations
execute-migrations:
	docker exec -it $(DOCKER_IMAGE) php bin/console doctrine:migrations:migrate

.PHONY: create-database
create-database:
	docker exec -it $(DOCKER_IMAGE) php bin/console doctrine:database:create


.PHONY: drop-database
drop-database:
	docker exec -it $(DOCKER_IMAGE) php bin/console doctrine:database:drop --force

.PHONY: clean-cache
clean-cache:
	docker exec -it $(DOCKER_IMAGE) php bin/console cache:clear

.PHONY: test
test-all:
	docker exec -it $(DOCKER_IMAGE) php -dxdebug.mode=coverage vendor/bin/phpunit --testsuite All

.PHONY: test-coverage
test-coverage:
	docker exec -it $(DOCKER_IMAGE) php -dxdebug.mode=coverage vendor/bin/phpunit --coverage-html /var/www/html/coverage