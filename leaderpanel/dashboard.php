<?php
session_start();
error_reporting(1);

require '../common/php/config.php';
require '../common/php/loginmanager.php';
require '../common/php/settingsManager.php';
require '../common/php/activityProofManager.php';
require '../common/php/equipManager.php';
require '../common/php/mcapi.php';

$activityProofMySQL = new activityProofMySQL();
$settings = new settingsManager();
$login = new loginManager();
$equip = new equipManager();
$mcapi = new mcapi();

$rankPrefixes = array(
    'zero' => $settings->getPrefix('rankZero'),
    'one' => $settings->getPrefix('rankOne'),
    'two' => $settings->getPrefix('rankTwo'),
    'three' => $settings->getPrefix('rankThree'),
    'four' => $settings->getPrefix('rankFour'),
    'five' => $settings->getPrefix('rankFive'),
    'six' => $settings->getPrefix('rankSix')
);

$rankDrugs = array();
$rankMoney = array();
$rankRoleplay = array();

for ($i = 0; $i <= 6; $i++) {
    $rankDrugs[$i] = $settings->getMinimumDrugs($i);
    $rankMoney[$i] = $settings->getMinimumMoney($i);
    $rankRoleplay[$i] = $settings->getMinimumRoleplay($i);
}

$rankPrefixes = array_map(function ($prefix) {
    return str_replace("%s", "'", $prefix);
}, $rankPrefixes);

if (!isset($_SESSION['logged']) || intval($_SESSION['rank']) < 5) {
    header('Location: https://lemilieu.de/index?accessdenied=true');
    exit;
}

$redirect = false;

if (intval($_SESSION['rank']) > 4) {
    if (!empty($_GET['addevent'])) {
        //$settings->addEvent();
        $redirect = true;
    }

    if (!empty($_GET['removeevent'])) {
        //$settings->removeEvent();
        $redirect = true;
    }

    if (!empty($_GET['changephase'])) {
        $phase = $_GET['changephase'];
        if (strcmp('offen', $phase) == 0) {
            $settings->changePhase("geöffnet");
        } elseif (strcmp('warteliste', $phase) == 0) {
            $settings->changePhase("warteliste");
        } elseif (strcmp('geschlossen', $phase) == 0) {
            $settings->changePhase("geschlossen");
        }
        $redirect = true;
    }

    if (!empty($_GET['clearactivitytests'])) {
        $activityProofMySQL->clearActivityProof();
        $login->resetBanner();
        $equip->clearEquip();
        $redirect = true;
    }
}

if ($redirect) header('Location: ./dashboard');

?>

<!doctype html>
<html lang="de">

<head>
    <?php include "../common/php/head.php"; ?>
    <title>Le Milieu | Aktinachweis</title>
    <link rel="stylesheet" href="../common/css/materialize.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
</head>

