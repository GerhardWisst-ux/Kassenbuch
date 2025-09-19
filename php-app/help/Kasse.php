<?php
ob_start();
// Session starten, falls noch nicht erfolgt
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if ($_SESSION['userid'] == "") {
    // Wenn kein Benutzer angemeldet ist, weiterleiten zur Login-Seite
    header("Location: ../Login.php");
}

?>

<!DOCTYPE html>
<html lang="de">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CashControl - Benutzerhilfe Kassenübersicht</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>    

        h2 {
            margin-top: 30px;
        }

        .card-help {
            margin-bottom: 15px;
            cursor: pointer;
        }

        .card-body-help {
            display: none;
            background: #f8f9fa;
            border-radius: 5px;
            padding: 10px;
        }
    </style>

    <style>
        /* === Grundlayout === */
        html,
        body {
            height: 100%;
            margin: 0;
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, sans-serif;
        }

        /* Wrapper für Flex */
        .wrapper {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* === Navbar & Header === */
        .custom-header {
            background: linear-gradient(90deg, #1e3c72, #2a5298);
            color: #fff;
            border-bottom: 2px solid #1b3a6d;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
            border-radius: 0 0 12px 12px;
        }

        .custom-header h2 {
            font-weight: 600;
            letter-spacing: 0.5px;
        }

        /* === Buttons === */
        .btn {
            border-radius: 30px;
            font-size: 0.85rem;
            padding: 0.45rem 0.9rem;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn-primary {
            background-color: #2a5298;
            border-color: #1e3c72;
        }

        .btn-primary:hover {
            background-color: #1e3c72;
        }

        .btn-darkgreen {
            background-color: #198754;
            border-color: #146c43;
        }

        .btn-darkgreen:hover {
            background-color: #146c43;
        }

        /* === Karten & Tabellen === */
        .custom-container {
            background-color: #fff;
            border-radius: 12px;
            /* padding: 20px; */
            margin-top: 0px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.05);
        }

        #TableBestaende {
            width: 100%;
            font-size: 0.9rem;
        }

        #TableBestaende tbody tr:hover {
            background-color: #f1f5ff;
        }

        /* === Navbar Design === */
        .navbar-custom {
            background: linear-gradient(to right, #cce5f6, #e6f2fb);
            border-bottom: 1px solid #b3d7f2;
        }

        .navbar-custom .navbar-brand,
        .navbar-custom .nav-link {
            color: #0c2c4a;
            font-weight: 500;
        }

        .navbar-custom .nav-link:hover,
        .navbar-custom .nav-link:focus {
            color: #04588c;
            text-decoration: underline;
        }

        /* === Modal === */
        .modal-content {
            border-radius: 12px;
        }

        .modal-header {
            background-color: #0946c9ff;
            color: #fff;
            border-radius: 12px 12px 0 0;
        }

        /* === Toast === */
        .toast-green {
            background-color: #198754;
            color: #fff;
        }
    </style>
</head>

<body>
    <?php

    require_once '../includes/header.php';
    $email = $_SESSION['email'];
    $userid = $_SESSION['userid'];
    ?>

    <div class="wrapper">
        <header class="custom-header py-2 text-white">
            <div class="container-fluid">
                <div class="row align-items-center">

                    <!-- Titel zentriert -->
                    <div class="col-12 text-center mb-2 mb-md-0">
                        <h2 class="h4 mb-0">CashControl - Hilfe Kassenübersicht</h2>
                    </div>

                    <?php
                    require_once '../includes/benutzerversion.php';
                    ?>
                </div>
        </header>
        <div class="container-fluid mt-4">
            <div class="custom-container">
                <p>Diese Seite zeigt Ihnen alle Kassen Ihres Kontos und ermöglicht die Verwaltung Ihrer Finanzen.</p>

                <h2>1. Übersicht der Kassen</h2>
                <div class="card card-help" onclick="toggleHelp(this)">
                    <div class="card-header">Was sind aktive und archivierte Kassen?</div>
                    <div class="card-body card-body-help">
                        <ul>
                            <li><strong>Aktive Kassen:</strong> Hier werden Ihre Kassen angezeigt, die Sie aktuell
                                verwenden. Sie
                                können Einlagen, Ausgaben und Bestände verwalten.</li>
                            <li><strong>Archivierte Kassen:</strong> Kassen, die nicht mehr aktiv genutzt werden. Daten
                                werden
                                weiterhin gespeichert, aber nicht bearbeitet.</li>
                        </ul>
                    </div>
                </div>

                <h2>2. Hinzufügen einer neuen Kasse</h2>
                <div class="card card-help" onclick="toggleHelp(this)">
                    <div class="card-header">Wie füge ich eine neue Kasse hinzu?</div>
                    <div class="card-body card-body-help">
                        Klicken Sie oben auf den <strong>+ Kasse</strong>-Button. Geben Sie einen Namen für die Kasse
                        ein und
                        speichern Sie. Die neue Kasse erscheint dann in der Übersicht der aktiven Kassen.
                    </div>
                </div>

                <h2>3. Kasseninformationen ansehen</h2>
                <div class="card card-help" onclick="toggleHelp(this)">
                    <div class="card-header">Was zeigen die Kassen-Cards?</div>
                    <div class="card-body card-body-help">
                        Jede Kasse wird als Karte dargestellt und zeigt:
                        <ul>
                            <li>Den aktuellen Bestand</li>
                            <li>Datum der letzten Buchung</li>
                            <li>Optionen wie Bearbeiten oder Löschen</li>
                        </ul>
                    </div>
                </div>

                <h2>4. Kasse bearbeiten</h2>
                <div class="card card-help" onclick="toggleHelp(this)">
                    <div class="card-header">Wie bearbeite ich eine Kasse?</div>
                    <div class="card-body card-body-help">
                        Klicken Sie auf das Stift-Symbol auf der jeweiligen Kassen-Karte. Sie können Name, Startsaldo
                        und andere
                        Einstellungen ändern.
                    </div>
                </div>

                <h2>5. Kasse löschen</h2>
                <div class="card card-help" onclick="toggleHelp(this)">
                    <div class="card-header">Wie lösche ich eine Kasse?</div>
                    <div class="card-body card-body-help">
                        Klicken Sie auf das Mülleimer-Symbol. Es erscheint eine Bestätigung, um versehentliches Löschen
                        zu
                        verhindern. Nach Bestätigung wird die Kasse gelöscht.
                        <br><em>Achtung: Alle Buchungen der Kasse werden ebenfalls gelöscht!</em>
                    </div>
                </div>

                <h2>6. Anzeige von Beständen</h2>
                <div class="card card-help" onclick="toggleHelp(this)">
                    <div class="card-header">Wie werden Bestände angezeigt?</div>
                    <div class="card-body card-body-help">
                        Für jede Kasse wird automatisch der aktuelle Bestand berechnet. Dies basiert auf allen Einlagen
                        und Ausgaben
                        der Kasse.
                    </div>
                </div>

                <h2>7. Weitere Tipps</h2>
                <div class="card card-help" onclick="toggleHelp(this)">
                    <div class="card-header">Nützliche Hinweise</div>
                    <div class="card-body card-body-help">
                        <ul>
                            <li>Nutzen Sie die Tab-Navigation, um schnell zwischen aktiven und archivierten Kassen zu
                                wechseln.</li>
                            <li>Verwenden Sie die Suche oder Filter, um Kassen schnell zu finden.</li>
                            <li>Klicken Sie auf die Buttons oben rechts für zusätzliche Funktionen wie Hilfe oder Zurück
                                zur
                                Hauptübersicht.</li>
                        </ul>
                    </div>
                </div>

                <a href="../Index.php" class="btn btn-secondary">
                    <i class="fa fa-arrow-left"></i> Zurück zur Startseite
                </a>

                <script>
                    function toggleHelp(card) {
                        const body = card.querySelector('.card-body-help');
                        if (body.style.display === 'block') {
                            body.style.display = 'none';
                        } else {
                            body.style.display = 'block';
                        }
                    }
    </s
        </p >
      </div >
    </div >
  </div >
                        <script src="js/jquery.min.js"></script>
                <script src="js/bootstrap.bundle.min.js"></script>


</body>
<script>
          function toggleHelp(card) {
            const body = card.querySelector('.card-body-help');
          if (body.style.display === 'block') {
              body.style.display = 'none';
            } else {
              body.style.display = 'block';
            }
        }
</script>

</html>