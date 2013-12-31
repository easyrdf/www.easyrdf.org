COMPOSER_FLAGS=--no-ansi --verbose --no-interaction

all: build

build: composer-install \
       build-docs \
       public/docs/api \
       public/css/bootstrap.css \
       public/packages.json \
       data/examples.ttl \
       public/doap.rdf \
       public/js/bootstrap-collapse.js

composer-install: composer.phar
	php composer.phar $(COMPOSER_FLAGS) install

update: clean composer.phar
	php composer.phar $(COMPOSER_FLAGS) update

composer.phar:
	curl -s -z composer.phar -o composer.phar http://getcomposer.org/composer.phar

build-docs: composer-install
	php scripts/build-docs.php

public/docs/api: composer-install
	./vendor/bin/sami.php update vendor/easyrdf/easyrdf/config/sami.php -n
	cp -Rf vendor/easyrdf/easyrdf/docs/api public/docs/

public/css/bootstrap.css:
	php scripts/compile-less.php

public/doap.rdf:
	php vendor/easyrdf/easyrdf/doap.php > $@

public/packages.json:
	php scripts/build-packages-data.php

data/examples.ttl:
	php scripts/build-example-data.php

public/js/bootstrap-collapse.js: vendor/twitter/bootstrap/js/bootstrap-collapse.js
	cp -fp $^ $@

clean:
	rm -f composer.phar
	rm -Rf vendor/
	rm -Rf public/docs/api
	rm -f public/css/bootstrap.css
	rm -f public/daop.rdf
	rm -f public/js/bootstrap-collapse.js
	rm -f public/packages.json
	rm -f data/examples.ttl
	rm -f logs/*
	rm -Rf tmp/*

.PHONY: all build update clean
