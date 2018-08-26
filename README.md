# SLIM Skeleton

WORK IN PROGRESS

This project is a custom implementation of Slim PHP Framework.

Included in this project : 
  - A custom micro ORM based on Doctrine DBAL :
    - An abstract Entity class to convert object to array and vice-versa. Usefull to build API
    - Default Entity Repository with build-in SQL requests
    - Define your custom Entity Repository for more complex SQL requests
  - A console interface based on the Symfony console component :
    - Write your own console command !
    - Define your custom console output style with the Symfony StyleInterface
  - A solid folder structure based on common best practices :
    - Environment variable with .env support
    - Support for environment (prod, dev)
    - Bringing MCV pattern design
  - A Twig template engine integration :
    - A elegant solution to extends templates
    - A simpler syntax than PHP's based templates
    - Adding security with escaped code
    - Template cached
  - On the top of Slim PHP Framework :
    - Add or remove dependencies from the Slim world
    - Designed to be as powerful as possible

## Install the Application

Run this command from the directory in which you want to install your new Slim Framework application.

    git clone https://github.com/hermann8u/slim-setup.git

Duplicate the .env.dist file in .env file and adapt it.

Point your virtual host document root to your new application's `public/` directory.

## TODO
  - ORM :
    - Finish the Core\Database\Repository methods.
    - Manage join.

  - API :
    - Adding handler for XHR request with JSON response

  - Default :
    - Adding Logger Middleware to track request lifecycle
    - Customs errors handlers
    - Call error handlers on try catch
