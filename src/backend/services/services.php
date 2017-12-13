#!php -q

<?php

if(count($argv) < 2) {
    die('Service is not specified');
}

if(count($argv) > 2) {
    die('Too many arguments');
}

$service = preg_replace('/[^a-zA-Z_\-0-9]/', '', $argv[1]);

$path = __DIR__ . '/' . $service . '/update.php';

if(!file_exists($path)) {
    die('Unknown service: ' . $service);
}

echo 'Launching ' . $service . PHP_EOL;

require($path);
