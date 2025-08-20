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
  session_start();
  if (!isset($_SESSION['userid'])) {
    header('Location: Login.php'); // zum Loginformular
  }

  // Abfrage der E-Mail vom Login
  $email = $_SESSION['email'];

  ?>
  <form action="ImportCSV.php" method="post" enctype="multipart/form-data">
    <header class="custom-header py-2 text-white">
      <div class="container-fluid">
        <div class="row align-items-center">
          <!-- Titel zentriert -->
          <div class="col-12 text-center mb-2 mb-md-0">
            <h2 class="h4 mb-0">Kassenbuch - Import</h2>
          </div>

          <!-- Benutzerinfo + Logout -->
          <div class="col-12 col-md-auto ms-md-auto text-center text-md-end">
            <!-- Auf kleinen Bildschirmen: eigene Zeile für E-Mail -->
            <div class="d-block d-md-inline mb-1 mb-md-0">
              <span class="me-2">Angemeldet als:
                <?= htmlspecialchars($_SESSION['email']) ?></span>
            </div>
            <!-- Logout-Button -->
            <a class="btn btn-darkgreen btn-sm mt-2 mx-2" title="Abmelden vom Webshop" href="logout.php">
              <i class="fa fa-sign-out" aria-hidden="true"></i> Ausloggen
            </a>
          </div>
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

          <a href="Index.php" title="Zurück zur Hauptübersicht" class="btn btn-primary btn-sm"><i
              class="fa fa-arrow-left" aria-hidden="true"></i></a>
        </div>
      </div>

    </div>
  </form>