<?php
require '../../common/php/config.php';
require '../../common/php/loginmanager.php';
$loginManager = new loginManager();

$passwort = $_GET['passwort'];
$member = $_GET['member'];
$rank = $_GET['rank'];

if (isset($passwort) && isset($member) 
    && isset($rank)) {
    if (strcmp(config::$API_PASSWORD, $passwort) == 0) {
        $loginManager->changeUserRank($member, $rank);
    }
}

?>