<?php
require_once __DIR__ . '/config.php';

$errorAuth = false;
if (isset($_REQUEST['login']) && isset($_REQUEST['password'])) {
    if ($_REQUEST['login'] == LOGIN && $_REQUEST['password'] == PASSWORD) {
        session_start();
        $_SESSION['auth'] = true;
        header('Location: index.php');
    } else {
        $errorAuth = true;
    }
}
include __DIR__ . "/template/login.php";

