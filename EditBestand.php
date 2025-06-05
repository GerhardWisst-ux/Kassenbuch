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
  <title>Kassenbuch Bestand bearbeiten</title>

  <link href="css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

  <!-- JS -->
  <script src="js/jquery.min.js"></script>
  <script src="js/bootstrap.bundle.min.js"></script>

  <style>
    /* Allgemeine Einstellungen */
    body {
      font-family: 'Arial', sans-serif;
      background-color: #f4f7f6;
      margin: 0;
      padding: 0;
    }

    .topnav {
      background-color: #2d3436;
      overflow: hidden;
      display: flex;
      padding: 10px 20px;
      box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .topnav a {
      color: #fff;
      text-decoration: none;
      padding: 10px 15px;
      font-size: 16px;
      transition: background-color 0.3s ease;
    }

    .topnav a:hover {
      background-color: rgb(161, 172, 169);
      color: #2d3436;
    }

    .topnav .icon {
      display: none;
    }

    label {
      font-size: 14px;
      font-weight: 600;
      color: #333;
    }

    /* Tabelle Margins */
    .custom-container table {
      margin-left: 1.2rem !important;
      margin-right: 1.2rem !important;
      width: 98%;
    }

    .me-4 {
      margin-left: 1.2rem !important;
    }

    /* Spaltenbreiten optimieren */
    @media screen and (max-width: 767px) {

      .custom-container table {
        margin-left: 0.2rem !important;
        margin-right: 0.2rem !important;
        width: 98%;
      }

      .me-4 {
        margin-left: 0.2rem !important;
      }


      .topnav a:not(:first-child) {
        display: none;
      }

      .topnav a.icon {
        display: block;
        font-size: 30px;
      }

      .topnav.responsive {
        position: relative;
      }

      .topnav.responsive .icon {
        position: absolute;
        right: 0;
        top: 0;
      }

      .topnav.responsive a {
        display: block;
        text-align: left;
      }
    }

    /* Responsive Design */
    @media screen and (max-width: 600px) {
      .topnav a:not(:first-child) {
        display: none;
      }

      .topnav a.icon {
        display: block;
        font-size: 30px;
      }

      .topnav.responsive {
        position: relative;
      }

      .topnav.responsive .icon {
        position: absolute;
        right: 0;
        top: 0;
      }

      .topnav.responsive a {
        display: block;
        text-align: left;
      }

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
  ?>

  <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">
      <a class="navbar-brand" href="Index.php"><i class="fa-solid fa-house"></i></a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavDropdown"
        aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarNavDropdown">
        <ul class="navbar-nav">
          <li class="nav-item">
            <a href="Index.php" class="nav-link">Hauptseite</a>
          </li>
          <li class="nav-item">
            <a href="Buchungsarten.php" class="nav-link">Buchungsarten</a>
          </li>
          <li class="nav-item">
            <a href="Bestaende.php" class="nav-link">Bestände</a>
          </li>
          <li class="nav-item">
            <a href="Impressum.php" class="nav-link">Impressum</a>
          </li>
        </ul>
      </div>
    </div>
  </nav>

  <div id="addbuchung">
    <form action="AddBuchungEntry.php" method="post">
      <div class="custom-container">
        <div class="mt-0 p-5 bg-secondary text-white text-center rounded-bottom">
          <h1>Kassenbuch</h1>
          <p>Buchungen Bestand bearbeiten</p>
        </div>
        <br>
        <div class="form-group row me-4">
          <div class="container-fluid mt-3">
            <div class="row">
              <div class="col-12 text-end" style="text-align: right;">
                <?php echo "<span>Angemeldet als: " . $email . "</span>"; ?>
                <a class="btn btn-primary" title="Abmelden vom Kassenbuch" href="logout.php"><span><i
                      class="fa fa-sign-out" aria-hidden="true"></i></span></a>
              </div>
            </div>
          </div>
          <div class="form-group row">
            <label class="col-sm-2 col-form-label text-dark">Datum:</label>
            <div class="col-sm-1">
              <input id="updated_at" class="form-control" type="date" name="datum"
                value="<?= htmlspecialchars($result['datum']) ?>">
            </div>
          </div>
          <div class="form-group row">
            <label class="col-sm-2 col-form-label text-dark">Einnahmen:</label>
            <div class="col-sm-1">
              <input id="einnahmen" class="form-control" type="text" name="einnahmen"
                value="<?= htmlspecialchars($result['einlagen']) ?>">
            </div>
          </div>
          <div class="form-group row">
            <label class="col-sm-2 col-form-label text-dark">Ausgaben:</label>
            <div class="col-sm-1">
              <input id="ausgaben" class="form-control" type="text" name="ausgaben"
                value="<?= htmlspecialchars($result['ausgaben']) ?>">
            </div>
          </div>
          <div class="form-group row">
            <label class="col-sm-2 col-form-label text-dark">Bestand:</label>
            <div class="col-sm-1">
              <input id="bestand" class="form-control" type="text" name="bestand"
                value="<?= htmlspecialchars($result['bestand']) ?>">
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