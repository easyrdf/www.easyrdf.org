www.easyrdf.org
===============

The GIT repository contains the EasyRdf website.


Developing on Mac OS X
----------------------

In order to develop the EasyRdf website on your own machine, follow these steps:

1. Turn on 'Web Sharing' in Mac OS X Settings, under the Sharing section.

2. Enable PHP by editing ```/etc/apache2/httpd.conf``` and uncommenting the ```LoadModule php5_module``` line.
   See this page for more information: http://php.net/manual/en/install.macosx.bundled.php

3. Restart apache using:

    ```
    sudo apachectl restart
    ```

4. Checkout the respository into your sites directory:

    ```
    cd ~/Sites
    git clone git@github.com:njh/www.easyrdf.org.git
    ```

5. Install the dependencies using composer:

    ```
    cd ~/Sites/www.easyrdf.org
    make build
    ```

6. Edit the ```.htaccess``` file to point to your install path.
   The RewriteBase line needs to be uncommented and changed to match your username, for example:

    ```
    RewriteBase /~username/www.easyrdf.org/public/
    ```

7. Launch the website in your browser:

   http://localhost/~username/www.easyrdf.org/public/

Alternatively
----------------------

1. http://php-osx.liip.ch/
2. Symlink thingie
3. cd to the public dir
4. php -S localhost:8000

