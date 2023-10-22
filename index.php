<?php
session_start();
error_reporting(1);
?>

<!doctype html>
<html lang="de">

<head>
    <?php include "common/php/head.php"; ?>
    <title>Le Milieu | Aktinachweis</title>
</head>

<body>

    <video autoplay muted loop id="backgroundVideo">
        <source src="common/img/UnicacityAddonWebsiteBackground.mp4" type="video/mp4">
    </video>

    <?php include "common/php/nav.php"; ?>

    <div class="index-main">
        <div class="index-main-content">
            <p class="text-80-700">Aktinachweis <span class="text-40-400">v<span style="color: var(--blue)">1.8.0</span></span></p>
            <p class="text-30-700" style="color: var(--blue);">Wir sind T1 Frak</p>
        </div>
    </div>

    <?php include "common/php/footer.php"; ?>
    <script src="assets/js/materialize.js"></script>

    <script>
        let url_string = window.location.href;
        let url = new URL(url_string);
        let login = url.searchParams.get("login");

        if (login) {
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
                title: 'Angemeldet'
            })
        }
    </script>

</body>

</html>