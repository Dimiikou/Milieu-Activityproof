<?php
session_start();
error_reporting(1);

require 'common/php/config.php';
require 'common/php/hostageManager.php';

$hostageManager = new hostageManager();

if (!isset($_SESSION['logged'])) {
    header('Location: https://lemilieu.de/index?accessdenied=true');
    exit;
}

if (!is_null($_GET['addWin'])) {
    if ($_SESSION['rank'] > 3) {
        $hostageManager->insertPfandnahme($_GET['addWin'], "win");
        header('Location: ./pfandnahme');
    }
}

if (!is_null($_GET['addLose'])) {
    if ($_SESSION['rank'] > 3) {
        $hostageManager->insertPfandnahme($_GET['addLose'], "lose");
        header('Location: ./pfandnahme');
    }
}

?>

<!doctype html>
<html lang="de">

<head>
    <?php include "common/php/head.php"; ?>
    <title>Le Milieu | Übersicht</title>
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
                <div class="card hoverable ">
                    <div class="card-content">
                        <h3 class="center">Geiselnahmen</h3>
                        <div class="center">
                            <?php
                            $fraktionen = array(
                                array(
                                    'name' => '<span class="green-text">La C</span>osa No<span class="red-text">stra</span>',
                                    'param' => 'mafia'
                                ),
                                array(
                                    'name' => 'Calderón Kartell',
                                    'param' => 'kartell',
                                    'color' => 'orange-text'
                                ),
                                array(
                                    'name' => 'Westside Ballas',
                                    'param' => 'gang',
                                    'color' => 'purple-text'
                                ),
                                array(
                                    'name' => 'Kerza<span class="blue-text">kov F</span><span class="red-text">amilie</span>',
                                    'param' => 'kerzakov'
                                ),
                                array(
                                    'name' => 'O\'Brien Familie',
                                    'param' => 'obrien',
                                    'color' => 'green-text'
                                ),
                                array(
                                    'name' => 'Staat',
                                    'param' => 'staat',
                                    'color' => 'blue-text'
                                )
                            );

                            foreach ($fraktionen as $fraktion) {
                                $name = $fraktion['name'];
                                $param = $fraktion['param'];
                                $color = isset($fraktion['color']) ? $fraktion['color'] : '';

                                echo '<span class="' . $color . '">' . $name . '</span>';
                                if ($_SESSION['rank'] > 3) {
                                    echo ' <a href="/pfandnahme?addWin=' . $param . '"><i class="material-icons green-text">add_box</i></a>';
                                    echo ' <a href="/pfandnahme?addLose=' . $param . '"><i class="material-icons red-text">indeterminate_check_box</i></a>';
                                }
                                echo '<br />';

                                $wins = $hostageManager->winsAgainstFaction($param);
                                $loses = $hostageManager->losesAgainstFaction($param);
                                $total = $wins + $loses;

                                if ($total > 0) {
                                    $winlose = number_format($wins / $total * 100, 2);
                                    echo '» ' . $wins . ' | ' . $loses . ' (' . $winlose . '%)';
                                } else {
                                    echo '» ' . $wins . ' | ' . $loses . ' (/)';
                                }

                                echo '<br /><br />';
                            }

                            echo '<span style="font-weight: bold;">Gesamt</span><br />';
                            $wins = $hostageManager->totalWins();
                            $loses = $hostageManager->totalLoses();
                            $total = $wins + $loses;

                            if ($total > 0) {
                                $winlose = number_format($wins / $total * 100, 2);
                                echo '» ' . $wins . ' | ' . $loses . ' (' . $winlose . '%)';
                            } else {
                                echo '» ' . $wins . ' | ' . $loses . ' (/)';
                            }
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php include "common/php/footer.php"; ?>
</body>

</html>