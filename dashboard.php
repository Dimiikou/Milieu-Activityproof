<?php
session_start();
error_reporting(E_ALL);

require 'common/php/config.php';
require 'common/php/loginmanager.php';
require 'common/php/activityProofManager.php';
require 'common/php/logoutManager.php';
require 'common/php/valueManager.php';
require 'common/php/mcapi.php';

$login = new loginManager();
$activityProof = new activityProofMySQL();
$logoutManager = new logoutManager();
$valueManager = new valueManager();
$mcapi = new mcapi();

$members = $login->getMembers();
$logouts = $logoutManager->getLogouts();
$effectiveDrugRevenue = $activityProof->geteffectiveDrugRevenue();

$factionBankData = $valueManager->getFactionBank();

$lsd = $valueManager->getLSD();

$cocaine = $valueManager->getCocaine();
$weed = $valueManager->getWeed();
$meth = $valueManager->getMeth();
$lsd = $valueManager->getLSD();

if (!empty($_GET['deletelogout'])) {
    if (isset($_SESSION['rank']) && ($_SESSION['rank'] > 4) || (strcmp($_GET['member'], $_SESSION['uuid']) == 0)) {
        $logoutManager->deleteLogout($_GET['deletelogout']);
        header('Location: ./dashboard');
    } else {
        header('Location: https://lemilieu.de/index?notpermitted=true');
        exit;
    }
}

if (!isset($_SESSION['logged'])) {
    header('Location: https://lemilieu.de/index?accessdenied=true');
    exit;
}
?>

<!doctype html>
<html lang="de">

<head>
    <?php include "common/php/head.php"; ?>
    <title>Le Milieu | Übersicht</title>
    <link rel="stylesheet" href="common/css/materialize.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript">
        google.charts.load("current", {
            packages: ["corechart"]
        });
        google.charts.setOnLoadCallback(drawChart);

        function drawChart() {
            var activePlayersData = google.visualization.arrayToDataTable([
                ['Member', 'Geldeinnahmen'],
                <?php
                $moneyActivities = $activityProof->getMoneyActivities();
                if ($activityProof->numAllMonetaryIncome() > 0) {
                    while ($row = mysqli_fetch_assoc($moneyActivities)) {
                        echo "['" . $login->getName($row['memberID']) . "', " . $row['moneysum'] . "],";
                    }
                }
                echo "['', 0]";
                ?>
            ]);

            var activePlayersData2 = google.visualization.arrayToDataTable([
                ['Member', 'Drogeneinnahmen'],
                <?php
                $drugActivities = $activityProof->getDrugActivities();
                if ($activityProof->numAllDrugIncome() > 0) {
                    while ($row = mysqli_fetch_assoc($drugActivities)) {
                        echo "['" . $login->getName($row['memberID']) . "', " . $row['drugAmount'] . "],";
                    }
                }
                echo "['', 0]";
                ?>
            ]);

            var activePlayersData3 = google.visualization.arrayToDataTable([
                ['Member', 'Roleplays'],
                <?php
                $roleplayActivities = $activityProof->getRoleplayActivities();
                if ($activityProof->numAllRolePlays() > 0) {
                    while ($row = mysqli_fetch_assoc($roleplayActivities)) {
                        echo "['" . $login->getName($row['memberID']) . "', " . $row['roleplayAmount'] . "],";
                    }
                }
                echo "['', 0]";
                ?>
            ]);

            var options = {
                pieHole: 0.4,
                backgroundColor: 'none',
                legend: 'none',
                chartArea: {
                    top: 10,
                    right: 10,
                    bottom: 10,
                    left: 10
                },
                pieSliceBorderColor: 'transparent'
            };

            new google.visualization.PieChart(document.getElementById('donutchart')).draw(activePlayersData, options);
            new google.visualization.PieChart(document.getElementById('donutchart2')).draw(activePlayersData2, options);
            new google.visualization.PieChart(document.getElementById('donutchart3')).draw(activePlayersData3, options);
        }
    </script>
</head>

