.PHONY: help
help: ## Shows this help
	@awk 'BEGIN {FS = ":.*?## "} /^[a-zA-Z_\-\.]+:.*?## / {printf "\033[36m%-30s\033[0m %s\n", $$1, $$2}' $(MAKEFILE_LIST)

.PHONY: init ## Update composer dependencies
init: composer-update-app composer-update-dev-ops

.PHONY: composer-update-app
composer-update-app:
	composer update

.PHONY: composer-update-dev-ops
composer-update-dev-ops:
	composer update -d ./dev-ops/ci

.PHONY: cs-fix
cs-fix: ## Run php-cs-fixer
	php dev-ops/ci/vendor/bin/php-cs-fixer fix --config=dev-ops/ci/config/php-cs-fixer.dist.php

.PHONY: cs-fix-diff
cs-fix-diff: ## Run php-cs-fixer with diff
	php dev-ops/ci/vendor/bin/php-cs-fixer fix --config=dev-ops/ci/config/php-cs-fixer.dist.php --diff --dry-run

.PHONY: phpstan
phpstan: ## Run phpstan
	php dev-ops/ci/vendor/bin/phpstan analyse --configuration=dev-ops/ci/config/phpstan.neon

.PHONY: phpunit
phpunit: ## Run phpunit
	php dev-ops/ci/vendor/bin/phpunit --configuration dev-ops/ci/config/phpunit.xml.dist

.PHONY: tests
tests: cs-fix phpstan phpunit ## Run all tests
