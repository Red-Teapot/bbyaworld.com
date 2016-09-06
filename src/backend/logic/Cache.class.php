<?php

class Cache {
    private static $_config = false;

    private static function getConfig() {
        if(static::$_config)
            return static::$_config;

        static::$_config = require(__DIR__ . '/../../../config/cache.php');
    }

    public static function store($key, $value, $ttl) {
        $dir = static::getConfig()['dir'];
        if(!$dir)
            return false;

        $data = [
            'expires' => time() + $ttl,
            'data' => $value,
        ];

        mkdir($dir, 0777, true);
        $file = fopen($dir . strval($key) . '.json.tmp', 'w');
        fwrite($file, json_encode($data));
        fclose($file);

        return true;
    }

    public static function fetch($key) {
        $dir = static::getConfig()['dir'];
        if(!$dir)
            return false;

        $data = json_decode(file_get_contents($dir . strval($key) . '.json.tmp'), true);

        if($data['expires'] < time()) {
            return false;
        } else {
            return $data['data'];
        }
    }
}
