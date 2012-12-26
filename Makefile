
all: build

build:
	php composer.phar --no-ansi --verbose --no-interaction install

update:
	curl -s -z composer.phar -o composer.phar http://getcomposer.org/composer.phar
	php composer.phar --no-ansi --verbose --no-interaction update

.PHONY: all build update
