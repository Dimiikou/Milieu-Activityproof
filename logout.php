<?php
session_start();
error_reporting(1);

require 'common/php/config.php';
require 'common/php/logoutManager.php';

$logout = new logoutManager();

if (!isset($_SESSION['logged'])) {
    header('Location: https://lemilieu.de/index?accessdenied=true');
    exit;
}

if (isset($_POST['startdate']) && isset($_POST['enddate']) && isset($_POST['reason'])) {
    $logoutStart = date('d/m/Y', strtotime($_POST['startdate']));
    $logoutEnd = date('d/m/Y', strtotime($_POST['enddate']));

    $logout->addLogout($_SESSION['uuid'], $logoutStart, $logoutEnd, $_POST['reason']);
    header('Location: ./addlogout?success=true');
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
                            <h4 class="center">Abmelden</h4>
                            <!-- 
                                Von
                                Bis
                                Grund
                            -->
                            <div class="row">
                                <div class="input-field col s3">
                                    <label for="startdate"></label>
                                    <input placeholder="Von" name="startdate" id="startdate" type="date" class="validate">
                                </div>
                                <div class="center col s1">
                                    Bis
                                </div>
                                <div class="input-field col s3">
                                    <label for="enddate"></label>
                                    <input placeholder="Bis" name="enddate" id="enddate" type="date" class="validate">
                                </div>
                                <div class="input-field col s5">
                                    <label for="reason"></label>
                                    <input placeholder="Grund" name="reason" id="reason" type="text" class="text">
                                </div>
                            </div>

                            <button class="modal-close btn waves-effect waves-light crgreen" type="submit" name="action">Abmelden
                                <i class="material-icons right">send</i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>

    <div style="padding-top: 20vh;"></div>

    <?php include "common/php/footer.php"; ?>
    <script src="assets/js/materialize.js"></script>

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
                title: 'Abmeldung eingetragen'
            })
        }
    </script>

</body>

</html>