<?php

session_set_cookie_params([
  'httponly' => true,
  'secure' => true,  // Nur bei HTTPS
  'samesite' => 'Strict'
]);
session_start();

ob_start();

// Nutzerprüfung
if (empty($_SESSION['userid'])) {
  header('Location: Login.php');
  exit();
}

// CSRF-Token erzeugen
if (empty($_SESSION['csrf_token'])) {
  $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$formData = $_SESSION['form_data'] ?? [];
unset($_SESSION['form_data']); // nur einmal anzeigen

require 'db.php';
$userid = $_SESSION['userid'];
require_once 'includes/header.php';

// Buchungsarten laden
$sql = "SELECT DISTINCT ID, Buchungsart FROM buchungsarten WHERE userid = :userid ORDER BY Buchungsart";
$stmt = $pdo->prepare($sql);
$stmt->execute(['userid' => $userid]);
$buchungsarten = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="de">

<head>
  <title>CashControl - Buchungsart hinzufügen</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="Datensicherung für das Kassenbuch – einfache Verwaltung und sichere Backups.">
  <meta name="author" content="Gerhard Wißt">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <link rel="icon" type="image/png" href="images/favicon.png" /> <!-- CSS -->
  <link href="css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
  <link href="css/jquery.dataTables.min.css" rel="stylesheet">
  <link href="css/responsive.dataTables.min.css" rel="stylesheet">
  <link href="css/style.css" rel="stylesheet">
</head>

<body>
  <?php
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
  <header class="custom-header text-center">
    <h2 class="h4 mb-0">CashControl - Buchungsart hinzufügen</h2>
    <div class="text-end me-3">
      <?php
      require_once 'includes/benutzerversion.php';
      ?>
    </div>
  </header>
 

  <div id="addbuchung" class="container-fluid mt-4">
    <form action="AddBuchungsartEntry.php" method="post" class="p-1 border rounded bg-light shadow-sm">

    <div class="d-flex justify-content-between align-items-center m-2">
    <!-- Linke Buttons -->
    <div>
      <button class="btn btn-primary btn-sm rounded-circle me-2 circle-btn" type="submit" title="Speichern">
        <i class="fas fa-save"></i>
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
      <input type="hidden" name="created_at" value="<?= date('Y-m-d') ?>">
      <input type="hidden" name="updated_at" value="<?= date('Y-m-d') ?>">

      <!-- Buchungsart -->
      <div class="mb-3 row">
        <label for="buchungsart" class="col-sm-2 col-form-label">Buchungsart:</label>
        <div class="col-sm-10">
          <input class="form-control" id="buchungsart" type="text" name="buchungsart"
            value="<?= htmlspecialchars($formData['buchungsart'] ?? '', ENT_QUOTES) ?>"
            placeholder="Buchungsart eingeben" autofocus>
        </div>
      </div>

      <!-- MwSt -->
      <div class="mb-3 row">
        <label for="mwst" class="col-sm-2 col-form-label">Mwst:</label>
        <div class="col-sm-1">
          <input class="form-control" id="mwst" type="number" step="0.01" name="mwst"
            value="<?= htmlspecialchars($formData['mwst'] ?? '') ?>">
        </div>
      </div>

      <!-- MwSt ermässigt -->
      <div class="mb-3 row">
        <label for="mwst_ermaessigt" class="col-sm-2 col-form-label">Mwst ermässigt:</label>
        <div class="col-sm-10">
          <input class="form-check-input" id="mwst_ermaessigt" type="checkbox" name="mwst_ermaessigt"
            <?= !empty($formData['mwst_ermaessigt']) ? 'checked' : '' ?>>
        </div>
      </div>

      <!-- Dauerbuchung -->
      <div class="mb-3 row">
        <label for="dauerbuchung" class="col-sm-2 col-form-label">Dauerbuchung:</label>
        <div class="col-sm-10">
          <input class="form-check-input" id="dauerbuchung" type="checkbox" name="dauerbuchung"
            <?= !empty($formData['dauerbuchung']) ? 'checked' : '' ?>>
        </div>
      </div>
    </form>
  </div>

  <!-- JS -->
  <script src="js/jquery.min.js"></script>
  <script src="js/bootstrap.bundle.min.js"></script>

</body>

</html>
<script>
  document.addEventListener('DOMContentLoaded', () => {
    const mwstInput = document.getElementById('mwst');
    const buchungsart = document.getElementById('buchungsart');
    const mwstErmaessigtCheckbox = document.getElementById('mwst_ermaessigt');

    function updateMwst() {
      mwstInput.value = mwstErmaessigtCheckbox.checked ? '1.07' : '1.19';
    }

    // Eventlistener für Änderung der Checkbox
    mwstErmaessigtCheckbox.addEventListener('change', updateMwst);

    // MwSt direkt beim Laden setzen
    updateMwst();

    // Optional: Fokus auf das MwSt-Feld setzen
    mwstInput.focus();
    buchungsart.focus();
  });
</script>