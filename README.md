www.easyrdf.org
===============

The GIT repository contains the EasyRdf website.


Developing using PHP CLI server
-------------------------------

1. Install php command line tools:

   On Mac OS install using [Homebrew](https://brew.sh):
   
   ```
   brew install php
   ```

   On Debian / Ubuntu install using:

   ```
   sudo apt-get install php-cli
   ```

2. Checkout the respository into directory of your choice:

    ```
    git clone git@github.com:easyrdf/www.easyrdf.org.git
    ```

4. Start PHP server in the public directory:

    ```
    php -S localhost:8000 -t public public/index.php
    ```

5. Launch the website in your browser:

   http://localhost:8000/
