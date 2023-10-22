<?php
session_start();
error_reporting(E_ALL);

require '../common/php/config.php';
require '../common/php/activityProofManager.php';

$activityProofMySQL = new activityProofMySQL();

if (!isset($_SESSION['logged'])) {
    header('Location: https://lemilieu.de/index?accessdenied=true');
    exit;
}

if (isset($_POST['droge']) && isset($_POST['reinheit']) && isset($_POST['menge']) && isset($_POST['screenshot'])) {
        date_default_timezone_set("Europe/Amsterdam");
        $date = date('d/m/Y', time());

        $activityProofMySQL->insertDrugActivity($_SESSION['uuid'], $_POST['droge'], $_POST['reinheit'], $_POST['menge'], $date, $_POST['screenshot']);
        header('Location: ./addDrugActivity?success=true');
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
                <div class="card-content"> <!-- $_SESSION['uuid']; -->
                    <form method="post">
                        <h4 class="center">Geldaktivität</h4>
                        <div class="row">
                            <div class="center col s3">
                                Droge <br />
                                <p style="font-size: 55%;">Pulver, Kräuter, Kristalle, Wundertüte</p>
                            </div>
                            <div class="center col s3">
                                Reinheit <br />
                                <p style="font-size: 55%;">0, 1, 2, 3</p>
                            </div>
                            <div class="center col s3">
                                Menge
                            </div>
                            <div class="center col s3">
                                Screenshot
                            </div>
                        </div>
                        <div class="row">
                            <div class="input-field col s3">
                                <label for="droge"></label>
                                <input placeholder="Droge" name="droge" id="droge" type="text" class="validate">
                            </div>
                            <div class="input-field col s3">
                                <label for="reinheit"></label>
                                <input placeholder="Reinheit" name="reinheit" id="reinheit" type="text" class="validate">
                            </div>
                            <div class="input-field col s3">
                                <label for="menge"></label>
                                <input placeholder="Menge" name="menge" id="menge" type="text" class="validate">
                            </div>
                            <div class="input-field col s3">
                                <label for="screenshot"></label>
                                <input placeholder="Screenshot" name="screenshot" id="screenshot" type="text" class="validate">
                            </div>
                        </div>
                        <div class="row">
                            <div class="left col s12">
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

<div style="padding-top: 20vh;"></div>

<?php include "../common/php/footer.php"; ?>
<script src="../assets/js/materialize.js"></script>

<script>
    let url_string = window.location.href;
    let url = new URL(url_string);
    let success = url.searchParams.get("success");

    if (success) {
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
            title: 'Aktivität eingetragen'
        })
    }
</script>
</body>
</html>