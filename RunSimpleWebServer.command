#!/bin/sh

cd "`dirname "$0"`/public"
exec php -S localhost:8000 ./index.php
