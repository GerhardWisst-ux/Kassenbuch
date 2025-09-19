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
    <title>CashControl - Benutzerhilfe Bestände</title>
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
                        <h2 class="h4 mb-0">CashControl - Hilfe Bestände</h2>
                    </div>

                    <?php
                    require_once '../includes/benutzerversion.php';
                    ?>
                </div>
        </header>
        <div class="container-fluid mt-4">
            <div class="custom-container">
                <!DOCTYPE html>
                <html lang="de">

                <head>
                    <meta charset="UTF-8">
                    <title>Hilfe – Bestände</title>
                    <meta name="viewport" content="width=device-width, initial-scale=1.0">                    
                </head>

                <body>
                    <h1>Hilfe zur Seite „Bestände“</h1>
                    <p>
                        Auf dieser Seite sehen Sie die <strong>monatlichen Bestände</strong> Ihrer Kasse.
                        Sie können die Daten berechnen lassen, ein bestimmtes Jahr auswählen und einzelne Einträge
                        bearbeiten oder löschen.
                    </p>

                    <h2>Funktionen in der Toolbar</h2>
                    <ul>
                        <li><strong><i class="fas fa-calculator"></i> Berechnen:</strong> Aktualisiert die Bestände für
                            das ausgewählte Jahr. Fehlende Monate werden ergänzt.</li>
                        <li><strong><i class="fas fa-chart-bar"></i> Diagramm:</strong> Öffnet eine grafische
                            Darstellung der Bestände.</li>
                        <li><strong><i class="fa fa-arrow-left"></i> Zurück:</strong> Wechselt zurück zur
                            Kassenübersicht.</li>
                        <li><strong><i class="fa fa-question-circle"></i> Hilfe:</strong> Öffnet diese Hilfeseite.</li>
                    </ul>

                    <h2>Jahr auswählen</h2>
                    <p>
                        Über das Auswahlfeld können Sie ein <strong>Jahr</strong> auswählen.
                        Nach der Auswahl werden automatisch die Bestände dieses Jahres neu berechnet und angezeigt.
                    </p>

                    <h2>Bestände-Tabelle</h2>
                    <p>
                        In der Tabelle sehen Sie für jeden Monat:
                    </p>
                    <ul>
                        <li><strong>Datum</strong> – Monat und Jahr der Buchung</li>
                        <li><strong>Einlagen</strong> – alle Einnahmen im Monat</li>
                        <li><strong>Ausgaben</strong> – alle Ausgaben im Monat</li>
                        <li><strong>Bestand</strong> – aktueller Kassenbestand am Monatsende</li>
                        <li><strong>Aktionen</strong> – Bearbeiten (<i class="fa-solid fa-pen-to-square"></i>) oder
                            Löschen (<i class="fa-solid fa-trash"></i>)</li>
                    </ul>

                    <div class="tip">
                        <strong>Tipp:</strong> Mit der Suche über der Tabelle können Sie schnell nach bestimmten Monaten
                        oder Beträgen suchen.
                    </div>

                    <h2>Benachrichtigungen</h2>
                    <p>
                        Nach einer Berechnung erscheint oben rechts eine <strong>Benachrichtigung</strong>, die
                        bestätigt,
                        ob neue Monate hinzugefügt oder Bestände aktualisiert wurden. Dort wird auch der Gesamtsaldo
                        angezeigt.
                    </p>

                    <h2>Löschen von Beständen</h2>
                    <p>
                        Wenn Sie auf <i class="fa-solid fa-trash"></i> klicken, werden Sie zunächst um eine Bestätigung
                        gebeten.
                        Erst danach wird der Eintrag endgültig gelöscht.
                    </p>

                    <h2>Zusammenfassung</h2>
                    <p>
                        Die Seite „Bestände“ hilft Ihnen, den Überblick über die Entwicklung Ihrer Kasse zu behalten.
                        Nutzen Sie die Berechnungsfunktion regelmäßig, um Ihre Daten auf dem neuesten Stand zu halten.
                    </p>

                    <p class="mt-4">
                    <a href="../Bestaende.php" class="btn btn-secondary">
                        <i class="fa fa-arrow-left"></i> Zurück zu den Beständen 
                    </a>
                </p>
                </body>

                </html>
            </div>
        </div>

        <script src="js/jquery.min.js"></script>
        <script src="js/bootstrap.bundle.min.js"></script>


</body>

</html>