COMPOSER_FLAGS=--no-ansi --verbose --no-interaction

all: build

build: composer-install
	php scripts/compile-less.php

composer-install: composer.phar
	php composer.phar $(COMPOSER_FLAGS) install

update: clean composer.phar
	php composer.phar $(COMPOSER_FLAGS) update

composer.phar:
	curl -s -z composer.phar -o composer.phar http://getcomposer.org/composer.phar

clean:
	rm -f composer.phar
	rm -Rf vendor/
	rm -f logs/*
	rm -Rf tmp/*

.PHONY: all build update clean
