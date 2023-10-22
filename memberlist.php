<?php
session_start();
error_reporting(1);

require 'common/php/config.php';
require 'common/php/loginmanager.php';
require 'common/php/activityProofManager.php';
require 'common/php/mcapi.php';
require 'common/php/settingsManager.php';
require 'common/php/equipManager.php';

$login = new loginManager();
$activityProofMySQL = new activityProofMySQL();
$mcapi = new mcapi();
$settings = new settingsManager();
$equipManager = new equipManager();

if (!isset($_SESSION['logged'])) {
    header('Location: https://lemilieu.de/index?accessdenied=true');
    exit;
}

if (isset($_GET['member']) && isset($_GET['addFreeKev']) || isset($_GET['deletemember'])) {
    if ($_SESSION['rank'] > 4) {
        if (isset($_GET['addFreeKev'])) {
            $login->addFreeKev($_GET['member']);
        }

        if (isset($_GET['deletemember'])) {
            $login->removeUser($_GET['deletemember']);
            $equipManager->deleteAdditionalCostViaUUID($_GET['deletemember']);
        }
        
    }
}

$ranks = array(
    0 => $settings->getPrefix("rankZero"),
    1 => $settings->getPrefix("rankOne"),
    2 => $settings->getPrefix("rankTwo"),
    3 => $settings->getPrefix("rankThree"),
    4 => $settings->getPrefix("rankFour"),
    5 => $settings->getPrefix("rankFive"),
    6 => $settings->getPrefix("rankSix")
);

$drugs = array();
$money = array();
$roleplay = array();

for ($i = 0; $i <= 6; $i++) {
    $drugs[$i] = $settings->getMinimumDrugs($i);
    $money[$i] = $settings->getMinimumMoney($i);
    $roleplay[$i] = $settings->getMinimumRoleplay($i);
}

$activityProofs = array();

for ($i = 0; $i <= 6; $i++) {
    $activityProofs[$i] = $activityProofMySQL->getActivityProofs($i);
}
?>

<!doctype html>
<html lang="de">

<head>
    <?php include "common/php/head.php"; ?>
    <title>Le Milieu | Memberlist</title>
    <link rel="stylesheet" href="/common/css/materialize.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
</head>

<body>

    <video autoplay muted loop id="backgroundVideo" style="filter: brightness(15%) saturate(150%);">
        <source src="common/img/UnicacityAddonWebsiteBackground.mp4" type="video/mp4">
    </video>

    <?php include "common/php/nav.php"; ?>

    <div class="row container aktinachweis-main-content" style="padding-top: 10vh;">
        <div class="col s12">

            <?php

            function generateRankTable($rankTitle, $rankData, $activityProofMySQL, $equipManager, $drugs, $roleplay, $money)
            {
                echo '<h4 class="center white-text text-darken-4">' . $rankTitle . '</h4>';
                echo '<table class="highlight responsive-table">';
                echo '<thead>';
                echo '<tr>';
                echo '<th></th>';
                echo '<th>Name</th>';
                echo '<th>Geldeinnahmen</th>';
                echo '<th>Drogeneinnahmen</th>';
                echo '<th>Equip</th>';
                echo '<th>Roleplay</th>';
                echo '<th>Banner</th>';
                echo '</tr>';
                echo '</thead>';
                echo '<tbody>';
                if ($activityProofMySQL->numActivityProofs($rankData['rank']) > 0) {
                    while ($row = mysqli_fetch_assoc($rankData['data'])) {
                        echo '<tr>';
                        echo '<td><img src="https://minotar.net/helm/' . $row['UUID'] . '/40.png" \></td>';
                        echo '<td>' . $row['name'] . '</td>';
                        echo '<td>' . number_format($activityProofMySQL->getMoneyRevenue($row['UUID']), 0, ',', '.') . '$</td>';
                        echo '<td>' . number_format($activityProofMySQL->getDrugRevenue($row['UUID']), 0, ',', '.') . 'g</td>';
                        echo '<td>' . number_format($equipManager->getEquipCosts($row['UUID']), 0, ',', '.') . '$</td>';
                        echo '<td>' . number_format($activityProofMySQL->getRoleplayActivity($row['UUID']), 0, ',', '.') . ' RPs</td>';
                        echo '<td>' . number_format($activityProofMySQL->getBanner($row['UUID']), 0, ',', '.') . '</td>';
                        if ((intval($activityProofMySQL->getDrugRevenue($row['UUID'])) >= intval($drugs[$rankData['rank']]))
                            && (intval($activityProofMySQL->getRoleplayActivity($row['UUID'])) >= intval($roleplay[$rankData['rank']]))
                            && (intval($activityProofMySQL->getMoneyRevenue($row['UUID'])) >= intval($money[$rankData['rank']]))
                        ) {
                            echo '<td><span class="green-text darken-2">✔</span></td>';
                        } else {
                            echo '<td><span class="red-text darken-4">✘</span></td>';
                        }

                        echo '<td><a href="../activityproof/' . $row['UUID'] . '" \><i class="material-icons">assignment</i></a></td>';
                        if ($_SESSION['rank'] > 4) {
                            echo '<td><a href="./editmember?member=' . $row['UUID'] . '" \><i class="material-icons orange-text">edit</i></a></td>';
                            echo '<td><a href="./memberlist?member=' . $row['UUID'] . '&addFreeKev=true" \><i class="material-icons green-text">add_box</i></a></td>';
                            echo '<td></td>';
                            echo '<td><a href="./memberlist?deletemember=' . $row['UUID'] . '" \><span class="red-text darken-2"><i class="material-icons">delete</i></span></a></td>';
                        }
                        echo '</tr>';
                    }
                    echo '</tbody>';
                    echo '</table>';
                } else {
                    echo '</tbody>';
                    echo '</table>';
                    echo '<h4 class="center">Keine Nachweise vorhanden</h4>';
                }
            }

            for ($i = 6; $i >= 0; $i--) {
                generateRankTable($ranks[$i], array('rank' => $i, 'data' => $activityProofs[$i]), $activityProofMySQL, $equipManager, $drugs, $roleplay, $money);
            }

            ?>
        </div>
    </div>

    <?php include "common/php/footer.php"; ?>

</body>

</html>