<body>

    <video autoplay muted loop id="backgroundVideo">
        <source src="common/img/UnicacityAddonWebsiteBackground.mp4" type="video/mp4">
    </video>

    <?php include "common/php/nav.php"; ?>


    <div class="aktinachweis-main-content" style="padding-top: 25vh;">
        <div class="container">
            <div class="row">
                <div class="card hoverable">
                    <div class="card-content">
                        <h2 class="center">Aktivitäten</h2>
                        <div class="row">
                            <div class="col s4 center">
                                <h4>Geldeinnahmen</h4>
                            </div>
                            <div class="col s4 center">
                                <h4>Drogeneinnahmen</h4>
                            </div>
                            <div class="col s4 center">
                                <h4>Roleplays</h4>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col s4 center">
                                <div id="donutchart" style="width: 300px; height: 300px"></div>
                            </div>
                            <div class="col s4 center">
                                <div id="donutchart2" style="width: 300px; height: 300px"></div>
                            </div>
                            <div class="col s4 center">
                                <div id="donutchart3" style="width: 300px; height: 300px"></div>
                            </div>
                        </div>
                        <div class="row">

                            <div class="col s4 center">
                                <?php if (strcmp($activityProof->getMoneyMVP(), "Noch niemand..") != 0) { ?>
                                    MVP | <?php echo $login->getName($activityProof->getMoneyMVP()); ?>
                                <?php   } else {
                                    echo 'MVP | Keiner';
                                }
                                ?>

                            </div>
                            <div class="col s4 center">
                                <?php if (strcmp($activityProof->getDrugMVP(), "Noch niemand..") != 0) { ?>
                                    MVP | <?php echo $login->getName($activityProof->getDrugMVP()); ?>
                                <?php   } else {
                                    echo 'MVP | Keiner';
                                }
                                ?>
                            </div>
                            <div class="col s4 center">
                                <?php if (strcmp($activityProof->getRoleplayMVP(), "Noch niemand..") != 0) { ?>
                                    MVP | <?php echo $login->getName($activityProof->getRoleplayMVP()); ?>
                                <?php   } else {
                                    echo 'MVP | Keiner';
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container aktinachweis-main-content">
        <div class="row">
            <div class="col s8">
                <div class="card hoverable">
                    <div class="card-content">
                        <h3 class="center">Abmeldungen <a href="/addlogout"><i class="material-icons green-text">add_box</i></a></h3>
                        <table class="highlight responsive-table">
                            <thead>
                                <th></th>
                                <th>Member</th>
                                <th>Von</th>
                                <th>Bis</th>
                                <th>Grund</th>
                            </thead>
                            <tbody>
                                <?php
                                if ($logoutManager->numLogouts() > 0) {
                                    while ($row = mysqli_fetch_assoc($logouts)) {
                                        $uuid = $login->getUUID($row['memberID']);

                                        echo '<tr>';
                                        echo '<td><img src="https://minotar.net/helm/' . $uuid . '/40.png" \></td>';
                                        echo '<td>' . $login->getName($row['memberID']);
                                        '</td>';
                                        echo '<td>' . $row['startDate'] . '</td>';
                                        echo '<td>' . $row['endDate'] . '</td>';
                                        echo '<td>' . $row['reason'] . '</td>';

                                        date_default_timezone_set("Europe/Amsterdam");
                                        $currentDate = date('d/m/Y', time());
                                        $logoutStart = date('d/m/Y', strtotime($row['startDate']));
                                        $logoutEnd = date('d/m/Y', strtotime($row['endDate']));

                                        if ($currentDate >= $logoutStart && $currentDate <= $logoutEnd) {
                                            echo '<td><span class="green-text darken-2">✔</span></td>';
                                        } else {
                                            echo '<td><span class="red-text darken-4">✘</span></td>';
                                        }

                                        if ($_SESSION['rank'] > 4 || (strcmp($uuid, $_SESSION['uuid']) == 0)) {
                                            echo '<td><a href="./dashboard?member=' . $uuid . '&deletelogout=' . $row['logoutID'] . '" \><span class="red-text darken-2"><i class="material-icons">delete</i></span></a></td>';
                                        }
                                        echo '</tr>';
                                    }
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="card hoverable">
                    <div class="card-content">
                        <h3 class="center">Drogenbank</h3>
                        <table class="highlight responsive-table">
                            <thead>
                                <th>Droge</th>
                                <th>Reinheit</th>
                                <th>Vorher</th>
                                <th>Nachher</th>
                                <th>Veränderung</th>
                            </thead>
                            <tbody>
                                <?php
                                function printDrugActivity($result)
                                {
                                    while ($row = mysqli_fetch_assoc($result)) {
                                        $valueBefore = $row['valueBefore'];
                                        $newValue = $row['newValue'];
                                        $change = $newValue - $valueBefore;

                                        echo '<tr>';
                                        echo '<td>' . $row['drugType'] . '</td>';
                                        echo '<td>' . $row['drugQuality'] . '</td>';
                                        echo '<td>' . number_format((float) $valueBefore, 0, ',', '.') . 'g</td>';
                                        echo '<td>' . number_format((float) $newValue, 0, ',', '.') . 'g</td>';
                                        if ($change < 0) {
                                            echo '<td class="red-text darken-2" style="font-weight: bold;">' . number_format((float) $change, 0, ',', '.') . 'g</td>';
                                        } else {
                                            echo '<td class="green-text darken-2" style="font-weight: bold;">+' . number_format((float) $change, 0, ',', '.') . 'g</td>';
                                        }
                                        echo '</tr>';
                                    }
                                }
                                ?>

                                <?php
                                printDrugActivity($cocaine);
                                echo '<tr><td></td><td></td><td></td><td></td><td></td></tr>';
                                printDrugActivity($weed);
                                echo '<tr><td></td><td></td><td></td><td></td><td></td></tr>';
                                printDrugActivity($meth);
                                echo '<tr><td></td><td></td><td></td><td></td><td></td></tr>';
                                printDrugActivity($lsd);
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
            <div class="col s4">
                <div class="card hoverable ">
                    <div class="card-content">
                        <h3 class="center">Drogenumsatz</h3>
                        <table class="highlight responsive-table">
                            <thead>
                                <th></th>
                                <th>Member</th>
                                <th>Umsatz</th>
                            </thead>
                            <tbody>
                                <?php
                                if ($activityProof->numEffectiveDrugRevenue() > 0) {
                                    while ($row = mysqli_fetch_assoc($effectiveDrugRevenue)) {
                                        $uuid = $login->getUUID($row['memberID']);
                                        $revenue = $row['drugRevenue'];

                                        echo '<tr>';
                                        echo '<td><img src="https://minotar.net/helm/' . $uuid . '/40.png" \></td>';
                                        echo '<td>' . $login->getName($row['memberID']);
                                        '</td>';
                                        if (!str_contains($revenue, "-")) {
                                            echo '<td><span class="green-text darken-2">' . $revenue . '</span></td>';
                                        } else {
                                            echo '<td><span class="red-text darken-4">' . $revenue . '</span></td>';
                                        }
                                        echo '</tr>';
                                    }
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="card hoverable ">
                    <div class="card-content">
                        <h3 class="center">F-Bank</h3>
                        <table class="highlight responsive-table">
                            <thead>
                                <th>Datum</th>
                                <th>Wert</th>
                                <th>Change</th>
                            </thead>
                            <tbody>
                                <?php
                                while ($row = mysqli_fetch_assoc($factionBankData)) {

                                    echo '<tr>';
                                    echo '<td>' . $row['date'] . '</td>';
                                    echo '<td>' . number_format((float) $row['value'], 0, ',', '.') . '$</td>';
                                    if (str_contains($row['change'], "-")) {
                                        echo '<td class="red-text darken-2" style="font-weight: bold;">' . number_format((float) $row['change'], 0, ',', '.') . '$</td>';
                                    } else {
                                        echo '<td class="green-text darken-2" style="font-weight: bold;">+' . number_format((float) $row['change'], 0, ',', '.') . '$</td>';
                                    }
                                    echo '</tr>';
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>

    <?php include "common/php/footer.php"; ?>
</body>

</html>