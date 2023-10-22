<?php
session_start();
error_reporting(E_ALL);

require 'common/php/config.php';
require 'common/php/loginmanager.php';

$login = new loginManager();

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

    <div class="container aktinachweis-main-content" style="padding-top: 25vh;">
        <div class="col s12">
            <div class="row">
                <div class="card hoverable">
                    <ul class="collapsible">
                        <li>
                            <div class="collapsible-header"><i class="material-icons">filter_drama</i>First</div>
                            <div class="collapsible-body"><span>Lorem ipsum dolor sit amet.</span></div>
                        </li>
                        <li>
                            <div class="collapsible-header"><i class="material-icons">place</i>Second</div>
                            <div class="collapsible-body"><span>Lorem ipsum dolor sit amet.</span></div>
                        </li>
                        <li>
                            <div class="collapsible-header"><i class="material-icons">whatshot</i>Third</div>
                            <div class="collapsible-body"><span>Lorem ipsum dolor sit amet.</span></div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    </div>

    <div style="padding-top: 20vh;"></div>

    <?php include "common/php/footer.php"; ?>
    <script src="common/js/materialize.js"></script>
    <script>
        M.AutoInit();
    </script>
    
</body>

</html>