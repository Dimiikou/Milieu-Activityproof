<?php
require '../../common/php/config.php';
require '../../common/php/loginmanager.php';
require '../../common/php/mcapi.php';
require '../apiUtils.php';

$loginManager = new loginManager();
$apiUtils = new apiUtils();
$mcapi = new mcapi();

$passwort = $_GET['passwort'];
$member = $_GET['member'];
$firstTimePasswort = $_GET['firstTimePasswort'];
$dateInMillis = floor(microtime(true) * 1000);

if (isset($passwort) && isset($member) 
    && isset($firstTimePasswort)) {
    if (strcmp(config::$API_PASSWORD, $passwort) == 0) {
        if ($loginManager->checkUserExist($member) > 0) {
            $loginManager->setName($mcapi->getNameFromUUID($member), $member);
        } else {
            $date = $apiUtils->extractDate($dateInMillis);
            $loginManager->createUser($member, $firstTimePasswort, $date, 0);
            $loginManager->setName($mcapi->getNameFromUUID($member), $member);
        }
    }
}

?>