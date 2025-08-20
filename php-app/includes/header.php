<style>
    .btn-darkgreen {
        background-color: #2a5298;
        color: #fff;
        border-color: #1e3c72;
    }

    .btn-darkgreen:hover {
        color: #2a5298;
        background-color: #fff;
        border-color: #1e3c72;
    }
</style>
<?php
// Session starten, falls noch nicht erfolgt
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$current = basename($_SERVER['SCRIPT_NAME']); // z. B. index.php oder cart.php
//echo $current;
$prefix = (strpos($_SERVER['SCRIPT_NAME'], '../') !== false) ? '/Cash/php-app/' : '';

?>

<nav class="navbar navbar-expand-lg navbar-custom shadow-sm">
    <div class="container">
        <a class="navbar-brand" href="<?= $prefix ?>index.php">Kassenbuch</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navMenu">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link <?= ($current == 'Index.php') || ($current == 'AddBuchung.php') || ($current == 'EditBuchung.php') ? 'active text-primary' : '' ?>"
                        href="<?= $prefix ?>Index.php">Hauptseite</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= ($current == 'Buchungsarten.php') || ($current == 'AddBuchungsart.php') || ($current == 'EditBuchungsart.php') ? 'active text-primary' : '' ?>"
                        href="<?= $prefix ?>Buchungsarten.php">Buchungsarten</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= ($current == 'Bestaende.php' || ($current == 'EditBestand.php')) ? 'active text-primary' : '' ?>"
                        href="<?= $prefix ?>Bestaende.php">Bestände</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= ($current == 'Auswertungen.php') ? 'active text-primary' : '' ?>"
                        href="<?= $prefix ?>Auswertungen.php">Auswertungen</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= ($current == 'Impressum.php') ? 'active text-primary' : '' ?>"
                        href="<?= $prefix ?>Impressum.php">Impressum</a>
                </li>
            </ul>
        </div>
    </div>
</nav>