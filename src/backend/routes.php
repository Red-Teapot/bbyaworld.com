<?php

include_once __DIR__ . '/logic/OnlineStats.class.php';
include_once __DIR__ . '/logic/cache/Cache.class.php';
include_once __DIR__ . '/logic/PlayerRegionsAreas.class.php';
include_once __DIR__ . '/logic/server_status/ServerStatus.class.php';

$app->get('/[index.php]', function ($request, $response) {
    return $this->renderer->render($response, 'index.html');
});

$app->get('/map[.php]', function($request, $response) {
    return $this->renderer->render($response, 'map.html', [
        'map_args' => $request->getUri()->getQuery(),
    ]);
});

$app->get('/map-redir', function($request, $response) {
    return $this->renderer->render($response, 'map_redir.html', [
        'map_args' => $request->getUri()->getQuery(),
    ]);
});

$app->get('/rules[.php]', function($request, $response) {
    return $this->renderer->render($response, 'rules.html');
});

$app->get('/newb_info[.php]', function($request, $response) {
    return $this->renderer->render($response, 'newbie_info.html');
});
$app->get('/newbie-info', function($request, $response) {
    return $this->renderer->render($response, 'newbie_info.html');
});

$app->get('/contacts', function($request, $response) {
    return $this->renderer->render($response, 'contacts.html');
});

$app->get('/staff-and-vacancies', function($request, $response) {
    return $this->renderer->render($response, 'staff_and_vacancies.html');
});

$app->get('/stats[.php]', function($request, $response) {

    $stats = new OnlineStats($this->db);
    $totalCount = $stats->getTotalCount();

    $params = $request->getQueryParams();
    $page = isset($params['p']) ?
        intval($params['p']) :
        1;

    if($page > ceil($totalCount / 50))
        $page = ceil($totalCount / 50);
    if($page < 1)
        $page = 1;

    $playersPage = $stats->getStats($page);

    $latestUpdate = Cache::fetch('last_online_stats_launch');
    if($latestUpdate)
        $latestUpdate = $latestUpdate['time'];

    return $this->renderer->render($response, 'stats.html', [
        'players_stats' => $playersPage,
        'total_count' => $totalCount,
        'current_page' => $page,
        'latest_update' => $latestUpdate,
    ]);
});

$app->get('/regions', function($request, $response) {

    $areas = new PlayerRegionsAreas($this->db);
    $totalCount = $areas->getTotalCount();

    $params = $request->getQueryParams();
    $page = isset($params['p']) ?
        intval($params['p']) :
        1;

    if($page > ceil($totalCount / 50))
        $page = ceil($totalCount / 50);
    if($page < 1)
        $page = 1;

    $sort = $request->getQueryParam('sort', 'label');
    $sort_dir = $request->getQueryParam('dir', 'asc');

    $areasPage = $areas->getAreas($page, 50, $sort, $sort_dir);

    return $this->renderer->render($response, 'region_areas.html', [
        'areas' => $areasPage,
        'total_count' => $totalCount,
        'current_page' => $page,
        'sort' => $sort,
        'sort_dir' => $sort_dir,
    ]);
});

$app->get('/game-server-status', function($request, $response) {

    $status = ServerStatus::getStatus('play.bbyaworld.com', 25565);

    $response = $response->withHeader('Content-Type', 'application/json; charset=utf-8');
    $response = $response->withHeader('Access-Control-Allow-Origin', 'http://forum.bbyaworld.com');
    $response = $response->withJson($status);

    return $response;
});
