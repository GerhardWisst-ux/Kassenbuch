<?php
ob_start();
session_set_cookie_params([
  'httponly' => true,
  'secure' => true,  // Nur bei HTTPS
  'samesite' => 'Strict'
]);
session_start();
if ($_SESSION['userid'] == "") {
  header('Location: Login.php'); // zum Loginformular
}

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// CSRF-Token erzeugen
if (empty($_SESSION['csrf_token'])) {
  $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$mandantennummer = $_SESSION['mandantennummer'];
//echo $mandantennummer . " " . $_SESSION['userid'] . " " . $_SESSION['kassennummer'] ;
?>

<!DOCTYPE html>
<html>

<head>
  <title>CashControl - Buchungsarten</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="Datensicherung für das Kassenbuch – einfache Verwaltung und sichere Backups.">
  <meta name="author" content="Gerhard Wißt">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <link rel="icon" type="image/png" href="images/favicon.png" />

  <!-- CSS -->
  <link href="css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
  <link href="css/jquery.dataTables.min.css" rel="stylesheet">
  <link href="css/responsive.dataTables.min.css" rel="stylesheet">
  <link href="css/style.css" rel="stylesheet">
  <style>
    #TableBuchungsarten {
      width: 100%;
      font-size: 0.9rem;
    }

    #TableBuchungsarten tbody tr:hover {
      background-color: #f1f5ff;
    }
    
  </style>
</head>

<body>

  <?php

  require 'db.php';
  $email = $_SESSION['email'];
  $userid = $_SESSION['userid'];
  $kassennummer = $_SESSION['kassennummer'] ?? null;
  //echo $kassennummer;
  require_once 'includes/header.php';

  ?>

  <header class="custom-header py-2 mb-3 text-white">
    <div class="container-fluid">
      <div class="row align-items-center">
        <?php
        $sql = "SELECT * FROM kasse WHERE userid = :userid AND mandantennummer = :mandantennummer AND id = :kassennummer";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
          'userid' => $userid,
          'kassennummer' => $kassennummer,
          'mandantennummer' => $mandantennummer
        ]);

        $kasse = "Unbekannte Kasse";
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
          $kasse = $row['kasse'];
        }
        ?>
        <!-- Titel zentriert -->
        <div class="col-12 text-center mb-2 mb-md-0">
          <h4 class="h4 mb-0"><?php echo htmlspecialchars($kasse); ?> - Buchungungsarten</h2>
        </div>

        <?php
        require_once 'includes/benutzerversion.php';
        ?>
  </header>

  <div id="buchungsarten" class="mx-2">
    <form id="buchungsartenform">
      <input type="hidden" id="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
      <div class="custom-container">

        <div class="d-flex justify-content-between align-items-center">
          <!-- Linke Buttons -->
          <div>
            <a href="AddBuchungsart.php" title="Position hinzufügen"
              class="btn btn-primary btn-sm rounded-circle me-2 circle-btn">
              <i class="fa fa-plus"></i>
            </a>

            <a href="Index.php" title="Zurück zur Kassenübersicht"
              class="btn btn-primary btn-sm rounded-circle me-2 circle-btn">
              <i class="fa fa-arrow-left"></i>
            </a>
          </div>

          <!-- Rechte Buttons -->
          <div>
            <a href="help/Buchungsarten.php" title="Hilfe"
              class="btn btn-primary btn-sm rounded-circle circle-btn">
              <i class="fa fa-question-circle"></i>
            </a>
          </div>
        </div>

        <div class="table-responsive mx-2 mt-3" style="width: 100%;">
          <table id="TableBuchungsarten" class="display nowrap table table-striped w-100">
            <thead>
              <tr>
                <th>ID</th>
                <th>Buchungsart</th>
                <th>MwSt</th>
                <th>MwSt ermäßigt</th>
                <th>Dauerbuchung</th>
                <th></th>
              </tr>
            </thead>
            <tbody>
              <?php
              $sql = "SELECT * FROM buchungsarten WHERE userid = :userid  AND mandantennummer = :mandantennummer ORDER BY Buchungsart DESC";
              $stmt = $pdo->prepare($sql);
              $stmt->execute(['userid' => $userid, 'mandantennummer' => $mandantennummer]);
              while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $dauerbuchung = $row['Dauerbuchung'] == 1 ? 'Ja' : 'Nein';
                $mwst_ermaessigt = $row['mwst_ermaessigt'] == 1 ? 'Ja' : 'Nein';
                $mwst = ($row['mwst'] * 100) - 100 . ' %';

                if ($row['buchungsart'] == "Einlage") {
                  $mwst = " 0 %";
                }
                echo "<tr>
                            <td>{$row['id']}</td>
                            <td>{$row['buchungsart']}</td>      
                            <td style='vertical-align: top;text-align:right;'>{$mwst}</td>                            
                            <td style='vertical-align: top;'>{$mwst_ermaessigt}</td>
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

    <!-- JS -->
    <script src="js/jquery.min.js"></script>
    <script src="js/bootstrap.bundle.min.js"></script>
    <script src="js/jquery.dataTables.min.js"></script>
    <script src="js/dataTables.min.js"></script>
    <script src="js/dataTables.responsive.min.js"></script>

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
            const form = $('<form>', {
              action: 'DeleteBuchungsart.php',
              method: 'POST'
            }).append($('<input>', {
              type: 'hidden',
              name: 'id',
              value: deleteId
            })).append($('<input>', {
              type: 'hidden',
              name: 'csrf_token',
              value: $('#csrf_token').val() // <- Das Session-Token wird übernommen
            }));

            $('body').append(form);
            form.submit();
          }
          $('#confirmDeleteModal').modal('hide');

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
          language: { url: "https://cdn.datatables.net/plug-ins/1.13.4/i18n/de-DE.json" },
          responsive: {
            details: {
              display: $.fn.dataTable.Responsive.display.modal({
                header: function (row) {
                  var data = row.data();
                  return 'Details zu ' + data[1];
                }
              }),
              renderer: $.fn.dataTable.Responsive.renderer.tableAll({
                tableClass: 'table'
              })
            }
          },
          scrollX: false,
          pageLength: 50,
          autoWidth: false,
          stateSave: true,
          stateDuration: -1,
          columnDefs: [
            { targets: 0, visible: false, searchable: false } // ID unsichtbar
          ]
        });
      });
    </script>

</body>

</html>
<?php
ob_end_flush();
?>