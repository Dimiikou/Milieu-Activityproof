<?php
session_start();
error_reporting(E_ALL);
require 'common/php/config.php';
require 'common/php/loginmanager.php';
require 'common/php/schutzgeldManager.php';
require 'common/php/mcapi.php';

$login = new loginManager();
$mcapi = new mcapi();

$protectionManager = new protecionMoneyManager();

$protectionMoney = $protectionManager->getProtectionMoney();

if (!isset($_SESSION['logged'])) {
    header('Location: https://lemilieu.de/index?accessdenied=true');
    exit;
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
            <?php
            $schutzgeldAmount = $protectionManager->getProtectionMoneyAmount();
            $firstLoop = ceil($schutzgeldAmount / 3);
            $schutzgelder = array();
            $i4 = 0;
            $i = 0;
            
            while ($row = mysqli_fetch_assoc($protectionMoney)) {
                $schutzgelder[$i] = array('uuid' => $row['UUID'], 'name' => $row['name'], 'price' => $row['price'], 'lastPayed' => $row['lastPayed'], 'payed' => $row['payed'], 'member' => $row['lastMember'], 'screenshot' => $row['lastScreen']);
                $i++;
            }

            for ($i2 = 0; $i2 < $firstLoop; $i2++) {
                echo '<div class="row">';

                for ($i3 = 0; $i3 < 3; $i3++) {
                    echo '<div class="col s4">';
                    echo '<div class="card hoverable ">';
                    echo '<div class="card-content">';
                    echo '<div class="center">';
                    echo '<img src="https://minotar.net/helm/' . $schutzgelder[$i4]['uuid'] . '/150.png" \>';
                    echo '<h3>' . $schutzgelder[$i4]['name'] . '</h3>';
                    echo ' » ' . number_format($schutzgelder[$i4]['price'], 0, ',', '.') . '$ <br />';
                    echo '» ' . $schutzgelder[$i4]['lastPayed'] . ' <br />';
                    echo ' » ' . $schutzgelder[$i4]['member'] . '<br />';
                    echo '» <a href="' . $schutzgelder[$i4]['screenshot'] . '" target="_blank" \>*Klick*</a> <br />  <br />';
                    $payed = $schutzgelder[$i4]['payed'];

                    if (strcmp($payed, "bezahlt") == 0) {
                        echo '<span class="green-text">Bezahlt</span>';
                    } else if (strcmp($payed, "ausstehend") == 0) {
                        echo '<span class="orange-text">Ausstehend</span>';
                    } else {
                        echo '<span class="red-text">Abgelaufen</span>';
                    }

                    echo '</div>';
                    echo '</div>';
                    echo '</div>';
                    echo '</div>';
                    $i4++;
                    if ($i4 == $schutzgeldAmount) break;
                }

                echo '</div>';
            }
            ?>

        </div>
    </div>
    <?php include "common/php/footer.php"; ?>

</body>

</html>