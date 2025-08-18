<?php
ob_start();
session_start();
if ($_SESSION['userid'] == "") {
  header('Location: Login.php'); // zum Loginformular
}
?>

<!DOCTYPE html>
<html>

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Kassenbuch Buchungen</title>

  <!-- CSS -->
  <link href="css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
  <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
  <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.4.1/css/responsive.dataTables.min.css">

  <!-- JS -->
  <script src="js/jquery.min.js"></script>
  <script src="js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
  <script src="https://cdn.datatables.net/responsive/2.4.1/js/dataTables.responsive.min.js"></script>
  <style>
    /* === Grundlayout === */
    html,
    body {
      height: 100%;
      margin: 0;
      background-color: #dedfe0ff;
      /* hellgrau statt reinweiß */
    }


    /* Wrapper nimmt die volle Höhe ein und ist Flex-Container */
    .wrapper {
      min-height: 100vh;
      /* viewport height */
      display: flex;
      flex-direction: column;
    }

    /* Container oder Content-Bereich wächst flexibel */
    .container {
      flex: 1;
      /* nimmt den verfügbaren Platz ein */
    }

    /* Footer bleibt unten */
    footer {
      /* kein spezielles CSS nötig, wenn wrapper und container wie oben */
    }

    /* === Karten-Design mit Schatten === */
    .card {
      font-size: 0.9rem;
      background-color: #ffffff;
      border: 1px solid #dee2e6;
      box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
      /* leichter Schatten */
      transition: transform 0.2s ease-in-out;
    }

    .card:hover {
      transform: scale(1.01);
      /* kleine Hover-Interaktion */
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    }

    .card-title {
      font-size: 1.1rem;
    }

    .card-body p {
      margin-bottom: 0.5rem;
    }

    .card-img-top {
      height: 200px;
      /* Einheitliche Höhe */
      object-fit: cover;
      /* Bild wird beschnitten, nicht verzerrt */
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

    .custom-header {
      background: linear-gradient(to right, #2a55e0ff, #4670e4ff);
      /* dunkles, klassisches Grün */
      border-bottom: 2px solid #0666f7ff;
      box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
      border-radius: 0 0 1rem 1rem;
    }

    .btn-darkgreen {
      background-color: #0d3dc2ff;
      border-color: #145214;
      color: #fff;
    }

    .btn-darkgreen:hover {
      background-color: #0337e4ff;
      ;
      border-color: #2146beff;
    }

    .btn {
      border-radius: 50rem;
      /* pill-shape */
      font-size: 0.9rem;
      padding: 0.375rem 0.75rem;
      font-size: 0.85rem;
    }
  </style>
</head>

<body>

  <?php

  require 'db.php';
  $Anfangsbestand = 0;
  $yearFilter = date("Y");

  $monatNumFilter = 0;
  $email = $_SESSION['email'];
  $userid = $_SESSION['userid'];

  require_once 'includes/header.php';
  ?>

  <div id="index">
    <form id="indexform">
      <div class="custom-container">
        <header class="custom-header py-2 text-white">
          <div class="container-fluid">
            <div class="row align-items-center">

              <!-- Titel zentriert -->
              <div class="col-12 text-center mb-2 mb-md-0">
                <h2 class="h4 mb-0">Kassenbuch - Buchungen</h2>
              </div>

              <!-- Benutzerinfo + Logout -->
              <div class="col-12 col-md-auto ms-md-auto text-center text-md-end">
                <!-- Auf kleinen Bildschirmen: eigene Zeile für E-Mail -->
                <div class="d-block d-md-inline mb-1 mb-md-0">
                  <span class="me-2">Angemeldet als:
                    <?= htmlspecialchars($_SESSION['email']) ?></span>
                </div>
                <!-- Logout-Button -->
                <a class="btn btn-darkgreen btn-sm" title="Abmelden vom Webshop" href="logout.php">
                  <i class="fa fa-sign-out" aria-hidden="true"></i> Ausloggen
                </a>
              </div>
            </div>
          </div>
        </header>
        <?php

        echo '<div class="btn-toolbar mt-2 mb-2 mx-2" role="toolbar" aria-label="Toolbar with button groups">';
        echo '<div class="btn-group" role="group" aria-label="First group">';
        echo '<a href="AddBuchung.php" title="Position hinzufügen" class="btn btn-primary btn-sm me-4"><span><i class="fa fa-plus" aria-hidden="true"></i></span></a>';
        echo '</div>';

        // Export und Import Buttons in einen flexiblen Container für kleine Bildschirmauflösung
        echo '<div class="d-flex flex-nowrap">';
        echo '<div class="btn-group me-1" role="group" aria-label="Second group">';
        echo '<a href="#" data-bs-toggle="modal" data-bs-target="#exportModal" title="Export Buchungen in CSV-Datei" class="btn btn-primary btn-sm">
              <i class="fa-solid fa-file-export"></i></a>';
        echo '</div>';
        echo '<div class="btn-group me-1" role="group" aria-label="Third group">';
        echo '<a href="Import.php" title="Import Buchungen in CSV-Datei" class="btn btn-primary btn-sm"><span><i class="fa-solid fa-file-import"></i></span></a>';
        echo '</div>';
        echo '<div class="btn-group me-2" role="group" aria-label="First group">';
        if (isset($_GET['monat']) && !empty($_GET['monat'])) {
          echo '<a href="CreatePDF.php?monat=' . htmlspecialchars($_GET['monat']) . '" title="PDF erzeugen" class="btn btn-primary btn-sm">
              <span><i class="fa-solid fa-file-pdf"></i></span>
            </a>';
        }
        echo '</div>';
        echo '</div>';
        echo '</div>';

        // Abrufen der verfügbaren Monate
        // Aktueller Monat im Format YYYY-MM
        $currentMonth = date('Y-m');

        // Abrufen der verfügbaren Monate
        $sql = "SELECT DISTINCT DATE_FORMAT(datum, '%Y-%m') AS monat 
        FROM buchungen 
        WHERE Userid = :userid 
        ORDER BY datum DESC, Day(datum) DESC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['userid' => $userid]);

        echo '<form method="GET" action="" style="display: flex; flex-direction: column; gap: 10px;">';

        // Erste Zeile: Labels
        echo '<div id="divLabels" style="display: flex; justify-content: space-between; width: 30%;">';
        echo '<label for="monat" class="label me-4 mx-2" style="width: 45%; text-align: left;">Bewegungen im Monat:</label>';
        echo '<label for="anfangsbestand" style="width: 45%; text-align: left;">Anfangsbestand:</label>';
        echo '</div>';

        // Zweite Zeile: Eingabefelder
        echo '<div id="divInputs" style="display: flex; justify-content: space-between; width: 30%;">';

        // Dropdown für Bewegungen im Monat
        echo '<select id="monat" name="monat" class="form-control me-4 mx-2" style="width: 45%;" onchange="this.form.submit()">';

        // Option "Alle Monate" nur anzeigen, wenn wir das brauchen
        echo '<option value="">Alle Monate</option>';

        // Deutsche Monatsnamen
        $monatNames = [
          1 => 'Januar',
          2 => 'Februar',
          3 => 'März',
          4 => 'April',
          5 => 'Mai',
          6 => 'Juni',
          7 => 'Juli',
          8 => 'August',
          9 => 'September',
          10 => 'Oktober',
          11 => 'November',
          12 => 'Dezember'
        ];

        // Bestimmen, welcher Monat ausgewählt sein soll
        $selectedMonth = isset($_GET['monat']) && !empty($_GET['monat']) ? $_GET['monat'] : $currentMonth;

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
          $monat = $row['monat'];
          $monatNum = (new DateTime($monat . '-01'))->format('n'); // Monatsnummer
          $monatFormatted = $monatNames[$monatNum] . ' ' . (new DateTime($monat . '-01'))->format('Y');

          $selected = ($selectedMonth === $monat) ? 'selected' : '';
          echo "<option value=\"$monat\" $selected>$monatFormatted</option>";
        }

        echo '</select><br>';


        // Wenn ein Monat ausgewählt wurde, dann filtern wir die Buchungen
        $monatFilter = $selectedMonth; // Aus der Dropdown-Logik
        $monatNumFilter = (new DateTime($monatFilter . '-01'))->format('n'); // 'n' gibt die Monatszahl zurück
        
        if ($monatFilter <> '')
          $yearFilter = substr($monatFilter, 0, 4);

        if ($monatFilter <> '') {
          $startDatum = $monatFilter . "-01";
          $endDatum = date("Y-m-t", strtotime($startDatum)); // Letzter Tag des Monats
          $sql = "SELECT * FROM buchungen 
            WHERE datum BETWEEN :startDatum AND :endDatum 
            AND userid = :userid 
            AND barkasse = 1 
            ORDER BY datum DESC";
          $stmt = $pdo->prepare($sql);
          $stmt->execute(['startDatum' => $startDatum, 'endDatum' => $endDatum, 'userid' => $userid]);

        } else {
          //Wenn kein Monat ausgewählt wurde, alle Buchungen anzeigen
          $sql = "SELECT * FROM buchungen WHERE userid = :userid and barkasse = 1 ORDER BY datum DESC";
          $stmt = $pdo->prepare($sql);
          $stmt->execute(['userid' => $userid]);
        }

        $sql = "SELECT * FROM bestaende WHERE  DATE_FORMAT(datum, '%Y-%m') = :monat AND Year(datum) = :year AND userid = :userid ORDER BY datum DESC";
        $stmtAB = $pdo->prepare($sql);
        $stmtAB->execute(['year' => $yearFilter, 'monat' => $monatFilter, 'userid' => $userid]);

        while ($row = $stmtAB->fetch(PDO::FETCH_ASSOC)) {
          // Textfeld für Anfangsbestand
          $Anfangsbestand = $row['bestand'];
          echo '<input class="form-control" type="text" name="anfangsbestand" id="anfangsbestand" value="' . number_format($row['bestand'], 2, '.', '') . ' €" style="width: 45%; text-align:right;" step="0.01" disabled>';
        }

        echo '</div>';
        echo '</div>';


        ?>
        <br>
        <div class="custom-container mx-2">
          <table id="TableBuchungen" class="display nowrap me-4">
            <thead>
              <tr>
                <th>Datum</th>
                <th class='visible-column'>Typ</th>
                <th class='visible-column'>Beleg-Nr</th>
                <th class='betrag-right'>Betrag</th>
                <th>Beschreibung</th>
                <th class='visible-column'>Verwendungszweck</th>
                <th></th>
              </tr>
            </thead>
            <tbody>
              <?php

              while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                // Datum ins deutsche Format umwandeln
                $formattedDate = (new DateTime($row['datum']))->format('d.m.Y');

                echo "<tr>
                
                  <td style='vertical-align: top; width:7%;' >{$formattedDate}</td>
                  <td style='vertical-align: top; width:7%;' class='visible-column'>{$row['typ']}</td>
                  <td style='vertical-align: top; width:10%;' class='visible-column'>{$row['belegnr']}</td>
                  <td style='vertical-align: top; width:7%; text-align:right; white-space: nowrap;'>" . number_format($row['betrag'], 2, '.', ',') . " €</td>
                  <td style='vertical-align: top; width:20%;' >{$row['vonan']}</td>
                  <td style='vertical-align: top; width:50%;' class='visible-column'>{$row['beschreibung']}</td>
                  <td style='vertical-align: top; width:7%; white-space: nowrap;'>
                      <a href='EditBuchung.php?id={$row['id']}' style='width:60px;' title='Buchung bearbeiten' class='btn btn-primary btn-sm'><i class='fa-solid fa-pen-to-square'></i></a> 
                      <a href='DeleteBuchung.php?id={$row['id']}' data-id={$row['id']} style='width:60px;' title='Buchung löschen' class='btn btn-danger btn-sm delete-button'><i class='fa-solid fa-trash'></i></a>
                  </td>
                  </tr>";
              }
              ?>
            </tbody>
          </table>

          <div class="modal fade" id="exportModal" tabindex="-1" aria-labelledby="exportModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
              <div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title" id="exportModalLabel">Export auswählen</h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                  <p>Bitte wählen Sie, ob der Export nach Monat oder Jahr erfolgen soll:</p>
                </div>
                <div class="modal-footer">
                  <a href="Export.php?type=monat" class="btn btn-primary">Monat</a>
                  <a href="Export.php?type=jahr" class="btn btn-secondary">Jahr</a>
                </div>
              </div>
            </div>
          </div>
          <?php

          //echo $monatFilter;
          if ($monatFilter <> '') {

            $sql = "SELECT COUNT(*) AS anzahl FROM buchungen WHERE userid = :userid and barkasse = 1 AND Year(datum) = :year AND MONTH(datum) = :monat";
            $stmt = $pdo->prepare($sql);
            $stmt->execute(['year' => 2025, 'monat' => $monatNumFilter, 'userid' => $userid]);
            $resultCount = $stmt->fetch(PDO::FETCH_ASSOC);
          } else {

            $sql = "SELECT COUNT(*) AS anzahl FROM buchungen WHERE userid = :userid and barkasse = 1";
            $stmt = $pdo->prepare($sql);
            $stmt->execute(['userid' => $userid]);
            $resultCount = $stmt->fetch(PDO::FETCH_ASSOC);
          }

          // Summen für den ausgewählten Monat
          if ($monatFilter <> '') {
            $sql = "SELECT SUM(CASE WHEN typ = 'Einlage' THEN betrag ELSE 0 END) AS einlagen,
                    SUM(CASE WHEN typ = 'Ausgabe' THEN betrag ELSE 0 END) AS ausgaben
                    FROM buchungen
                    WHERE Year(datum) = :year AND MONTH(datum) = :monat and userid = :userid and barkasse =1 ";
            $stmt = $pdo->prepare($sql);
            $stmt->execute(['year' => 2025, 'monat' => $monatNumFilter, 'userid' => $userid]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

          } else {
            $sql = "SELECT SUM(CASE WHEN typ = 'Einlage' THEN betrag ELSE 0 END) AS einlagen,
                    SUM(CASE WHEN typ = 'Ausgabe' THEN betrag ELSE 0 END) AS ausgaben
                    FROM buchungen WHERE userid = :userid and barkasse = 1";
            $stmt = $pdo->prepare($sql);
            $stmt->execute(['userid' => $userid]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

          }

          $saldo = $Anfangsbestand + $result['einlagen'] - $result['ausgaben'];

          echo '<div class="col-md-5">';

          // Anzahl Buchungen
          echo '<div class="form-group row me-2">
            <div style="vertical-align: top;" class="col-3">Anzahl Buchungen:</div>
            <div style="text-align:right;vertical-align: top;" class="col-9 col-md-3">' . number_format($resultCount['anzahl'], 0, '.', '.') . '</div>
          </div>';

          // Einnahmen
          echo '<div class="form-group row me-2">
            <div style="vertical-align: top;" class="col-3">Einlagen:</div>
            <div style="text-align:right;vertical-align: top;" class="col-9 col-md-3">' . number_format($Anfangsbestand + $result['einlagen'], 2, '.', '.') . ' €</div>
          </div>';

          // Ausgaben
          echo '<div class="form-group row me-2">
            <div style="vertical-align: top;" class="col-3">Ausgaben:</div>
            <div style="text-align:right;vertical-align: top;" class="col-9 col-md-3">' . number_format($result['ausgaben'], 2, '.', '.') . ' €</div>
          </div>';

          // Saldo
          echo '<div class="form-group row me-2">
            <div style="vertical-align: top;" class="col-3"><b>Neuer Bestand:</b></div>
            <div style="text-align:right;vertical-align: top;" class="col-9 col-md-3"><b>' . number_format($saldo, 2, '.', '.') . ' €</b></div>
          </div>';

          echo '</div>';

          $ausgaben = number_format($result['ausgaben'], 2, '.', ',');

          // // Update Bestände
          if ($monatFilter <> '') {
            $sqlBestaende = "UPDATE bestaende  SET ausgaben = :ausgaben, einlagen = :einlagen WHERE monat = :monat AND  userid = :userid AND Year(datum) = :year";
            $stmtBestaende = $pdo->prepare($sqlBestaende);
            $stmtBestaende->execute(['ausgaben' => $ausgaben, 'einlagen' => $Anfangsbestand + $result['einlagen'], 'userid' => $userid, 'monat' => $monatNumFilter, 'year' => 2025]);
          }

          $format = "txt"; //Moeglichkeiten: csv und txt
          
          $datum_zeit = date("d.m.Y H:i:s");
          $ip = $_SERVER["REMOTE_ADDR"];
          $site = $_SERVER['REQUEST_URI'];
          $browser = $_SERVER["HTTP_USER_AGENT"];

          $monate = array(1 => "Januar", 2 => "Februar", 3 => "Maerz", 4 => "April", 5 => "Mai", 6 => "Juni", 7 => "Juli", 8 => "August", 9 => "September", 10 => "Oktober", 11 => "November", 12 => "Dezember");
          $monat = date("n");
          $jahr = date("y");

          $dateiname = "logs/log_" . $monate[$monat] . "_$jahr.$format";

          $header = array("Datum", "IP", "Seite", "Browser");
          $infos = array($datum_zeit, $ip, $site, $browser);

          if ($format == "csv") {
            $eintrag = '"' . implode('", "', $infos) . '"';
          } else {
            $eintrag = implode("\t", $infos);
          }

          $write_header = !file_exists($dateiname);

          $datei = fopen($dateiname, "a");

          if ($write_header) {
            if ($format == "csv") {
              $header_line = '"' . implode('", "', $header) . '"';
            } else {
              $header_line = implode("\t", $header);
            }

            fputs($datei, $header_line . "\n");
          }

          fputs($datei, $eintrag . "\n");
          fclose($datei);

          // Sicherstellen, dass die Datei existiert
          $file = __DIR__ . "/counter.txt";
          if (!file_exists($file)) {
            file_put_contents($file, "0");
          }

          // Dateiinhalt lesen und in Integer umwandeln
          $counterstand = intval(file_get_contents($file));

          // Prüfen, ob die Session-Variable gesetzt ist
          if (!isset($_SESSION['counter_ip'])) {
            $counterstand++;
            file_put_contents($file, $counterstand);

            $_SESSION['counter_ip'] = true;
          }

          ?>


        </div>
        <!-- Bootstrap Modal -->
        <div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-labelledby="confirmDeleteModalLabel"
          aria-hidden="true">
          <div class="modal-dialog">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="confirmDeleteModalLabel">Löschbestätigung</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Schließen"></button>
              </div>
              <div class="modal-body">
                Möchten Sie diese Buchung wirklich löschen?
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Abbrechen</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Löschen</button>
              </div>
            </div>
          </div>
        </div>
        <!-- Toast -->
        <div class="toast-container position-fixed top-0 end-0 p-3">
          <div id="deleteToast" class="toast toast-green" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="toast-header">
              <strong class="me-auto">Benachrichtigung</strong>
              <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
            <div class="toast-body">
              Buchung wurde gelöscht.
            </div>
          </div>
        </div>
    </form>

    <script>
      $(document).ready(function () {
        let deleteId = null; // Speichert die ID für die Löschung

        $('.delete-button').on('click', function (event) {
          event.preventDefault();
          deleteId = $(this).data('id'); // Hole die ID aus dem Button-Datenattribut          
          //alert(deleteId);
          $('#confirmDeleteModal').modal('show'); // Zeige das Modal an
        });

        $('#confirmDeleteBtn').on('click', function () {
          if (deleteId) {
            // Dynamisches Formular erstellen und absenden
            const form = $('<form>', {
              action: 'DeleteBuchung.php',
              method: 'POST'
            }).append($('<input>', {
              type: 'hidden',
              name: 'id',
              value: deleteId
            }));

            $('body').append(form);
            form.submit();
          }
          $('#confirmDeleteModal').modal('hide'); // Schließe das Modal

          // Zeige den Toast an
          var toast = new bootstrap.Toast($('#deleteToast')[0]);
          toast.show();
        });
      });

      function NavBarClick() {
        var x = document.getElementById("myTopnav");
        if (x.className === "topnav") {
          x.className += " responsive";
        } else {
          x.className = "topnav";
        }
      }

      $(document).ready(function () {
        $('#TableBuchungen').DataTable({
          language: {
            url: "https://cdn.datatables.net/plug-ins/1.13.4/i18n/de-DE.json"
          },
          responsive: true,
          pageLength: 50,
          autoWidth: false,
          columnDefs: [
            {
              targets: 1, // Dauerbuchung
              className: "dt-body-nowrap" // Keine Zeilenumbrüche
            }
          ]
        });
      });
    </script>

</body>

</html>

<?php
ob_end_flush();
?>