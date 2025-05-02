<head>
  <title>Kassenbuch Hinzufügen Buchungsart</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!-- CSS -->
  <link href="css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

  <!-- JS -->
  <script src="js/jquery.min.js"></script>
  <script src="js/bootstrap.bundle.min.js"></script>
</head>
<body>
<?php

require 'db.php';
session_start();


if ($_SERVER['REQUEST_METHOD'] === 'POST') {    
    $buchungsart = $_POST['buchungsart'];
    $dauerbuchung = $_POST['dauerbuchung'];    
    $created_at = $_POST['created_at'];
    $updated_at = $_POST['updated_at'];
    $userid = $_SESSION['userid'];

    $sql = "INSERT INTO buchungsarten (Buchungsart, Dauerbuchung, created_at, updated_at, userid) VALUES (:buchungsart, :dauerbuchung, :created_at, :updated_at, :userid )";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['buchungsart' => $buchungsart, 'dauerbuchung' => $dauerbuchung, 'created_at' => $created_at, 'updated_at' => $updated_at, 'userid' => $userid]);

    echo "Eintrag hinzugefügt!";
    sleep(3);
    header('Location: Index.php'); // Zurück zur Übersicht
    
}
?>
</body>