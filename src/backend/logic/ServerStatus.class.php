<?php

include_once __DIR__ . '/MCServerQuery.class.php';
include_once __DIR__ . '/Cache.class.php';

class ServerStatus {

    const CACHE_TIME = 300; // 5 minutes

    public static function getStatus($address, $port) {

        $players_online = Cache::fetch('server_status');

        if($players_online) {
            return $players_online;
        } else {
            $mc_server_query = new MCServerQuery(false);
            $players_online = $mc_server_query->getPlayers($address, $port, 5);

            Cache::store('server_status', $players_online, static::CACHE_TIME);

            return $players_online;
        }
    }
}
