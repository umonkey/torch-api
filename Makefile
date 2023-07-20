autofix:
	php vendor/bin/phpcbf --standard=dev/phpcs.xml --basepath=$(PWD) src

clean:
	rm -rf var/*

deploy:
ifndef WIKI_API_DEPLOY_REMOTE
	$(error WIKI_API_DEPLOY_REMOTE not defined)
endif
	composer install --no-dev
	composer dump-autoload -o
	rsync -avzu -c --delete --delete-after -e "ssh -o StrictHostKeyChecking=no" bin config public src vendor $(WIKI_API_DEPLOY_REMOTE)

db:
	sqlite3 var/database.sqlite

export:
	mkdir -p var/export
	rm -f var/export/*.json
	bin/export-data var/export

integration-tests:
	composer run-script integration-tests

lint:
	composer run-script lint

lint-composer:
	composer validate --strict

phpunit:
	rm -f var/errors.log
ifneq (,$(FILTER))
	composer run phpunit -- -- --filter "$(FILTER)"
else
	composer run phpunit
endif

schema-sqlite:
	sqlite3 var/database.sqlite < config/schema-sqlite.sql

serve:
	php -S localhost:8000 -t public src/bootstrap-builtin.php

tags:
	dev/update-vim-tags

test: lint-composer lint phpunit
