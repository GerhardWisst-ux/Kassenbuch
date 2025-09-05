<!DOCTYPE html>
<html>

<?php
ob_start();

session_start();
if ($_SESSION['userid'] == "") {
    header('Location: Login.php'); // zum Loginformular
}

// CSRF-Token erzeugen
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

?>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>CashControl - Kassenübersicht</title>

    <!-- CSS -->
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="css/jquery.dataTables.min.css" rel="stylesheet">
    <link href="css/responsive.dataTables.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">

    <style>
        /* Hover-Effekt für Cards */
        < !-- Hover Effekt -->.card-hover {
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .card-hover:hover {
            transform: translateY(-5px);
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.3);
        }
    </style>
</head>

<body>

    <?php
    require 'db.php';
    $email = $_SESSION['email'];
    $userid = $_SESSION['userid'] ?? null;
    $_SESSION['kassennummer'] = null;
    require_once 'includes/header.php';
    require_once 'includes/bestaende_berechnen.php';
    ?>

    <div id="kasse">
        <form id="kasseform" method="get">
            <input type="hidden" id="csrf_token" name="csrf_token"
                value="<?= htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8') ?>">
            <header class="custom-header py-2 text-white">
                <div class="container-fluid">
                    <div class="row align-items-center">

                        <!-- Titel zentriert -->
                        <div class="col-12 text-center mb-2 mb-md-0">
                            <h2 class="h4 mb-0">CashControl - Kassenübersicht</h2>
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
            <?php

            echo '<div class="btn-toolbar mx-2 mt-2" role="toolbar" aria-label="Toolbar with button groups">';
            echo '<div class="btn-group" role="group" aria-label="First group">';
            echo '<a href="Addkasse.php" title="Kasse hinzufügen" class="btn btn-primary btn-sm me-4"><span><i class="fa fa-plus" aria-hidden="true"></i></span></a>';
            echo '</div>';
            echo '</div><br>';

            ?>
            <div class="row g-3 position-relative">
                <?php
                // Alle Kassen für den Benutzer
                $sql = "SELECT * FROM kasse WHERE userid = :userid";
                $stmt = $pdo->prepare($sql);
                $stmt->execute(['userid' => $userid]);

                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

                    $jahr = date('Y');

                    $result = berechneBestaende($pdo, $userid, $row['id'], $jahr);

                    $kassenId = $row['id'];
                    $formattedDate = (new DateTime($row['datumab']))->format('d.m.Y');
                    $anfangsbestand = (float) $row['anfangsbestand'];
                    $anfangsbestandFormatted = number_format($anfangsbestand, 2, ',', '.') . ' €';

                    // Letzten Bestand aus bestaende holen
                    $stmtBestand = $pdo->prepare("
                                        SELECT bestand 
                                        FROM bestaende 
                                        WHERE kassennummer = :kassennummer 
                                        AND userid = :userid
                                        AND bestand > 0
                                        ORDER BY datum DESC
                                        LIMIT 1
                                    ");
                    $stmtBestand->execute([
                        ':kassennummer' => $kassenId,
                        ':userid' => $userid
                    ]);
                    $aktuellerBestand = $stmtBestand->fetchColumn();
                    $aktuellerBestandFormatted = $aktuellerBestand !== false
                        ? number_format((float) $aktuellerBestand, 2, ',', '.') . ' €'
                        : '-';

                    // Badge für Kasse minus mit Tooltip
                    $checkminusBadge = $row['checkminus'] == 1
                        ? '<span class="badge bg-danger" data-bs-toggle="tooltip" title="Kasse kann ins Minus gehen">Ja</span>'
                        : '<span class="badge bg-success" data-bs-toggle="tooltip" title="Kasse darf nicht ins Minus gehen">Nein</span>';

                    // Header-Farbe je nach Anfangsbestand
                    if ($aktuellerBestand >= 200) {
                        $headerClass = 'bg-success text-white';
                    } elseif ($aktuellerBestand >= 100) {
                        $headerClass = 'bg-warning text-dark';
                    } else {
                        $headerClass = 'bg-danger text-white';
                    }

                    // Kritisches Label bei < 200 €
                    $kritischLabel = $aktuellerBestand < 100
                        ? '<span class="position-absolute top-0 end-0 m-2 px-2 py-1 bg-danger text-white rounded-pill small" title="Bestand sehr niedrig!">KRITISCH</span>'
                        : '';

                    echo "
        <div class='col-sm-6 col-md-4 col-lg-3 position-relative'>
            <div class='card shadow-sm d-flex h-100 flex-column card-hover'>
                {$kritischLabel}
                <div class='card-header {$headerClass} py-2 px-3'>
                    <h6 class='mb-0'>{$row['kasse']}</h6>
                </div>
                <div class='card-body py-2 px-3 flex-grow-1'>
                    <p class='card-text mb-1'>
                        <strong>Kontonummer:</strong> {$row['kontonummer']}
                    </p>
                    <p class='card-text mb-1'>
                        <strong>Datum ab:</strong> {$formattedDate}
                    </p>
                    <p class='card-text mb-1'>
                        <strong>Anfangsbestand:</strong> {$anfangsbestandFormatted}
                    </p>
                    <p class='card-text mb-1'>
                        <strong>Aktueller Bestand:</strong> {$aktuellerBestandFormatted}
                    </p>
                  
                    <p class='card-text mb-0'>
                        <strong>Kasse minus:</strong> {$checkminusBadge}
                    </p>
                </div>
                <div class='card-footer bg-light py-2 px-3 d-flex justify-content-end'>
                    <a href='Editkasse.php?id={$row['id']}' 
                       class='btn btn-primary btn-sm me-2' title='Kasse bearbeiten'>
                       <i class='fa-solid fa-pen-to-square'></i>
                    </a>
                    <a href='Buchungen.php?kassennummer={$row['id']}' 
                       class='btn btn-secondary btn-sm me-2' title='Buchungen ansehen'>
                       <i class='fa-solid fa-ticket'></i>
                    </a>
                    <a href='DeleteKasse.php?id={$row['id']}' 
                       data-id='{$row['id']}' 
                       class='btn btn-danger btn-sm delete-button' title='Kasse löschen'>
                       <i class='fa-solid fa-trash'></i>
                    </a>
                </div>
            </div>
        </div>";
                }
                ?>
            </div>




            <!-- Hover Effekt -->
            <style>
                .card-hover {
                    transition: transform 0.2s ease, box-shadow 0.2s ease;
                }

                .card-hover:hover {
                    transform: translateY(-5px);
                    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.3);
                }
            </style>

            <!-- Bootstrap Tooltip aktivieren -->
            <script>
                document.addEventListener('DOMContentLoaded', function () {
                    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
                    tooltipTriggerList.forEach(function (tooltipTriggerEl) {
                        new bootstrap.Tooltip(tooltipTriggerEl)
                    });
                });
            </script>


        </form>
    </div>
    <!-- Bootstrap Tooltip aktivieren -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
            tooltipTriggerList.forEach(function (tooltipTriggerEl) {
                new bootstrap.Tooltip(tooltipTriggerEl)
            });
        });
    </script>

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
                    Möchten Sie diese Kasse mit allen Positionen wirklich löschen?
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
                Kasse wurde gelöscht.
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
                //alert(deleteId);
                $('#confirmDeleteModal').modal('show'); // Zeige das Modal an
            });

            $('#confirmDeleteBtn').on('click', function () {
                if (deleteId) {
                    const form = $('<form>', {
                        action: 'Deletekasse.php',
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
            $('#TableKassen').DataTable({
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
                autoWidth: false
            });
        });
    </script>
</body>

</html>