<?php

require __DIR__ . '/../../../../vendor/autoload.php';

$settings = require(__DIR__ . '/../../../../config/clans_cells/config.php');

$log = new Monolog\Logger($settings['logger']['name']);
$log->pushProcessor(new Monolog\Processor\UidProcessor());
$log->pushHandler(new Monolog\Handler\RotatingFileHandler($settings['logger']['path'], 7, $settings['logger']['level']));


$log->pushHandler(new Monolog\Handler\ErrorLogHandler());


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

$log->info('Downloaded data: ' . strlen($regions_data_raw) . ' bytes');

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

$cells = $regions_data['sets']['Clans']['areas'];

if(!$cells) {
    echo("\$cells is null!\n");
}

$clans = [];

foreach($cells as $cell_id => $cell_data) {
    $cell_label = $cell_data['label'];

    $clan_name_start = strpos($cell_label, '(');
    $clan_name_end = strpos($cell_label, ')');

    if($clan_name_start === false || $clan_name_end === false)
        continue;

    $clan_name_length = $clan_name_end - $clan_name_start;

    $clan_name = substr($cell_label, $clan_name_start + 1, $clan_name_length - 1);
    
    if(strtolower($clan_name) !== 'free') {
        if(array_key_exists($clan_name, $clans)) {
            $clans[$clan_name]++;
        } else {
            $clans[$clan_name] = 1;
        }
    }
}

arsort($clans, SORT_NUMERIC);

$rows = [];

$i = 0;
foreach($clans as $name => $cell_count) {
    $rows[] = [
        'order' => $i,
        'name' => $name,
        'cell_count' => $cell_count,
        'is_in_council' => false,
    ];

    $i++;
}

$cell_counts = [];

for($i = 0; $i < count($rows); $i++) {
    $row = $rows[$i];

    if(count($cell_counts) < 5) {
        if(in_array($rows[$i]['cell_count'], $cell_counts)) {
            $row['is_in_council'] = true;
        } else {
            $row['is_in_council'] = true;
            $cell_counts[] = $row['cell_count'];
        }
    } else {
        if(in_array($rows[$i]['cell_count'], $cell_counts)) {
            $row['is_in_council'] = true;
        }
    }

    $rows[$i] = $row;
}

$log->info('Extracted clan cell counts');
$log->info('Saving results in DB');

$pdo->beginTransaction();

// Clear table data
$sql = 'DELETE FROM `clans`;';
$pdo->exec($sql);

$sql = 'ALTER TABLE `clans` AUTO_INCREMENT = 1;';
$pdo->exec($sql);

// Fill new table data
$sql = 'INSERT INTO `clans`(`order`, `name`, `cell_count`, `is_in_council`) VALUES (:order, :name, :cell_count, :is_in_council)';
$stmt = $pdo->prepare($sql);

foreach($rows as $row) {
    $stmt->execute([
        ':order' => $row['order'],
        ':name' => $row['name'],
        ':cell_count' => $row['cell_count'],
        'is_in_council' => $row['is_in_council'] ? 1 : 0,
    ]);
}

$pdo->commit();

$log->info('Done');