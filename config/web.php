<?php

require_once(__DIR__ . '/../src/backend/utils/array_merge.php');

// Default settings, aimed at production
$web = [
    // Common settings
    'displayErrorDetails' => false,
    'addContentLengthHeader' => false,

    // Router settings
    'router' => [
        'cacheFile' => __DIR__ . '/../runtime/router-cache.tmp',
    ],

    // DB settings
    'db' => require(__DIR__ . '/db.php'),

    // Renderer settings
    'renderer' => [
        'template_path' => __DIR__ . '/../src/templates/',
        'cache' => __DIR__ . '/../runtime/template-cache/',
    ],

    // Monolog settings
    'logger' => [
        'name' => 'Site',
        'path' => __DIR__ . '/../runtime/logs/site.log',
        'level' => \Monolog\Logger::WARNING,
    ],

    // Server state cache settings
    'server-state-cache' => [
        'dir' => __DIR__ . '/../runtime/cache/',
    ],
];

$web_local = require(__DIR__ . '/web-local.php');

return [
    'settings' => array_merge_recursive_distinct($web, $web_local),
];
