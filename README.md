www.easyrdf.org
===============

The GIT repository contains the EasyRdf website.


Developing using PHP 5.4 server
-------------------------------

1. Install php command line tools:

   On Mac OS X following instructions at: http://php-osx.liip.ch/
   Or install using homebrew: https://github.com/josegonzalez/homebrew-php
   On Debian Wheezy install using:

   ```
   sudo apt-get install php5-cli
   ```

2. Checkout the respository into directory of your choice:

    ```
    cd ~/Projects/
    git clone git@github.com:njh/www.easyrdf.org.git
    ```

4. Start PHP server in the public directory:

    ```
    cd public
    php -S localhost:8000
    ```

5. Launch the website in your browser:

   http://localhost:8000/


Developing using Apache on Mac OS X
-----------------------------------

In order to develop the EasyRdf website on your own machine, follow these steps:

1. Enable PHP by editing ```/etc/apache2/httpd.conf``` and uncommenting the ```LoadModule php5_module``` line.
   See this page for more information: http://php.net/manual/en/install.macosx.bundled.php

2. Start Apache using:

    ```
    sudo apachectl start
    ```

    Or restart Apache using:

    ```
    sudo apachectl restart
    ```

3. Checkout the respository into your Sites directory:

    ```
    cd ~/Sites
    git clone git@github.com:njh/www.easyrdf.org.git
    ```

4. Install the dependencies using composer:

    ```
    cd ~/Sites/www.easyrdf.org
    make build
    ```

5. Edit the ```.htaccess``` file to point to your install path.
   The RewriteBase line needs to be uncommented and changed to match your username, for example:

    ```
    RewriteBase /~username/www.easyrdf.org/public/
    ```

7. Launch the website in your browser:

   http://localhost/~username/www.easyrdf.org/public/
