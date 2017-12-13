<?php

include_once __DIR__ . '/logic/OnlineStats.class.php';
include_once __DIR__ . '/logic/cache/Cache.class.php';
include_once __DIR__ . '/logic/PlayerRegionsAreas.class.php';
include_once __DIR__ . '/logic/ClansCells.class.php';
include_once __DIR__ . '/logic/server_status/ServerStatus.class.php';

$app->get('/[index.php]', function ($request, $response) {
    return $this->renderer->render($response, 'index.twig');
});

$app->get('/map[.php]', function($request, $response) {
    return $this->renderer->render($response, 'map.twig', [
        'map_args' => $request->getUri()->getQuery(),
    ]);
});

$app->get('/rules[.php]', function($request, $response) {
    return $this->renderer->render($response, 'rules.twig');
});

$app->get('/newb_info[.php]', function($request, $response) {
    return $this->renderer->render($response, 'newbie_info.twig');
});
$app->get('/newbie-info', function($request, $response) {
    return $this->renderer->render($response, 'newbie_info.twig');
});

$app->get('/contacts', function($request, $response) {
    return $this->renderer->render($response, 'contacts.twig');
});

/*$app->get('/staff-and-vacancies', function($request, $response) {
    return $this->renderer->render($response, 'staff_and_vacancies.twig');
});*/

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

    return $this->renderer->render($response, 'stats.twig', [
        'players_stats' => $playersPage,
        'total_count' => $totalCount,
        'current_page' => $page,
        'latest_update' => $latestUpdate,
    ]);
});

$app->get('/regions', function($request, $response) {

    $areas = new PlayerRegionsAreas($this->db);

    $params = $request->getQueryParams();

    $sort = $request->getQueryParam('sort', 'area');
    $sort_dir = $request->getQueryParam('dir', 'desc');

    $areasPage = $areas->getAreas($sort, $sort_dir);

    return $this->renderer->render($response, 'region_areas.twig', [
        'list' => $areasPage['grouped'],
        'misc' => $areasPage['misc'],
        'sort' => $sort,
        'sort_dir' => $sort_dir,
    ]);
});

$app->get('/clans', function($request, $response) {

    $clans = new ClansCells($this->db);

    $list = $clans->getList();

    $council_list = [];
    $other_list = [];

    foreach($list as $clan) {
        if($clan['is_in_council'] > 0) {
            $council_list[] = $clan;
        } else {
            $other_list[] = $clan;
        }
    }

    return $this->renderer->render($response, 'clans_cells.twig', [
        'council_list' => $council_list,
        'other_list' => $other_list,
    ]);
});

$app->get('/game-server-status', function($request, $response) {

    $status = ServerStatus::getStatus('play.bbyaworld.com', 25565);

    $response = $response->withHeader('Content-Type', 'application/json; charset=utf-8');
    $response = $response->withHeader('Access-Control-Allow-Origin', 'http://forum.bbyaworld.com');
    $response = $response->withJson($status);

    return $response;
});
