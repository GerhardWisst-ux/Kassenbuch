<?php

session_set_cookie_params([
  'httponly' => true,
  'secure' => true,  // Nur bei HTTPS
  'samesite' => 'Strict'
]);
session_start();
ob_start();

if ($_SESSION['userid'] == "") {
  header('Location: Login.php'); // zum Loginformular
}
$mandantennummer = $_SESSION['mandantennummer'];

$formData = $_SESSION['form_data'] ?? [];
unset($_SESSION['form_data']); // nur einmal anzeigen

?>

<!DOCTYPE html>
<html>

<head>
  <title>CashControl - Buchung bearbeiten</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="Datensicherung für das Kassenbuch – einfache Verwaltung und sichere Backups.">
  <meta name="author" content="Gerhard Wißt">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <link rel="icon" type="image/png" href="images/favicon.png" />
  <link href="css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
  <link href="css/style.css" rel="stylesheet">
</head>

<body>

  <?php

  // CSRF-Token erzeugen
  if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
  }

  require 'db.php';

  // Fehler anzeigen
  error_reporting(E_ALL);
  ini_set('display_errors', 1);

  // Prüfen, ob die Verbindung zur Datenbank steht
  if (!$pdo) {
    die("Datenbankverbindung fehlgeschlagen: " . mysqli_connect_error());
  }

  $email = $_SESSION['email'];
  $userid = $_SESSION['userid'];
  $kassennummer = $_SESSION['kassennummer'] ?? null;

  // Abfrage der E-Mail vom Login
  $email = $_SESSION['email'];
  if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $_SESSION['id'] = $_GET['id'];
    $email = $_SESSION['email'];
    $sql = "Select * FROM buchungen WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['id' => $id]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
  } else {
    echo "Keine ID angegeben.";
  }

  require_once 'includes/header.php';

  if (isset($_SESSION['success_message'])) {
    echo '<div class="alert alert-success alert-dismissible fade show" role="alert">'
      . htmlspecialchars($_SESSION['success_message']) .
      '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
         </div>';
    // Meldung nach einmaligem Anzeigen löschen
    unset($_SESSION['success_message']);
  }

  if (!empty($_SESSION['error_message'])): ?>
    <div class="alert alert-<?= htmlspecialchars($_SESSION['error_message']['type']) ?>">
      <?= htmlspecialchars($_SESSION['error_message']['text']) ?>
    </div>
    <?php unset($_SESSION['error_message']); // Meldung nach Anzeige löschen ?>
  <?php endif; ?>


  <div id="editbuchung">
    <form action="EditBuchungEntry.php" method="post">
      <input type="hidden" name="csrf_token"
        value="<?= htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8') ?>">
      <header class="custom-header py-2 text-white">
        <div class="container-fluid">
          <div class="row align-items-center">

            <?php
            $sql = "SELECT * FROM kasse WHERE userid = :userid AND id = :kassennummer AND mandantennummer = :mandantennummer ";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
              'userid' => $userid,
              'kassennummer' => $kassennummer,
              'mandantennummer' => $mandantennummer
            ]);

            $kasse = null;
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
              $kasse = $row['kasse'];
            }
            ?>

            <!-- Titel zentriert -->
            <div class="col-12 text-center mb-2 mb-md-0">
              <h2 class="h2 mb-0"><?php echo htmlspecialchars($kasse); ?> - Buchung bearbeiten</h2>
            </div>

            <?php
            require_once 'includes/benutzerversion.php';
            ?>
          </div>
        </div>
      </header>

      <br>
      <?php

      $stmtBestand = $pdo->prepare("
            SELECT bestand 
            FROM bestaende 
            WHERE kassennummer = :kassennummer 
              AND userid = :userid
              AND mandantennummer = :mandantennummer
            ORDER BY datum DESC
            LIMIT 1
        ");
      $stmtBestand->execute([
        ':kassennummer' => $kassennummer,
        ':userid' => $userid,
        ':mandantennummer' => $mandantennummer

      ]);
      $aktuellerBestand = $stmtBestand->fetchColumn();
      ?>

      <div class="d-flex justify-content-between align-items-center mx-2">
        <!-- Linke Buttons -->
        <div>
          <button class="btn btn-primary btn-sm rounded-circle me-2 circle-btn" type="submit"><i
              class="fas fa-save"></i></button>

          <a href="Index.php" title="Zurück zur Kassenübersicht"
            class="btn btn-primary btn-sm rounded-circle me-2 circle-btn">
            <i class="fa fa-arrow-left"></i>
          </a>
        </div>

        <!-- Rechte Buttons -->
        <div>
          <a href="help/Buchungen.php" title="Hilfe" class="btn btn-primary btn-sm rounded-circle circle-btn">
            <i class="fa fa-question-circle"></i>
          </a>
        </div>
      </div>

      <div class="mt-2 mx-2">
        <div class="form-group row me-4 mb-3">
          <label class="col-sm-2 col-form-label text-dark">Aktueller Bestand:</label>
          <div class="col-sm-1">
            <div class="input-group">
              <input class="form-control" type="text" name="aktuellerbestand" value="<?= $aktuellerBestand ?>" disabled>
              <span class="input-group-text">€</span>
            </div>
          </div>
        </div>
        <div class="form-group row me-4 mb-3">
          <label class="col-sm-2 col-form-label text-dark">Beleg-Nr:</label>
          <div class="col-sm-2">
            <input class="form-control" type="text" name="belegnr" value="<?= htmlspecialchars($belegnr ?? '') ?>"
              disabled>
          </div>
        </div>
        <div class="form-group row me-4 mb-3">
          <label class="col-sm-2 col-form-label text-dark">Datum:</label>
          <?php
          $datumValue = '';
          if (!empty($result['datum'])) {
            // Datenbankwert ins richtige Format für input type="date"
            $datumValue = (new DateTime($result['datum']))->format('Y-m-d');
          }
          ?>
          <div class="col-sm-1">
            <input id="datum" class="form-control" type="date" name="datum" value="<?= htmlspecialchars($datumValue) ?>"
              required>
          </div>
        </div>
        <div class="form-group row me-4 mb-3">
          <label class="col-sm-2 col-form-label text-dark">Typ:</label>
          <div class="col-sm-1">
            <select class="form-control" name="typ">
              <option value="Einlage" <?= $result['typ'] === 'Einlage' ? 'selected' : '' ?>>Einlage</option>
              <option value="Ausgabe" <?= $result['typ'] === 'Ausgabe' ? 'selected' : '' ?>>Ausgabe</option>
            </select>
          </div>
        </div>
        <div class="form-group row me-4 mb-3">
          <label for="betrag" class="col-sm-2 col-form-label text-dark">Betrag:</label>
          <div class="col-sm-1">
            <div class="input-group">
              <input class="form-control" type="number" step="0.01" id="betrag" name="betrag"
                data-bestand="<?= $aktuellerBestand ?>"
                value="<?= isset($formData['betrag']) ? $formData['betrag'] : number_format((float) $result['betrag'], 2, '.', '') ?>">
              <span class="input-group-text">€</span>
            </div>
            <small id="betragWarnung" class="text-danger fw-bold" style="display:none;">
              Betrag überschreitet den aktuellen Bestand!
            </small>
          </div>
        </div>
        <div class="form-group row me-4 mb-3">
          <label id="labelvonan" class="col-sm-2 col-form-label text-dark">Buchungsart:</label>
          <div class="col-sm-10">
            <select class="form-control" id="buchungsarten-dropdown" name="buchungart_id"
              onchange="toggleCustomInput(this)">
              <?php
              $sql = "SELECT ID, buchungsart FROM buchungsarten WHERE userid = :userid AND mandantennummer = :mandantennummer ORDER BY buchungsart";
              $stmt = $pdo->prepare($sql);
              $stmt->execute(['userid' => $userid, 'mandantennummer' => $mandantennummer]);

              while ($baRow = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $selected = ($baRow['buchungsart'] === $result['buchungsart']) ? "selected" : "";
                echo "<option value='" . htmlspecialchars($baRow['ID']) . "' $selected>" . htmlspecialchars($baRow['buchungsart']) . "</option>";
              }
              ?>
              <option value="custom" <?= ($result['buchungsart'] === 'custom') ? "selected" : "" ?>>Wert eingeben</option>
            </select>

            <input class="form-control mt-2 d-none" type="text" id="custom-input" name="custom_buchungsart"
              placeholder="Wert eingeben"
              value="<?= htmlspecialchars($result['buchungsart'] === 'custom' ? $result['custom_buchungsart'] : '') ?>">
          </div>
        </div>
        <div class="form-group row me-4 mb-3">
          <label class="col-sm-2 col-form-label text-dark">Verwendungszweck:</label>
          <div class="col-sm-10">
            <input class="form-control" type="text" name="beschreibung"
              value="<?= isset($formData['beschreibung']) ? $formData['beschreibung'] : $result['beschreibung'] ?>">
          </div>
        </div>

      </div>
    </form>
  </div>
  <label for="TicketFile" class="col-sm-3 col-form-label text-dark mx-2">Dateien</label>
  <div class="col-sm-9">
    <form action="uploadcashfile.php" method="POST" enctype="multipart/form-data">
      <input type="hidden" name="buchungsid" value="<?= $id ?>">
      <input type="hidden" name="kassennummer" value="<?= htmlspecialchars((string) $kassennummer) ?>">
      <input type="file" name="ticketfile" accept="application/pdf" class="form-control mb-2">
      <button type="submit" class="btn btn-primary btn-sm mx-2">
        <i class="fa-solid fa-upload"></i> Hochladen
      </button>
    </form>
  </div>
  <?php

  $sqlFiles = "SELECT id, kassennummer,mandantennummer, FilePath, UploadedAt 
             FROM cash_files 
             WHERE buchungsid = :buchungsid              
             ORDER BY UploadedAt DESC";
  $stmtFiles = $pdo->prepare($sqlFiles);
  $stmtFiles->execute(['buchungsid' => $id]);
  $files = $stmtFiles->fetchAll(PDO::FETCH_ASSOC);

  if ($files): ?>
    <div class="mt-3 mx-2">
      <h5>Hochgeladene Dateien</h5>
      <table class="table table-bordered table-hover mt-3">
        <thead class="table-light">
          <tr>
            <th>Dateiname</th>
            <th>Aktion</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($files as $file): ?>
            <tr>
              <td>
                <a href="<?= htmlspecialchars($file['FilePath']) ?>" target="_blank">
                  <i class="fa-solid fa-file-pdf text-danger"></i>
                  <?= htmlspecialchars(basename($file['FilePath'])) ?>
                </a>
              </td>
              <td>
                <form action="DeleteCashFile.php" method="POST" style="display:inline;">
                  <input type="hidden" name="FilePath" value="<?= htmlspecialchars($file['FilePath']) ?>">
                  <input type="hidden" name="buchungsid" value="<?= $id ?>">
                  <button type="submit" name="delete" title="Löschen Datei" class="btn btn-danger btn-sm"
                    onclick="return confirm('Soll diese Datei wirklich gelöscht werden?');">
                    <i class="fa-solid fa-trash"></i>
                  </button>
                </form>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  <?php endif; ?>


  <!-- JS -->
  <script src="js/jquery.min.js"></script>
  <script src="js/bootstrap.bundle.min.js"></script>

  <script>
    document.addEventListener("DOMContentLoaded", function () {
      const betragInput = document.getElementById("betrag");
      const warnung = document.getElementById("betragWarnung");
      const typ = document.getElementById("typ");

      const bestand = parseFloat(betragInput.getAttribute("data-bestand"));

      betragInput.addEventListener("input", function () {
        const wert = parseFloat(this.value);
        if (!isNaN(wert) && wert > bestand && typ.value === "Ausgabe") {
          warnung.style.display = "block";
          betragInput.classList.add("is-invalid");
        } else {
          warnung.style.display = "none";
          betragInput.classList.remove("is-invalid");
        }
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

    function toggleCustomInput(select) {

      const customInput = document.getElementById('custom-input');
      if (select.value === 'custom') {
        customInput.classList.remove('d-none');
        customInput.removeAttribute('disabled');
        customInput.setAttribute('required', 'required');
      } else {
        customInput.classList.add('d-none');
        customInput.setAttribute('disabled', 'disabled');
        customInput.removeAttribute('required');
        customInput.value = '';
      }

      const customLabel = document.getElementById('custom-label');
      if (select.value === 'custom') {
        customLabel.classList.remove('d-none');
      } else {
        customLabel.classList.add('d-none');
      }

      const Label = document.getElementById('label');
      if (select.value === 'custom') {
        Label.classList.add('d-none');
      } else {
        Label.classList.remove('d-none');
      }

      // Debug-Ausgabe
      console.log("Aktueller Wert:", select.value || "Keiner ausgewählt");
    }
  </script>