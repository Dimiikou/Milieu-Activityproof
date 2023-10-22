<?php
session_start();
error_reporting(1);

require '../common/php/config.php';
require '../common/php/loginmanager.php';
require '../common/php/settingsManager.php';
require '../common/php/activityProofManager.php';
require '../common/php/mcapi.php';

$activityProofMySQL = new activityProofMySQL();
$settings = new settingsManager();
$login = new loginManager();
$mcapi = new mcapi();

$rankzero = str_replace("%s", "'", $settings->getPrefix('rankZero'));
$rankone = str_replace("%s", "'", $settings->getPrefix('rankOne'));
$ranktwo = str_replace("%s", "'", $settings->getPrefix('rankTwo'));
$rankthree = str_replace("%s", "'", $settings->getPrefix('rankThree'));
$rankfour = str_replace("%s", "'", $settings->getPrefix('rankFour'));
$rankfive = str_replace("%s", "'", $settings->getPrefix('rankFive'));
$ranksix = str_replace("%s", "'", $settings->getPrefix('rankSix'));

$rankMoney = array();
$rankDrugs = array();
$rankRoleplay = array();

for ($i = 0; $i<=6; $i++) {
    $rankMoney[$i] = $settings->getMinimumMoney($i);
    $rankDrugs[$i] = $settings->getMinimumDrugs($i);
    $rankRoleplay[$i] = $settings->getMinimumRoleplay($i);
}

if (!isset($_SESSION['logged'])) {
    header('Location: https://lemilieu.de/index?accessdenied=true');
    exit;
}

if (intval($_SESSION['rank']) < 5) {
    header('Location: https://lemilieu.de/index?accessdenied=true');
    exit;
}

if (isset($_POST['moneyactivitysix']) && isset($_POST['drugactivitysix']) && isset($_POST['roleplayactivitysix'])) {

    if (intval($_SESSION['rank']) > 4) {
        if (ctype_digit($_POST['moneyactivitysix'])) {
            $settings->changeMoneyActivity(6, $_POST['moneyactivitysix']);
        }
        if (ctype_digit($_POST['drugactivitysix'])) {
            $settings->changeDrugActivity(6, $_POST['drugactivitysix']);
        }
        if (ctype_digit($_POST['roleplayactivitysix'])) {
            $settings->changeRolePlayActivity(6, $_POST['roleplayactivitysix']);
        }

        header('Location: ./editMinimumActivity');
    }
}

if (isset($_POST['moneyactivityfive']) && isset($_POST['drugactivityfive']) && isset($_POST['roleplayactivityfive'])) {

    if (intval($_SESSION['rank']) > 4) {
        if (ctype_digit($_POST['moneyactivityfive'])) {
            $settings->changeMoneyActivity(5, $_POST['moneyactivityfive']);
        }
        if (ctype_digit($_POST['drugactivityfive'])) {
            $settings->changeDrugActivity(5, $_POST['drugactivityfive']);
        }
        if (ctype_digit($_POST['roleplayactivityfive'])) {
            $settings->changeRolePlayActivity(5, $_POST['roleplayactivityfive']);
        }

        header('Location: ./editMinimumActivity');
    }
}

if (isset($_POST['moneyactivityfour']) && isset($_POST['drugactivityfour']) && isset($_POST['roleplayactivityfour'])) {

    if (intval($_SESSION['rank']) > 4) {
        if (ctype_digit($_POST['moneyactivityfour'])) {
            $settings->changeMoneyActivity(4, $_POST['moneyactivityfour']);
        }
        if (ctype_digit($_POST['drugactivityfour'])) {
            $settings->changeDrugActivity(4, $_POST['drugactivityfour']);
        }
        if (ctype_digit($_POST['roleplayactivityfour'])) {
            $settings->changeRolePlayActivity(4, $_POST['roleplayactivityfour']);
        }

        header('Location: ./editMinimumActivity');
    }
}

if (isset($_POST['moneyactivitythree']) && isset($_POST['drugactivitythree']) && isset($_POST['roleplayactivitythree'])) {

    if (intval($_SESSION['rank']) > 4) {
        if (ctype_digit($_POST['moneyactivitythree'])) {
            $settings->changeMoneyActivity(3, $_POST['moneyactivitythree']);
        }
        if (ctype_digit($_POST['drugactivitythree'])) {
            $settings->changeDrugActivity(3, $_POST['drugactivitythree']);
        }
        if (ctype_digit($_POST['roleplayactivitythree'])) {
            $settings->changeRolePlayActivity(3, $_POST['roleplayactivitythree']);
        }

        header('Location: ./editMinimumActivity');
    }
}

if (isset($_POST['moneyactivitytwo']) && isset($_POST['drugactivitytwo']) && isset($_POST['roleplayactivitytwo'])) {

    if (intval($_SESSION['rank']) > 4) {
        if (ctype_digit($_POST['moneyactivitytwo'])) {
            $settings->changeMoneyActivity(2, $_POST['moneyactivitytwo']);
        }
        if (ctype_digit($_POST['drugactivitytwo'])) {
            $settings->changeDrugActivity(2, $_POST['drugactivitytwo']);
        }
        if (ctype_digit($_POST['roleplayactivitytwo'])) {
            $settings->changeRolePlayActivity(2, $_POST['roleplayactivitytwo']);
        }

        header('Location: ./editMinimumActivity');
    }
}

