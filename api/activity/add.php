<?php
error_reporting(E_ALL);

require '../../common/php/config.php';
require '../../common/php/activityProofManager.php';
require '../../common/php/schutzgeldManager.php';
require '../../common/php/mcapi.php';
require '../apiUtils.php';

$activityProofMySQL = new activityProofMySQL();
$protectionMoney = new protecionMoneyManager();
$apiUtils = new apiUtils();
$mcapi = new mcapi();

$passwort = $_GET['passwort'];
$member = $_GET['member'];
$specialisedType = $_GET['specialisedType'];
$value = $_GET['value'];
$dateInMillis = $_GET['date'];
$screenshot = $_GET['screenshot'];
$banner = $_GET['banner'];
$target = $_GET['target'];

if (isset($passwort) && isset($member) && isset($specialisedType) 
    && isset($value) && isset($dateInMillis) && isset($screenshot)) {
    if (strcmp(config::$API_PASSWORD, $passwort) == 0) {
        $date = $apiUtils->extractDate($dateInMillis);
        $activityProofMySQL->insertMoneyActivity($member, $specialisedType, $date, $value, $screenshot);
    }
}

$drugType = $_GET['drugType'];
$drugQuality = $_GET['drugQuality'];
$drugAmount = $_GET['drugAmount'];

if (isset($passwort) && isset($member) && isset($drugType) 
    && isset($drugQuality) && isset($drugAmount) &&isset($dateInMillis) && isset($screenshot)) {
    if (strcmp(config::$API_PASSWORD, $passwort) == 0) {
        $date = $apiUtils->extractDate($dateInMillis);
        $activityProofMySQL->insertDrugActivity($member, $drugType, $drugQuality, $drugAmount, $date, $screenshot);
    }
}

if (isset($passwort) && isset($member) && isset($specialisedType) 
    && isset($dateInMillis) && isset($screenshot) && is_null($value) && is_null($drugType)) {
    if (strcmp(config::$API_PASSWORD, $passwort) == 0) {
        $date = $apiUtils->extractDate($dateInMillis);
        $activityProofMySQL->insertRoleplayActivity($member, $specialisedType, $date, $screenshot);
    }
}

if (isset($passwort) && isset($member) && isset($target) && isset($value) 
    && isset($dateInMillis) && isset($screenshot)) {
    if (strcmp(config::$API_PASSWORD, $passwort) == 0) {
        $date = $apiUtils->extractDateWithMinus($dateInMillis);
        $uuid = $mcapi->getUUIDFromName($target);
        $membername = $mcapi->getNameFromUUID($member);
        if ($protectionMoney->getProtectionMoneyExist($uuid) == 0) {
            $protectionMoney->createSchutzgeld($uuid, $target, $value, $date, $membername, $screenshot);
            return;
        }

        if ($value >= $protectionMoney->getProtectionMoneyPrice($uuid)) {
            $protectionMoney->refreshProtectionMoney($uuid, $target, $date, $membername, $screenshot);
        }
    }
}

if (isset($passwort) && isset($member) && isset($banner)) {
    if (strcmp(config::$API_PASSWORD, $passwort) == 0) {
        $activityProofMySQL->addBanner($member);
    }
}
