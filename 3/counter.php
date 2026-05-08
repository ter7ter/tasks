<?php
// CORS headers — разрешаем кросс-доменные запросы с любого источника
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json; charset=utf-8");

// Preflight-запрос OPTIONS — браузер отправляет его перед POST на другой домен
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

require_once __DIR__ . "/config.php";
require_once __DIR__ . "/Database.php";
use Counter\Database;

if (!isset($_REQUEST["url"])) {
    // Не передана станица, для которой считаем посещение
    echo json_encode([
            "success" => false,
            "err" => 'Page url not found',
        ]);
    exit;
}

$ip = $_SERVER['HTTP_CLIENT_IP']
    ?? $_SERVER['HTTP_X_FORWARDED_FOR']
    ?? $_SERVER['REMOTE_ADDR'];
$userAgent = $_SERVER['HTTP_USER_AGENT'];

Database::getInstance()->saveVisit(
    $ip,
    htmlspecialchars($_REQUEST['city']) ?? null,
    htmlspecialchars($_REQUEST['country']) ?? null,
    $userAgent,
    htmlspecialchars($_REQUEST['url']),
);

echo json_encode(["success" => true]);