if (isset($_POST['moneyactivityone']) && isset($_POST['drugactivityone']) && isset($_POST['roleplayactivityone'])) {

    if (intval($_SESSION['rank']) > 4) {
        if (ctype_digit($_POST['moneyactivityone'])) {
            $settings->changeMoneyActivity(1, $_POST['moneyactivityone']);
        }
        if (ctype_digit($_POST['drugactivityone'])) {
            $settings->changeDrugActivity(1, $_POST['drugactivityone']);
        }
        if (ctype_digit($_POST['roleplayactivityone'])) {
            $settings->changeRolePlayActivity(1, $_POST['roleplayactivityone']);
        }

        header('Location: ./editMinimumActivity');
    }
}

if (isset($_POST['moneyactivityzero']) && isset($_POST['drugactivityzero']) && isset($_POST['roleplayactivityzero'])) {

    if (intval($_SESSION['rank']) > 4) {
        if (ctype_digit($_POST['moneyactivityzero'])) {
            $settings->changeMoneyActivity(0, $_POST['moneyactivityzero']);
        }
        if (ctype_digit($_POST['drugactivityzero'])) {
            $settings->changeDrugActivity(0, $_POST['drugactivityzero']);
        }
        if (ctype_digit($_POST['roleplayactivityzero'])) {
            $settings->changeRolePlayActivity(0, $_POST['roleplayactivityzero']);
        }

        header('Location: ./editMinimumActivity');
    }
}
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
            <div class="row">
                <div class="card hoverable" style="margin-right: 5%;">
                    <div class="card-content">
                        <?php
                        $activityFields = array(
                            array('rank' => $ranksix, 'money' => $rankMoney[6], 'drug' => $rankDrugs[6], 'roleplay' => $rankRoleplay[6], 'prefix' => 'six'),
                            array('rank' => $rankfive, 'money' => $rankMoney[5], 'drug' => $rankDrugs[5], 'roleplay' => $rankRoleplay[5], 'prefix' => 'five'),
                            array('rank' => $rankfour, 'money' => $rankMoney[4], 'drug' => $rankDrugs[4], 'roleplay' => $rankRoleplay[4], 'prefix' => 'four'),
                            array('rank' => $rankthree, 'money' => $rankMoney[3], 'drug' => $rankDrugs[3], 'roleplay' => $rankRoleplay[3], 'prefix' => 'three'),
                            array('rank' => $ranktwo, 'money' => $rankMoney[2], 'drug' => $rankDrugs[2], 'roleplay' => $rankRoleplay[2], 'prefix' => 'two'),
                            array('rank' => $rankone, 'money' => $rankMoney[1], 'drug' => $rankDrugs[1], 'roleplay' => $rankRoleplay[1], 'prefix' => 'one'),
                            array('rank' => $rankzero, 'money' => $rankMoney[0], 'drug' => $rankDrugs[0], 'roleplay' => $rankRoleplay[0], 'prefix' => 'zero')
                        );
                        ?>

                        <?php foreach ($activityFields as $field) : ?>
                            <form method="post">
                                <div class="row">
                                    <h5 class="center"></h5>
                                    <div class="col s3">
                                        <?php echo $field['rank']; ?>
                                    </div>
                                    <div class="input-field col s2">
                                        <label for="moneyactivity<?php echo $field['prefix']; ?>"></label>
                                        <input placeholder="<?php echo $field['money']; ?>" name="moneyactivity<?php echo $field['prefix']; ?>" id="moneyactivity<?php echo $field['prefix']; ?>" type="number" class="validate">
                                    </div>
                                    <div class="input-field col s2">
                                        <label for="drugactivity<?php echo $field['prefix']; ?>"></label>
                                        <input placeholder="<?php echo $field['drug']; ?>" name="drugactivity<?php echo $field['prefix']; ?>" id="drugactivity<?php echo $field['prefix']; ?>" type="number" class="validate">
                                    </div>
                                    <div class="input-field col s2">
                                        <label for="roleplayactivity<?php echo $field['prefix']; ?>"></label>
                                        <input placeholder="<?php echo $field['roleplay']; ?>" name="roleplayactivity<?php echo $field['prefix']; ?>" id="roleplayactivity<?php echo $field['prefix']; ?>" type="number" class="validate">
                                    </div>
                                    <div class="left col s3">
                                        <button class="modal-close btn waves-effect waves-light crgreen" type="submit" name="action">Aktualisieren
                                            <i class="material-icons right">send</i>
                                        </button>
                                    </div>
                                </div>
                            </form>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

        </div>
    </div>
    <?php include "../common/php/footer.php"; ?>
</body>

</html>