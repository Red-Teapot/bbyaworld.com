<?php

return [
    'displayErrorDetails' => true,

    'renderer' => [
        'cache' => false,
    ],
    
    'logger' => [
        'name' => 'Site',
        'path' => __DIR__ . '/../runtime/logs/site.log',
        'level' => \Monolog\Logger::INFO,
    ],
];