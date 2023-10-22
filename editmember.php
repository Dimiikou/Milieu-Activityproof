<?php
session_start();
error_reporting(1);

require 'common/php/config.php';
require 'common/php/loginmanager.php';
require 'common/php/mcapi.php';

$login = new loginManager();
$mcapi = new mcapi();

if (!isset($_SESSION['logged'])) {
    header('Location: https://lemilieu.de/index?accessdenied=true');
    exit;
}

if (intval($_SESSION['rank']) < 5) {
    header('Location: https://lemilieu.de/index?accessdenied=true');
    exit;
}

if (isset($_POST['rank'])) {
    $login->changeUserRank($_GET['member'], $_POST['rank']);
}

if (isset($_POST['password'])) {
    $login->resetPassword($_GET['member'], $_POST['password']);
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

    <div class="container aktinachweis-main-content" style="padding-top: 25vh;">
        <div class="col s12">
            <div class="row">
                <div class="card hoverable">
                    <div class="card-content">
                        <form method="post">
                            <h4></h4>
                            <div class="row">
                                <div class="col s4">
                                    Passwort ändern
                                </div>
                                <div class="input-field col s4">
                                    <label for="password"></label>
                                    <input placeholder="password" name="password" id="password" type="text" class="validate">
                                </div>
                                <div class="left col s4">
                                    <button class="modal-close btn waves-effect waves-light crgreen" type="submit" name="action">Aktualisieren
                                        <i class="material-icons right">send</i>
                                    </button>
                                </div>
                            </div>
                        </form>

                        <form method="post">
                            <h2></h2>
                            <div class="row">
                                <div class="col s4">
                                    Rang einstellen
                                </div>
                                <div class="input-field col s4">
                                    <label for="rank"></label>
                                    <input placeholder="<?php echo $login->getRank($_GET['member']); ?>" name="rank" id="rank" type="number" class="validate">
                                </div>
                                <div class="left col s4">
                                    <button class="modal-close btn waves-effect waves-light crgreen" type="submit" name="action">Aktualisieren
                                        <i class="material-icons right">send</i>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>

    <div style="padding-top: 10vh;"></div>

    <?php include "common/php/footer.php"; ?>
    <script src="assets/js/materialize.js"></script>
</body>

</html>