<body>

    <video autoplay muted loop id="backgroundVideo">
        <source src="../common/img/UnicacityAddonWebsiteBackground.mp4" type="video/mp4">
    </video>

    <?php include "../common/php/nav.php"; ?>
    <script src="../common/js/materialize.js"></script>
    <div class="row container aktinachweis-main-content" style="padding-top: 25vh;">
        <div id="edit">
            <div class="col s6">
                <div class="row">
                    <div class="card hoverable" style="margin-right: 5%;">
                        <div class="card-content">
                            <form method="post">
                                <h4 class="center">Mindestaktis <a href="./editMinimumActivity" \><i class="material-icons orange-text">edit</i></a></h4>
                                <table class="highlight responsive-table">
                                    <thead>
                                        <tr>
                                            <th>Rang</th>
                                            <th>Geld</th>
                                            <th>Drogen</th>
                                            <th>RP</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <h6><br></h6>
                                        <?php
                                        $ranks = array(
                                            array('rank' => str_replace("%s", "'", $rankPrefixes['six']), 'money' => $rankMoney[6], 'drugs' => $rankDrugs[6], 'roleplay' => $rankRoleplay[6]),
                                            array('rank' => str_replace("%s", "'", $rankPrefixes['five']), 'money' => $rankMoney[5], 'drugs' => $rankDrugs[5], 'roleplay' => $rankRoleplay[5]),
                                            array('rank' => str_replace("%s", "'", $rankPrefixes['four']), 'money' => $rankMoney[4], 'drugs' => $rankDrugs[4], 'roleplay' => $rankRoleplay[4]),
                                            array('rank' => str_replace("%s", "'", $rankPrefixes['three']), 'money' => $rankMoney[3], 'drugs' => $rankDrugs[3], 'roleplay' => $rankRoleplay[3]),
                                            array('rank' => str_replace("%s", "'", $rankPrefixes['two']), 'money' => $rankMoney[2], 'drugs' => $rankDrugs[2], 'roleplay' => $rankRoleplay[2]),
                                            array('rank' => str_replace("%s", "'", $rankPrefixes['one']), 'money' => $rankMoney[1], 'drugs' => $rankDrugs[1], 'roleplay' => $rankRoleplay[1]),
                                            array('rank' => str_replace("%s", "'", $rankPrefixes['zero']), 'money' => $rankMoney[0], 'drugs' => $rankDrugs[0], 'roleplay' => $rankRoleplay[0])
                                        );
                                        ?>

                                        <?php foreach ($ranks as $rank) : ?>
                                            <tr>
                                                <td><?php echo $rank['rank']; ?></td>
                                                <td><?php echo $rank['money']; ?>$</td>
                                                <td><?php echo $rank['drugs']; ?>g</td>
                                                <td><?php echo $rank['roleplay']; ?></td>
                                            </tr>
                                        <?php endforeach; ?>

                                    </tbody>
                                </table>
                        </div>
                        </form>
                    </div>
                </div>

                <div class="row">
                    <div class="card hoverable" style="margin-right: 5%;">
                        <div class="card-content">
                            <form method="post">
                                <h4 class="center">Aktinachweise</h4>
                                <div class="container">
                                    <div class="row center">
                                        <a href="dashboard?clearactivitytests=true" class="waves-effect waves-light btn red"><strong>Clearn</strong></a>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <div class="col s6">
            <div class="row">
                <div class="card hoverable">
                    <div class="card-content">
                        <form method="post">
                            <h4 class="center">Bündnis <a href="./editAlliance" \><i class="material-icons orange-text">edit</i></a></h4>
                            <div class="container">
                                <div class="row">
                                    <h5><strong>Fraktion</strong>:</h5>
                                    » <?php echo $settings->getAlliance(); ?>
                                </div>
                                <div class="row">
                                    <h5><strong>Leader</strong>:</h5>
                                    » <?php echo $settings->getAllianceLeader(); ?>
                                </div>
                                <div class="row">
                                    <h5><strong>Gründungsdatum</strong>:</h5>
                                    » <?php echo $settings->getAllianceFoundDate(); ?>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="card hoverable">
                    <div class="card-content">
                        <form method="post">
                            <h4 class="center">Bewerbungsphase</h4>
                            <div class="container">
                                <div class="row center">
                                    <?php if (strcmp('geöffnet', $settings->getAppliancePhase()) == 0) { ?>
                                        <h5><span class="green-text">Geöffnet</span></h5>
                                    <?php } else if (strcmp('warteliste', $settings->getAppliancePhase()) == 0) { ?>
                                        <h5><span class="orange-text">Warteliste</span></h5>
                                    <?php } else { ?>
                                        <h5><span class="red-text">Geschlossen</span></h5>
                                    <?php } ?>
                                </div>
                                <div class="row center">
                                    <a href="dashboard?changephase=offen" class="waves-effect waves-light btn green"><strong>+</strong></a>
                                    <a style="margin-left: 5%" href="dashboard?changephase=warteliste" class="waves-effect waves-light btn orange"><strong>/</strong></a>
                                    <a style="margin-left: 5%" href="dashboard?changephase=geschlossen" class="waves-effect waves-light btn red"><strong>-</strong></a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
    <?php include "../common/php/footer.php"; ?>
</body>

</html>