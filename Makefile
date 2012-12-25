
all: build

build:
	curl -s -z composer.phar -o composer.phar http://getcomposer.org/composer.phar
	php composer.phar --no-ansi --verbose --no-interaction install

.PHONY: all build
