# Это старый репозиторий - он теперь в состоянии архива
**Новый репозиторий: https://github.com/Red-Teapot/bbyaworld.com-django**

# Описание

В этом репозитории находится исходный код веб-сайта сервера Minecraft BBya World.

Проект собран с помощью Slim PHP Framework, препроцессора SASS, шаблонизатора Twig и системы сборки Gulp.

# Установка и сборка

## Требования

1. NodeJS
2. Composer
3. Глобальный gulp-cli
4. Глобальный Bower

## Процесс установки

1. Склонировать данный репозиторий
2. Скачать `composer.phar` в корневую папку репозитория
3. Установить зависимости Composer:

   ```
   php composer.phar install
   ```

   Или (без dev-зависимостей):

   ```
   php composer.phar intall --no-dev
   ```

4. Установить зависимости npm:

   ```
   npm install
   ```

5. Создать папки `runtime` и `temp` с правами `777` (Gulp требует доступ на запись/чтение в `temp`, веб-сервер требует доступ на запись/чтение в `runtime`)
6. Установить зависимости Bower:

   ```
   bower install
   ```

7. Собрать ассеты Gulp:

   ```
   gulp
   ```

8. Создать БД и необходимые таблицы:

   ```sql
   CREATE TABLE `online_stats` (
   `uuid` varchar(32) NOT NULL PRIMARY KEY,
   `nickname` varchar(63) NOT NULL UNIQUE,
   `time` int(11) NOT NULL DEFAULT '0'
   ) ENGINE=InnoDB DEFAULT CHARSET=utf8;

   CREATE TABLE `regions` (
      `name` varchar(128) NOT NULL PRIMARY KEY,
      `label` varchar(128) NOT NULL,
      `owner_nickname` varchar(128) NOT NULL,
      `area` float NOT NULL DEFAULT '-1'
   ) ENGINE=InnoDB DEFAULT CHARSET=utf8;

   CREATE TABLE `clans` (
      `order` int unsigned NOT NULL PRIMARY KEY,
      `name` varchar(128) NOT NULL,
      `cell_count` int unsigned NOT NULL DEFAULT 0,
      `is_in_council` tinyint unsigned NOT NULL DEFAULT 0
   ) ENGINE='InnoDB' COLLATE 'utf8_general_ci';
   ```

9. Создать `src/settings-local.php` на основе приведенного шаблона:

   ```php
   <?php

   return [
       'settings' => [
           'displayErrorDetails' => true, // Необходимо выключить на продакшне

           'db' => [
               'host' => 'localhost', // Хост БД
               'user' => 'user', // Пользователь БД
               'pass' => 'pass', // Пароль пользователя БД
               'dbname' => 'database', // Имя БД
           ],

           'renderer' => [
               // Можно установить в true для кеширования шаблонов
               // Полезно на продакшне
               'cache' => false,
           ],
       ],
   ];
   ```

10. Настроить корневую директорию веб-сервера в `public/` и включить rewrite.
11. Скачать `bg_video.mp4` и `bg_video.webm` в `public/assets/videos`.

# Лицензия

The content of this project itself is licensed under the [Creative Commons Attribution 3.0 license](http://creativecommons.org/licenses/by/3.0/us/deed.en_US),
and the underlying source code used to format and display that content is licensed under the [MIT license](http://opensource.org/licenses/mit-license.php).
