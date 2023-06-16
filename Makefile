autofix:
	php vendor/bin/phpcbf --standard=dev/phpcs.xml --basepath=$(PWD) src

deploy:
ifndef WIKI_API_DEPLOY_REMOTE
	$(error WIKI_API_DEPLOY_REMOTE not defined)
endif
	composer install --no-dev
	composer dump-autoload -o
	rsync -avzu --delete --delete-after -e "ssh -o StrictHostKeyChecking=no" config public src vendor $(WIKI_API_DEPLOY_REMOTE)

db:
	sqlite3 var/database.sqlite

lint:
	composer run-script lint

lint-composer:
	composer validate --strict

phpunit:
	composer run phpunit

schema-sqlite:
	sqlite3 var/database.sqlite < config/schema-sqlite.sql

serve:
	php -S localhost:8000 -t public src/bootstrap-builtin.php

tags:
	dev/update-vim-tags

test: lint-composer lint phpunit
