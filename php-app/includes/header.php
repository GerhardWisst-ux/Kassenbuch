<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

define('APP_PATH', $_SERVER['DOCUMENT_ROOT'] . '/Cash/php-app/');
define('APP_URL', '/Cash/php-app/');

// Aktuelles Script immer in Kleinbuchstaben vergleichen
$current = strtolower(basename($_SERVER['SCRIPT_NAME']));
$isAdmin = isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1;

// robuste Prüfung, ob eine gültige Kassennummer gesetzt ist.
// Wir behandeln nur positive ganze Zahlen als gültige Kassennummern.
$kassennummer = $_SESSION['kassennummer'] ?? null;
$hasKasse = filter_var($kassennummer, FILTER_VALIDATE_INT, [
    'options' => ['min_range' => 1]
]) !== false;

require_once APP_PATH . 'AppVersion.php';
require_once APP_PATH . 'includes/functions.php';

$appVersion = new AppVersion('1.0.0');
?>
<style>
    .btn-darkblue {
        background-color: #2a5298;
        color: #fff;
        border-color: #1e3c72;
    }

    .btn-darkblue:hover {
        color: #2a5298;
        background-color: #2a5298;
        border-color: #1e3c72;
    }

    .header-lightblue {
        background-color: #cfe8fc;
    }

    .header-lightblue .nav-link {
        color: #003366;
    }

    .header-lightblue .nav-link.active,
    .header-lightblue .nav-link:hover,
    .header-lightblue .dropdown-item.active,
    .header-lightblue .dropdown-item:hover {
        color: #0056b3;
        font-weight: bold;
        background-color: #e6f2ff;
    }

    .header-lightblue .navbar-brand {
        color: #002244;
        font-weight: bold;
    }
</style>

<nav class="navbar navbar-expand-lg navbar-light header-lightblue shadow-sm">
    <div class="container">
        <a class="navbar-brand" href="<?= APP_URL ?>Index.php">CashControl</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu"
            aria-controls="navMenu" aria-expanded="false" aria-label="Menü umschalten">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navMenu">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link <?= in_array($current, ['addkasse.php', 'editkasse.php', 'index.php']) ? 'active' : '' ?>"
                        href="<?= APP_URL ?>Index.php">Kassenübersicht</a>
                </li>

                <?php if ($hasKasse): ?>
                    <li class="nav-item">
                        <a class="nav-link <?= ($current == 'buchungen.php') ? 'active' : '' ?>"
                            href="<?= APP_URL ?>Buchungen.php">Buchungen</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= in_array($current, ['buchungsarten.php', 'addbuchungsart.php', 'editbuchungsart.php']) ? 'active' : '' ?>"
                            href="<?= APP_URL ?>Buchungsarten.php">Buchungsarten</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= in_array($current, ['bestaende.php', 'editbestand.php']) ? 'active' : '' ?>"
                            href="<?= APP_URL ?>Bestaende.php">Bestände</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= ($current == 'auswertungen.php') ? 'active' : '' ?>"
                            href="<?= APP_URL ?>Auswertungen.php">Auswertungen</a>
                    </li>

                    <?php if ($isAdmin): ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle <?= in_array($current, ['mapping_admin.php', 'exportdatev.php', 'users.php', 'mandanten.php', 'datensicherung.php']) ? 'active' : '' ?>"
                                href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                Admin
                            </a>
                            <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                                <li><a class="dropdown-item <?= ($current == 'users.php') ? 'active' : '' ?>"
                                        href="<?= APP_URL ?>Users.php">User</a></li>
                                <li><a class="dropdown-item <?= ($current == 'Mandanten.php') ? 'active' : '' ?>"
                                        href="<?= APP_URL ?>Mandanten.php">Mandanten</a></li>
                                <li><a class="dropdown-item <?= ($current == 'mapping_admin.php') ? 'active' : '' ?>"
                                        href="<?= APP_URL ?>Mapping_Admin.php">Mapping</a></li>
                                <li><a class="dropdown-item <?= ($current == 'exportdatev.php') ? 'active' : '' ?>"
                                        href="<?= APP_URL ?>ExportDatev.php">DATEV-Export</a></li>
                                <li><a class="dropdown-item <?= ($current == 'Neuberechnen.php') ? 'active' : '' ?>"
                                        href="<?= APP_URL ?>Neuberechnen.php">Neuberechnen Kassen</a></li>
                                <li><a class="dropdown-item <?= ($current == 'datensicherung.php') ? 'active' : '' ?>"
                                        href="<?= APP_URL ?>Datensicherung.php">Datensicherung</a></li>
                            </ul>
                        </li>
                    <?php endif; ?>
                <?php endif; ?>

                <li class="nav-item">
                    <a class="nav-link <?= ($current == 'impressum.php') ? 'active' : '' ?>"
                        href="<?= APP_URL ?>Impressum.php">Impressum</a>
                </li>
            </ul>
        </div>
    </div>
</nav>