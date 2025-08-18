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
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Kassenbuch Bestände</title>

  <!-- CSS -->
  <link href="css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

  <!-- JS -->
  <script src="js/jquery.min.js"></script>
  <script src="js/bootstrap.bundle.min.js"></script>
  <style>
    /* === Grundlayout === */
    html,
    body {
      height: 100%;
      margin: 0;
      background-color: #dedfe0ff;
      /* hellgrau statt reinweiß */
    }


    /* Wrapper nimmt die volle Höhe ein und ist Flex-Container */
    .wrapper {
      min-height: 100vh;
      /* viewport height */
      display: flex;
      flex-direction: column;
    }

    /* Container oder Content-Bereich wächst flexibel */
    .container {
      flex: 1;
      /* nimmt den verfügbaren Platz ein */
    }

    /* Footer bleibt unten */
    footer {
      /* kein spezielles CSS nötig, wenn wrapper und container wie oben */
    }

    /* === Karten-Design mit Schatten === */
    .card {
      font-size: 0.9rem;
      background-color: #ffffff;
      border: 1px solid #dee2e6;
      box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
      /* leichter Schatten */
      transition: transform 0.2s ease-in-out;
    }

    .card:hover {
      transform: scale(1.01);
      /* kleine Hover-Interaktion */
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    }

    .card-title {
      font-size: 1.1rem;
    }

    .card-body p {
      margin-bottom: 0.5rem;
    }

    .card-img-top {
      height: 200px;
      /* Einheitliche Höhe */
      object-fit: cover;
      /* Bild wird beschnitten, nicht verzerrt */
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

    .custom-header {
      background: linear-gradient(to right, #2a55e0ff, #4670e4ff);
      /* dunkles, klassisches Grün */
      border-bottom: 2px solid #0666f7ff;
      box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
      border-radius: 0 0 1rem 1rem;
    }

    .btn-darkgreen {
      background-color: #0d3dc2ff;
      border-color: #145214;
      color: #fff;
    }

    .btn-darkgreen:hover {
      background-color: #0337e4ff;
      ;
      border-color: #2146beff;
    }

    .btn {
      border-radius: 50rem;
      /* pill-shape */
      font-size: 0.9rem;
      padding: 0.375rem 0.75rem;
      font-size: 0.85rem;
    }
  </style>
</head>

<body>

  <?php
  // Abfrage der E-Mail vom Login
  $email = $_SESSION['email'];
  require_once 'includes/header.php';
  ?>

  <div id="addbuchung">
    <form action="AddBuchungsartEntry.php" method="post">
      <header class="custom-header py-2 text-white">
        <div class="container-fluid">
          <div class="row align-items-center">
            <!-- Titel zentriert -->
            <div class="col-12 text-center mb-2 mb-md-0">
              <h2 class="h4 mb-0">Kassenbuch - Buchungsart neu</h2>
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
      <div class="mt-2 mx-2">
        <div class="form-group row me-4">
          <label class="col-sm-2 col-form-label text-dark">Buchungsart:</label>
          <div class="col-sm-10">
            <input class="form-control" id="buchungsart" type="text" name="buchungsart" required>
          </div>
        </div>
        <div class="form-group row me-4">
          <label for="dauerbuchung" class="col-sm-2 col-form-label text-dark">Dauerbuchung:</label>
          <div class="col-sm-1">
            <input class="form-check-input" id="dauerbuchung" type="checkbox" name="dauerbuchung">
          </div>
        </div>
        <div class="form-group row" style="visibility: hidden;">
          <label class="col-sm-2 col-form-label text-dark">Angelegt am:</label>
          <div class="col-sm-1">
            <input class="form-control" id="created_at" type="date" name="created_at" disabled>
          </div>
        </div>
        <div class="form-group row" style="visibility: hidden;">
          <label class="col-sm-2 col-form-label text-dark">Geändert am:</label>
          <div class="col-sm-1">
            <input class="form-control" id="updated_at" type="date" name="updated_at" disabled>
          </div>
        </div>
        <div class="form-group row me-4">
          <div class="col-sm-offset-2 col-sm-10">
            <button class="btn btn-primary" type="submit" aria-hidden="true"><i class="fas fa-save"></i></button>

            <a href="Buchungsarten.php" title="Zurück zu den Buchungsarten" class="btn btn-primary"><span><i
                  class="fa fa-arrow-left" aria-hidden="true"></i></span></a>'
          </div>
        </div>
      </div>
    </form>
  </div>

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