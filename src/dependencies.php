<?php
$container = $app->getContainer();

function query($args) {
    $params = array();
    foreach($args as $key => $value) {
        $params[] = urlencode($key) . '=' . urlencode($value);
    }
    return '?' . implode('&', $params);
}

// view renderer
$container['renderer'] = function ($c) {
    $renderer = new \Slim\Views\Twig($c['settings']['renderer']['template_path'], [
        //'cache' => '../template-cache',
        'cache' => $c['settings']['renderer']['cache'],
    ]);
    $renderer->getEnvironment()->addFunction('query', new Twig_Function_Function('query'));
    $renderer->addExtension(new \Slim\Views\TwigExtension(
        $c['router'],
        $c['request']->getUri()
    ));

    return $renderer;
};

// monolog
$container['logger'] = function ($c) {
    $settings = $c->get('settings')['logger'];
    $logger = new Monolog\Logger($settings['name']);
    $logger->pushProcessor(new Monolog\Processor\UidProcessor());
    $logger->pushHandler(new Monolog\Handler\RotatingFileHandler($settings['logger']['path'], 7, $settings['logger']['level']));
    return $logger;
};

// database PDO
$container['db'] = function($c) {
    $db = $c['settings']['db'];
    $pdo = new PDO("mysql:host=" . $db['host'] . ";dbname=" . $db['dbname'],
        $db['user'], $db['pass']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    return $pdo;
};
