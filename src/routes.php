<?php
// Routes

$app->get('/[index.php]', function ($request, $response, $args) {
    return $this->renderer->render($response, 'index.html', []);
});
