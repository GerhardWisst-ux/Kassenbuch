<head>
  <title>Kassenbuch Hinzufügen Buchungsart Position</title>
  <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Kassenbuch Bestände</title>

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
  <div class="custom-container">
    <form action="AddBuchungsartEntry.php" method="post">
      <div class="custom-container">
        <div class="mt-4 p-5 bg-secondary text-white text-center rounded">
          <h1>Kassenbuch</h1>
          <p>Buchungsart Eintrag hinzufügen</pü>
        </div>
        <div class="container-fluid mt-3">
          <div class="row">
            <div class="col-12 text-end" style="text-align: right;">
              <?php echo "<span>Angemeldet als: " . $email . "</span>"; ?>
              <a class="btn btn-primary" title="Abmelden vom Kassenbuch" href="logout.php"><span><i
                    class="fa fa-sign-out" aria-hidden="true"></i></span></a>
            </div>
          </div>
        </div>
      </div>
      <div class="form-group row">
        <label class="col-sm-2 col-form-label text-dark">Buchungsart:</label>
        <div class="col-sm-10">
          <input class="form-control" id="buchungsart" type="text" name="buchungsart" required>
        </div>
      </div>
      <div class="form-group row">
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
      <div class="form-group row">
        <div class="col-sm-offset-2 col-sm-10">
          <button class="btn btn-primary" type="submit" aria-hidden="true"><i class="fas fa-save"></i></button>
          
            <a href="Buchungsarten.php" title="Zurück zu den Buchungsarten" class="btn btn-primary"><span><i
                  class="fa fa-arrow-left" aria-hidden="true"></i></span></a>'
        </div>
      </div>
    </form>
  </div>
  <script>
    // Heutiges Datum automatisch setzen
    document.addEventListener("DOMContentLoaded", function () {
      const today = new Date();
      const formattedDate = today.toISOString().split('T')[0];
      document.getElementById("created_at").value = formattedDate;
      document.getElementById("updated_at").value = formattedDate;
    });
  </script>