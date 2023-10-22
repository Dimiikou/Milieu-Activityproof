<?php
session_start();
error_reporting(1);

require '../common/php/config.php';
require '../common/php/loginmanager.php';
require '../common/php/settingsManager.php';

$settings = new settingsManager();
$login = new loginManager();

if (!isset($_SESSION['logged']) || intval($_SESSION['rank']) < 5) {
    header('Location: https://lemilieu.de/index?accessdenied=true');
    exit;
}

if (isset($_POST['alliance'])) {
    if (intval($_SESSION['rank']) > 4) {
        if (strlen($_POST['alliance']) > 2) { $settings->changeAlliance($_POST['alliance']); }
    }
}

if (isset($_POST['allianceleader'])) {
    if (intval($_SESSION['rank']) > 4) {
        if (strlen($_POST['allianceleader']) > 2) { $settings->changeAllianceLeader($_POST['allianceleader']); }
    }
}

if (isset($_POST['founddate'])) {
    if (intval($_SESSION['rank']) > 4) {
        if (strlen($_POST['founddate']) > 2) { $settings->changeAllianceFoundDate($_POST['founddate']); }
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

<div class="container aktinachweis-main-content" style="padding-top: 25vh;">
    <div class="col s12">
            <div class="row">
                <div class="card hoverable">
                    <div class="card-content">
                    <form method="post">
                        <h4 class="center">Bündnis</h4>
                        <div class="row">
                            <div class="input-field col s4">
                                <h5 class="center">Bündnis</h5>
                                <label for="alliance"></label>
                                <input placeholder="<?php echo $settings->getAllianceSetting("allianceFaction");?>" name="alliance" id="alliance" type="text" class="validate">
                            </div>
                            <div class="input-field col s4">
                                <h5 class="center">Leader</h5>
                                <label for="allianceleader"></label>
                                <input placeholder="<?php echo $settings->getAllianceSetting("allianceLeader");?>" name="allianceleader" id="allianceleader" type="text" class="validate">
                            </div>
                            <div class="input-field col s4">
                                <h5 class="center">Gründung</h5>
                                <label for="founddate"></label>
                                <input placeholder="<?php echo $settings->getAllianceSetting("foundDate");?>" name="founddate" id="founddate" type="date" class="validate">
                            </div>
                            </div>
                        <div class="center">
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
<div style="padding-top: 10vh;"></div>
<?php include "../common/php/footer.php"; ?>
</body>
</html>