<?php
require_once __DIR__ . "/config.php";
require_once __DIR__ . "/Database.php";
use Counter\Database;

require_once "jpgraph/src/jpgraph.php";
require_once "jpgraph/src/jpgraph_line.php";

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

$hour_stats = Database::getInstance()->getVisitsByHour($select_url, $dateFrom, $dateTo);
if (count($hour_stats) < 2) {
    exit;
}
$min_time = strtotime($hour_stats[0]['hour_label']);
$max_time = strtotime($hour_stats[count($hour_stats)-1]['hour_label']);

//Заполняем данные для часов где не было посещений
$current = $min_time;
$hour_labels = [];
$hour_values = [];
$data_index = 0;
while ($current <= $max_time) {
    if (strtotime($hour_stats[$data_index]['hour_label']) == $current) {
        $hour_labels[] = $hour_stats[$data_index]['hour_label'];
        $hour_values[] = $hour_stats[$data_index]['visit_count'];
        $data_index++;
    } else {
        $hour_labels[] = date('d.m H:i', $current);
        $hour_values[] = 0;
    }
    $current = strtotime('+1 hour', $current);
}

$total_labels = count($hour_labels);
$text_tick_interval = 1; // По умолчанию, если меток мало

if ($total_labels > MAX_HOUR_LABELS) {
    $text_tick_interval = ceil($total_labels / MAX_HOUR_LABELS);
}

$time_graph = new Graph(800, 500);
$time_graph->SetScale("int", 0, max($hour_values) * 1.1);
$time_graph->title->Set("Статистика по времени");
$time_graph->xaxis->title->Set("Время");
$time_graph->xaxis->SetTickLabels($hour_labels);
$time_graph->xaxis->SetLabelAngle(90);
$time_graph->xaxis->SetTextTickInterval($text_tick_interval);
$time_graph->yaxis->title->Set("Посещения");

$lineplot = new LinePlot($hour_values);
$time_graph->add($lineplot);

$time_graph->Stroke();