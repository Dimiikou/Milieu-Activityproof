<?php
require '../../common/php/config.php';
require '../../common/php/loginmanager.php';
require '../../common/php/mcapi.php';

$loginManager = new loginManager();
$mcapi = new mcapi();

$passwort = $_GET['passwort'];

if (isset($passwort)) {
    if (strcmp(config::$API_PASSWORD, $passwort) == 0) {
        $members = $loginManager->getMembers();

        while ($row = mysqli_fetch_assoc($members)) {
            if (!strcmp($row['UUID'], "dd9b550e15714a2180f49166262c9140") == 0) {
                $loginManager->setName($mcapi->getNameFromUUID($row['UUID']), $row['UUID']);
            }
        }
    }
}

?>