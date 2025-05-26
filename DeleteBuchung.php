<head>
  <title>Buchunngen - Position löschen</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!-- CSS -->
  <link href="css/bootstrap.min.css" rel="stylesheet">

  <!-- JS -->
  <script src="js/jquery.min.js"></script>
  <script src="js/bootstrap.bundle.min.js"></script>
</head>

<body>
  
  <?php
  require 'db.php';
  session_start();

  if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $sql = "DELETE FROM buchungen WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['id' => $id]);
    //$stmt->"Kassenbuch Buchungen - Position mit der ID" . $id . " wurde gelöscht!";
    sleep(1);
    header('Location: Index.php'); // Zurück zur Übersicht
  
    //exit();
  } else {
    echo "Ungültige Anfrage.";
  }


  ?>
</body>