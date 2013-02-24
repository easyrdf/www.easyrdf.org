#!/bin/sh
#
# Script to configure a Debian server to host www.easyrdf.org
#

# If anything fails, throw error
set -e

# Disable interactive configuration options
echo "set debconf/frontend Noninteractive" | debconf-communicate
echo "set debconf/priority critical" | debconf-communicate

# Configure locale for this system
echo "set locales/locales_to_be_generated en_GB.UTF-8 UTF-8" | debconf-communicate
echo "set locales/default_environment_locale None" | debconf-communicate
locale-gen

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
mkdir -p /srv/www/easyrdf/tmp/twig
chown www-data:www-data /srv/www/easyrdf/tmp/twig
make

# Configure and restart apache
cp -v config/apache2.conf /etc/apache2/sites-available/default
a2enmod rewrite
service apache2 restart
