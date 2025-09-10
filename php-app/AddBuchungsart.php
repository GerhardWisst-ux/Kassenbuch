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
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="Datensicherung für das Kassenbuch – einfache Verwaltung und sichere Backups.">
  <meta name="author" content="Dein Name oder Firma">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>CashControl - Buchungsart hinzufügen</title>
  <link rel="icon" type="image/png" href="images/favicon.png" />
  <!-- CSS -->
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
    <form action="AddBuchungsartEntry.php" method="post" class="p-3 border rounded bg-light shadow-sm">
      <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
      <input type="hidden" name="created_at" value="<?= date('Y-m-d') ?>">
      <input type="hidden" name="updated_at" value="<?= date('Y-m-d') ?>">

      <div class="mb-3 row">
        <label for="buchungsart" class="col-sm-2 col-form-label">Buchungsart:</label>
        <div class="col-sm-10">
          <input class="form-control" id="buchungsart" type="text" name="buchungsart" required>
        </div>
      </div>

      <div class="mb-3 row">
        <label for="mwst" class="col-sm-2 col-form-label">Mwst:</label>
        <div class="col-sm-10">
          <input class="form-control" id="mwst" type="number" step="0.01" name="mwst" required>
        </div>
      </div>

      <div class="mb-3 row">
        <label for="mwst_ermaessigt" class="col-sm-2 col-form-label">Mwst ermässigt:</label>
        <div class="col-sm-10">
          <input class="form-check-input" id="mwst_ermaessigt" type="checkbox" name="mwst_ermaessigt">
        </div>
      </div>

      <div class="mb-3 row">
        <label for="dauerbuchung" class="col-sm-2 col-form-label">Dauerbuchung:</label>
        <div class="col-sm-10">
          <input class="form-check-input" id="dauerbuchung" type="checkbox" name="dauerbuchung">
        </div>
      </div>

      <div>
        <button class="btn btn-primary" type="submit" title="Speichern">
          <i class="fas fa-save"></i>
        </button>
        <a href="Buchungsarten.php" class="btn btn-primary" title="Zurück">
          <i class="fa fa-arrow-left"></i>
        </a>
      </div>
    </form>
  </div>

  <!-- JS -->
  <script src="js/jquery.min.js"></script>
  <script src="js/bootstrap.bundle.min.js"></script>

</body>

</html>