<?php

include_once __DIR__ . '/MCServerQuery.class.php';
include_once __DIR__ . '/../cache/Cache.class.php';

class ServerStatus {

    const CACHE_TIME = 5; // 5 seconds

    public static function getStatus($address, $port, $cache = true) {
        $result = [];
        if($cache) {
            $players_online = Cache::fetch('server_status');
            if($players_online) {
                $result['status'] = true;
                $result['cached'] = true;
                $result['players'] = $players_online;
                return $result;
            }
        }

        $mc_server_query = new MCServerQuery(false);
        $players_online = $mc_server_query->getPlayers($address, $port, 5);

        Cache::store('server_status', $players_online, static::CACHE_TIME);

        $result['status'] = ($players_online != false);
        $result['cached'] = false;
        $result['players'] = $players_online;

        return $result;
    }
}
