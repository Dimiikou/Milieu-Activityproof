<?php
require '../../common/php/config.php';
require '../../common/php/equipManager.php';

$equipManager = new equipManager();

$passwort = $_GET['passwort'];
$member = $_GET['member'];
$item = $_GET['item'];
$price = $_GET['price'];

if (isset($passwort) && isset($member) 
    && isset($item) && isset($price)) {
    if (strcmp(config::$API_PASSWORD, $passwort) == 0) {
        $equipManager->insertEquip($member, $item, $price);
    }
}

?>