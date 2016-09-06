<?php

require_once(__DIR__ . '/../src/backend/utils/array_merge.php');

$cache = [
    'dir' => __DIR__ . '/../runtime/cache/',
];

$cache_local = require(__DIR__ . '/cache-local.php');

return array_merge_recursive_distinct($cache, $cache_local);
