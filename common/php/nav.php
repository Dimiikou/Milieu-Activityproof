<nav id="nav" class="nav">
    <?php 
    if (isset($_SESSION['logged'])) { ?>
        <a class="nav-child nav-child-right text-20-400" href="/logout">Logout</a>
    <?php } else {?>
        <a class="nav-child nav-child-right text-20-400" href="/login">Login</a>
    <?php } ?>
    <?php if (isset($_SESSION['logged'])) { ?>
    <span class="nav-child text-20-400" style="float: right"> | </span>
    <a class="nav-child nav-child-right text-20-400" href="/activityproof/<?php echo $_SESSION['uuid'] ?>">Aktivitätsnachweis</a>
    <a class="nav-child nav-child-right text-20-400" href="/memberlist">Memberliste</a>
    <a class="nav-child nav-child-right text-20-400" href="/dashboard">Übersicht</a>
    <a class="nav-child nav-child-right text-20-400" href="/punishments">Nachzahlung</a>
        <?php 
        if ($_SESSION['rank'] > 4) {
           echo '<a class="nav-child nav-child-right text-20-400" href="/leaderpanel/dashboard">Dashboard</a>';
        }?>
    <?php } ?>
    
    <a class="nav-child nav-child-left text-20-800" href="https://lemilieu.de/">Le Milieu Aktivitätsnachweis</a>
</nav>