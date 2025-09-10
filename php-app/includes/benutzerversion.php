<!-- Benutzerinfo + Logout -->
<div class="col-12 col-md-auto ms-md-auto text-center text-md-end">
    <!-- Auf kleinen Bildschirmen: eigene Zeile für E-Mail -->
    <div class="d-block d-md-inline mb-1 mb-md-0">
        <span class="me-2">Benutzer: <?= htmlspecialchars($_SESSION['email']) ?></span>
    </div>
    <!-- Version -->
    <span class="version-info" title="Git-Hash + Build-Datum">
        Version: <?= htmlspecialchars($appVersion->getVersion()) ?>
    </span>
    <span>
        <!-- Logout-Button -->
        <a class="btn btn-darkgreen btn-sm text-white" title="Abmelden vom Kassenbuch" href="logout.php">
            <i class="fa fa-sign-out" aria-hidden="true"></i> Ausloggen
        </a>

    </span>
</div>