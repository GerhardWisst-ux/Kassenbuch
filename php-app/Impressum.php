<?php
ob_start();
// Session starten, falls noch nicht erfolgt
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

if ($_SESSION['userid'] == "") {
  // Wenn kein Benutzer angemeldet ist, weiterleiten zur Login-Seite
  header("Location: Login.php");
}
require_once 'db.php';

?>

<!DOCTYPE html>
<html lang="de">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="Datensicherung für das Kassenbuch – einfache Verwaltung und sichere Backups.">
  <meta name="author" content="Gerhard Wißt">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <link rel="icon" type="image/png" href="images/favicon.png" />
  <title>CashControl - Impressum</title>
  <link href="css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link href="css/style.css" rel="stylesheet">
</head>

<body>
  <?php

  require_once 'includes/header.php';
  $email = $_SESSION['email'];
  $userid = $_SESSION['userid'];
  ?>

  <div class="wrapper">
    <header class="custom-header py-2 text-white">
      <div class="container-fluid">
        <div class="row align-items-center">

          <!-- Titel zentriert -->
          <div class="col-12 text-center mb-2 mb-md-0">
            <h2 class="h4 mb-0">CashControl - Impressum</h2>
          </div>

          <?php
          require_once 'includes/benutzerversion.php';
          ?>
        </div>
    </header>
    <div class="container-fluid mt-4">
      <div class="custom-container">
        <!-- Toolbar -->
        <div class="btn-toolbar mb-3" role="toolbar" aria-label="Toolbar">
          <a href="Index.php" title="Zurück zur Kassenübersicht" class="btn btn-primary btn-sm me-4 "><i
              class="fa fa-arrow-left" aria-hidden="true"></i></a>
          <br>
        </div>


        <p><strong>CashControl wurde 2025 erstellt<br>
            Author : Gerhard Wißt<br>
            Langjährige Programmiererfahrung unter .NET,ASP.NET, C#, VB.NET, PHP, Blazor,
            Webtechnologien und SQL-Server. <br> Erstellt Webanwendungen und datenbankgestützte
            Applikationen
          </strong>
        </p>
        <?php
        echo "<!-- App Version: " . htmlspecialchars($appVersion->getVersion()) . " -->";
        ?>
        <p class="me-4" style="text-align:left" style="text-align:left">
        <h3 class="me-4" style="text-align:left">Allgemeine Angaben</h3>
        <p class="me-4" style="text-align:left"><b>Internet:</b> <a href="http://www.CashControl-wisst.de"
            target="_blank">www.CashControl-wisst.de</a></p>

        <p class="me-4" style="text-align:left" style="text-align:left"><b>Name des
            Diensteanbieters:</b> EDV Beratung Wißt Einzelunternehmen</>

        <p class="me-4" style="text-align:left"><b>Vertreten durch:</b> Gerhard Wißt</p>

        <br>
        <h3 class="me-4" style="text-align:left">Anschrift und Kontakt</h3>
        <p class="me-4" style="text-align:left">Augsburger Straße. 717</p>

        <p class="me-4" style="text-align:left">70329 Stuttgart</p>

        <p class="me-4" style="text-align:left"><b>Telefon:</b> <a href="tel:015208750327">015208750327</a>
        </p>

        <p class="me-4" style="text-align:left"><b>Email:</b> <a href="mailto:g.wisst@web.de">g.wisst@web.de</a><br><br>
        </p>

        <h3 class="me-4" style="text-align:left">Haftung für Links</h3>
        <p class="me-4" style="text-align:left">Unser Angebot enthält Links zu externen Webseiten Dritter,
          auf deren Inhalte wir keinen Einfluss haben. Deshalb können wir für diese fremden Inhalte auch
          keine Gewähr übernehmen. Für die Inhalte der verlinkten Seiten ist stets der jeweilige Anbieter
          oder Betreiber der Seiten verantwortlich. Die verlinkten Seiten wurden zum Zeitpunkt der
          Verlinkung auf mögliche Rechtsverstöße überprüft. Rechtswidrige Inhalte waren zum Zeitpunkt der
          Verlinkung nicht erkennbar. Eine permanente inhaltliche Kontrolle der verlinkten Seiten ist
          jedoch ohne konkrete Anhaltspunkte einer Rechtsverletzung nicht zumutbar. Bei Bekanntwerden von
          Rechtsverletzungen werden wir derartige Links umgehend entfernen.</p>

        <br>
        <h3 class="me-4" style="text-align:left">Haftung für Inhalte</h3>
        <p class="me-4" style="text-align:left">Die Inhalte unserer Seiten wurden mit größter Sorgfalt
          erstellt. Für die Richtigkeit, Vollständigkeit und Aktualität der Inhalte können wir jedoch
          keine Gewähr übernehmen. Wir sind als Diensteanbieter jedoch nicht verpflichtet, übermittelte
          oder gespeicherte fremde Informationen zu überwachen oder nach Umständen zu forschen, die auf
          eine rechtswidrige Tätigkeit hinweisen. Verpflichtungen zur Entfernung oder Sperrung der Nutzung
          von Informationen nach den allgemeinen Gesetzen bleiben hiervon unberührt. Eine diesbezügliche
          Haftung ist jedoch erst ab dem Zeitpunkt der Kenntnis einer konkreten Rechtsverletzung möglich.
          Bei Bekanntwerden von entsprechenden Rechtsverletzungen werden wir diese Inhalte umgehend
          entfernen.</p>

        <h3 class="me-4" style="text-align:left"> Urheberrecht</h3>
        <p class="me-4" style="text-align:left">Die durch die Seitenbetreiber erstellten Inhalte und Werke
          auf diesen Seiten unterliegen dem deutschen Urheberrecht. Die Vervielfältigung, Bearbeitung,
          Verbreitung und jede Art der Verwertung außerhalb der Grenzen des Urheberrechtes bedürfen der
          schriftlichen Zustimmung des jeweiligen Autors bzw. Erstellers. Downloads und Kopien dieser
          Seite sind nur für den privaten, nicht kommerziellen Gebrauch gestattet. Soweit die Inhalte auf
          dieser Seite nicht vom Betreiber erstellt wurden, werden die Urheberrechte Dritter beachtet.
          Insbesondere werden Inhalte Dritter als solche gekennzeichnet. Sollten Sie trotzdem auf eine
          Urheberrechtsverletzung aufmerksam werden, bitten wir um einen entsprechenden Hinweis. Bei
          Bekanntwerden von Rechtsverletzungen werden wir derartige Inhalte umgehend entfernen.

        </p>
      </div>
    </div>
  </div>
  <script src="js/jquery.min.js"></script>
  <script src="js/bootstrap.bundle.min.js"></script>
  <script>
    function NavBarClick() {
      var x = document.getElementById("myTopnav");
      if (x.className === "topnav") {
        x.className += " responsive";
      } else {
        x.className = "topnav";
      }
    }
  </script>


</body>

</html>