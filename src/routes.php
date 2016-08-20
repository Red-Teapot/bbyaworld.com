<?php
// Routes

$app->get('/[index.php]', function ($request, $response, $args) {
    return $this->renderer->render($response, 'index.html', []);
});

$app->get('/map[.php]', function($request, $response, $args) {
    return $this->renderer->render($response, 'map.html', [
        'map_args' => $request->getUri()->getQuery(),
    ]);
});

$app->get('/rules[.php]', function($request, $response, $args) {
    return $this->renderer->render($response, 'rules.html', []);
});
