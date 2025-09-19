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
                        <h2 class="h4 mb-0">CashControl - Hilfe Auswertungen</h2>
                    </div>

                    <?php
                    require_once '../includes/benutzerversion.php';
                    ?>
                </div>
        </header>
        <div class="container-fluid mt-4">
            <div class="custom-container">
                <p>Diese Hilfeseite erklärt die Funktionen und Bedienung der Auswertungsseite deiner
                    CashControl-Installation.</p>

                <h2>1. Anmeldung und Zugriff</h2>
                <ul>
                    <li>Die Seite prüft, ob du angemeldet bist. Ist <code>$_SESSION['userid']</code> nicht gesetzt,
                        wirst du zur
                        Login-Seite weitergeleitet.</li>
                    <li>Nach erfolgreicher Anmeldung werden die Benutzerinformationen und die zugehörige Kasse geladen.
                    </li>
                </ul>

                <h2>2. Monatsauswahl</h2>
                <ul>
                    <li>Die Dropdown-Liste oben erlaubt die Auswahl eines Monats.</li>
                    <li>Alle verfügbaren Monate werden automatisch aus den Buchungen der gewählten Kasse und des
                        Benutzers
                        generiert.</li>
                    <li>Die Buchungen werden nur für den ausgewählten Monat angezeigt.</li>
                </ul>

                <h2>3. Tabellenübersicht</h2>
                <ul>
                    <li>Die Tabelle zeigt die Ausgaben nach Buchungsart gruppiert:</li>
                    <ul>
                        <li><strong>Buchungsart:</strong> Kategorie der Ausgabe</li>
                        <li><strong>Anzahl:</strong> Anzahl der Buchungen</li>
                        <li><strong>Gesamtbetrag:</strong> Summe aller Buchungen in Euro</li>
                        <li><strong>Anteil:</strong> Prozentualer Anteil am Gesamtbetrag</li>
                    </ul>
                    <li>Die Fußzeile der Tabelle zeigt die Gesamtsummen.</li>
                    <li>Plus-/Minus-Symbole in der Tabelle: Zeigen oder verbergen Details zu jeder Buchungsart.</li>
                </ul>

                <h3>Beispiel-Tabelle</h3>
                <table class="table-schema" id="exampleTable">
                    <thead>
                        <tr>
                            <th></th>
                            <th>Buchungsart</th>
                            <th>Anzahl</th>
                            <th>Gesamtbetrag</th>
                            <th>Anteil</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>+</td>
                            <td>Miete</td>
                            <td>2</td>
                            <td>1.200,00 €</td>
                            <td>40%</td>
                        </tr>
                        <tr>
                            <td>+</td>
                            <td>Material</td>
                            <td>5</td>
                            <td>900,00 €</td>
                            <td>30%</td>
                        </tr>
                        <tr>
                            <td>+</td>
                            <td>Sonstiges</td>
                            <td>3</td>
                            <td>900,00 €</td>
                            <td>30%</td>
                        </tr>
                        <tr>
                            <th colspan="2">Gesamt</th>
                            <th>10</th>
                            <th>3.000,00 €</th>
                            <th>100%</th>
                        </tr>
                    </tbody>
                </table>

                <h2>4. Charts</h2>
                <ul>
                    <li>Mit dem Button <code>Chart</code> wird ein Kreisdiagramm (Pie-Chart) angezeigt.</li>
                    <li>Das Diagramm zeigt den prozentualen Anteil jeder Buchungsart am Gesamtbetrag.</li>
                    <li>Klicke auf die farbigen Kreise oder die Legende, um die entsprechende Zeile in der Tabelle
                        hervorzuheben.
                    </li>
                </ul>

                <h3>Schema Pie-Chart mit interaktiver Legende</h3>
                <div class="chart-schema">
                    <div class="chart-part" style="background-color:#007bff;" data-buchungsart="Miete"></div>
                    <div class="chart-part" style="background-color:#28a745;" data-buchungsart="Material"></div>
                    <div class="chart-part" style="background-color:#dc3545;" data-buchungsart="Sonstiges"></div>
                </div>
                <div class="chart-legend">
                    <div class="legend-item" data-buchungsart="Miete"><span class="legend-color"
                            style="background-color:#007bff;"></span>Miete</div>
                    <div class="legend-item" data-buchungsart="Material"><span class="legend-color"
                            style="background-color:#28a745;"></span>Material</div>
                    <div class="legend-item" data-buchungsart="Sonstiges"><span class="legend-color"
                            style="background-color:#dc3545;"></span>Sonstiges</div>
                </div>

                <h2>5. Benutzerführung</h2>
                <ul>
                    <li>Zurück-Button: Führt zur Hauptseite der Kasse (<code>Index.php</code>).</li>
                    <li>Dropdown-Filter: Wähle einen Monat, um die Auswertung einzugrenzen.</li>
                    <li>Plus-/Minus-Symbole in der Tabelle: Zeigen oder verbergen Details zu jeder Buchungsart.</li>
                    <li>Chart-Button: Visualisiert die Auswertung als Kreisdiagramm.</li>
                </ul>

                <h2>6. Technische Hinweise</h2>
                <ul>
                    <li>Die Berechnungen erfolgen serverseitig über PDO-Abfragen an die Datenbank.</li>
                    <li>Die Auswertung berücksichtigt nur Buchungen mit <code>barkasse=1</code> und
                        <code>typ='Ausgabe'</code>.</li>
                    <li>Die Tabelle verwendet <code>DataTables</code> für Sortierung, Suche und responsive Darstellung.
                    </li>
                    <li>Ajax wird genutzt, um bei Klick auf die Details weitere Buchungsinformationen zu laden
                        (<code>GetBuchungenDetails.php</code>).</li>
                </ul>

                <h2>7. Sicherheit</h2>
                <ul>
                    <li>Session-Cookies sind sicher (HTTPOnly, Secure, SameSite).</li>
                    <li>Nur angemeldete Benutzer können auf die Auswertungen zugreifen.</li>
                </ul>
                
                <a href="../Auswertungen.php" class="btn btn-secondary">
                    <i class="fa fa-arrow-left"></i> Zurück zu den Auswertungen
                </a>

                <script>
                    // Interaktive Legende: Highlight Tabelle
                    const legendItems = document.querySelectorAll('.legend-item');
                    const tableRows = document.querySelectorAll('#exampleTable tbody tr');

                    legendItems.forEach(item => {
                        item.addEventListener('click', () => {
                            const buchungsart = item.dataset.buchungsart;
                            tableRows.forEach(row => {
                                row.classList.remove('highlight');
                                if (row.cells[1] && row.cells[1].innerText === buchungsart) {
                                    row.classList.add('highlight');
                                    row.scrollIntoView({ behavior: 'smooth', block: 'center' });
                                }
                            });
                        });
                    });

                    // Optional: Klick auf Chart-Part ebenfalls Highlight
                    const chartParts = document.querySelectorAll('.chart-part');
                    chartParts.forEach(part => {
                        part.addEventListener('click', () => {
                            const buchungsart = part.dataset.buchungsart;
                            tableRows.forEach(row => {
                                row.classList.remove('highlight');
                                if (row.cells[1] && row.cells[1].innerText === buchungsart) {
                                    row.classList.add('highlight');
                                    row.scrollIntoView({ behavior: 'smooth', block: 'center' });
                                }
                            });
                        });
                    });
                </script>

</body>

</html>