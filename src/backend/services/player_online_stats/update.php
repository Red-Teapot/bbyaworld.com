<?php

require __DIR__ . '/../../../../vendor/autoload.php';

require_once(__DIR__ . '/../../../logic/MCServerQuery.class.php');

$settings = require(__DIR__ . '/../../../../config/player_online_stats/config.php');

$log = new Monolog\Logger($settings['logger']['name']);
$log->pushProcessor(new Monolog\Processor\UidProcessor());
$log->pushHandler(new Monolog\Handler\RotatingFileHandler($settings['logger']['path'], 7, $settings['logger']['level']));

$log->info("Script started");

$log->info("Getting player list");

$serverQuery = new MCServerQuery($log);

$log->info("Created server query");
$log->info("Getting players");

$players = $serverQuery->getPlayers($settings["server"]["address"], $settings["server"]["port"], 5);

if(!$players) {
    $log->error("Could not get players");
    die();
}

$log->debug("Current players: " . count($players), $players);

$log->info("Resolving UUIDs");

$data = json_encode($players);
$curl = curl_init();
curl_setopt($curl, CURLOPT_URL, $settings["mojangApiUrl"]);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl, CURLOPT_POST, true);
curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 10);
curl_setopt($curl, CURLOPT_TIMEOUT, 15);
curl_setopt($curl, CURLOPT_HTTPHEADER, array(
    'Content-Type: application/json',
    'Content-Length: ' . strlen($data))
);
$api_response = curl_exec($curl);
curl_close($curl);
$response_decoded = json_decode($api_response, true);
$player_uuids = array();
foreach($response_decoded as $player) {
    $player_uuids[$player['name']] = $player['id'];
}

$log->debug("Resolved " . count($player_uuids) . " UUIDs", $player_uuids);

$log->info("Opening connection to DB");

try {
    $db = $settings['db'];
    $pdo = new PDO("mysql:host=" . $db['host'] . ";dbname=" . $db['dbname'],
        $db['user'], $db['pass']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $log->critical("Caught PDO exception", $e);
}

$statement = $pdo->prepare("INSERT INTO online_stats(uuid, nickname, time) VALUES(:uuid, :nickname, :interval) ON DUPLICATE KEY UPDATE nickname=:nickname, time=time+:interval");

$log->info("Updating player stats");

foreach($players as $name) {
    if(empty($name))
        continue;

    $uuid = $player_uuids[$name];

    $statement->bindParam(':uuid', $uuid);
    $statement->bindParam(':nickname', $name);
    $statement->bindParam(':interval', $settings['updateInterval']);

    $statement->execute();
}

$log->info("Closing connections");

$log->info("Done");
