<?php
ob_start();
session_start();
if (empty($_SESSION['userid'])) {
  header('Location: Login.php');
  exit;
}

require 'db.php';
$userid = $_SESSION['userid'];
$email = $_SESSION['email'];
if (!isset($_SESSION['kassennummer']) || empty($_SESSION['kassennummer'])) {

  $_SESSION['kassennummer'] = isset($_GET['kassennummer']) ? $_GET['kassennummer'] : 0;
}
$kassennummer = $_SESSION['kassennummer'] ?? null;

?>

<!DOCTYPE html>
<html>

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>CashControl Buchungen</title>

  <!-- CSS -->
  <link href="css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
  <link href="css/jquery.dataTables.min.css" rel="stylesheet">
  <link href="css/responsive.dataTables.min.css" rel="stylesheet">

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

    #TableBuchungen {
      width: 100%;
      font-size: 0.9rem;
    }

    #TableBuchungen tbody tr:hover {
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

  $Anfangsbestand = 0;
  $yearFilter = date("Y");


  $monatNumFilter = 0;
  $email = $_SESSION['email'];
  $userid = $_SESSION['userid'];


  require_once 'includes/header.php';

  // CSRF-Token erzeugen
  if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
  }
  ?>

  <div id="index">
    <form id="indexform">
      <input type="hidden" id="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
      <div class="custom-container">
        <header class="custom-header py-2 text-white">
          <div class="container-fluid">
            <div class="row align-items-center">

              <?php
              $sql = "SELECT * FROM kasse WHERE userid = :userid AND id = :kassennummer";
              $stmt = $pdo->prepare($sql);
              $stmt->execute([
                'userid' => $userid,
                'kassennummer' => $kassennummer
              ]);

              $kasse = "Unbekannte Kasse";
              while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $kasse = $row['kasse'];
              }
              ?>
              <!-- Titel zentriert -->
              <div class="col-12 text-center mb-2 mb-md-0">
                <h2 class="h4 mb-0"><?php echo htmlspecialchars($kasse); ?> - Buchungen</h2>
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

        echo '<form method="GET" action="" style="class="d-flex mb-2" style="gap: 20px;" flex-direction: column; gap: 10px;">';

        // Erste Zeile: Labels        
        echo '<div id="divLabels" class="d-flex mb-2 mx-2" style="gap: 20px;">';
        echo '<label for="monat" class="form-label mb-0" style="width: 200px;">Bewegungen im Monat:</label>';
        echo '<label for="anfangsbestand" class="form-label mb-0" style="width: 200px;">Anfangsbestand:</label>';
        echo '</div>';

        // Zweite Zeile: Eingabefelder
        echo '<div id="divInputs" class="d-flex mb-3 mx-2" style="gap: 20px;">';

        // Dropdown für Bewegungen im Monat
        echo '<select id="monat" name="monat" class="form-control" style="width: 200px;" onchange="this.form.submit()">';

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
        if (isset($_GET['monat'])) {
          $selectedMonth = $_GET['monat']; // kann leer sein
        } else {
          $selectedMonth = $currentMonth; // nur beim ersten Laden
        }

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
          $monat = $row['monat'];
          $monatNum = (new DateTime($monat . '-01'))->format('n'); // Monatsnummer
          $monatFormatted = $monatNames[$monatNum] . ' ' . (new DateTime($monat . '-01'))->format('Y');
          $selected = ($selectedMonth === $monat) ? 'selected' : '';
          echo "<option value=\"$monat\" $selected>$monatFormatted</option>";
        }

        echo '</select>';

        // Wenn ein Monat ausgewählt wurde, dann filtern wir die Buchungen
        if (!empty($selectedMonth)) {
          $monatFilter = $selectedMonth;
          $monatNumFilter = (new DateTime($monatFilter . '-01'))->format('n');
          $yearFilter = substr($monatFilter, 0, 4);
        } else {
          $monatFilter = '';
        }

        if ($monatFilter <> '')
          $yearFilter = substr($monatFilter, 0, 4);

        $stmtAB = null; // Initialisierung
        
        if ($monatFilter <> '') {
          $startDatum = $monatFilter . "-01";
          $endDatum = date("Y-m-t", strtotime($startDatum));

          // Buchungen für gewählten Monat
          $sql = "SELECT * FROM buchungen 
                  WHERE datum BETWEEN :startDatum AND :endDatum 
                  AND userid = :userid 
                  AND kassennummer = :kassennummer 
                  AND barkasse = 1 
                  ORDER BY datum DESC";
          $stmt = $pdo->prepare($sql);
          $stmt->execute(['startDatum' => $startDatum, 'endDatum' => $endDatum, 'userid' => $userid, 'kassennummer' => $kassennummer]);

          // Vormonat berechnen
          $vorMonat = date("Y-m", strtotime("-1 month", strtotime($startDatum)));

          // Endbestand des Vormonats holen
          $sql = "SELECT bestand FROM bestaende
            WHERE DATE_FORMAT(datum, '%Y-%m') = :vormonat
            AND userid = :userid AND kassennummer = :kassennummer
            ORDER BY datum DESC 
            LIMIT 1";
          $stmtVB = $pdo->prepare($sql);
          $stmtVB->execute(['vormonat' => $vorMonat, 'userid' => $userid, 'kassennummer' => $kassennummer]);
          $anfangsbestand = $stmtVB->fetchColumn() ?: 0;

        } else {
          // Alle Buchungen
          $sql = "SELECT * FROM buchungen 
        WHERE userid = :userid         
        AND barkasse = 1 
        AND kassennummer = :kassennummer 
         ORDER BY YEAR(datum) ASC, MONTH(datum) ASC, datum ASC, id ASC"; // älteste zuerst, dann nach ID
          $stmt = $pdo->prepare($sql);
          $stmt->execute(['userid' => $userid, 'kassennummer' => $kassennummer]);

          $monatFilter = '';
          $anfangsbestand = 0;
        }

        // Nur ausgeben, wenn $stmtAB existiert und erfolgreich ausgeführt wurde
        if ($anfangsbestand <> 0) {

          echo '<input class="form-control text-end" type="text" name="anfangsbestand" id="anfangsbestand" value="'
            . number_format($anfangsbestand, 2, ',', '.')
            . ' €" style="width: 200px;" step="0.01" disabled>';

        } else {
          // Optional: Wenn kein Monat gewählt, Anfangsbestand 0 anzeigen
          echo '<input class="form-control text-end" type="text" name="anfangsbestand" id="anfangsbestand" value="0,00 €" style="width: 200px;" step="0.01" disabled>';
        }
        echo '</div>'; // Ende divInputs        
        echo '</div>';
        echo '</div>';

        ?>
        <br>
        <div class="table-responsive mx-2">
          <table id="TableBuchungen" class="table table-striped nowrap" style="width:100%">
            <thead>
              <tr>
                <th>Datum</th>
                <th class='visible-column'>Typ</th>
                <th class='visible-column'>Beleg-Nr</th>
                <th class='betrag-right'>Betrag</th>
                <th class='betrag-right'>Mwst.</th>
                <th>Buchungsart</th>                
                <th class='visible-column'>Verwendungszweck</th>
                <th></th>
              </tr>
            </thead>
            <tbody>
              <?php

              // Alle Buchungsarten und deren MwSt-Ermäßigung einmal laden
              $buchungsartenMwst = [];
              $stmtBA = $pdo->query("SELECT buchungsart, mwst_ermaessigt FROM buchungsarten");
              while ($rowBA = $stmtBA->fetch(PDO::FETCH_ASSOC)) {
                $buchungsartenMwst[$rowBA['buchungsart']] = $rowBA['mwst_ermaessigt'];
              }

              // Datenzeilen durchlaufen
              while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                // Datum ins deutsche Format umwandeln
                $formattedDate = (new DateTime($row['datum']))->format('d.m.Y');

                // Ursprünglicher Betrag
                $betragmwst = $row['betrag'];
                $betragFormatted = number_format($row['betrag'], 2, '.', ',') . " €";

                // Farbe für Betrag setzen
                $farbe = ($row['typ'] === 'Einlage') ? 'green' : 'red';
                $betragFormatted = "<span style='color: {$farbe}; font-weight: bold;'>{$betragFormatted}</span>";

                // Berechnung der MwSt
                if ($row['typ'] === 'Ausgabe') {
                  $erm = isset($buchungsartenMwst[$row['buchungsart']]) && $buchungsartenMwst[$row['buchungsart']] == 1;

                  if ($erm) {
                    // 7% MwSt
                    $betragmwst = $betragmwst - ($betragmwst / 1.07);
                  } else {
                    // 19% MwSt
                    $betragmwst = $betragmwst - ($betragmwst / 1.19);
                  }
                } else if ($row['typ'] === 'Einlage') {
                  $betragmwst = 0;
                }

                // MwSt Betrag formatieren
                $betragMwstFormatted = number_format($betragmwst, 2, '.', ',') . " €";
                $betragMwstFormatted = "<span style='color: {$farbe}; font-weight: bold;'>{$betragMwstFormatted}</span>";

                // Tabelle ausgeben
                echo "<tr>
            <td style='vertical-align: top; width:7%;'>{$formattedDate}</td>
            <td style='vertical-align: top; width:7%;' class='visible-column'>{$row['typ']}</td>
            <td style='vertical-align: top; width:10%;' class='visible-column'>{$row['belegnr']}</td>
            <td style='vertical-align: top; width:5%; text-align:right; white-space: nowrap;'>{$betragFormatted}</td>
            <td style='vertical-align: top; width:5%; text-align:right; white-space: nowrap;'>{$betragMwstFormatted}</td>
            <td style='vertical-align: top; width:20%;'>{$row['vonan']}</td>            
            <td style='vertical-align: top; width:20%;'>{$row['beschreibung']}</td>
            <td style='vertical-align: top; width:7%; white-space: nowrap;'>
                <a href='EditBuchung.php?id={$row['id']}' style='width:60px;' title='Buchung bearbeiten' class='btn btn-primary btn-sm'><i class='fa-solid fa-pen-to-square'></i></a> 
                <a href='DeleteBuchung.php?id={$row['id']}' data-id='{$row['id']}' style='width:60px;' title='Buchung löschen' class='btn btn-danger btn-sm delete-button'><i class='fa-solid fa-trash'></i></a>
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
                  <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Abbrechen</button>
                </div>
              </div>
            </div>
          </div>
          <?php

          //echo $monatFilter;
          if ($monatFilter <> '') {
            $sql = "SELECT COUNT(*) AS anzahl FROM buchungen WHERE userid = :userid AND kassennummer = :kassennummer AND barkasse = 1 AND Year(datum) = :year AND MONTH(datum) = :monat";
            $stmt = $pdo->prepare($sql);
            $stmt->execute(['year' => $yearFilter, 'monat' => $monatNumFilter, 'userid' => $userid, 'kassennummer' => $kassennummer]);
            $resultCount = $stmt->fetch(PDO::FETCH_ASSOC);
          } else {
            $sql = "SELECT COUNT(*) AS anzahl FROM buchungen WHERE userid = :userid AND kassennummer = :kassennummer AND barkasse = 1";
            $stmt = $pdo->prepare($sql);
            $stmt->execute(['userid' => $userid, 'kassennummer' => $kassennummer]);
            $resultCount = $stmt->fetch(PDO::FETCH_ASSOC);
          }

          // Summen für den ausgewählten Monat
          if ($monatFilter <> '') {
            $sql = "SELECT SUM(CASE WHEN typ = 'Einlage' THEN betrag ELSE 0 END) AS einlagen,
                    SUM(CASE WHEN typ = 'Ausgabe' THEN betrag ELSE 0 END) AS ausgaben
                    FROM buchungen
                    WHERE Year(datum) = :year AND MONTH(datum) = :monat and userid = :userid AND kassennummer = :kassennummer and barkasse =1 ";
            $stmt = $pdo->prepare($sql);
            $stmt->execute(['year' => $yearFilter, 'monat' => $monatNumFilter, 'userid' => $userid, 'kassennummer' => $kassennummer]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

          } else {
            $sql = "SELECT SUM(CASE WHEN typ = 'Einlage' THEN betrag ELSE 0 END) AS einlagen,
                    SUM(CASE WHEN typ = 'Ausgabe' THEN betrag ELSE 0 END) AS ausgaben
                    FROM buchungen WHERE userid = :userid kassennummer = :kassennummer and barkasse = 1";
            $stmt = $pdo->prepare($sql);
            $stmt->execute(['userid' => $userid, 'kassennummer' => $kassennummer]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
          }

          $einlagen = (float) ($result['einlagen'] ?? 0);
          $ausgaben = (float) ($result['ausgaben'] ?? 0);
          $saldo = $anfangsbestand + $einlagen - $ausgaben;
          $anzahl = (int) ($resultCount['anzahl'] ?? 0);
          ?>
          <div class="card shadow-sm mb-4">
            <div class="card-header bg-primary text-white fw-bold">
              <i class="fa-solid fa-coins me-2"></i> Finanzübersicht
            </div>
            <div class="card-body">
              <div class="row mb-2">
                <div class="col-2 col-md-4">
                  <i class="fa-solid fa-piggy-bank text-secondary me-2"></i> Anfangsbestand:
                </div>
                <div class="col-6 col-md-4 text-end">
                  <?= number_format($anfangsbestand, 2, '.', '.') ?> €
                </div>
              </div>

              <div class="row mb-2">
                <div class="col-2 col-md-4">
                  <i class="fa-solid fa-arrow-down text-success me-2"></i> Einlagen:
                </div>
                <div class="col-6 col-md-4 text-end text-success fw-bold">
                  <?= number_format($einlagen, 2, '.', '.') ?> €
                </div>
              </div>

              <div class="row mb-2">
                <div class="col-6 col-md-4">
                  <i class="fa-solid fa-arrow-up text-danger me-2"></i> Ausgaben:
                </div>
                <div class="col-6 col-md-4 text-end text-danger fw-bold">
                  <?= number_format($ausgaben, 2, '.', '.') ?> €
                </div>
              </div>

              <hr>

              <?php $saldoClass = ($saldo >= 0) ? 'text-success' : 'text-danger'; ?>
              <div class="row">
                <div class="col-6 col-md-4 fw-bold">
                  <i class="fa-solid fa-scale-balanced me-2"></i> Neuer Bestand:
                </div>
                <div class="col-6 col-md-4 text-end fw-bold <?= $saldoClass ?>">
                  <?= number_format($saldo, 2, '.', '.') ?> €
                </div>
              </div>
            </div>
          </div>

          <?php


          // Deutsche Monatsnamen
          $monatsnamen = [
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

          // Anfangsbestand vom Vormonat ermitteln
          $anfangsbestand = 0;
          $stmtVor = $pdo->prepare("
                    SELECT bestand FROM bestaende 
                    WHERE userid = :userid AND kassennummer = :kassennummer AND datum < :erstesDatum 
                    ORDER BY datum DESC LIMIT 1
                ");
          $erstesDatum = "$yearFilter-01-01";
          $stmtVor->execute(['userid' => $userid, 'kassennummer' => $kassennummer, 'erstesDatum' => $erstesDatum]);
          $anfangsbestand = (float) $stmtVor->fetchColumn() ?: 0;

          $saldoVormonat = $anfangsbestand;

          // Deutsche Monatsnamen
          $monatsnamen = [
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

          // Anfangsbestand vom Vormonat ermitteln
          $anfangsbestand = 0;
          $stmtVor = $pdo->prepare("
                  SELECT bestand FROM bestaende 
                  WHERE userid = :userid AND kassennummer = :kassennummer AND datum < :erstesDatum 
                  ORDER BY datum DESC LIMIT 1
              ");
          $erstesDatum = "$yearFilter-01-01";
          $stmtVor->execute(['userid' => $userid, 'kassennummer' => $kassennummer, 'erstesDatum' => $erstesDatum]);
          $anfangsbestand = (float) $stmtVor->fetchColumn() ?: 0;

          $saldoVormonat = $anfangsbestand;

          // Deutsche Monatsnamen
          $monatsnamen = [
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

          // Alle Buchungsarten mit MwSt-Ermäßigung einmal laden
          $buchungsartenMwst = [];
          $buchungsartenMwst = [];

          // prepare statt query
          $stmtBA = $pdo->prepare("SELECT buchungsart, mwst_ermaessigt 
                         FROM buchungsarten  
                         WHERE userid = :userid 
                           AND kassennummer = :kassennummer");

          // execute mit Parametern
          $stmtBA->execute([
            ':userid' => $userid,
            ':kassennummer' => $kassennummer
          ]);

          while ($rowBA = $stmtBA->fetch(PDO::FETCH_ASSOC)) {
            $buchungsartenMwst[$rowBA['buchungsart']] = $rowBA['mwst_ermaessigt'];
          }

          // Anfangsbestand vom Vormonat ermitteln
          $erstesDatum = "$yearFilter-01-01";
          $stmtVor = $pdo->prepare("
              SELECT bestand 
              FROM bestaende 
              WHERE userid = :userid 
                AND kassennummer = :kassennummer
                AND datum <= DATE_SUB(:erstesDatum, INTERVAL 1 DAY)
              ORDER BY datum DESC 
              LIMIT 1
          ");
          $stmtVor->execute(['userid' => $userid, 'kassennummer' => $kassennummer, 'erstesDatum' => $erstesDatum]);
          $anfangsbestand = (float) $stmtVor->fetchColumn() ?: 0;

          $saldoVormonat = $anfangsbestand;

          // Accordion starten
          echo '<div class="accordion mb-4" id="accordionFinanzuebersicht">';

          // Finanzübersicht-Header (am Anfang zugeklappt, blau)
          echo '<div class="accordion-item">
                <h2 class="accordion-header fw-bold" id="headingFinanz">
                    <button class="accordion-button collapsed text-white fw-bold" type="button"
                            data-bs-toggle="collapse" data-bs-target="#collapseFinanz"
                            aria-expanded="false" aria-controls="collapseFinanz"
                            style="background-color:#0d6efd;">
                        <i class="fa-solid fa-coins me-2"></i> Finanzübersicht für ' . $yearFilter . ' (Klick zum Öffnen)
                    </button>
                </h2>
                <div id="collapseFinanz" class="accordion-collapse collapse" aria-labelledby="headingFinanz" data-bs-parent="#accordionFinanzuebersicht">
                    <div class="accordion-body">';

          // Anfangsbestand anzeigen
          echo '<div class="row mb-3">
                    <div class="col-6 col-md-4">
                        <i class="fa-solid fa-piggy-bank text-secondary me-2"></i> Anfangsbestand:
                    </div>
                    <div class="col-6 col-md-4 text-end">
                        ' . number_format($anfangsbestand, 2, '.', '.') . ' €
                    </div>
                  </div>';

          // Monatsübersicht inklusive Buchungen
          for ($monat = 1; $monat <= 12; $monat++) {
            // Gesamtsummen für den Monat
            $stmt = $pdo->prepare("
                    SELECT 
                        SUM(CASE WHEN typ='Einlage' THEN betrag ELSE 0 END) AS einlagen,
                        SUM(CASE WHEN typ='Ausgabe' THEN betrag ELSE 0 END) AS ausgaben
                    FROM buchungen
                    WHERE YEAR(datum)=:jahr AND MONTH(datum)=:monat AND userid=:userid AND kassennummer = :kassennummer AND barkasse=1
                ");
            $stmt->execute(['jahr' => $yearFilter, 'monat' => $monat, 'userid' => $userid, 'kassennummer' => $kassennummer]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            $einlagen = (float) ($result['einlagen'] ?? 0);
            $ausgaben = (float) ($result['ausgaben'] ?? 0);
            $saldo = $saldoVormonat + $einlagen - $ausgaben;
            $saldoClass = ($saldo >= 0) ? 'text-success' : 'text-danger';

            echo '<div class="accordion-item">
                <h2 class="accordion-header" id="heading' . $monat . '">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse' . $monat . '" aria-expanded="false" aria-controls="collapse' . $monat . '">
                        ' . $monatsnamen[$monat] . ' ' . $yearFilter . '
                    </button>
                </h2>
                <div id="collapse' . $monat . '" class="accordion-collapse collapse" aria-labelledby="heading' . $monat . '" data-bs-parent="#collapseFinanz">
                    <div class="accordion-body">
                        <div class="row mb-2">
                            <div class="col-6 col-md-4 fw-bold text-success">
                                <i class="fa-solid fa-arrow-down me-2"></i> Einlagen:
                            </div>
                            <div class="col-6 col-md-4 text-end text-success fw-bold">
                                ' . number_format($einlagen, 2, '.', '.') . ' €
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-6 col-md-4 fw-bold text-danger">
                                <i class="fa-solid fa-arrow-up me-2"></i> Ausgaben:
                            </div>
                            <div class="col-6 col-md-4 text-end text-danger fw-bold">
                                ' . number_format($ausgaben, 2, '.', '.') . ' €
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-12 col-md-8 fw-bold text-end ' . $saldoClass . '">
                                Saldo: ' . number_format($saldo, 2, '.', '.') . ' €
                            </div>
                        </div>';

            // Einzelne Buchungen für den Monat laden
            $stmtB = $pdo->prepare("
                SELECT * FROM buchungen 
                WHERE YEAR(datum)=:jahr AND MONTH(datum)=:monat AND userid=:userid AND barkasse=1 AND kassennummer = :kassennummer
                ORDER BY datum ASC
            ");
            $stmtB->execute(['jahr' => $yearFilter, 'monat' => $monat, 'userid' => $userid, 'kassennummer' => $kassennummer]);

            echo '<table class="table table-sm table-striped">';
            echo '<thead>
            <tr>
                <th>Datum</th>
                <th>Typ</th>
                <th>Belegnr.</th>
                <th class="text-end">Betrag</th>
                <th>Von/An</th>
                <th class="text-end">MwSt</th>
                <th>Beschreibung</th>
            </tr>
          </thead><tbody>';

            while ($row = $stmtB->fetch(PDO::FETCH_ASSOC)) {
              $formattedDate = (new DateTime($row['datum']))->format('d.m.Y');
              $betrag = (float) $row['betrag'];
              $farbe = ($row['typ'] === 'Einlage') ? 'green' : 'red';
              $betragFormatted = "<span style='color: {$farbe}; font-weight: bold;'>" . number_format($betrag, 2, '.', ',') . " €</span>";

              // MwSt berechnen
              if ($row['typ'] === 'Ausgabe') {
                $erm = isset($buchungsartenMwst[$row['buchungsart']]) && $buchungsartenMwst[$row['buchungsart']] == 1;
                $mwstBetrag = $erm ? $betrag - ($betrag / 1.07) : $betrag - ($betrag / 1.19);
              } else {
                $mwstBetrag = 0;
              }
              $mwstFormatted = "<span style='color: {$farbe}; font-weight: bold;'>" . number_format($mwstBetrag, 2, '.', ',') . " €</span>";

              echo "<tr>
                <td>{$formattedDate}</td>
                <td>{$row['typ']}</td>
                <td>{$row['belegnr']}</td>
                <td class='text-end'>{$betragFormatted}</td>
                <td>{$row['vonan']}</td>
                <td class='text-end'>{$mwstFormatted}</td>
                <td>{$row['beschreibung']}</td>
              </tr>";
            }

            echo '</tbody></table>';

            echo '</div></div></div>'; // Accordion-Body, Collapse, Item
          
            $saldoVormonat = $saldo; // für nächsten Monat
          }

          echo '</div></div>'; // Accordion-Body, Accordion-Finanzübersicht
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

    <!-- JS -->
    <script src="js/jquery.min.js"></script>
    <script src="js/bootstrap.bundle.min.js"></script>
    <script src="js/jquery.dataTables.min.js"></script>
    <script src="js/dataTables.min.js"></script>
    <script src="js/dataTables.responsive.min.js"></script>

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
            })).append($('<input>', {
              type: 'hidden',
              name: 'csrf_token',
              value: $('#csrf_token').val() // <- Das Session-Token wird übernommen
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
          language: { url: "https://cdn.datatables.net/plug-ins/1.13.4/i18n/de-DE.json" },
          responsive: {
            details: {
              display: $.fn.dataTable.Responsive.display.modal({
                header: function (row) {
                  var data = row.data();
                  return 'Details zu ' + data[1];
                }
              }),
              renderer: $.fn.dataTable.Responsive.renderer.tableAll({
                tableClass: 'table'
              })
            }
          },
          scrollX: false,
          pageLength: 50,
          autoWidth: false
        });
      });
    </script>

</body>

</html>

<?php ob_end_flush(); ?>