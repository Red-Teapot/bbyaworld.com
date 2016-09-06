<?php

require_once(__DIR__ . '/../src/backend/utils/array_merge.php');

$db = [
    'host' => null,
    'user' => null,
    'pass' => null,
    'dbname' => null,
];

$db_local = require(__DIR__ . '/db-local.php');

return array_merge_recursive_distinct($db, $db_local);
