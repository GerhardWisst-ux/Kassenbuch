<!DOCTYPE html>
<html>

<?php
ob_start();
session_start();
if ($_SESSION['userid'] == "") {
  header('Location: Login.php'); // zum Loginformular
}
?>

<head>
  <title>Kassenbuch</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">

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
    /* Allgemeine Einstellungen */
    body {
      font-family: 'Arial', sans-serif;
      background-color: #f4f7f6;
      margin: 0;
      padding: 0;
    }

    label {
      font-size: 14px;
      font-weight: 600;
      color: #333;
    }

    /* Tabelle Margins */
    .custom-container table {
      margin-left: 1.2rem !important;
      margin-right: 1.2rem !important;
      width: 98%;
    }

    .dataTables_wrapper .dataTables_length select,
    .dataTables_wrapper .dataTables_filter,
    .dataTables_info {
      margin-left: 1.2rem !important;
      margin-right: 0.8rem !important;
    }

    .me-4 {
      margin-left: 1.2rem !important;
    }

    .me-2 {
      margin-left: 0.6rem !important;
    }

    .betrag-right {
      text-align: right;
    }

    .topnav {
      background-color: #2d3436;
      overflow: hidden;
      display: flex;
      padding: 10px 20px;
      box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .topnav a {
      color: #fff;
      text-decoration: none;
      padding: 10px 15px;
      font-size: 16px;
      transition: background-color 0.3s ease;
    }

    .topnav a:hover {
      background-color: rgb(161, 172, 169);
      color: #2d3436;
    }

    .topnav .icon {
      display: none;
    }

    /* Spaltenbreiten optimieren */
    @media screen and (max-width: 767px) {

      .custom-container table {
        margin-left: 0.2rem !important;
        margin-right: 0.2rem !important;
        width: 98%;
      }

      .visible-column {
        display: none;
      }

      .me-4 {
        margin-left: 0.2rem !important;
      }

      .mr-2 {
        margin-right: 1.2rem !important;
      }

      .dataTables_wrapper .dataTables_length select,
      .dataTables_wrapper .dataTables_filter,
      .dataTables_info {
        margin-left: 0.2rem !important;
        margin-right: 0.5rem !important;
      }

      #TableBuchungen td,
      #TableBuchungen th {
        /* white-space: nowrap; */
        font-size: 12px;
        /* Schriftgröße anpassen */
      }

      #TableBuchungen td:nth-child(1),
      #TableBuchungen td:nth-child(2),
      #TableBuchungen td:nth-child(3),
      #TableBuchungen td:nth-child(4),
      #TableBuchungen td:nth-child(5),
      #TableBuchungen td:nth-child(6) {
        display: table-cell;
      }

      .topnav a:not(:first-child) {
        display: none;
      }

      .topnav a.icon {
        display: block;
        font-size: 30px;
      }

      .topnav.responsive {
        position: relative;
      }

      .topnav.responsive .icon {
        position: absolute;
        right: 0;
        top: 0;
      }

      .topnav.responsive a {
        display: block;
        text-align: left;
      }

    }

    /* Responsive Design */
    @media screen and (max-width: 626px) {

      #TableBuchungen td,
      #TableBuchungen th {
        /* white-space: nowrap; */
        font-size: 12px;
        /* Schriftgröße anpassen */
      }

      #TableBuchungen td:nth-child(1),
      #TableBuchungen td:nth-child(2),
      #TableBuchungen td:nth-child(3),
      #TableBuchungen td:nth-child(4),
      #TableBuchungen td:nth-child(5),
      #TableBuchungen td:nth-child(6) {
        display: table-cell;
      }


      .topnav a:not(:first-child) {
        display: none;
      }

      .topnav a.icon {
        display: block;
        font-size: 30px;
      }

      .topnav.responsive {
        position: relative;
      }

      .topnav.responsive .icon {
        position: absolute;
        right: 0;
        top: 0;
      }

      .topnav.responsive a {
        display: block;
        text-align: left;
      }


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
  ?>

  <div class="topnav" id="myTopnav">
    <a href="Index.php" class="active">Haupseite</a>
    <a href="Buchungsarten.php">Buchungsarten</a>
    <a href="Bestaende.php">Bestände</a>
    <a class="disabled" href="Impressum.php">Impressum</a>
    <a href="javascript:void(0);" class="icon" onclick="NavBarClick()">
      <i class="fa fa-bars"></i>
    </a>
  </div>
  <form id="bestaendeform">
    <div class="custom-container">
      <div class="mt-0 p-5 bg-secondary text-white text-center rounded-bottom">
        <h1>Kassenbuch</h1>
        <p>Hauptseite</p>
      </div>

      <div class="container-fluid mt-3">
        <div class="row">
          <div class="col-12 text-end">
            <?php echo "<span>Angemeldet als: " . htmlspecialchars($email) . "</span>"; ?>
            <a class="btn btn-primary" title="Abmelden vom Kassenbuch" href="logout.php">
              <i class="fa fa-sign-out" aria-hidden="true"></i>
            </a>
          </div>
        </div>
      </div>
      <?php
      echo '<div class="btn-toolbar" role="toolbar" aria-label="Toolbar with button groups">';
      echo '<div class="btn-group" role="group" aria-label="First group">';
      echo '<a href="AddBuchung.php" title="Position hinzufügen" class="btn btn-primary btn-sm me-4"><span><i class="fa fa-plus" aria-hidden="true"></i></span></a>';
      echo '</div>';

      // Export und Import Buttons in einen flexiblen Container für kleine Bildschirmauflösung
      echo '<div class="d-flex flex-nowrap">';
      echo '<div class="btn-group me-1" role="group" aria-label="Second group">';
      echo '<a href="Export.php" title="Export Buchungen in CSV-Datei" class="btn btn-primary btn-sm"><span><i class="fa-solid fa-file-export"></i></span></a>';
      echo '</div>';
      echo '<div class="btn-group me-1" role="group" aria-label="Third group">';
      echo '<a href="Import.php" title="Import Buchungen in CSV-Datei" class="btn btn-primary btn-sm"><span><i class="fa-solid fa-file-import"></i></span></a>';
      echo '</div>';
      echo '<div class="btn-group me-2" role="group" aria-label="First group">';
      echo '<a href="CreatePDF.php" title="PDF erzeugen" class="btn btn-primary btn-sm"><span><i class="fa-solid fa-file-pdf"></i></span></a>';
      echo '</div>';
      echo '</div>';
      echo '</div>';

      // Abrufen der verfügbaren Monate
      $sql = "SELECT DISTINCT DATE_FORMAT(datum, '%Y-%m') AS monat FROM buchungen WHERE Userid = " . $userid . " ORDER BY datum DESC, Day(datum) DESC";
      $stmt = $pdo->query($sql);

      echo '<form method="GET" action="" style="display: flex; flex-direction: column; gap: 10px;">';

      // Erste Zeile: Labels
      echo '<div id="divLabels" style="display: flex; justify-content: space-between; width: 30%;">';
      echo '<label for="monat" class="label me-4" style="width: 45%; text-align: left;">Bewegungen im Monat:</label>';
      echo '<label for="anfangsbestand" style="width: 45%; text-align: left;">Anfangsbestand:</label>';
      echo '</div>';

      // Zweite Zeile: Eingabefelder
      echo '<div id ="divInputs" style="display: flex; justify-content: space-between; width: 30%;">';

      // Dropdown für Bewegungen im Monat
      echo '<select id="monat" name="monat" class="form-control me-4" style="width: 45%;" onchange="this.form.submit()">';
      echo '<option value="">Alle Monate</option>';

      // Combobox mit den verfügbaren Monaten
      setlocale(LC_TIME, 'de_DE.UTF-8', 'de_DE', 'deu_deu'); // Locale auf Deutsch setzen
      while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $monat = $row['monat'];
        $timestamp = DateTime::createFromFormat('Y-m', $monat)->getTimestamp(); // Zeitstempel aus Monat erzeugen
      
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

        $monatNum = (new DateTime($monat . '-01'))->format('n'); // 'n' gibt die Monatszahl zurück
        $monatFormatted = $monatNames[$monatNum] . ' ' . (new DateTime($monat . '-01'))->format('Y');
        $selected = isset($_GET['monat']) && $_GET['monat'] == $monat ? 'selected' : '';
        echo "<option value=\"$monat\" $selected>$monatFormatted</option>";
      }

      echo '</select><br>';


      // Wenn ein Monat ausgewählt wurde, dann filtern wir die Buchungen
      $monatFilter = isset($_GET['monat']) ? $_GET['monat'] : '';
      $monatNumFilter = (new DateTime($monatFilter . '-01'))->format('n'); // 'n' gibt die Monatszahl zurück
      
      if ($monatFilter <> '')
        $yearFilter = substr($monatFilter, 0, 4);

      //echo $monatFilter;
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
      $stmtAB->execute(['year' => 2025, 'monat' => $monatFilter, 'userid' => $userid]);

      while ($row = $stmtAB->fetch(PDO::FETCH_ASSOC)) {
        // Textfeld für Anfangsbestand
        $Anfangsbestand = $row['bestand'];
        echo '<input class="form-control" type="text" name="anfangsbestand" id="anfangsbestand" value="' . number_format($row['bestand'], 2, '.', '') . ' €" style="width: 45%; text-align:right;" step="0.01" disabled>';
      }

      echo '</div>';

      echo '</div>';

      ?>
      <table id="TableBuchungen" class="display nowrap me-4">
        <thead>
          <tr>
            <th>Datum</th>
            <th class='visible-column'>Typ</th>
            <th>Betrag</th>
            <th>Von/An</th>
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
                  <td style='vertical-align: top; width:7%; white-space: nowrap;' class='betrag-right'>" . number_format($row['betrag'], 2, '.', ',') . " €</td>                  
                  <td style='vertical-align: top; width:10%;' >{$row['vonan']}</td>
                  <td style='vertical-align: top; width:40%;' class='visible-column'>{$row['beschreibung']}</td>
                  <td style='vertical-align: top; width:7%; white-space: nowrap;'>
                      <a href='EditBuchung.php?id={$row['id']}' style='width:60px;' title='Buchung bearbeiten' class='btn btn-primary btn-sm'><i class='fa-solid fa-pen-to-square'></i></a> 
                      <a href='DeleteBuchung.php?id={$row['id']}' style='width:60px;' title='Buchung löschen' class='btn btn-danger btn-sm delete-button'><i class='fa-solid fa-trash'></i></a>
                  </td>
                  </tr>";
          }
          ?>
        </tbody>
      </table>
      <?php

      echo $monatFilter;
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
            <div style="vertical-align: top;" class="col-md-3">Anzahl Buchungen:</div>
            <div style="text-align:right;vertical-align: top;" class="col-md-2">' . number_format($resultCount['anzahl'], 0, '.', '.') . '</div>
          </div>';

      // Einnahmen
      echo '<div class="form-group row me-2">
            <div style="vertical-align: top;" class="col-md-3">Einlagen:</div>
            <div style="text-align:right;vertical-align: top;" class="col-md-2">' . number_format($result['einlagen'], 2, '.', '.') . ' €</div>
          </div>';

      // Ausgaben
      echo '<div class="form-group row me-2">
            <div style="vertical-align: top;" class="col-md-3">Ausgaben:</div>
            <div style="text-align:right;vertical-align: top;" class="col-md-2">' . number_format($result['ausgaben'], 2, '.', '.') . ' €</div>
          </div>';

      // Saldo
      echo '<div class="form-group row me-2">
            <div style="vertical-align: top;" class="col-md-3"><b>Neuer Bestand:</b></div>
            <div style="text-align:right;vertical-align: top;" class="col-md-2"><b>' . number_format($saldo, 2, '.', '.') . ' €</b></div>
          </div>';

      echo '</div>';

      $ausgaben = number_format($result['ausgaben'], 2, '.', ',');

      // // Update Bestände
      if ($monatFilter = '') {
        $sqlBestaende = "UPDATE bestaende  SET ausgaben = :ausgaben WHERE monat = :monat AND  userid = :userid AND Year(datum) = :year";
        $stmtBestaende = $pdo->prepare($sqlBestaende);
        $stmtBestaende->execute(['ausgaben' => $ausgaben, 'userid' => $userid, 'monat' => $monatNumFilter, 'year' => 2025]);
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

      // $fh = fopen("counter.txt", 'w') or die("Can't create file counter.txt.");
      
      // $counterstand = intval(file_get_contents("counter.txt"));
      
      // if (!isset($_SESSION['counter_ip'])) {
      //   $counterstand++;
      //   file_put_contents("counter.txt", $counterstand);
      
      //   $_SESSION['counter_ip'] = true;
      // }
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
            Möchten Sie diese Position wirklich löschen?
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
          Position wurde gelöscht.
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
      const topnav = document.getElementById("myTopnav");
      if (topnav.className === "topnav") {
        topnav.className += " responsive";
      } else {
        topnav.className = "topnav";
      }
    }

    $(document).ready(function () {
      $('#TableBuchungen').DataTable({
        language: {
          url: "https://cdn.datatables.net/plug-ins/1.13.4/i18n/de-DE.json"
        },
        responsive: true,
        pageLength: 25
            columnDefs: [{
          targets: 1,
          visible: true
        } // Sichtbarkeit der Spalten einstellen
        ]
      });
    });
  </script>

</body>

</html>