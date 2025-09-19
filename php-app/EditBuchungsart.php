<?php
ob_start();
session_set_cookie_params([
  'httponly' => true,
  'secure' => true,  // Nur bei HTTPS
  'samesite' => 'Strict'
]);
session_start();

// Nutzerprüfung
if (empty($_SESSION['userid'])) {
  header('Location: Login.php');
  exit();
}

// CSRF-Token erzeugen
if (empty($_SESSION['csrf_token'])) {
  $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>

<!DOCTYPE html>
<html>

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="Datensicherung für das Kassenbuch – einfache Verwaltung und sichere Backups.">
  <meta name="author" content="Gerhard Wißt">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>CashControl Buchungsart bearbeiten</title>
  <link rel="icon" type="image/png" href="images/favicon.png" />

  <link href="css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
  <link href="css/style.css" rel="stylesheet">

</head>

<body>

  <?php

  require 'db.php';

  // Fehler anzeigen
  error_reporting(E_ALL);
  ini_set('display_errors', 1);

  // Prüfen, ob die Verbindung zur Datenbank steht
  if (!$pdo) {
    die("Datenbankverbindung fehlgeschlagen: " . mysqli_connect_error());
  }

  if (isset($_SESSION['success_message'])) {
    echo '<div class="alert alert-success alert-dismissible fade show" role="alert">'
      . htmlspecialchars($_SESSION['success_message']) .
      '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
         </div>';
    // Meldung nach einmaligem Anzeigen löschen
    unset($_SESSION['success_message']);
  }

  $email = $_SESSION['email'];
  $userid = $_SESSION['userid'];


  // Abfrage der E-Mail vom Login
  $email = $_SESSION['email'];

  if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $_SESSION['id'] = $_GET['id'];
    $email = $_SESSION['email'];
    $sql = "Select * FROM buchungsarten WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['id' => $id]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
  } else {
    echo "Keine ID angegeben.";
  }
  require_once 'includes/header.php';

  // Erfolgsmeldung anzeigen
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
          <h2 class="h4 mb-0">CashControl - Buchungsart bearbeiten</h2>
        </div>

        <?php
        require_once 'includes/benutzerversion.php';
        ?>
      </div>
  </header>

  <div id="editbuchungsart" class="container-fluid mt-4">
    <form action="EditBuchungsartEntry.php" method="post">
      <div class="d-flex justify-content-between align-items-center m-2">
        <!-- Linke Buttons -->
        <div>
          <button type="submit" class="btn btn-primary btn-sm rounded-circle me-2 circle-btn">
            <i class="fa fa-save"></i>
          </button>
          <a href="Buchungsarten.php" title="Zurück zu den Buchungsarten"
            class="btn btn-primary btn-sm rounded-circle me-2 circle-btn">
            <i class="fa fa-arrow-left"></i>
          </a>
        </div>

        <!-- Rechte Buttons -->
        <div>
          <a href="help/Buchungsarten.php" title="Hilfe" class="btn btn-primary btn-sm rounded-circle circle-btn">
            <i class="fa fa-question-circle"></i>
          </a>
        </div>
      </div>
      <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
      <div class="custom-container">
        <div class="container-fluid mt-3">
          <!-- Buchungsart -->
          <div class="mb-3 row">
            <label for="buchungsart" class="col-sm-2 col-form-label text-dark">Buchungsart:</label>
            <div class="col-sm-10">
              <input type="text" id="buchungsart" name="buchungsart" class="form-control"
                value="<?= htmlspecialchars($result['buchungsart']) ?>" required>
            </div>
          </div>

          <!-- MWST -->
          <div class="mb-3 row">
            <label for="mwst" class="col-sm-2 col-form-label text-dark">MwSt:</label>
            <div class="col-sm-1 d-flex align-items-center">
              <input class="form-control" type="number" id="mwst" name="mwst" step="0.01"
                value="<?= isset($result['mwst_ermaessigt']) && $result['mwst_ermaessigt'] == 1 ? '1.07' : '1.19' ?>"
                required>
            </div>
          </div>

          <!-- MWST Ermässigt -->
          <div class="mb-3 row">
            <label for="mwst_ermaessigt" class="col-sm-2 col-form-label text-dark">MwSt ermässigt:</label>
            <div class="col-sm-10 d-flex align-items-center">
              <div class="form-check">
                <input class="form-check-input" type="checkbox" id="mwst_ermaessigt" name="mwst_ermaessigt" value="1"
                  <?= isset($result['mwst_ermaessigt']) && $result['mwst_ermaessigt'] == 1 ? 'checked' : '' ?>>
                <label class="form-check-label" for="mwst_ermaessigt"></label>
              </div>
            </div>
          </div>

          <!-- Dauerbuchung -->
          <div class="mb-3 row">
            <label for="dauerbuchung" class="col-sm-2 col-form-label text-dark">Dauerbuchung:</label>
            <div class="col-sm-10 d-flex align-items-center">
              <div class="form-check">
                <input class="form-check-input" type="checkbox" id="dauerbuchung" name="dauerbuchung" value="1"
                  <?= isset($result['Dauerbuchung']) && $result['Dauerbuchung'] == 1 ? 'checked' : '' ?>>
                <label class="form-check-label" for="dauerbuchung"></label>
              </div>
            </div>
          </div>

          <!-- Geändert am (hidden) -->
          <div class="mb-3 row" style="visibility: hidden;">
            <label for="updated_at" class="col-sm-2 col-form-label text-dark">Geändert am:</label>
            <div class="col-sm-10">
              <input type="date" id="updated_at" name="updated_at" class="form-control"
                value="<?= htmlspecialchars($result['updated_at']) ?>">
            </div>
          </div>
        </div>
    </form>
  </div>

  <!-- JS -->
  <script src="js/jquery.min.js"></script>
  <script src="js/bootstrap.bundle.min.js"></script>

  <script>
    // Heutiges Datum automatisch setzen
    document.addEventListener("DOMContentLoaded", function () {
      const today = new Date();
      const formattedDate = today.toISOString().split('T')[0];
      document.getElementById("datum").value = formattedDate;
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
  <script>
    const mwstInput = document.getElementById('mwst');
    const mwstErmaessigtCheckbox = document.getElementById('mwst_ermaessigt');

    // Funktion, um MwSt automatisch zu setzen
    function updateMwst() {
      mwstInput.value = mwstErmaessigtCheckbox.checked ? '1.07' : '1.19';
    }

    // Eventlistener hinzufügen
    mwstErmaessigtCheckbox.addEventListener('change', updateMwst);
  </script>