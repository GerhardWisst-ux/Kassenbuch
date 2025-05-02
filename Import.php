<head>
  <title>Kassenbuch Hinzufügen Position</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!-- CSS -->
  <link href="css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

  <!-- JS -->
  <script src="js/jquery.min.js"></script>
  <script src="js/bootstrap.bundle.min.js"></script>
  <style>
    .custom-container {
      margin-left: 20px;
      margin-right: 20px;
    }
  </style>
</head>

<body>

  <?php
  session_start();
  if (!isset($_SESSION['userid'])) {
    header('Location: Login.php'); // zum Loginformular
  }

  // Abfrage der E-Mail vom Login
  $email = $_SESSION['email'];

  ?>
  <form action="ImportCSV.php" method="post" enctype="multipart/form-data">
    <div class="custom-container">
      <div class="mt-1 p-5 bg-secondary text-white text-center rounded">
        <h1>Kassenbuch</h1>
      </div>

      <div class="container-fluid mt-3">
        <div class="row">
          <div class="col-12 text-end" style="text-align: right;">
            <?php echo "<span>Angemeldet als: " . $email . "</span>"; ?>
            <a class="btn btn-primary" title="Abmelden vom Kassenbuch" href="logout.php"><i class="fa fa-sign-out" aria-hidden="true"></i></a>
          </div>
        </div>
      </div>

      <label class="col-sm-2 col-form-label text-dark">CSV-Datei hochladen:</label>
      <input type="file" class="form-control" name="csv_file" accept=".csv" required>
      <button class="btn btn-primary btn-sm" title="Csv-Datei importieren" type="submit"><i class="fa-solid fa-file-import"></i></button>
      <a href="Index.php" title="Zurück zur Hauptübersicht" class="btn btn-primary btn-sm"><i class="fa fa-arrow-left" aria-hidden="true"></i></a>
  </form>