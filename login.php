<?php
session_start();
error_reporting(E_ALL);
require 'common/php/config.php';
require 'common/php/loginmanager.php';
require 'common/php/mcapi.php';

$login = new loginManager();
$mcapi = new mcapi();

if (isset($_GET['logout'])) {
    $_SESSION['logged'] = false;
    session_destroy();
    header('Location: login');
}

if (isset($_POST['uname']) && isset($_POST['psw'])) {
    $login->loginPost($mcapi->getUUIDFromName($_POST['uname']), $_POST['psw']);
}
?>

<!doctype html>
<html lang="de">

<head>
    <?php include "common/php/head.php"; ?>
    <title>Le Milieu | Login</title>
</head>

<body>

    <video autoplay muted loop id="backgroundVideo">
        <source src="common/img/UnicacityAddonWebsiteBackground.mp4" type="video/mp4">
    </video>

    <?php include "common/php/nav.php"; ?>

    <div class="login-main">
        <div class="login-main-content">
            <form action="login.php" method="post">
                <div class="imgcontainer">
                    <img src="../common/img/Logo.png" alt="Avatar" class="avatar" height="300px">
                </div>

                <div class="container">
                    <label for="uname"><b>Spielername</b></label>
                    <input type="text" placeholder="Username" name="uname" required>

                    <label for="psw"><b>Passwort</b></label>
                    <input type="password" placeholder="Passwort" name="psw" required>

                    <button type="submit">Anmelden</button>
                </div>
            </form>
        </div>
    </div>

    <?php include "common/php/footer.php"; ?>

</body>

</html>