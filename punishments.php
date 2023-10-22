<?php
session_start();
error_reporting(1);

require './common/php/config.php';
require './common/php/equipManager.php';
require './common/php/loginmanager.php';

$equip = new equipManager();
$login = new loginManager();

$equipCosts = $equip->getEquipAdditionalCosts();

if (!isset($_SESSION['logged'])) {
    header('Location: https://lemilieu.de/index?accessdenied=true');
    exit;
}

if (!is_null($_GET['deleteEquip']) && $_SESSION['rank'] > 4) {
    $equip->deleteAdditionalCost($_GET['deleteEquip']);
    header('Location: ./punishments');
}
?>

<!doctype html>
<html lang="de">

<head>
    <?php include "common/php/head.php"; ?>
    <title>Le Milieu | Aktinachweis</title>
    <link rel="stylesheet" href="common/css/materialize.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
</head>

<body>

    <video autoplay muted loop id="backgroundVideo">
        <source src="common/img/UnicacityAddonWebsiteBackground.mp4" type="video/mp4">
    </video>

    <?php include "common/php/nav.php"; ?>

    <div class="aktinachweis-main-content" style="padding-top: 25vh;">
        <div class="container">
            <div class="row">
                <div class="card hoverable col s5">
                    <div class="card-content">
                        <h2 class="center">Nachzahlungen</h2>
                        <table>
                            <thead>
                                <th></th>
                                <th>Member</th>
                                <th>Menge</th>
                            </thead>
                            <tbody>
                                <?php
                                while ($row = mysqli_fetch_assoc($equipCosts)) {
                                    $memberID = $row['memberID'];
                                    $uuid = $login->getUUID($memberID);

                                    if ($login->getRank($uuid) < 5) {
                                        echo '<tr></tr>';
                                        echo '<td><img src="https://minotar.net/helm/' . $uuid . '/40.png" \></td>';
                                        echo '<td>' . $login->getName($memberID) . '</td>';
                                        echo '<td>' . number_format($row['price'], 0, ',', '.') . '$</td>';
                                        if ($_SESSION['rank'] > 4) {
                                            echo '<td><a href="./punishments?deleteEquip=' . $row['id'] . '"><span class="red-text darken-2"><i class="material-icons">delete</i></span></a></td>';
                                        }
                                        echo '<tr></tr>';
                                    }
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include "common/php/footer.php"; ?>

</body>

</html>