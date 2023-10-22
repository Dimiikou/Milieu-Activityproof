<?php
session_start();
error_reporting(1);

require 'common/php/config.php';
require 'common/php/activityProofManager.php';
require 'common/php/mcapi.php';
require 'common/php/equipManager.php';
require 'common/php/loginmanager.php';

$activityProofMySQL = new activityProofMySQL();
$mcapi = new mcapi();
$equipManager = new equipManager();
$login = new loginManager();

$freeKevAmount = $login->getFreeKevs($_GET['member']);

$selectMonetaryActivitys = $activityProofMySQL->selectMonetaryIncomes($_GET['member']);
$selectDrugIncomeActivitys = $activityProofMySQL->selectDrugIncomes($_GET['member']);
$selectRolePlayActivitys = $activityProofMySQL->selectRoleplay($_GET['member']);
$selectEquipCosts = $equipManager->selectEquipCosts($_GET['member']);

if (!isset($_SESSION['logged'])) {
    header('Location: https://lemilieu.de/index?accessdenied=true');
    exit;
}

if ($login->checkUserExist($_GET['member']) == 0) {
    header('Location: https://lemilieu.de/index?memberdoesnotexist=true');
    exit;
}

if (!empty($_GET['deleteactivity'])) {
    if (isset($_SESSION['rank']) && ($_SESSION['rank'] > 3) || (strcmp($_GET['member'], $_SESSION['uuid']) == 0)) {
        $activityProofMySQL->deleteActivity($_GET['deleteactivity']);
        header('Location: ../activityproof/' . $_GET['member'] . '');
    } else {
        header('Location: https://lemilieu.de/index?notpermitted=true');
        exit;
    }
}

if (!empty($_GET['deleteequip'])) {
    if (isset($_SESSION['rank']) && ($_SESSION['rank'] > 3)) {
        $equipManager->deleteEquip($_GET['deleteequip']);
        header('Location: ../activityproof/' . $_GET['member'] . '');
    } else {
        header('Location: https://lemilieu.de/index?notpermitted=true');
        exit;
    }
}

if (!empty($_GET['useFreeKev'])) {
    if ((strcmp($_GET['member'], $_SESSION['uuid']) == 0) && $freeKevAmount > 0) {
        $login->useFreeKev($_GET['member'], $_GET['useFreeKev']);
        header('Location: ../activityproof/' . $_GET['member'] . '');
    } else {
        header('Location: https://lemilieu.de/index?notpermitted=true');
        exit;
    }
}

?>

<!doctype html>
<html lang="de">

<head>
    <?php include "common/php/head.php"; ?>
    <title>Le Milieu | <?php echo $mcapi->getNameFromUUID($_SESSION['uuid']); ?></title>
    <link rel="stylesheet" href="/common/css/materialize.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
</head>

