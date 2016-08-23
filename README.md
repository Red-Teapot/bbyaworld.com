# Description

This is BByaWorld Minecraft server website source code.

This project is built using Slim PHP Framework, SASS preprocessor, Twig template engine and Gulp task runner.

# Installation

## Prerequisites

1. NodeJS
2. Composer
3. gulp-cli installed globally
4. Bower installed globally

## Installation process

1. Clone this repo
2. Copy `composer.phar` to repo root folder
3. Install Composer dependencies:
   ```
   php composer.phar install
   ```
   Or (to not install dev dependencies):
   ```
   php composer.phar intall --no-dev
   ```
4. Install npm requirements:
   ```
   npm install
   ```
5. Make `/runtime` and `/temp` dirs with `777` rights (Gulp needs rw access to `/temp`, webserver needs rw access to `/runtime`)
6. Install Bower dependencies:
   ```
   bower install
   ```
7. Build Gulp assets:
   ```
   gulp
   ```
8. Create database and necessary tables:
   ```sql
   CREATE TABLE `online_stats` (
   `uuid` varchar(32) NOT NULL,
   `nickname` varchar(63) NOT NULL,
   `time` int(11) NOT NULL DEFAULT '0',
   PRIMARY KEY (`uuid`),
   UNIQUE KEY `nickname` (`nickname`)
   ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
   ```
9. Create `/src/settings-local.php` file using following template:
   ```php
   <?php

   return [
       'settings' => [
           'displayErrorDetails' => true, // Set it to false on production

           'db' => [
               'host' => 'localhost', // Database host
               'user' => 'user', // Database user
               'pass' => 'pass', // Database password
               'dbname' => 'database', // Database name
           ],

           'renderer' => [
               // Set it to true to enable renderer teplate cache
               // Useful in production
               'cache' => false,
           ],
       ],
   ];
   ```
10. Configure your webserver to point to `/public/` directory and enable rewrite.
11. Put `bg_video.mp4` and `bg_video.webm` to `/public/assets/videos` folder.

# License

The content of this project itself is licensed under the [Creative Commons Attribution 3.0 license](http://creativecommons.org/licenses/by/3.0/us/deed.en_US), 
and the underlying source code used to format and display that content is licensed under the [MIT license](http://opensource.org/licenses/mit-license.php).
