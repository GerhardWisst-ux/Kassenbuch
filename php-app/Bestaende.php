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

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Kassenbuch Bestände</title>

    <!-- CSS -->
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.4.1/css/responsive.dataTables.min.css">


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

    require 'db.php';
    $email = $_SESSION['email'];
    $userid = $_SESSION['userid'];


    // Verfügbare Jahre aus der Datenbank holen
    $sqlYears = "SELECT DISTINCT YEAR(datum) AS Jahr FROM bestaende WHERE userid = :userid ORDER BY Jahr DESC";
    $stmtYears = $pdo->prepare($sqlYears);
    $stmtYears->execute(['userid' => $userid]);
    $jahre = $stmtYears->fetchAll(PDO::FETCH_COLUMN);

    // Aktuelles Jahr als Standard
    $jahrFilter = isset($_GET['jahr']) ? (int) $_GET['jahr'] : date("Y");

    require_once 'includes/header.php';


    ?>

    <div id="bestaende">
        <form id="bestaendeform">
            <div class="custom-container">
                <header class="custom-header py-2 text-white">
                    <div class="container-fluid">
                        <div class="row align-items-center">
                            <!-- Titel zentriert -->
                            <div class="col-12 text-center mb-2 mb-md-0">
                                <h2 class="h4 mb-0">Kassenbuch - Bestände</h2>
                            </div>
                            <!-- Benutzerinfo + Logout -->
                            <div class="col-12 col-md-auto ms-md-auto text-center text-md-end">
                                <!-- Auf kleinen Bildschirmen: eigene Zeile für E-Mail -->
                                <div class="d-block d-md-inline mb-1 mb-md-0">
                                    <span class="me-2">Angemeldet als:
                                        <?= htmlspecialchars($_SESSION['email']) ?></span>
                                </div>
                                <!-- Logout-Button -->
                                <a class="btn btn-darkgreen btn-sm" title="Abmelden vom Webshop" href="logout.php">
                                    <i class="fa fa-sign-out" aria-hidden="true"></i> Ausloggen
                                </a>
                            </div>
                        </div>
                    </div>
                </header>
                <?php

                echo '<div class="btn-toolbar mt-2 mx-2" role="toolbar" aria-label="Toolbar with button groups">';
                echo '<div class="btn-group" role="group" aria-label="First group">';
                echo '<a href="BestaendeChart.php" title="Position hinzufügen" class="btn btn-primary btn-sm me-4"><span><i class="fas fa-chart-bar"></i></span></a>';
                echo '</div>';

                echo '<div class="btn-group me-0 mx-2" role="group" aria-label="First group">';
                echo '<a href="Index.php" title="Zurück zur Hauptübersicht" class="btn btn-primary btn-sm"><span><i class="fa fa-arrow-left" aria-hidden="true"></i></span></a>';
                echo '</div>';
                echo '</div>';
                echo '</div><br>';

                ?>
                <br>
                <!-- Jahresfilter -->
                <form method="get" class="mb-3">
                    <label for="jahr" class="form-label fw-bold mx-2">Jahr auswählen:</label>
                    <select name="jahr" id="jahr" class="form-select w-auto d-inline-block"
                        onchange="this.form.submit()">
                        <option value="0" <?= $jahrFilter == 0 ? 'selected' : '' ?>>Alle Jahre</option>
                        <?php foreach ($jahre as $jahr): ?>
                            <option value="<?= $jahr ?>" <?= ($jahr == $jahrFilter) ? 'selected' : '' ?>><?= $jahr ?></option>
                        <?php endforeach; ?>
                    </select>
                </form>

                <h4 class="mb-3 mx-2">
                    <?php
                    if ($jahrFilter == 0) {
                        echo "Alle Bestände";
                    } else {
                        echo "Bestände für das Jahr $jahrFilter";
                    }
                    ?>
                </h4>
                <div class="table-responsive mx-2" style="width: 100%;">
                    <table id="TableBestaende" class="display nowrap table table-striped w-100">
                        <thead>
                            <tr>
                                <th>Datum</th>
                                <th style="text-align:right;width:25%;">Einlagen</th>
                                <th style="text-align:right;width:25%;">Ausgaben</th>
                                <th style="text-align:right;width:35%;">Bestand</th>
                                <th style="text-align:center;">Aktionen</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $userid = $_SESSION['userid'];
                            // SQL-Abfrage je nach Filter
                            if ($jahrFilter == 0) {
                                $sql = "SELECT * FROM bestaende WHERE userid = :userid ORDER BY datum DESC";
                                $stmt = $pdo->prepare($sql);
                                $stmt->execute(['userid' => $userid]);
                            } else {
                                $sql = "SELECT * FROM bestaende WHERE userid = :userid AND YEAR(datum) = :jahr ORDER BY datum DESC";
                                $stmt = $pdo->prepare($sql);
                                $stmt->execute(['userid' => $userid, 'jahr' => $jahrFilter]);
                            }

                            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                $formattedDate = (new DateTime($row['datum']))->format('d.m.Y');
                                $bestand = number_format($row['einlagen'] - $row['ausgaben'], 2, ',', '.');

                                echo "<tr>
                                    <td>{$formattedDate}</td>
                                    <td style='text-align:right;'>{$row['einlagen']}</td>
                                    <td style='text-align:right;'>{$row['ausgaben']}</td>
                                    <td style='text-align:right;'>$bestand</td>
                                    <td style='white-space: nowrap;text-align:center;'>
                                        <a href='EditBestand.php?id={$row['id']}' class='btn btn-primary btn-sm me-1'>
                                            <i class='fa-solid fa-pen-to-square'></i>
                                        </a>
                                        <a href='DeleteBestand.php?id={$row['id']}' data-id='{$row['id']}' class='btn btn-danger btn-sm delete-button'>
                                            <i class='fa-solid fa-trash'></i>
                                        </a>
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
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Schließen"></button>
                            </div>
                            <div class="modal-body">
                                Möchten Sie diese Position wirklich löschen?
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary"
                                    data-bs-dismiss="modal">Abbrechen</button>
                                <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Löschen</button>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Toast -->
                <div class="toast-container position-fixed top-0 end-0 p-3">
                    <div id="deleteToast" class="toast toast-green" role="alert" aria-live="assertive"
                        aria-atomic="true">
                        <div class="toast-header">
                            <strong class="me-auto">Benachrichtigung</strong>
                            <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
                        </div>
                        <div class="toast-body">
                            Position wurde gelöscht.
                        </div>
                    </div>
                </div>
        </form>

        <!-- JS -->
        <script src="js/jquery.min.js"></script>
        <script src="js/bootstrap.bundle.min.js"></script>
        <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
        <script src="https://cdn.datatables.net/responsive/2.4.1/js/dataTables.responsive.min.js"></script>

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
                            action: 'DeleteBestand.php',
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
                $('#TableBestaende').DataTable({
                    language: {
                        url: "https://cdn.datatables.net/plug-ins/1.13.4/i18n/de-DE.json"
                    },
                    responsive: true,
                    pageLength: 12,
                    autoWidth: false,
                    columnDefs: [
                        {
                            targets: 1,
                            className: "dt-body-nowrap" // Keine Zeilenumbrüche
                        }
                    ]
                });
            });
        </script>

</body>

</html>
<?php
ob_end_flush();
?>