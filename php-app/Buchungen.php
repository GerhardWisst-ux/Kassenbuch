<?php

ob_start();
session_start();
require 'db.php';

// --- Validierung / Session ---
$userid = $_SESSION['userid'] ?? null;
$email = $_SESSION['email'] ?? null;

if (!$userid) {
  // klassisch: Zugriff verweigern
  header('Location: Login.php');
  exit;
}

// Kassennummer aus Session oder GET
if (empty($_SESSION['kassennummer'])) {
  $_SESSION['kassennummer'] = isset($_GET['kassennummer']) ? (int) $_GET['kassennummer'] : 0;
}
$kassennummer = (int) ($_SESSION['kassennummer'] ?? 0);

// CSRF-Token
if (empty($_SESSION['csrf_token'])) {
  $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// --- Hilfsfunktionen ---
function renderFlash(): void
{
  if (!empty($_SESSION['flash'])) {
    $flash = $_SESSION['flash'];
    $type = htmlspecialchars($flash['type'], ENT_QUOTES, 'UTF-8');
    $text = htmlspecialchars($flash['text'], ENT_QUOTES, 'UTF-8');
    echo '<div class="alert alert-' . $type . ' alert-dismissible fade show mt-3 mx-2" role="alert">';
    echo nl2br($text);
    echo '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Schließen"></button>';
    echo '</div>';
    unset($_SESSION['flash']);
  }
}

function fmtMoney($value): string
{
  // deutsche Formatierung: 1.234,56 €
  return number_format((float) $value, 2, ',', '.') . ' €';
}

function calcVatAndNet(float $brutto, string $typ, array $buchungsartenMwst, string $buchungsart): array
{
  $netto = $brutto;
  $mwst = 0.0;
  $steuersatz = 0;

  if ($typ === 'Ausgabe') {
    $erm = isset($buchungsartenMwst[$buchungsart]) && $buchungsartenMwst[$buchungsart] == 1;
    if ($erm) {
      $steuersatz = 7;
      $netto = $brutto / 1.07;
    } else {
      $steuersatz = 19;
      $netto = $brutto / 1.19;
    }
    $mwst = $brutto - $netto;
  }

  return [
    'netto' => round($netto, 2),
    'mwst' => round($mwst, 2),
    'steuersatz' => $steuersatz
  ];
}

// --- Grundeinstellungen für Seite ---
$yearFilter = date('Y');
$currentMonth = date('Y-m');

// Lade Kassenname
$stmt = $pdo->prepare("SELECT kasse FROM kasse WHERE userid = :userid AND id = :kassennummer LIMIT 1");
$stmt->execute(['userid' => $userid, 'kassennummer' => $kassennummer]);
$kasse = $stmt->fetchColumn() ?: 'Unbekannte Kasse';

// Lade alle Buchungsarten/Ermäßigungen (einmalig)
$buchungsartenMwst = [];
$stmtBA = $pdo->prepare("SELECT buchungsart, mwst_ermaessigt FROM buchungsarten WHERE userid = :userid AND kassennummer = :kassennummer");
$stmtBA->execute(['userid' => $userid, 'kassennummer' => $kassennummer]);
while ($r = $stmtBA->fetch(PDO::FETCH_ASSOC)) {
  $buchungsartenMwst[$r['buchungsart']] = $r['mwst_ermaessigt'];
}

// Verfügbare Monate für Filter
$stmtMonths = $pdo->prepare("SELECT DISTINCT DATE_FORMAT(datum, '%Y-%m') AS monat FROM buchungen WHERE userid = :userid AND kassennummer = :kassennummer AND barkasse = 1 ORDER BY monat DESC");
$stmtMonths->execute(['userid' => $userid, 'kassennummer' => $kassennummer]);

// Bestimme ausgewählten Monat (GET oder default currentMonth)
$selectedMonth = isset($_GET['monat']) ? trim($_GET['monat']) : $currentMonth;

// Wenn user "Alle Monate" gewählt hat (leerer Wert), setze auf ''
if ($selectedMonth === '') {
  $monatFilter = '';
  $monatNumFilter = 0;
} else {
  $monatFilter = $selectedMonth;
  $monatNumFilter = (int) (new DateTime($monatFilter . '-01'))->format('n');
  $yearFilter = substr($monatFilter, 0, 4);
}

// Lade Buchungen (abhängig vom Filter)
if ($monatFilter !== '') {
  $startDatum = $monatFilter . '-01';
  $endDatum = date('Y-m-t', strtotime($startDatum));
  $sql = "SELECT * FROM buchungen 
            WHERE datum BETWEEN :startDatum AND :endDatum
              AND userid = :userid
              AND kassennummer = :kassennummer
              AND barkasse = 1
            ORDER BY datum DESC, id DESC";
  $stmt = $pdo->prepare($sql);
  $stmt->execute(['startDatum' => $startDatum, 'endDatum' => $endDatum, 'userid' => $userid, 'kassennummer' => $kassennummer]);

  // Vormonat-Endbestand
  $vorMonat = date('Y-m', strtotime('-1 month', strtotime($startDatum)));
  $stmtVB = $pdo->prepare("SELECT bestand FROM bestaende WHERE DATE_FORMAT(datum, '%Y-%m') = :vormonat AND userid = :userid AND kassennummer = :kassennummer ORDER BY datum DESC LIMIT 1");
  $stmtVB->execute(['vormonat' => $vorMonat, 'userid' => $userid, 'kassennummer' => $kassennummer]);
  $anfangsbestand = (float) ($stmtVB->fetchColumn() ?: 0);
} else {
  // Alle Buchungen
  $sql = "SELECT * FROM buchungen WHERE userid = :userid AND kassennummer = :kassennummer AND barkasse = 1 ORDER BY YEAR(datum) DESC, MONTH(datum) DESC, datum DESC, id DESC";
  $stmt = $pdo->prepare($sql);
  $stmt->execute(['userid' => $userid, 'kassennummer' => $kassennummer]);
  $anfangsbestand = 0;
}

// --- HTML Ausgabe ---
?>
<!DOCTYPE html>
<html lang="de">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="Datensicherung für das Kassenbuch – einfache Verwaltung und sichere Backups.">
  <meta name="author" content="Dein Name oder Firma">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>CashControl - Buchungen</title>
  <link rel="icon" type="image/png" href="images/favicon.png" />
  <!-- CSS -->
  <link href="css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
  <link href="css/jquery.dataTables.min.css" rel="stylesheet">
  <link href="css/responsive.dataTables.min.css" rel="stylesheet">
  <link href="css/style.css" rel="stylesheet">

  <style>
    .card-hover:hover {
      transform: translateY(-5px);
      box-shadow: 0 8px 16px rgba(0, 0, 0, .2);
      transition: all .3s ease;
    }
  </style>
</head>

<body>
  <?php require_once 'includes/header.php'; ?>

  <header class="custom-header py-2 text-white">
    <div class="container-fluid">
      <div class="row align-items-center">
        <!-- Titel zentriert -->
        <div class="col-12 text-center mb-2 mb-md-0">
          <h2 class="h4 mb-0"><?php echo htmlspecialchars($kasse); ?> - Buchungen</h2>
        </div>

        <?php
        require_once 'includes/benutzerversion.php';
        ?>
      </div>
  </header>
  <div class="container-fluid mt-3">
    <?php renderFlash(); ?>



    <!-- Toolbar -->
    <div class="btn-toolbar mb-3" role="toolbar" aria-label="Toolbar">
      <div class="btn-group me-2" role="group">
        <a href="AddBuchung.php" class="btn btn-primary btn-sm" title="Position hinzufügen"><i
            class="fa fa-plus"></i></a>
      </div>

      <div class="btn-group me-2" role="group">
        <a href="#" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#exportModal"
          title="Export"><i class="fa-solid fa-file-export"></i></a>
      </div>

      <div class="btn-group me-2" role="group">
        <a href="Import.php" class="btn btn-primary btn-sm" title="Import"><i class="fa-solid fa-file-import"></i></a>
      </div>

      <div class="ms-auto">
        <?php if (!empty($monatFilter)): ?>
          <a href="CreatePDF.php?monat=<?= urlencode($monatFilter) ?>" class="btn btn-outline-secondary btn-sm"
            title="PDF erzeugen"><i class="fa-solid fa-file-pdf"></i></a>
        <?php endif; ?>
      </div>
    </div>

    <!-- Filter: Monat + Anfangsbestand -->
    <form method="get" class="row g-2 align-items-end mb-3">
      <div class="col-auto">
        <label for="monat" class="form-label mb-0">Bewegungen im Monat</label>
        <select id="monat" name="monat" class="form-select" onchange="this.form.submit()">
          <option value="">Alle Monate</option>
          <?php while ($m = $stmtMonths->fetch(PDO::FETCH_ASSOC)):
            $monat = $m['monat'];
            $monatNum = (int) (new DateTime($monat . '-01'))->format('n');
            $monatFormatted = [
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
            ][$monatNum] . ' ' . (new DateTime($monat . '-01'))->format('Y');
            $sel = ($selectedMonth === $monat) ? 'selected' : '';
            ?>
            <option value="<?= htmlspecialchars($monat) ?>" <?= $sel ?>><?= htmlspecialchars($monatFormatted) ?></option>
          <?php endwhile; ?>
        </select>
      </div>

      <div class="col-auto">
        <label for="anfangsbestand" class="form-label mb-0">Anfangsbestand</label>
        <input id="anfangsbestand" class="form-control text-end" type="text" value="<?= fmtMoney($anfangsbestand) ?>"
          disabled style="min-width:160px">
      </div>
    </form>

    <!-- Tabelle Buchungen -->
    <div class="table-responsive">
      <table id="TableBuchungen" class="table table-striped table-sm nowrap w-100">
        <thead>
          <tr>
            <th>Datum</th>
            <th class="visible-column">Typ</th>
            <th class="visible-column">Beleg-Nr</th>
            <th class="betrag-right">Betrag</th>
            <th class="betrag-right">Mwst.</th>
            <th class="betrag-right">erm.</th>
            <th>Buchungsart</th>
            <th class="visible-column">Verwendungszweck</th>
            <th></th>
          </tr>
        </thead>
        <tbody>
          <?php
          $summe = 0.0;
          $mwstsumme = 0.0;

          while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $formattedDate = (new DateTime($row['datum']))->format('d.m.Y');
            $brutto = (float) $row['betrag'];
            $typ = $row['typ'];
            $buchungsart = $row['buchungsart'] ?? '';
            $calc = calcVatAndNet($brutto, $typ, $buchungsartenMwst, $buchungsart);
            $mwst = $calc['mwst'];

            // Formatierungen
            $farbe = ($typ === 'Einlage') ? 'green' : 'red';
            $betragFormatted = "<span style='color:{$farbe}; font-weight:bold;'>" . fmtMoney($brutto) . "</span>";
            $mwstFormatted = "<span style='color:{$farbe}; font-weight:bold;'>" . fmtMoney($mwst) . "</span>";
            $erm = ($typ === 'Einlage') ? '' : ((isset($buchungsartenMwst[$buchungsart]) && $buchungsartenMwst[$buchungsart] == 1) ? '7%' : '19%');

            // Summenrechnung
            if ($typ === 'Einlage') {
              $summe += $brutto;
              $mwstsumme += $mwst;
            } else {
              $summe -= $brutto;
              $mwstsumme += $mwst;
            }

            // safe output
            $vonan = htmlspecialchars($row['vonan'] ?? '', ENT_QUOTES, 'UTF-8');
            $beschreibung = htmlspecialchars($row['beschreibung'] ?? '', ENT_QUOTES, 'UTF-8');
            $belegnr = htmlspecialchars($row['belegnr'] ?? '', ENT_QUOTES, 'UTF-8');
            $id = (int) $row['id'];

            // data-order auf ISO-Datum für richtige Sortierung
            $orderDate = (new DateTime($row['datum']))->format('Y-m-d');

            echo "<tr>
                    <td data-order='{$orderDate}'>{$formattedDate}</td>
                    <td class='visible-column'>" . htmlspecialchars($typ) . "</td>
                    <td class='visible-column'>{$belegnr}</td>
                    <td class='text-end'>{$betragFormatted}</td>
                    <td class='text-end'>{$mwstFormatted}</td>
                    <td class='text-end'>{$erm}</td>
                    <td>{$buchungsart}</td>
                    <td class='visible-column'>{$beschreibung}</td>
                    <td class='text-nowrap'>
                        <a href='EditBuchung.php?id={$id}' class='btn btn-primary btn-sm' title='Bearbeiten'><i class='fa-solid fa-pen-to-square'></i></a>
                        <button type='button' class='btn btn-danger btn-sm ms-1 delete-button' data-id='{$id}' title='Löschen'><i class='fa-solid fa-trash'></i></button>
                    </td>
                  </tr>";
          }

          // Summenzeile
          $farbesumme = ($summe >= 0) ? 'green' : 'red';
          $farbesummemwst = ($mwstsumme >= 0) ? 'green' : 'red';
          echo "<tr class='fw-bold'>
                <td>Summe</td>
                <td class='visible-column'></td>
                <td class='visible-column'></td>
                <td class='text-end'><span style='color:{$farbesumme};'>" . fmtMoney($summe) . "</span></td>
                <td class='text-end'><span style='color:{$farbesummemwst};'>" . fmtMoney($mwstsumme) . "</span></td>
                <td></td><td></td><td class='visible-column'></td><td></td>
              </tr>";
          ?>
        </tbody>
      </table>
    </div>

    <!-- Export Modal -->
    <div class="modal fade" id="exportModal" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Export auswählen</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Schließen"></button>
          </div>
          <div class="modal-body">
            <p>Export nach Monat oder Jahr?</p>
          </div>
          <div class="modal-footer">
            <a href="Export.php?type=monat" class="btn btn-primary">Monat</a>
            <a href="Export.php?type=jahr" class="btn btn-secondary">Jahr</a>
            <button class="btn btn-outline-secondary" data-bs-dismiss="modal">Abbrechen</button>
          </div>
        </div>
      </div>
    </div>

    <!-- Löschbestätigung Modal -->
    <div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-labelledby="confirmDeleteModalLabel"
      aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="confirmDeleteModalLabel">Löschbestätigung</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Schließen"></button>
          </div>
          <div class="modal-body">Möchten Sie diese Buchung wirklich löschen?</div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Abbrechen</button>
            <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Löschen</button>
          </div>
        </div>
      </div>
    </div>

    <!-- Toast (optional) -->
    <div class="toast-container position-fixed top-0 end-0 p-3">
      <div id="deleteToast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="toast-header">
          <strong class="me-auto">Benachrichtigung</strong>
          <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
        <div class="toast-body">Buchung wurde gelöscht.</div>
      </div>
    </div>

    <!-- Finanz-Statistiken: Anzahl, Einlagen/Ausgaben, Saldo -->
    <?php
    // Anzahl
    if ($monatFilter !== '') {
      $stmtCount = $pdo->prepare("SELECT COUNT(*) FROM buchungen WHERE userid=:userid AND kassennummer=:kassennummer AND barkasse=1 AND YEAR(datum)=:jahr AND MONTH(datum)=:monat");
      $stmtCount->execute(['userid' => $userid, 'kassennummer' => $kassennummer, 'jahr' => $yearFilter, 'monat' => $monatNumFilter]);
      $anzahl = (int) $stmtCount->fetchColumn();
    } else {
      $stmtCount = $pdo->prepare("SELECT COUNT(*) FROM buchungen WHERE userid=:userid AND kassennummer=:kassennummer AND barkasse=1");
      $stmtCount->execute(['userid' => $userid, 'kassennummer' => $kassennummer]);
      $anzahl = (int) $stmtCount->fetchColumn();
    }

    // Summen Einlagen/Ausgaben
    if ($monatFilter !== '') {
      $stmtSum = $pdo->prepare("SELECT SUM(CASE WHEN typ='Einlage' THEN betrag ELSE 0 END) AS einlagen, SUM(CASE WHEN typ='Ausgabe' THEN betrag ELSE 0 END) AS ausgaben FROM buchungen WHERE YEAR(datum)=:jahr AND MONTH(datum)=:monat AND userid=:userid AND kassennummer=:kassennummer AND barkasse=1");
      $stmtSum->execute(['jahr' => $yearFilter, 'monat' => $monatNumFilter, 'userid' => $userid, 'kassennummer' => $kassennummer]);
      $res = $stmtSum->fetch(PDO::FETCH_ASSOC);
    } else {
      $stmtSum = $pdo->prepare("SELECT SUM(CASE WHEN typ='Einlage' THEN betrag ELSE 0 END) AS einlagen, SUM(CASE WHEN typ='Ausgabe' THEN betrag ELSE 0 END) AS ausgaben FROM buchungen WHERE userid=:userid AND kassennummer=:kassennummer AND barkasse=1");
      $stmtSum->execute(['userid' => $userid, 'kassennummer' => $kassennummer]);
      $res = $stmtSum->fetch(PDO::FETCH_ASSOC);
    }

    $einlagen = (float) ($res['einlagen'] ?? 0);
    $ausgaben = (float) ($res['ausgaben'] ?? 0);
    $saldo = $anfangsbestand + $einlagen - $ausgaben;
    ?>

    <div class="card shadow-sm mb-4">
      <div class="card-header bg-primary text-white fw-bold"><i class="fa-solid fa-coins me-2"></i> Finanzübersicht
      </div>
      <div class="card-body">
        <div class="row mb-2">
          <div class="col-6 col-md-4">Anfangsbestand:</div>
          <div class="col-6 col-md-4 text-end"><?= fmtMoney($anfangsbestand) ?></div>
        </div>
        <div class="row mb-2">
          <div class="col-6 col-md-4">Einlagen:</div>
          <div class="col-6 col-md-4 text-end text-success fw-bold"><?= fmtMoney($einlagen) ?></div>
        </div>
        <div class="row mb-2">
          <div class="col-6 col-md-4">Ausgaben:</div>
          <div class="col-6 col-md-4 text-end text-danger fw-bold"><?= fmtMoney($ausgaben) ?></div>
        </div>
        <hr>
        <div class="row">
          <div class="col-6 col-md-4 fw-bold">Neuer Bestand:</div>
          <div class="col-6 col-md-4 text-end fw-bold <?= ($saldo >= 0) ? 'text-success' : 'text-danger' ?>">
            <?= fmtMoney($saldo) ?>
          </div>
        </div>
        <div class="mt-3">Anzahl Buchungen: <strong><?= $anzahl ?></strong></div>
      </div>
    </div>

    <!-- Accordion Jahres-/Monatsübersicht (Jahr = $yearFilter) -->
    <div class="accordion mb-4" id="accordionFinanzuebersicht">
      <?php
      $saldoVormonat = $anfangsbestand;
      $monatsnamen = [1 => 'Januar', 2 => 'Februar', 3 => 'März', 4 => 'April', 5 => 'Mai', 6 => 'Juni', 7 => 'Juli', 8 => 'August', 9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Dezember'];

      for ($monat = 1; $monat <= 12; $monat++):
        // Monatssummen
        $stmt = $pdo->prepare("SELECT SUM(CASE WHEN typ='Einlage' THEN betrag ELSE 0 END) AS einlagen, SUM(CASE WHEN typ='Ausgabe' THEN betrag ELSE 0 END) AS ausgaben FROM buchungen WHERE YEAR(datum)=:jahr AND MONTH(datum)=:monat AND userid=:userid AND kassennummer=:kassennummer AND barkasse=1");
        $stmt->execute(['jahr' => $yearFilter, 'monat' => $monat, 'userid' => $userid, 'kassennummer' => $kassennummer]);
        $res = $stmt->fetch(PDO::FETCH_ASSOC);
        $einl = (float) ($res['einlagen'] ?? 0);
        $ausg = (float) ($res['ausgaben'] ?? 0);
        $saldoMonat = $saldoVormonat + $einl - $ausg;
        $saldoClass = ($saldoMonat >= 0) ? 'text-success' : 'text-danger';
        ?>
        <div class="accordion-item">
          <h2 class="accordion-header" id="heading<?= $monat ?>">
            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
              data-bs-target="#collapse<?= $monat ?>" aria-expanded="false" aria-controls="collapse<?= $monat ?>">
              <?= $monatsnamen[$monat] . ' ' . $yearFilter ?>
            </button>
          </h2>
          <div id="collapse<?= $monat ?>" class="accordion-collapse collapse" aria-labelledby="heading<?= $monat ?>"
            data-bs-parent="#accordionFinanzuebersicht">
            <div class="accordion-body">
              <div class="row mb-2">
                <div class="col-6 col-md-4">Einlagen:</div>
                <div class="col-6 col-md-4 text-end text-success fw-bold"><?= fmtMoney($einl) ?></div>
              </div>
              <div class="row mb-2">
                <div class="col-6 col-md-4">Ausgaben:</div>
                <div class="col-6 col-md-4 text-end text-danger fw-bold"><?= fmtMoney($ausg) ?></div>
              </div>
              <div class="row mb-3">
                <div class="col-12 col-md-8 text-end fw-bold <?= $saldoClass ?>">Saldo: <?= fmtMoney($saldoMonat) ?></div>
              </div>

              <!-- Einzeltabelle für Monat -->
              <?php
              $stmtB = $pdo->prepare("SELECT * FROM buchungen WHERE YEAR(datum)=:jahr AND MONTH(datum)=:monat AND userid=:userid AND barkasse=1 AND kassennummer=:kassennummer ORDER BY datum ASC");
              $stmtB->execute(['jahr' => $yearFilter, 'monat' => $monat, 'userid' => $userid, 'kassennummer' => $kassennummer]);
              ?>
              <div class="table-responsive">
                <table class="table table-sm table-striped">
                  <thead>
                    <tr>
                      <th>Datum</th>
                      <th>Typ</th>
                      <th>Belegnr.</th>
                      <th class="text-end">Betrag</th>
                      <th>Von/An</th>
                      <th class="text-end">MwSt</th>
                      <th>Beschreibung</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php while ($r = $stmtB->fetch(PDO::FETCH_ASSOC)):
                      $date = (new DateTime($r['datum']))->format('d.m.Y');
                      $b = (float) $r['betrag'];
                      $c = calcVatAndNet($b, $r['typ'], $buchungsartenMwst, $r['buchungsart'] ?? '');
                      $mwstB = $c['mwst'];
                      $farbe = ($r['typ'] === 'Einlage') ? 'green' : 'red';
                      $betragFmt = "<span style='color:{$farbe}; font-weight:bold;'>" . fmtMoney($b) . "</span>";
                      $mwstFmt = "<span style='color:{$farbe}; font-weight:bold;'>" . fmtMoney($mwstB) . "</span>";
                      ?>
                      <tr>
                        <td><?= $date ?></td>
                        <td><?= htmlspecialchars($r['typ']) ?></td>
                        <td><?= htmlspecialchars($r['belegnr']) ?></td>
                        <td class="text-end"><?= $betragFmt ?></td>
                        <td><?= htmlspecialchars($r['vonan']) ?></td>
                        <td class="text-end"><?= $mwstFmt ?></td>
                        <td><?= htmlspecialchars($r['beschreibung']) ?></td>
                      </tr>
                    <?php endwhile; ?>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
        <?php
        $saldoVormonat = $saldoMonat;
      endfor;
      ?>
    </div>
  </div>

  <!-- JS -->
  <script src="js/jquery.min.js"></script>
  <script src="js/bootstrap.bundle.min.js"></script>
  <script src="js/jquery.dataTables.min.js"></script>
  <script src="js/dataTables.responsive.min.js"></script>
  <script src="js/date-eu.js"></script>

  <script>
    $(document).ready(function () {
      $('#TableBuchungen').DataTable({
        language: { url: "https://cdn.datatables.net/plug-ins/1.13.4/i18n/de-DE.json" },
        responsive: {
          details: {
            display: $.fn.dataTable.Responsive.display.modal({
              header: function (row) {
                var data = row.data();
                return 'Details zu ' + (data[1] || '');
              }
            }),
            renderer: $.fn.dataTable.Responsive.renderer.tableAll({ tableClass: 'table' })
          }
        },
        pageLength: 10,
        autoWidth: false,
        columnDefs: [{ type: 'date-eu', targets: 0 }],
        order: [[0, 'desc']]
      });
    });
    $(function () {


      // Lösch-Workflow
      let deleteId = null;
      $('.delete-button').on('click', function (e) {
        e.preventDefault();
        deleteId = $(this).data('id');
        $('#confirmDeleteModal').modal('show');
      });

      $('#confirmDeleteBtn').on('click', function () {
        if (!deleteId) return;
        // dynamisches Formular mit CSRF
        const form = $('<form>', { action: 'DeleteBuchung.php', method: 'POST' })
          .append($('<input>', { type: 'hidden', name: 'id', value: deleteId }))
          .append($('<input>', { type: 'hidden', name: 'csrf_token', value: '<?= $_SESSION['csrf_token'] ?>' }));
        $('body').append(form);
        form.submit();
        $('#confirmDeleteModal').modal('hide');

        // optionaler Toast
        const toastEl = $('#deleteToast')[0];
        if (toastEl) new bootstrap.Toast(toastEl).show();
      });
    });
  </script>
</body>

</html>

<?php ob_end_flush(); ?>