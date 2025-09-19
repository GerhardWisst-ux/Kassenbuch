<?php
ob_start();
session_start();
if (empty($_SESSION['userid'])) {
  http_response_code(403);
  echo json_encode(['error' => 'not authorized']);
  header('Location: Login.php'); // zum Loginformular
  exit;
}
?>

<head>
  <title>CashControl Hinzufügen Position</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!-- CSS -->
  <link href="css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
  <link href="css/style.css" rel="stylesheet">
</head>

<body>

  <?php

  // Abfrage der E-Mail vom Login
  $email = $_SESSION['email'];

  ?>
  <form action="ImportCSV.php" method="post" enctype="multipart/form-data">
    <header class="custom-header py-2 text-white">
      <div class="container-fluid">
        <div class="row align-items-center">
          <!-- Titel zentriert -->
          <div class="col-12 text-center mb-2 mb-md-0">
            <h2 class="h4 mb-0">CashControl - Import</h2>
          </div>

          <?php
          require_once 'includes/benutzerversion.php';
          ?>
        </div>
      </div>
    </header>
    <br>

    <div class="container-fluid mx-2">
      <label class="col-sm-2 col-form-label text-dark">CSV-Datei hochladen:</label>
      <input type="file" class="form-control" name="csv_file" accept=".csv" required>

      <div class="form-group row mt-2">
        <div class="col-sm-offset-2 col-sm-10">
          <button class="btn btn-primary btn-sm" title="Csv-Datei importieren" type="submit"><i
              class="fa-solid fa-file-import"></i></button>

          <a href="Index.php" title="Zurück zur Kassenübersicht" class="btn btn-primary btn-sm"><i
              class="fa fa-arrow-left" aria-hidden="true"></i></a>
        </div>
      </div>
    </div>
  </form>

  <!-- JS -->
  <script src="js/jquery.min.js"></script>
  <script src="js/bootstrap.bundle.min.js"></script>