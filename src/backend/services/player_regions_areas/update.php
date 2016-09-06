#!/usr/bin/php -q

<?php

require __DIR__ . '/../../../../vendor/autoload.php';

$settings = require(__DIR__ . '/../../../../config/player_regions_areas/config.php');

$log = new Monolog\Logger($settings['logger']['name']);
$log->pushProcessor(new Monolog\Processor\UidProcessor());
$log->pushHandler(new Monolog\Handler\RotatingFileHandler($settings['logger']['path'], 7, $settings['logger']['level']));

$log->info('Script started');

try {
    $db = $settings['db'];
    $pdo = new PDO('mysql:host=' . $db['host'] . ';dbname=' . $db['dbname'],
        $db['user'], $db['pass']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $log->critical('Caught PDO exception', $e);
    die();
}

$log->info('Starting downloading regions JSON file');

$curl = curl_init();
curl_setopt($curl, CURLOPT_URL, $settings['regions_file_url']);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 10);
curl_setopt($curl, CURLOPT_TIMEOUT, 15);
$regions_data_raw = curl_exec($curl);
curl_close($curl);

$log->info('Downloaded data: ' . strlen($regions_data_raw));

try {
    $regions_data = json_decode($regions_data_raw, true);
} catch (Exception $e) {
    $log->critical('Downloaded wrong data', $e);
    die();
}

$log->info('Downloaded data is correct JSON');

$log->info('Calculating areas');

$regions = $regions_data['sets']['players_houses']['areas'];

$result = array();

foreach($regions as $name => $region) {
    $label = $region['label'];
    $area = 0;

    $x = $region['x'];
    $z = $region['z'];

    $count = min(count($x), count($z));

    if($count == 2) {
        $area = ($x[1] - $x[0]) * ($z[1] - $z[0]);
    } else {
        for($i = 0; $i < $count; $i++) {
            $next = ($i >= $count - 1) ? 0 : $i + 1;

            $area += $x[$i] * $z[$next] - $x[$next] * $z[$i];
        }

        $area /= 2;
    }

    $area = abs($area);

    $result[] = [
        'name' => $name,
        'label' => $label,
        'area' => $area,
    ];
}

$log->info('Saving results in DB');

$pdo->beginTransaction();

// Clear table data
$sql = 'DELETE FROM `regions`;';
$pdo->exec($sql);

$sql = 'ALTER TABLE `regions` AUTO_INCREMENT = 1;';
$pdo->exec($sql);

// Fill new table data
$sql = 'INSERT INTO `regions`(`name`, `label`, `area`) VALUES (:name, :label, :area)';
$stmt = $pdo->prepare($sql);

foreach($result as $region) {
    $stmt->execute([
        ':name' => $region['name'],
        ':label' => $region['label'],
        ':area' => $region['area'],
    ]);
}

$pdo->commit();

$log->info('Done');
