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
?>
<!DOCTYPE html>
<html>

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Kassenbuch Bestand bearbeiten</title>

  <!-- CSS -->
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
    $sql = "Select * FROM bestaende WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['id' => $id]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
  } else {
    echo "Keine ID angegeben.";
  }

  if (isset($_SESSION['success_message'])) {
    echo '<div class="alert alert-success alert-dismissible fade show" role="alert">'
      . htmlspecialchars($_SESSION['success_message']) .
      '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
         </div>';
    // Meldung nach einmaligem Anzeigen löschen
    unset($_SESSION['success_message']);
  }

  require_once 'includes/header.php';
  ?>

  <header class="custom-header py-2 text-white">
    <div class="container-fluid">
      <div class="row align-items-center">

        <!-- Titel zentriert -->
        <div class="col-12 text-center mb-2 mb-md-0">
          <h2 class="h4 mb-0">Kassenbuch - Bestand bearbeiten</h2>
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
  <div id="editbestand" class="container-fluid mt-4">
    <form action="EditBestandEntry.php" method="post">
      <input type="hidden" name="csrf_token"
        value="<?= htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8') ?>">
      <div class="custom-container">

        <div class="mt-2 mx-2">
          <div class="form-group row">
            <label class="col-sm-2 col-form-label text-dark">Datum:</label>
            <div class="col-sm-1">
              <input id="updated_at" class="form-control" type="date" name="datum"
                value="<?= htmlspecialchars($result['datum']) ?>">
            </div>
          </div>
          <?php
          $isPast = isset($result['datum']) && strtotime($result['datum']) < strtotime(date('Y-m-d'));
          $bestand = number_format($result['einlagen'] - $result['ausgaben'], 2, '.', '');
          ?>
          <div class="form-group row">
            <label class="col-sm-2 col-form-label text-dark">Einlagen:</label>
            <div class="col-sm-1">
              <input id="einlagen" class="form-control text-end" type="text" name="einlagen"
                value="<?= htmlspecialchars($result['einlagen']) ?>">
            </div>
          </div>

          <div class="form-group row">
            <label class="col-sm-2 col-form-label text-dark">Ausgaben:</label>
            <div class="col-sm-1">
              <input id="ausgaben" class="form-control text-end" type="text" name="ausgaben"
                value="<?= htmlspecialchars($result['ausgaben']) ?>">
            </div>
          </div>


          <div class="form-group row">
            <label class="col-sm-2 col-form-label text-dark">Bestand:</label>
            <div class="col-sm-1">
              <input id="bestand" class="form-control text-end" type="text" name="bestand"
                value="<?= htmlspecialchars($bestand) ?>" <?= $isPast ? 'readonly' : '' ?>>
            </div>
          </div>

          <div class="form-group row">
            <div class="col-sm-offset-2 col-sm-10">
              <button class="btn btn-primary" title="Speichert Bestand ab" type="submit"><i
                  class="fas fa-save"></i></button>
              <a href="Bestaende.php" title="Zurück zur Übersicht Bestände" class="btn btn-primary"><span>
                  <i class="fa fa-arrow-left" aria-hidden="true"></i></span></a>'
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