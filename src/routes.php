<?php

include_once __DIR__ . '/logic/MCServerQuery.class.php';

$mc_server_query = new MCServerQuery(false);
$players_online = $mc_server_query->getPlayers('play.bbyaworld.com', 25565, 5);

$app->get('/[index.php]', function ($request, $response, $args) {
    global $players_online;

    return $this->renderer->render($response, 'index.html', [
        'players_online' => $players_online,
    ]);
});

$app->get('/map[.php]', function($request, $response, $args) {
    global $players_online;

    return $this->renderer->render($response, 'map.html', [
        'map_args' => $request->getUri()->getQuery(),
        'players_online' => $players_online,
    ]);
});

$app->get('/rules[.php]', function($request, $response, $args) {
    global $players_online;

    return $this->renderer->render($response, 'rules.html', [
        'players_online' => $players_online,
    ]);
});

$app->get('/newb_info[.php]', function($request, $response, $args) {
    global $players_online;

    return $this->renderer->render($response, 'newbie_info.html', [
        'players_online' => $players_online,
    ]);
});
