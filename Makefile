COMPOSER_FLAGS=--no-ansi --verbose --no-interaction

all: build

build: composer-install apidocs
	php scripts/compile-less.php
	php scripts/build-example-data.php

apidocs: composer-install
	./vendor/bin/sami.php update vendor/easyrdf/easyrdf/config/sami.php -n
	cp -Rf vendor/easyrdf/easyrdf/docs/api public/docs/

composer-install: composer.phar
	php composer.phar $(COMPOSER_FLAGS) install

update: clean composer.phar
	php composer.phar $(COMPOSER_FLAGS) update

composer.phar:
	curl -s -z composer.phar -o composer.phar http://getcomposer.org/composer.phar

clean:
	rm -f composer.phar
	rm -Rf vendor/
	rm -Rf public/docs/api
	rm -f logs/*
	rm -Rf tmp/*

.PHONY: all build update clean
