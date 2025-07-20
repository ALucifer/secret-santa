# Executables (local)
DOCKER_COMP = docker compose

# Docker containers
PHP_CONT = $(DOCKER_COMP) exec php
NODE = $(DOCKER_COMP) run --rm node

# Executables
PHP      = $(PHP_CONT) php
COMPOSER = $(PHP_CONT) composer
SYMFONY  = $(PHP) bin/console
CCODE = $(DOCKER_COMP) run -it --rm cleancode

# Misc
.DEFAULT_GOAL = help
.PHONY        : help build up start down logs sh composer vendor sf cc test

## —— 🎵 🐳 The Symfony Docker Makefile 🐳 🎵 ——————————————————————————————————
help: ## Outputs this help screen
	@grep -E '(^[a-zA-Z0-9\./_-]+:.*?##.*$$)|(^##)' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}{printf "\033[32m%-30s\033[0m %s\n", $$1, $$2}' | sed -e 's/\[32m##/[33m/'

## —— Docker 🐳 ————————————————————————————————————————————————————————————————
build: ## Builds the Docker images
	@$(DOCKER_COMP) build --pull --no-cache

up: ## Start the docker hub in detached mode (no logs)
	@$(DOCKER_COMP) up -d

start: build up ## Build and start the containers

down: ## Stop the docker hub
	@$(DOCKER_COMP) down --remove-orphans

logs: ## Show live logs
	@$(DOCKER_COMP) logs --tail=0 --follow

sh: ## Connect to the FrankenPHP container
	@$(PHP_CONT) sh

bash: ## Connect to the FrankenPHP container via bash so up and down arrows go to previous commands
	@$(PHP_CONT) bash

watch:
	$(NODE) npm run watch

dev:
	$(NODE) npm run dev

js-routing: ## Generate JS routing
	$(SYMFONY) fos:js-routing:dump --format=json --target=assets/js/routes.json

node-bash: ## Connect to node container
	$(NODE) bash

messenger:
	$(SYMFONY) messenger:consume --all
## —— Composer 🧙 ——————————————————————————————————————————————————————————————
composer: ## Run composer, pass the parameter "c=" to run a given command, example: make composer c='req symfony/orm-pack'
	@$(eval c ?=)
	@$(COMPOSER) $(c)

vendor: ## Install vendors according to the current composer.lock file
vendor: c=install --prefer-dist --no-dev --no-progress --no-scripts --no-interaction
vendor: composer

## —— Symfony 🎵 ———————————————————————————————————————————————————————————————
sf: ## List all Symfony commands or pass the parameter "c=" to run a given command, example: make sf c=about
	@$(eval c ?=)
	@$(SYMFONY) $(c)

cc: c=c:c ## Clear the cache
cc: sf

migrate:
	$(SYMFONY) doctrine:migrations:migrate --no-interaction

migration:
	$(SYMFONY) make:migration

fixtures:
	$(SYMFONY) doctrine:fixtures:load

## —— Clean code ——————————————————————————————————————————————————————————————

phpstan:
	$(CCODE) phpstan analyse src --level 9

phpmd:
	$(CCODE) phpmd src/ text phpmd.xml

phpcs:
	$(CCODE) phpcs src

phpunit:
	$(CCODE) phpunit-9

phpmetrics:
	$(CCODE) phpmetrics --report-html="./report" ./src