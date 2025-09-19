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

        h1,
        h2 {
            color: #003366;
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
                        <h2 class="h4 mb-0">CashControl - Hilfe Buchungen</h2>
                    </div>

                    <?php
                    require_once '../includes/benutzerversion.php';
                    ?>
                </div>
        </header>
        <div class="container-fluid mt-4">
            <div class="custom-container">
                <p>Im Bereich <b>Buchungen</b> verwalten Sie alle Einnahmen und Ausgaben Ihrer Kasse. Hier können Sie
                    neue Buchungen hinzufügen, bestehende bearbeiten oder löschen.</p>

                <h2>1. Navigation</h2>
                <ul>
                    <li><b>Zurück-Pfeil</b> – kehrt zur Startseite oder zur Übersicht der Kassen zurück.</li>
                    <li><b>Fragezeichen-Symbol</b> – öffnet diese Hilfe.</li>
                </ul>

                <h2>2. Funktionen in der Toolbar</h2>
                <ul>
                    <li><b><i class="fa fa-plus"></i> Neue Buchung</b> – öffnet ein Formular, um eine neue Buchung
                        anzulegen.</li>
                    <li><b><i class="fa fa-download"></i> Export</b> – exportiert die Buchungen (z. B. als Excel oder
                        CSV, sofern verfügbar).</li>
                    <li><b><i class="fa fa-arrow-left"></i> Zurück</b> – geht wieder zur Kassenübersicht.</li>
                </ul>

                <h2>3. Tabelle der Buchungen</h2>
                <p>Die Tabelle listet alle Buchungen für die gewählte Kasse auf. Angezeigt werden typischerweise:</p>
                <ul>
                    <li><b>Datum</b> – wann die Buchung erfolgt ist.</li>
                    <li><b>Buchungsart</b> – z. B. Einnahme oder Ausgabe.</li>
                    <li><b>Betrag</b> – Wert in Euro.</li>
                    <li><b>Beschreibung</b> – zusätzliche Informationen.</li>
                    <li><b>Aktionen</b> – zum Bearbeiten (<i class="fa fa-pen-to-square"></i>) oder Löschen (<i
                            class="fa fa-trash"></i>).</li>
                </ul>

                <h2>4. Buchung hinzufügen</h2>
                <p>Klicken Sie auf <b>„Neue Buchung“</b>. Es öffnet sich ein Formular, in dem Sie folgende Angaben
                    machen:</p>
                <ul>
                    <li>Datum</li>
                    <li>Betrag</li>
                    <li>Buchungsart (z. B. Einlage, Ausgabe)</li>
                    <li>Beschreibung (optional)</li>
                </ul>
                <div class="note">Tipp: Pflichtfelder sind mit einem Stern (*) gekennzeichnet.</div>

                <h2>5. Buchung bearbeiten oder löschen</h2>
                <ul>
                    <li><b>Bearbeiten</b> – ändert eine bestehende Buchung.</li>
                    <li><b>Löschen</b> – löscht die Buchung nach einer Sicherheitsabfrage dauerhaft.</li>
                </ul>

                <h2>6. Suche und Filter</h2>
                <p>Über die Suchleiste können Sie gezielt nach Buchungen suchen. Zusätzlich können Sie die Anzahl der
                    angezeigten Zeilen pro Seite ändern.</p>

                <hr>
                <a href="../Buchungen.php" class="btn btn-secondary">
                    <i class="fa fa-arrow-left"></i> Zurück zu den Buchungen
                </a>

                <p><small>CashControl – Hilfe für Buchungen | Stand: 2025</small></p>
            </div>
        </div>
    </div>
</body>

</html>