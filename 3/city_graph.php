<?php
require_once __DIR__ . "/config.php";
require_once __DIR__ . "/Database.php";
use Counter\Database;

require_once __DIR__ . "/jpgraph/src/jpgraph.php";
require_once __DIR__ . "/jpgraph/src/jpgraph_pie.php";

if (!isset($_REQUEST['url'])) {
    exit;
}
$select_url = $_REQUEST['url'];
session_start();
if (!isset($_SESSION['auth']) || !$_SESSION['auth']) {
    header('Location: login.php');
    exit;
}

$dateFrom = $_REQUEST['date_from'] ?? null;
$dateTo = $_REQUEST['date_to'] ?? null;

// Проверяем даты на корректность
if ($dateFrom && strtotime($dateFrom) === false) {
    $dateFrom = null;
}
if ($dateTo && strtotime($dateTo) === false) {
    $dateTo = null;
}

$city_stats = Database::getInstance()->getStatByCity($select_url, $dateFrom, $dateTo);
if (count($city_stats) < 1) {
    exit;
}
if (count($city_stats) > MAX_CITIES_COUNT) {
    $other_stats = array_slice($city_stats, MAX_CITIES_COUNT - 1);
    $city_stats = array_slice($city_stats, 0, MAX_CITIES_COUNT - 1);
    $other_count = 0;
    foreach ($other_stats as $stat) {
        $other_count += $stat['count_city'];
    }
    $city_stats[] = [
        'city' => '<Other>',
        'count_city' => $other_count,
    ];
}
$city_values = array_column($city_stats, 'count_city');
$city_labels = array_column($city_stats, 'city');

$city_graph = new PieGraph(550, 321);
$city_graph->title->Set("График посещений по городам");

$pieplot = new PiePlot($city_values);
$pieplot->SetLegends($city_labels);

$city_graph->add($pieplot);
$city_graph->legend->SetPos(0.5,0.97,'center','bottom');

$city_graph->Stroke();