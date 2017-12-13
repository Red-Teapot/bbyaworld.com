<?php

return [
    'logger' => [
        'name' => 'ClansCells',
        'path' => __DIR__ . '/../../runtime/logs/clans_cells.log',
        'level' => \Monolog\Logger::DEBUG,
    ],

    'db' => require(__DIR__ . '/../db.php'),

    //'regions_file_url' => 'http://play.bbyaworld.com:28565/tiles/_markers_/marker_world.json',
    'regions_file_url' => 'http://localhost/marker_world.json',
];
