#!/bin/sh
#
# Script to setup a Debian server to host www.easyrdf.org
#

# If anything fails, throw error
set -e

# Upgrade to latest versions of packages
apt-get update
apt-get upgrade -y

# Install dependencies
apt-get install -y \
  git \
  make \
  apache2-mpm-prefork \
  libapache2-mod-php5 \
  ntp \
  ntpdate \
  php5-cli \
  php5-curl

# Remove packages we don't need
apt-get remove --purge -y \
  nano \
  php5-suhosin

if [ ! -d "/srv/www" ]; then
  mkdir -p /srv/www
fi

if [ ! -d "/srv/www/easyrdf" ]; then
  git clone git://github.com/njh/www.easyrdf.org.git /srv/www/easyrdf
fi

# Build the app
cd /srv/www/easyrdf
mkdir -pf /srv/www/easyrdf/tmp/twig
chown -f www-data:www-data /srv/www/easyrdf/tmp/twig
rm -Rf /srv/www/easyrdf/tmp/twig/*
make

# Configure and restart apache
cp -v config/apache2.conf /etc/apache2/sites-available/easyrdf
a2enmod rewrite
a2ensite easyrdf
service apache2 restart
