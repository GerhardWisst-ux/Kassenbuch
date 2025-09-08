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

    /* Hellblau für gesamten Header-Bereich */
    .header-lightblue {
        background-color: #cfe8fc; /* sanftes Hellblau */
    }

    .header-lightblue .nav-link {
        color: #003366;
    }

    .header-lightblue .nav-link.active,
    .header-lightblue .nav-link:hover {
        color: #0056b3;
        font-weight: bold;
    }

    .header-lightblue .navbar-brand {
        color: #002244;
        font-weight: bold;
    }
</style>

<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$current = basename($_SERVER['SCRIPT_NAME']);
$prefix = (strpos($_SERVER['SCRIPT_NAME'], '../') !== false) ? '/Cash/php-app/' : '';

$isAdmin = isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1;

require_once 'AppVersion.php';
$appVersion = new AppVersion('1.0.0'); // Fallback-Version
?>

<nav class="navbar navbar-expand-lg header-lightblue shadow-sm">
    <div class="container">
        <a class="navbar-brand" href="<?= $prefix ?>index.php">CashControl</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu"
            aria-controls="navMenu" aria-expanded="false" aria-label="Menü umschalten">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navMenu">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link <?= ($current == 'Kassenuebersicht.php' || $current == 'EditKasse.php') ? 'active' : '' ?>"
                        href="<?= $prefix ?>Index.php">Kassenübersicht</a>
                </li>

                <?php if (isset($_SESSION['kassennummer'])): ?>
                    <li class="nav-item">
                        <a class="nav-link <?= ($current == 'Buchungen.php') ? 'active' : '' ?>"
                            href="<?= $prefix ?>Buchungen.php">Buchungen</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= ($current == 'Buchungsarten.php' || $current == 'AddBuchungsart.php' || $current == 'EditBuchungsart.php') ? 'active' : '' ?>"
                            href="<?= $prefix ?>Buchungsarten.php">Buchungsarten</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= ($current == 'Bestaende.php' || $current == 'EditBestand.php') ? 'active' : '' ?>"
                            href="<?= $prefix ?>Bestaende.php">Bestände</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= ($current == 'Auswertungen.php') ? 'active' : '' ?>"
                            href="<?= $prefix ?>Auswertungen.php">Auswertungen</a>
                    </li>

                    <?php if (!$isAdmin): ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle <?= ($current == 'Mapping_Admin.php' || $current == 'ExportDatev.php') ? 'active' : '' ?>"
                                href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                Export/Import
                            </a>
                            <ul class="dropdown-menu">
                                <li>
                                    <a class="dropdown-item <?= ($current == 'Mapping_Admin.php') ? 'active' : '' ?>"
                                        href="<?= $prefix ?>Mapping_Admin.php">Mapping</a>
                                </li>
                                <li>
                                    <a class="dropdown-item <?= ($current == 'ExportDatev.php') ? 'active' : '' ?>"
                                        href="<?= $prefix ?>ExportDatev.php">DATEV-Export</a>
                                </li>
                            </ul>
                        </li>
                    <?php endif; ?>
                <?php endif; ?>

                <li class="nav-item">
                    <a class="nav-link <?= ($current == 'Impressum.php') ? 'active' : '' ?>"
                        href="<?= $prefix ?>Impressum.php">Impressum</a>
                </li>
            </ul>
        </div>
    </div>
</nav>
