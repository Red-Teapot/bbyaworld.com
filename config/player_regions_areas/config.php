<?php

return [
    'logger' => [
        'name' => 'PlayerRegionsAreas',
        'path' => __DIR__ . '/../../runtime/logs/regions_areas.log',
        'level' => \Monolog\Logger::WARNING,
    ],

    'db' => require(__DIR__ . '/../db.php'),

    'regions_file_url' => 'http://play.bbyaworld.com:28565/tiles/_markers_/marker_world.json',
];