<body>

    <video autoplay muted loop id="backgroundVideo">
        <source src="../common/img/UnicacityAddonWebsiteBackground.mp4" type="video/mp4">
    </video>

    <?php include "common/php/nav.php"; ?>

    <div class="container aktinachweis-main-content" style="height: 300px; padding-top: 8vh;">
        <div class="row" style="padding-top: 8vh;">
            <div class="col s6">
                <img class="right" src="https://minotar.net/helm/<?php echo $_GET['member'] ?>/150.png" \>
            </div>
            <div class="col s6">
                <h3 style="margin-top: 0px; margin-bottom: 5px;"><?php echo strtoupper($mcapi->getNameFromUUID($_GET['member'])); ?></h3>
                <h5 style="margin-top: 0px;">» <?php echo number_format($activityProofMySQL->getMoneyRevenue($_GET['member']), 0, ',', '.'); ?>$</h4>
                    <h5 style="margin-top: 0px;">» <?php echo number_format($activityProofMySQL->getDrugRevenue($_GET['member']), 0, ',', '.'); ?>g</h4>
                        <h5 style="margin-top: 0px;">» <?php echo number_format($activityProofMySQL->getRoleplayActivity($_GET['member']), 0, ',', '.'); ?> RPs</h4>
            </div>
        </div>
    </div>

    <div class="row container" style="padding-top: 4vh;">

        <!-- 
    
        Geldeinnahmen

    -->
        <div id="Geldeinnahmen" class="col s6">
            <div class="card hoverable">
                <div class="card-content">
                    <h4 class="center">Geldeinnahmen <a href="/entryactivity/addMoneyActivity"><i class="material-icons orange-text">add_box</i></a></h4>

                    <div class="row center">
                        <table class="highlight responsive-table">
                            <thead>
                                <tr>
                                    <th>Datum</th>
                                    <th>Typ</th>
                                    <th>Menge</th>
                                    <th>Screen</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if ($activityProofMySQL->numMonetaryIncome($_GET['member']) > 0) {
                                    $activityTypes = array(
                                        'blacklist' => 'Blacklist',
                                        'ausraub' => 'Ausraub',
                                        'menschenhandel' => 'M. Handel',
                                        'transport' => 'Transport',
                                        'autoverkauf' => 'Autoverkauf'
                                    );

                                    echo '<tbody>';

                                    while ($row = mysqli_fetch_assoc($selectMonetaryActivitys)) {
                                        $specializedType = $row['specialisedType'];
                                        if (array_key_exists($specializedType, $activityTypes)) {
                                            echo '<tr>';
                                            echo '<td>' . $row['date'] . '</td>';
                                            echo '<td>' . $activityTypes[$specializedType] . '</td>';
                                            echo '<td>' . number_format($row['value'], 0, ',', '.') . '$</td>';
                                            echo '<td><a href="' . $row['screenshot'] . '" target="_blank">*Klick*</a></td>';
                                            if ($_SESSION['rank'] > 3 || strcmp($_GET['member'], $_SESSION['uuid']) == 0) {
                                                echo '<td><a href="../activityproof?member=' . $_GET['member'] . '&deleteactivity=' . $row['activityID'] . '"><span class="red-text darken-2"><i class="material-icons">delete</i></span></a></td>';
                                            }
                                            echo '</tr>';
                                        }
                                    }

                                    echo '</tbody>';
                                    echo '</table>'; // Tabellenelement schließen
                                } else {
                                    echo '</tbody>';
                                    echo '</table>';
                                    echo '<h4 class="center">Noch keine Einträge vorhanden</h4>';
                                } ?>

                    </div>
                </div>
            </div>
        </div>

        <!-- 
    
        Drogeneinnahmen

    -->

        <div id="Drogeneinnahmen" class="col s6">
            <div class="card hoverable">
                <div class="card-content">
                    <h4 class="center">Drogeneinnahmen <a href="/entryactivity/addDrugActivity"><i class="material-icons orange-text">add_box</i></a></h4>

                    <div class="row center">
                        <table class="highlight responsive-table">
                            <thead>
                                <tr>
                                    <th>Datum</th>
                                    <th>Droge</th>
                                    <th>Reinheit</th>
                                    <th>Menge</th>
                                    <th>Screen</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if ($activityProofMySQL->numDrugIncome($_GET['member']) > 0) {
                                    while ($row = mysqli_fetch_assoc($selectDrugIncomeActivitys)) {
                                        echo '<tr>';
                                        echo '<td>' . $row['date'] . '</td>';
                                        echo '<td>' . $row['drugType'] . '</td>';
                                        echo '<td>' . $row['drugQuality'] . '</td>';
                                        echo '<td>' . $row['drugAmount'] . 'g</td>';
                                        echo '<td><a href="' . $row['screenshot'] . '" target="_blank" \>*Klick*</a></td>';

                                        if ($_SESSION['rank'] > 3 || (strcmp($_GET['member'], $_SESSION['uuid']) == 0)) {
                                            echo '<td><a href="../activityproof?member=' . $_GET['member'] . '&deleteactivity=' . $row['activityID'] . '" \><span class="red-text darken-2"><i class="material-icons">delete</i></span></a></td>';
                                        }

                                        echo '</tr>';
                                    }
                                    echo '</tbody>';
                                    echo '</table>';
                                } else {
                                    echo '</tbody>';
                                    echo '</table>';
                                    echo '<h4 class="center">Noch keine eingetragen</h4>';
                                } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 
    
    Aktivitätskarten Large-Display zweite Reihe

