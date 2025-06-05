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
  <title>Kassenbuch Buchungsarten</title>

  <!-- CSS -->
  <link href="css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
  <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
  <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.4.1/css/responsive.dataTables.min.css">

  <!-- JS -->
  <script src="js/jquery.min.js"></script>
  <script src="js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
  <script src="https://cdn.datatables.net/responsive/2.4.1/js/dataTables.responsive.min.js"></script>
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

     .dataTables_wrapper .dataTables_length select,
      .dataTables_wrapper .dataTables_filter,
      .dataTables_info {
      margin-left: 1.2rem !important;
      margin-right: 0.8rem !important;
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

      .dataTables_wrapper .dataTables_length select,
      .dataTables_wrapper .dataTables_filter {
        margin-left: 0.2rem !important;

      }

      #TableBuchungsarten td,
      #TableBuchungsarten th {
        white-space: nowrap;
        font-size: 12px;
        /* Schriftgröße anpassen */
      }

      #TableBuchungsarten td:nth-child(1),
      #TableBuchungsarten td:nth-child(2),
      #TableBuchungsarten td:nth-child(3),
      #TableBuchungsarten td:nth-child(4) {
        display: table-cell;
        /* Sicherstellen, dass Dauerbuchung sichtbar bleibt */
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
  $email = $_SESSION['email'];
  $userid = $_SESSION['userid'];
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

  <div id="bestaende">
    <form id="bestaendeform">
      <div class="custom-container">
        <div class="mt-0 p-5 bg-secondary text-white text-center rounded-bottom">
          <h1>Kassenbuch</h1>
          <p>Buchungsarten</p>
        </div>

        <div class="container-fluid mt-3">
          <div class="row">
            <div class="col-12 text-end">
              <?php echo "<span>Angemeldet als: " . htmlspecialchars($email) . "</span>"; ?>
              <a class="btn btn-primary" title="Abmelden vom Kassenbuch" href="logout.php">
                <i class="fa fa-sign-out" aria-hidden="true"></i>
              </a>
            </div>
          </div>
        </div>
        <?php

        echo '<div class="btn-toolbar" role="toolbar" aria-label="Toolbar with button groups">';
        echo '<div class="btn-group" role="group" aria-label="First group">';
        echo '<a href="AddBuchungsart.php" title="Position hinzufügen" class="btn btn-primary btn-sm me-4"><span><i class="fa fa-plus" aria-hidden="true"></i></span></a>';
        echo '</div>';

        echo '<div class="btn-group me-0" role="group" aria-label="First group">';
        echo '<a href="Index.php" title="Zurück zur Hauptübersicht" class="btn btn-primary btn-sm"><span><i class="fa fa-arrow-left" aria-hidden="true"></i></span></a>';
        echo '</div>';
        echo '</div>';
        echo '</div><br>';

        ?>
        <br>
        <div class="custom-container">
          <table id="TableBuchungsarten" class="display nowrap">
            <thead>
              <tr>
                <th>Buchungsart</th>
                <th>Dauerbuchung</th>
                <th></th>
              </tr>
            </thead>
            <tbody>
              <?php
              $userid = $_SESSION['userid'];
              $sql = "SELECT * FROM buchungsarten WHERE userid = :userid ORDER BY Buchungsart DESC";
              $stmt = $pdo->prepare($sql);
              $stmt->execute(['userid' => $userid]);
              while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $dauerbuchung = $row['Dauerbuchung'] == 1 ? 'Ja' : 'Nein';
                echo "<tr>
                            <td>{$row['Buchungsart']}</td>
                            <td style='vertical-align: top;'>{$dauerbuchung}</td>                                    
                            <td style='vertical-align: top; width:7%; white-space: nowrap;'>
                                <a href='EditBuchungsart.php?id={$row['id']}' style='width:60px;'  class='btn btn-primary btn-sm'><i class='fa-solid fa-pen-to-square'></i></a>                                
                                <a href='DeleteBuchungsart.php?id={$row['id']}' data-id={$row['id']} style='width:60px;' title='Buchung löschen' class='btn btn-danger btn-sm delete-button'><i class='fa-solid fa-trash'></i></a>
                            </td>                            
                      </tr>";
              }
              ?>
            </tbody>
          </table>
        </div>
        <!-- Bootstrap Modal -->
        <div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-labelledby="confirmDeleteModalLabel"
          aria-hidden="true">
          <div class="modal-dialog">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="confirmDeleteModalLabel">Löschbestätigung</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Schließen"></button>
              </div>
              <div class="modal-body">
                Möchten Sie diese Buchungsart wirklich löschen?
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Abbrechen</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Löschen</button>
              </div>
            </div>
          </div>
        </div>
        <!-- Toast -->
        <div class="toast-container position-fixed top-0 end-0 p-3">
          <div id="deleteToast" class="toast toast-green" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="toast-header">
              <strong class="me-auto">Benachrichtigung</strong>
              <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
            <div class="toast-body">
              Buchungsart wurde gelöscht.
            </div>
          </div>
        </div>
    </form>

    <script>
      $(document).ready(function () {
        let deleteId = null; // Speichert die ID für die Löschung

        $('.delete-button').on('click', function (event) {
          event.preventDefault();
          deleteId = $(this).data('id'); // Hole die ID aus dem Button-Datenattribut          
          $('#confirmDeleteModal').modal('show'); // Zeige das Modal an
        });

        $('#confirmDeleteBtn').on('click', function () {
          if (deleteId) {
            // Dynamisches Formular erstellen und absenden
            const form = $('<form>', {
              action: 'DeleteBuchungsart.php',
              method: 'POST'
            }).append($('<input>', {
              type: 'hidden',
              name: 'id',
              value: deleteId
            }));

            $('body').append(form);
            form.submit();
          }
          $('#confirmDeleteModal').modal('hide'); // Schließe das Modal

          // Zeige den Toast an
          var toast = new bootstrap.Toast($('#deleteToast')[0]);
          toast.show();
        });
      });

      function NavBarClick() {
        var x = document.getElementById("myTopnav");
        if (x.className === "topnav") {
          x.className += " responsive";
        } else {
          x.className = "topnav";
        }
      }

      $(document).ready(function () {
        $('#TableBuchungsarten').DataTable({
          language: {
            url: "https://cdn.datatables.net/plug-ins/1.13.4/i18n/de-DE.json"
          },
          responsive: true,
          pageLength: 25,
          autoWidth: false,
          columnDefs: [
            {
              targets: 1, // Dauerbuchung
              className: "dt-body-nowrap" // Keine Zeilenumbrüche
            }
          ]
        });
      });
    </script>

</body>

</html>