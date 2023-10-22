<?php
require '../../common/php/config.php';
require '../../common/php/equipManager.php';
$equipManager = new equipManager();

$passwort = $_GET['passwort'];
$id = $_GET['id'];
$payed = $_GET['payed'];

if (isset($passwort) && isset($id) 
    && isset($payed)) {
    if (strcmp(config::$API_PASSWORD, $passwort) == 0) {
        $equipManager->changeEquip($id);
    }
}

?>