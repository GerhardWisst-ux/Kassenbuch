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
    <title>CashControl - Hilfe – Export Datev</title>
    <link href="../css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
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

        /* Flowchart */
        .flowchart {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin: 20px 0;
        }

        .flowchart .step {
            background-color: #007bff;
            color: white;
            padding: 10px 15px;
            border-radius: 5px;
            margin: 10px 0;
            width: 300px;
            text-align: center;
        }

        .flowchart .arrow {
            width: 0;
            height: 0;
            border-left: 10px solid transparent;
            border-right: 10px solid transparent;
            border-top: 20px solid #333;
            margin: 0 auto;
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
                        <h2 class="h4 mb-0">CashControl - Hilfe – Export Datev</h2>
                    </div>

                    <?php
                    require_once '../includes/benutzerversion.php';
                    ?>
                </div>
        </header>
        <div class="container-fluid mt-4">
            <div class="custom-container">
                <p>
                    Mit dieser Funktion können Sie Ihre Buchungen aus CashControl in das
                    DATEV-Format exportieren. Dieses Format wird von Steuerberatern und
                    Buchhaltungssoftware unterstützt.
                </p>

                <h2>1. Auswahl des Kontenrahmens</h2>
                <p>
                    Wählen Sie aus, ob der Export nach <b>SKR03</b> oder <b>SKR04</b> erfolgen soll.
                    Beide sind Standardkontenrahmen in Deutschland:
                </p>
                <ul>
                    <li><b>SKR03</b>: Konten beginnen meist mit 1xxx (z. B. 1000 Kasse).</li>
                    <li><b>SKR04</b>: Konten beginnen meist mit 1xxx (z. B. 1600 Kasse).</li>
                </ul>

                <h2>2. Datumsbereich</h2>
                <p>
                    Sie können den Zeitraum festlegen, für den die Buchungen exportiert werden.
                    Standardmäßig wird der aktuelle Monat vorgeschlagen.
                </p>

                <h2>3. Vorschau</h2>
                <p>
                    Mit der Schaltfläche <b>🔍 Vorschau anzeigen</b> sehen Sie alle relevanten Buchungen
                    tabellarisch mit Kontierung und Beträgen.
                </p>

                <h2>4. Export starten</h2>
                <p>
                    Mit <b>📥 Export starten</b> wird eine CSV-Datei im DATEV-Format erstellt.
                    Diese Datei finden Sie anschließend als Download-Link auf der Seite.
                </p>

                <h2>5. Besonderheiten</h2>
                <ul>
                    <li>Einnahmen sind grün markiert, Ausgaben rot.</li>
                    <li>Standardkonten (z. B. <code>9999</code>) werden verwendet, falls keine Zuordnung vorhanden ist.
                    </li>
                    <li>Die Datei liegt im Verzeichnis <code>/exports/</code> und kann jederzeit erneut erzeugt werden.
                    </li>
                </ul>

                <h2>6. Weitergabe an den Steuerberater</h2>
                <p>
                    Die erzeugte CSV-Datei können Sie direkt an Ihren Steuerberater weitergeben
                    oder in DATEV-kompatible Software importieren.
                </p>

                <hr>
                <p class="text-muted">CashControl – Hilfe zum DATEV-Export</p>

                <p class="mt-4">
                    <a href="../ExportDatev.php" class="btn btn-secondary">
                        <i class="fa fa-arrow-left"></i> Zurück zum DATEV-Export
                    </a>
                </p>

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
                        <script src="../js/jquery.min.js"></script>
                <script src="../js/bootstrap.bundle.min.js"></script>


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