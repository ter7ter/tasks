<?php
require_once __DIR__ . "/config.php";
require_once __DIR__ . "/Database.php";
use Counter\Database;

session_start();
if (!isset($_SESSION['auth']) || !$_SESSION['auth']) {
    header('Location: login.php');
    exit;
}
$urls = Database::getInstance()->getUrls();
if (isset($_REQUEST['select_url'])) {
    $select_url = $_REQUEST['select_url'];
} else {
    $select_url= $urls[0]['url'];
}
// Валидация и получение дат
$start_date = $_REQUEST['start_date'] ?? null;
$end_date = $_REQUEST['end_date'] ?? null;

if ($start_date && $end_date) {
    // Если у нас есть корректные даты - берём их
    if (strtotime($start_date) === false) {
        $start_date = null;
    };
    if (strtotime($end_date) === false) {
        $end_date = null;
    }
}
if (is_null($start_date) || is_null($end_date)) {
    // Если нету дат - получаем крайние даты для данного url
    [$start_date, $end_date] = Database::getInstance()->getStartEndDates($select_url);
}
include __DIR__ . "/template/index.php";