-->

    <div class="row container">
        <div id="Drogenverkauf" class="col s5">
            <div class="row">
                <div class="card hoverable">
                    <div class="card-content">
                        <h4 class="center">Roleplay <a href="/entryactivity/addRoleplayActivity"><i class="material-icons orange-text">add_box</i></a></h4>

                        <div class="row center">
                            <table class="highlight responsive-table">
                                <thead>
                                    <tr>
                                        <th>Datum</th>
                                        <th>Art</th>
                                        <th>screen</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    if ($activityProofMySQL->numRolePlays($_GET['member']) > 0) {
                                        while ($row = mysqli_fetch_assoc($selectRolePlayActivitys)) {
                                            echo '<tr>';
                                            echo '<td>' . $row['date'] . '</td>';
                                            echo '<td>' . $row['specialisedType'] . '</td>';
                                            echo '<td><a href="' . $row['screenshot'] . '" target="_blank" \>*Klick*</a></td>';

                                            if ($_SESSION['rank'] > 3 || (strcmp($_GET['member'], $_SESSION['uuid']) == 0)) {
                                                echo '<td><a href="../activityproof?member=' . $_GET['member'] . '&deleteactivity=' . $row['activityID'] . '" \><span class="red-text darken-2"><i class="material-icons">delete</i></span></a></td>';
                                            }

                                            echo '</tr>';
                                        }
                                        echo '</tbody>';
                                        echo '</table>';
                                    } else {
                                        echo '</tbody>';
                                        echo '</table>';
                                        echo '<h4 class="center">Noch keine eingetragen</h4>';
                                    }  ?>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="card hoverable">
                    <div class="card-content">
                        <h4 class="center">Banner » <?php echo $activityProofMySQL->getBanner($_GET['member']); ?></h4>
                    </div>
                </div>
            </div>
        </div>

        <div id="Equipkosten" class="col s7">
            <div class="card hoverable">
                <div class="card-content">
                    <h4 class="center">Equipkosten » <span class="green-text"><?php echo $freeKevAmount ?></span> Free Kevs</h4>

                    <div class="row center">
                        <table class="highlight responsive-table">
                            <thead>
                                <tr>
                                    <th>Id</th>
                                    <th>Item</th>
                                    <th>Preis</th>
                                    <th>Bezahlt</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if ($equipManager->numEquipCosts($_GET['member']) > 0) {
                                    while ($row = mysqli_fetch_assoc($selectEquipCosts)) {
                                        echo '<tr>';
                                        echo '<td>' . $row['id'] . '</td>';
                                        echo '<td>' . $row['item'] . '</td>';
                                        echo '<td>' . number_format($row['price'], 0, ',', '.') . '$</td>';
                                        if (strcmp('false', $row['payed']) == 0) {
                                            echo '<td><span class="red-text darken-4">✘</span></td>';
                                        } else {
                                            echo '<td><span class="green-text darken-2">✔</span></td>';
                                        }

                                        echo '<td><a href="../activityproof?member=' . $_GET['member'] . '&useFreeKev=' . $row['id'] . '" \><span class="green-text"><i class="material-icons">child_care</i></span></a></td>';
                                        if ($_SESSION['rank'] > 4) {
                                            echo '<td><a href="../activityproof?member=' . $_GET['member'] . '&deleteequip=' . $row['id'] . '" \><span class="red-text darken-2"><i class="material-icons">delete</i></span></a></td>';
                                        }

                                        echo '</tr>';
                                    }
                                    echo '</tbody>';
                                    echo '</table>';
                                } else {
                                    echo '</tbody>';
                                    echo '</table>';
                                    echo '<h4 class="center">Noch keine eingetragen</h4>';
                                }  ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include "common/php/footer.php"; ?>
    <script src="assets/js/materialize.js"></script>

    <script>
        let url_string = window.location.href;
        let url = new URL(url_string);
        let login = url.searchParams.get("login");

        if (login) {
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer)
                    toast.addEventListener('mouseleave', Swal.resumeTimer)
                }
            })

            Toast.fire({
                icon: 'success',
                title: 'Angemeldet'
            })
        }
    </script>

</body>

</html>