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
        body {
            padding: 20px;
            font-family: Arial, sans-serif;
        }

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

        .note {
            background: #f9f9f9;
            border-left: 4px solid #007bff;
            padding: 10px;
            margin: 15px 0;
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
                <p>In diesem Bereich sehen Sie die aktuellen Bestände Ihrer Kasse. Sie können die Daten nach Jahr
                    filtern, Bestände berechnen lassen und sich grafisch anzeigen lassen.</p>

                <h2>1. Navigation</h2>
                <ul>
                    <li><b>Zurück-Pfeil</b> – kehrt zur Startseite zurück.</li>
                    <li><b>Fragezeichen-Symbol</b> – öffnet diese Hilfe.</li>
                </ul>

                <h2>2. Funktionen in der Toolbar</h2>
                <ul>
                    <li><b><i class="fas fa-calculator"></i> Berechnen</b> – berechnet die Bestände für das gewählte
                        Jahr neu.</li>
                    <li><b><i class="fas fa-chart-bar"></i> Diagramm</b> – zeigt die Bestände als Diagramm an.</li>
                </ul>

                <div class="note">
                    Tipp: Wenn Sie ein anderes Jahr auswählen, wird die Berechnung automatisch gestartet.
                </div>

                <h2>3. Auswahl des Jahres</h2>
                <p>Über das Auswahlfeld <b>„Jahr auswählen“</b> können Sie ein bestimmtes Jahr auswählen. Die Tabelle
                    aktualisiert sich automatisch.</p>

                <h2>4. Tabelle der Bestände</h2>
                <p>Die Tabelle zeigt Ihnen für jeden Monat:</p>
                <ul>
                    <li><b>Datum</b></li>
                    <li><b>Einlagen</b></li>
                    <li><b>Ausgaben</b></li>
                    <li><b>Bestand</b></li>
                    <li><b>Aktionen</b> – Bearbeiten (<i class="fa fa-pen-to-square"></i>) oder Löschen (<i
                            class="fa fa-trash"></i>).</li>
                </ul>

                <h2>5. Benachrichtigungen</h2>
                <p>Nach einer Berechnung erscheint eine kurze Meldung oben rechts. Sie informiert Sie, ob neue Bestände
                    berechnet wurden und welcher Gesamtsaldo vorliegt.</p>

                <h2>6. Datensicherheit</h2>
                <p>Das Löschen eines Bestands erfordert eine Bestätigung. Erst nach Klick auf <b>„Löschen
                        bestätigen“</b> wird der Eintrag wirklich entfernt.</p>

                <hr>
                <p><small>CashControl – Hilfe für Bestände | Stand: 2025</small></p>



                <script src="js/jquery.min.js"></script>
                <script src="js/bootstrap.bundle.min.js"></script>
            </div>
        </div>
    </div>
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