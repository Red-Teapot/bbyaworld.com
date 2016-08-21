<?php

return [
    'logger' => [
        'name' => 'OnlinePlayerStats',
        'path' => __DIR__ . '/../../../runtime/logs/app.log',
        'level' => \Monolog\Logger::WARNING,
    ],

    'server' => [
        'address' => 'play.bbyaworld.com',
        'port' => 25565,
    ],

    'db' => (require(__DIR__ . '/../../settings-local.php'))['settings']['db'],

    'updateInterval' => 2, // Minutes
    'mojangApiUrl' => 'https://api.mojang.com/profiles/minecraft',
];
