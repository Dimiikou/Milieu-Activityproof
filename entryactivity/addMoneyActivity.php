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

if (isset($_POST['Aktivitaetstyp']) && isset($_POST['value']) && isset($_POST['screenshot'])) {
        date_default_timezone_set("Europe/Amsterdam");
        $date = date('d/m/Y', time());

        $activityProofMySQL->insertMoneyActivity($_SESSION['uuid'], $_POST['Aktivitaetstyp'], $date, $_POST['value'], $_POST['screenshot']);
        header('Location: ./addMoneyActivity?success=true');
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
                            <div class="center col s4">
                                Aktivitätstyp <br />
                                <p style="font-size: 55%;">blacklist, ausraub, menschenhandel, transport, autoverkauf</p>
                            </div>
                            <div class="center col s4">
                                Betrag
                            </div>
                            <div class="center col s4">
                                Screenshot
                            </div>
                        </div>
                        <div class="row">
                            <div class="input-field col s4">
                                <label for="Aktivitaetstyp"></label>
                                <input placeholder="Aktivitätstyp" name="Aktivitaetstyp" id="Aktivitaetstyp" type="text" class="validate">
                            </div>
                            <div class="input-field col s4">
                                <label for="value"></label>
                                <input placeholder="Betrag" name="value" id="value" type="text" class="validate">
                            </div>
                            <div class="input-field col s4">
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