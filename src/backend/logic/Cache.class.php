<?php

class Cache {
    public static function store($key, $value, $ttl) {
        $data = [
            'expires' => time() + $ttl,
            'data' => $value,
        ];

        mkdir(__DIR__ . '/../../runtime/cache/');
        $file = fopen(__DIR__ . '/../../runtime/cache/' . strval($key) . '.tmp', 'w');
        fwrite($file, json_encode($data));
        fclose($file);

        return false;
    }

    public static function fetch($key) {
        $data = json_decode(file_get_contents(__DIR__ . '/../../runtime/cache/' . strval($key) . '.tmp'), true);

        if($data['expires'] < time()) {
            return false;
        } else {
            return $data['data'];
        }
    }
}
