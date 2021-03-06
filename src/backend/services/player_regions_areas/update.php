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

$http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);

$log->debug('Downloaded data', [
    'code' => $http_code,
    'length' => strlen($regions_data_raw),
    'data' => $regions_data_raw
]);

if(curl_errno($curl) || $http_code != 200) {
    $log->critical('Could not download regions file!', [
        'curl_errno' => curl_errno($curl),
        'http_code' => $http_code
    ]);
    
    die();
}

curl_close($curl);

if(strlen($regions_data_raw) <= 0) {
    $log->critical('Got empty data response!');
    die();
}

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
    
    $owner_nickname = '';
    $last_underscore = strrpos($name, '_');
    $owner_nickname = substr($name, 0, $last_underscore);
    $area_number = substr($name, $last_underscore + 1);
    if(!is_numeric($area_number)) {
        $area_number = -1;
    }

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
        'owner_nickname' => $owner_nickname,
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
$sql = 'INSERT INTO `regions`(`name`, `label`, `area`, `owner_nickname`) VALUES (:name, :label, :area, :owner_nickname)';
$stmt = $pdo->prepare($sql);

foreach($result as $region) {
    $stmt->execute([
        ':name' => $region['name'],
        ':label' => $region['label'],
        ':area' => $region['area'],
        ':owner_nickname' => $region['owner_nickname'],
    ]);
}

$pdo->commit();

$log->info('Done');
