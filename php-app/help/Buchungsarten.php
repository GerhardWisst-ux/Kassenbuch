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
    <title>CashControl - Benutzerhilfe Buchungsarten</title>
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
                        <h2 class="h4 mb-0">CashControl - Hilfe Buchungsarten</h2>
                    </div>

                    <?php
                    require_once '../includes/benutzerversion.php';
                    ?>
                </div>
        </header>
        <div class="container-fluid mt-4">
            <div class="custom-container">
                < <div class="section">
                    <h2>1. Anmeldung und Zugriff</h2>
                    <ul>
                        <li>Die Seite prüft, ob der Benutzer angemeldet ist (<code>$_SESSION['userid']</code>).</li>
                        <li>Ist kein Benutzer angemeldet, wird auf <code>Login.php</code> weitergeleitet.</li>
                        <li>Session-Cookies sind gesichert (HTTPOnly, Secure, SameSite Strict).</li>
                        <li>CSRF-Token wird erzeugt (<code>$_SESSION['csrf_token']</code>) für sichere
                            Formularübertragungen.</li>
                    </ul>
            </div>

            <div class="section">
                <h2>2. Header & Kasseninformation</h2>
                <ul>
                    <li>Der Header zeigt die aktuelle Kasse anhand von <code>userid</code> und
                        <code>kassennummer</code>.
                    </li>
                    <li>Benutzerversion wird über <code>benutzerversion.php</code> eingebunden.</li>
                    <li>Die Überschrift enthält den Kassen-Namen und "Buchungsarten".</li>
                </ul>
            </div>

            <div class="section">
                <h2>3. Buttons & Toolbar</h2>
                <ul>
                    <li><strong>+</strong> Button: Weiterleitung zu <code>AddBuchungsart.php</code> zum Hinzufügen einer
                        neuen
                        Buchungsart.</li>
                    <li><strong>Zurück</strong> Button: Weiterleitung zu <code>Index.php</code>.</li>
                </ul>
            </div>

            <div class="section">
                <h2>4. Tabelle der Buchungsarten</h2>
                <ul>
                    <li>Spalten:
                        <ul>
                            <li>ID (unsichtbar, dient nur intern)</li>
                            <li>Buchungsart</li>
                            <li>MwSt</li>
                            <li>MwSt ermäßigt</li>
                            <li>Dauerbuchung</li>
                            <li>Aktionen: Bearbeiten, Löschen</li>
                        </ul>
                    </li>
                    <li>DataTables sorgt für Paging, Suche, Sortierung und responsive Darstellung.</li>
                    <li>Responsive Modus zeigt bei kleinen Bildschirmen die Details in einem Modal.</li>
                </ul>

                <h3>Beispiel-Tabelle</h3>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Buchungsart</th>
                            <th>MwSt</th>
                            <th>MwSt ermäßigt</th>
                            <th>Dauerbuchung</th>
                            <th>Aktionen</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>1</td>
                            <td>Einlage</td>
                            <td>0 %</td>
                            <td>Nein</td>
                            <td>Nein</td>
                            <td>
                                <button class="btn-demo">Bearbeiten</button>
                                <button class="btn-demo">Löschen</button>
                            </td>
                        </tr>
                        <tr>
                            <td>2</td>
                            <td>Ausgabe</td>
                            <td>19 %</td>
                            <td>7 %</td>
                            <td>Ja</td>
                            <td>
                                <button class="btn-demo">Bearbeiten</button>
                                <button class="btn-demo">Löschen</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="section">
                <h2>5. Löschfunktion</h2>
                <ul>
                    <li>Beim Klick auf "Löschen" öffnet sich ein Modal zur Bestätigung.</li>
                    <li>Nach Bestätigung wird ein verstecktes Formular mit ID und CSRF-Token an
                        <code>DeleteBuchungsart.php</code> gesendet.
                    </li>
                    <li>Nach erfolgreicher Löschung erscheint ein Toast, dass die Buchungsart gelöscht wurde.</li>
                </ul>
            </div>

            <div class="section">
                <h2>6. JavaScript / Interaktionen</h2>
                <ul>
                    <li>DataTables initialisiert die Tabelle, speichert Zustände und macht die ID-Spalte unsichtbar.
                    </li>
                    <li>Bootstrap-Modal wird für die Löschbestätigung verwendet.</li>
                    <li>CSRF-Token wird an das Lösch-Formular angehängt.</li>
                </ul>
            </div>

            <div class="section">
                <h2>7. Sicherheit</h2>
                <ul>
                    <li>Nur angemeldete Benutzer können Buchungsarten sehen oder bearbeiten.</li>
                    <li>Alle POST-Formulare verwenden CSRF-Token.</li>
                    <li>DataTables speichert Tabellenzustände sicher im Browser.</li>
                </ul>
            </div>

            <div class="section">
                <h2>8. Ablaufdiagramm Buchungsarten</h2>
                <div class="flowchart">
                    <div class="step">Seite laden</div>
                    <div class="arrow"></div>
                    <div class="step">Daten aus Tabelle <code>buchungsarten</code> laden</div>
                    <div class="arrow"></div>
                    <div class="step">DataTables initialisieren</div>
                    <div class="arrow"></div>
                    <div class="step">Benutzer klickt "Bearbeiten" oder "Löschen"</div>
                    <div class="arrow"></div>
                    <div class="step">Bei Löschen: Modal anzeigen → Formular absenden mit CSRF-Token</div>
                    <div class="arrow"></div>
                    <div class="step">Buchungsart wird gelöscht → Toast-Benachrichtigung</div>
                </div>
            </div>

            <a href="../Buchungsarten.php" class="btn btn-secondary">
                <i class="fa fa-arrow-left"></i> Zurück zu den Buchungsarten
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