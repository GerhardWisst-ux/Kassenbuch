<?php
ob_start();
// Session starten, falls noch nicht erfolgt
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

ini_set('display_errors', 1);
error_reporting(E_ALL);

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
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Kassenbuch - Impressum</title>
  <link href="css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"> 

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
            <h2 class="h4 mb-0">Kassenbuch - Impressum</h2>
          </div>

          <!-- Benutzerinfo + Logout -->
          <div class="col-12 col-md-auto ms-md-auto text-center text-md-end">
            <!-- Auf kleinen Bildschirmen: eigene Zeile für E-Mail -->
            <div class="d-block d-md-inline mb-1 mb-md-0">
              <span class="me-2">Angemeldet als: <?= htmlspecialchars($_SESSION['email']) ?></span>
            </div>
            <!-- Logout-Button -->
            <a class="btn btn-darkgreen btn-sm" title="Abmelden vom Webshop" href="logout.php">
              <i class="fa fa-sign-out" aria-hidden="true"></i> Ausloggen
            </a>
          </div>
        </div>
      </div>
    </header>
    <div class="container-fluid mt-4">
     <div class="custom-container">
                    <!-- <div>
                        <p>Before you enable the PWA, check out how slow this website is.</p>
                        <button class="formcontrol btn btn-primary" id="enable">Enable the PWA</button>
                    </div> -->

                    <a href="Index.php" title="Zurück zur Hauptübersicht" class="btn btn-primary btn-sm me-4 "><i
                            class="fa fa-arrow-left" aria-hidden="true"></i></a>

                    <p><strong>Das Kassenbuch wurde 2025 erstellt<br>
                            Author : Gerhard Wißt<br>
                            Langjährige Programmiererfahrung unter .NET,ASP.NET, C#, VB.NET, PHP, Blazor,
                            Webtechnologien und SQL-Server. <br> Erstellt Webanwendungen und datenbankgestützte
                            Applikationen
                        </strong>
                    </p>
                    <p class="me-4" style="text-align:left" style="text-align:left">
                    <h3 class="me-4" style="text-align:left">Allgemeine Angaben</h3>
                    <p class="me-4" style="text-align:left"><b>Internet:</b> <a href="http://www.kassenbuch-wisst.de"
                            target="_blank">www.kassenbuch-wisst.de</a></p>

                    <p class="me-4" style="text-align:left" style="text-align:left"><b>Name des
                            Diensteanbieters:</b> EDV Beratung Wißt Einzelunternehmen</>

                    <p class="me-4" style="text-align:left"><b>Vertreten durch:</b> Gerhard Wißt</p>

                    <br>
                    <h3 class="me-4" style="text-align:left">Anschrift und Kontakt</h3>
                    <p class="me-4" style="text-align:left">Augsburger Straße. 717</p>

                    <p class="me-4" style="text-align:left">70329 Stuttgart</p>

                    <p class="me-4" style="text-align:left"><b>Telefon:</b> <a href="tel:015208750327">015208750327</a>
                    </p>

                    <p class="me-4" style="text-align:left"><b>Email:</b> <a
                            href="mailto:g.wisst@web.de">g.wisst@web.de</a><br><br></p>

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