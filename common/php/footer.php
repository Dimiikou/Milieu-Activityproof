<div class="footer">
    <div class="footer-grid">
        <div class="grid-a">
            <img src="/common/img/Logo.png" height="200px" alt="Unicacity Logo">
        </div>
        <?php 
        if (!is_null($_SESSION['logged'])) {
            echo '<div class="grid-b">
                    <a href="changepassword" style="color: var(--lightgrey);">» Passwort ändern</a> <br />
                    <a href="pfandnahme" style="color: var(--lightgrey);">» Pfandnahmen</a> <br />
                    <a href="protectionMoney" style="color: var(--lightgrey);">» Schutzgelder</a>
                    </div>';
        }
        ?>
        <div class="grid-f grid-right">
            <p  class="text-14-400" style="color: var(--gray)">Aktivitätsnachweis<br>© <?php echo date("Y"); ?> <a href="https://forum.unicacity.de/core/index.php?user/16582-rettichlp/" style="text-decoration: none; color: var(--gray);">RettichLP</a> and <a href="https://forum.unicacity.de/core/index.php?user/6318-dimiikou/" style="text-decoration: none; color: var(--gray);">Dimiikou</a></p>
        </div>
    </div>
</div>