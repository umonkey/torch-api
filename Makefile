db:
	sqlite3 var/database.sqlite

lint: lint-composer lint-phpstan lint-phpcs

lint-composer:
	composer validate --strict

lint-phpcs:
	vendor/bin/phpcs -s --standard=dev/phpcs.xml --basepath=$(PWD) src

lint-phpstan:
	mkdir -p var/cache/phpstan
	vendor/bin/phpstan --configuration=dev/phpstan.neon analyze --no-progress --no-ansi src

phpunit:
	php vendor/bin/phpunit --testsuite unit 2>var/phpunit-errors.log

schema:

serve:
	php -S localhost:8000 -t public src/bootstrap-builtin.php

tags:
	dev/update-vim-tags

test: phpunit
