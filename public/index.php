<?php

require __DIR__ . '/../vendor/autoload.php';

session_start();

// Instantiate the app
$settings = require __DIR__ . '/../config/web.php';
$app = new \Slim\App($settings);

// Set up dependencies
require __DIR__ . '/../src/backend/dependencies.php';

// Register middleware
require __DIR__ . '/../src/backend/middleware.php';

// Register routes
require __DIR__ . '/../src/backend/routes.php';

// Run app
$app->run();

?>
