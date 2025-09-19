<?php
ob_start();
session_set_cookie_params([
  'httponly' => true,
  'secure' => true,  // Nur bei HTTPS
  'samesite' => 'Strict'
]);
session_start();
if ($_SESSION['userid'] == "") {
  header('Location: Login.php'); // zum Loginformular
}

$formData = $_SESSION['form_data'] ?? [];
unset($_SESSION['form_data']); // nur einmal anzeigen

?>

<!DOCTYPE html>
<html>

<head>
  <meta charset="utf-8">
  <title>CashControl - Kasse bearbeiten</title>
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


  // Abfrage der E-Mail vom Login
  $email = $_SESSION['email'];
  if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $_SESSION['id'] = $_GET['id'];
    $email = $_SESSION['email'];
    $sql = "Select * FROM kasse WHERE id = :id";
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
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
      <?= htmlspecialchars($_SESSION['error_message']) ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Schließen"></button>
    </div>
    <?php
    // Meldung nach Anzeige löschen        
    unset($_SESSION['error_message']);
  endif;
  ?>

  <header class="custom-header py-2 text-white">
    <div class="container-fluid">
      <div class="row align-items-center">
        <!-- Titel zentriert -->
        <div class="col-12 text-center mb-2 mb-md-0">
          <h2 class="h4 mb-0">CashControl - Kasse bearbeiten</h2>
        </div>
        <?php
        require_once 'includes/benutzerversion.php';
        ?>
      </div>
    </div>
  </header>

  <div id="editkasse" class="container-fluid mt-4">
    <form action="EditKasseEntry.php" method="post" class="p-1 border rounded bg-light shadow-sm">
      <div class="d-flex justify-content-between align-items-center m-2">
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
          <a href="help/Kasse.php" title="Hilfe" class="btn btn-primary btn-sm rounded-circle circle-btn">
            <i class="fa fa-question-circle"></i>
          </a>
        </div>
      </div>
      <input type="hidden" name="csrf_token"
        value="<?= htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8') ?>">

      <?php $kontonummer_val = $formData['kontonummer'] ?? $result['kontonummer'] ?? ''; ?>
      <div id="editkasse" class="container-fluid mt-4">
        <form action="EditKasseEntry.php" method="post" class="p-3 border rounded bg-light shadow-sm">
          <input type="hidden" name="csrf_token"
            value="<?= htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8') ?>">

          <div class="mb-3 row">
            <label for="kasse" class="col-sm-2 col-form-label">Kasse:</label>
            <div class="col-sm-4">
              <input class="form-control" type="text" id="kasse" name="kasse"
                value="<?= htmlspecialchars($formData['kasse'] ?? $result['kasse'] ?? '') ?>">
            </div>
          </div>

          <div class="mb-3 row">
            <label for="kunde_typ" class="col-sm-2 col-form-label">Kundentyp:</label>
            <div class="col-sm-4">
              <select class="form-select" id="kunde_typ" name="kunde_typ" onchange="toggleKundeFields(this)">
                <option value="privat" <?= (($formData['kunde_typ'] ?? $result['typ'] ?? '') === 'privat') ? 'selected' : '' ?>>Privat</option>
                <option value="gewerblich" <?= (($formData['kunde_typ'] ?? $result['typ'] ?? '') === 'gewerblich') ? 'selected' : '' ?>>Gewerblich</option>
              </select>
            </div>
          </div>

          <!-- Privat: Vorname / Nachname -->
          <div id="privat-fields"
            class="<?= (($formData['kunde_typ'] ?? $result['typ'] ?? '') === 'gewerblich') ? 'd-none' : '' ?>">

            <div class="mb-3 row">
              <label class="col-sm-2 col-form-label">Vorname:</label>
              <div class="col-sm-4">
                <input type="text" name="vorname" class="form-control"
                  value="<?= htmlspecialchars($formData['vorname'] ?? $result['vorname'] ?? '') ?>">
              </div>
            </div>

            <div class="mb-3 row">
              <label class="col-sm-2 col-form-label">Nachname:</label>
              <div class="col-sm-4">
                <input type="text" name="nachname" class="form-control"
                  value="<?= htmlspecialchars($formData['nachname'] ?? $result['nachname'] ?? '') ?>">
              </div>
            </div>

          </div>

          <!-- Gewerblich: Firmenname -->
          <div id="gewerblich-fields"
            class="mb-3 row <?= (($formData['kunde_typ'] ?? $result['typ'] ?? '') === 'gewerblich') ? '' : 'd-none' ?>">
            <label for="firma" class="col-sm-2 col-form-label">Firmenname:</label>
            <div class="col-sm-10">
              <input type="text" name="firma" id="firma" class="form-control"
                value="<?= htmlspecialchars($formData['firma'] ?? $result['firma'] ?? '') ?>">
            </div>
          </div>

          <div class="mb-3 row">
            <label for="kontenrahmen" class="col-sm-2 col-form-label">Kontenrahmen:</label>
            <div class="col-sm-4">
              <select class="form-select" id="kontenrahmen" name="kontenrahmen">
                <option value="">-- Bitte wählen --</option>
                <option value="SKR03">SKR03</option>
                <option value="SKR04">SKR04</option>
              </select>
            </div>
          </div>

          <div class="mb-3 row">
            <label for="kontonummer" class="col-sm-2 col-form-label">Kontonummer:</label>
            <div class="col-sm-4">
              <input readonly class="form-control bg-light" type="text" id="kontonummer" name="kontonummer" required
                pattern="\d{1,6}" title="Maximal 6 Ziffern" maxlength="6"
                value="<?= htmlspecialchars($formData['kontonummer'] ?? $result['kontonummer'] ?? '') ?>">
            </div>
          </div>

          <div class="mb-3 row">
            <label for="anfangsbestand" class="col-sm-2 col-form-label">Anfangsbestand:</label>
            <div class="col-sm-2">
              <div class="input-group">
                <input id="anfangsbestand" class="form-control" type="number" name="anfangsbestand" step="0.01"
                  value="<?= htmlspecialchars($formData['anfangsbestand'] ?? number_format((float) ($result['anfangsbestand'] ?? 0), 2, '.', '')) ?>">
                <span class="input-group-text">€</span>
              </div>
            </div>
          </div>

          <div class="mb-3 row">
            <label for="datumab" class="col-sm-2 col-form-label">Anfangsbestand Datum:</label>
            <div class="col-sm-4">
              <input id="datumab" class="form-control" type="date" name="datumab"
                value="<?= htmlspecialchars($formData['datumab'] ?? $result['datumab'] ?? '') ?>">
            </div>
          </div>

          <div class="mb-3 row">
            <label for="checkminus" class="col-sm-2 col-form-label">Kasse minus:</label>
            <div class="col-sm-10">
              <input class="form-check-input" id="checkminus" type="checkbox" name="checkminus" value="1"
                <?= !empty($formData['checkminus'] ?? $result['checkminus'] ?? null) ? 'checked' : '' ?>>
            </div>
          </div>
        </form>
      </div>

      <!-- JS -->
      <script src="js/jquery.min.js"></script>
      <script src="js/bootstrap.bundle.min.js"></script>

      <!-- JavaScript: Toggle-Logik + Kontenrahmen <-> Kontonummer Abgleich -->
      <script>
        function toggleKundeFields(selectElement) {
          const privatFields = document.getElementById("privat-fields");
          const gewerblichFields = document.getElementById("gewerblich-fields");
          if (!privatFields || !gewerblichFields) return;

          if (selectElement.value === "privat") {
            privatFields.classList.remove("d-none");
            gewerblichFields.classList.add("d-none");
          } else {
            privatFields.classList.add("d-none");
            gewerblichFields.classList.remove("d-none");
          }
        }

        function setKontonummer() {
          const kontenrahmen = document.getElementById("kontenrahmen");
          const kontonummer = document.getElementById("kontonummer");
          if (!kontenrahmen || !kontonummer) return;

          if (kontenrahmen.value === "SKR03") {
            kontonummer.value = "1000";
          } else if (kontenrahmen.value === "SKR04") {
            kontonummer.value = "1600";
          } else {
            kontonummer.value = "";
          }
        }

        function initKontenrahmenFromKontonummer() {
          const kontenrahmen = document.getElementById("kontenrahmen");
          const kontonummer = document.getElementById("kontonummer");
          if (!kontenrahmen || !kontonummer) return;

          const kn = parseInt(kontonummer.value, 10);
          if (kn === 1000) {
            kontenrahmen.value = "SKR03";
          } else if (kn === 1600) {
            kontenrahmen.value = "SKR04";
          } else {
            kontenrahmen.value = "";
          }
        }

        document.addEventListener("DOMContentLoaded", function () {
          // Kundentyp initialisieren (falls vorbefüllt)
          const kundeTypSelect = document.getElementById("kunde_typ");
          if (kundeTypSelect) toggleKundeFields(kundeTypSelect);

          // Kontenrahmen -> Kontonummer Listener
          const kontenrahmenSelect = document.getElementById("kontenrahmen");
          if (kontenrahmenSelect) {
            // initial: falls kontonummer bereits aus DB gesetzt ist, wähle passenden Rahmen
            initKontenrahmenFromKontonummer();

            // wenn der Benutzer den Rahmen ändert, setze die Kontonummer
            kontenrahmenSelect.addEventListener("change", setKontonummer);
          }
        });
      </script>