<?php
require '../../common/php/config.php';
require '../../common/php/activityProofManager.php';

$activityProofMySQL = new activityProofMySQL();

$passwort = $_GET['passwort'];
$member = $_GET['member'];
$drugType = $_GET['drugType'];
$drugPurity = $_GET['drugQuality'];
$drugAmount = $_GET['drugAmount'];

if (isset($passwort) && isset($member) && isset($drugType) 
    && isset($drugPurity) && isset($drugAmount)) {
    if (strcmp(config::$API_PASSWORD, $passwort) == 0) {
        $activityProofMySQL->insertDrugsUsed($member, $drugType, $drugPurity, $drugAmount);
    }
}

?>