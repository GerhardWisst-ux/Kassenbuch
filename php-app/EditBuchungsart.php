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
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Kassenbuch Buchungsart bearbeiten</title>

  <link href="css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

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

    #TableBestaende {
      width: 100%;
      font-size: 0.9rem;
    }

    #TableBestaende tbody tr:hover {
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
  ?>

  <header class="custom-header py-2 text-white">
    <div class="container-fluid">
      <div class="row align-items-center">

        <!-- Titel zentriert -->
        <div class="col-12 text-center mb-2 mb-md-0">
          <h2 class="h4 mb-0">Kassenbuch - Buchungsart bearbeiten</h2>
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
  <div id="editbuchungsart" class="container-fluid mt-4">
    <form action="EditBuchungsartEntry.php" method="post">
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

          <!-- Buttons -->
          <div class="mb-3 row">
            <div class="col-sm-12 d-flex gap-2">
              <button type="submit" class="btn btn-primary">
                <i class="fa fa-save"></i>
              </button>
              <a href="Buchungsarten.php" title="Zurück zu den Buchungsarten" class="btn btn-primary"><span> <i
                  class="fa fa-arrow-left" aria-hidden="true"></i></span></a>'
              </a>
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