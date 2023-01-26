autofix:
	php vendor/bin/phpcbf --standard=dev/phpcs.xml --basepath=$(PWD) src

deploy:
	composer install --no-dev
	composer dump-autoload -o
	rsync -avzub -e ssh config public src vendor $(DEPLOY_REMOTE)

db:
	sqlite3 var/database.sqlite

lint: lint-composer lint-phpstan lint-phpcs

lint-composer:
	composer validate --strict

lint-phpcs:
	vendor/bin/phpcs -s --standard=dev/phpcs.xml --basepath=$(PWD) src config

lint-phpstan:
	mkdir -p var/cache/phpstan
	vendor/bin/phpstan --configuration=dev/phpstan.neon analyze --no-progress --no-ansi src config

phpunit:
	APP_ENV=unit_tests php vendor/bin/phpunit --testsuite unit 2>var/phpunit-errors.log

schema-sqlite:
	sqlite3 var/database.sqlite < config/schema-sqlite.sql

serve:
	php -S localhost:8000 -t public src/bootstrap-builtin.php

tags:
	dev/update-vim-tags

test: lint phpunit
