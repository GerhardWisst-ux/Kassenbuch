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

<!DOCTYPE html>
<html>
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
        .card-hover {
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
                                <span class="me-2">Benutzer: <?= htmlspecialchars($_SESSION['email']) ?></span>
                            </div>
                            <!-- Version -->
                            <span class="version-info" title="Git-Hash + Build-Datum">
                                Version: <?= htmlspecialchars($appVersion->getVersion()) ?>
                            </span>
                            <span>
                                <!-- Logout-Button -->
                                <a class="btn btn-darkgreen btn-sm" title="Abmelden vom Webshop" href="logout.php">
                                    <i class="fa fa-sign-out" aria-hidden="true"></i> Ausloggen
                                </a>

                            </span>
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
            <?php



            // Anzahl aktive Kassen
            $stmtAktiv = $pdo->prepare("SELECT COUNT(*) FROM kasse WHERE userid = :userid AND archiviert = 0");
            $stmtAktiv->execute(['userid' => $userid]);
            $anzahlAktiv = (int) $stmtAktiv->fetchColumn();

            // Anzahl archivierte Kassen
            $stmtArchiv = $pdo->prepare("SELECT COUNT(*) FROM kasse WHERE userid = :userid AND archiviert = 1");
            $stmtArchiv->execute(['userid' => $userid]);
            $anzahlArchiv = (int) $stmtArchiv->fetchColumn();


            // Summe der aktuellen Bestände aktive Kassen
            $stmtSaldoAktiv = $pdo->prepare("
    SELECT SUM(bestand) 
    FROM bestaende b
    JOIN kasse k ON b.kassennummer = k.id
    WHERE k.userid = :userid AND k.archiviert = 0
      AND b.datum = (SELECT MAX(b2.datum) 
                     FROM bestaende b2 
                     WHERE b2.kassennummer = b.kassennummer)
");
            $stmtSaldoAktiv->execute(['userid' => $userid]);
            $summeAktiv = (float) $stmtSaldoAktiv->fetchColumn() ?: 0;

            // Summe der aktuellen Bestände archivierte Kassen
            $stmtSaldoArchiv = $pdo->prepare("
    SELECT SUM(bestand) 
    FROM bestaende b
    JOIN kasse k ON b.kassennummer = k.id
    WHERE k.userid = :userid AND k.archiviert = 1
      AND b.datum = (SELECT MAX(b2.datum) 
                     FROM bestaende b2 
                     WHERE b2.kassennummer = b.kassennummer)
");
            $stmtSaldoArchiv->execute(['userid' => $userid]);
            $summeArchiv = (float) $stmtSaldoArchiv->fetchColumn() ?: 0;
            ?>

            <ul class="nav nav-tabs" id="kassenTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="aktive-tab" data-bs-toggle="tab" data-bs-target="#aktive"
                        type="button" role="tab">
                        Aktive Kassen <span class="badge bg-primary"><?= $anzahlAktiv ?></span>
                        <small>(<?= number_format($summeAktiv, 2, ',', '.') ?> €)</small>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="archivierte-tab" data-bs-toggle="tab" data-bs-target="#archivierte"
                        type="button" role="tab">
                        Archivierte Kassen <span class="badge bg-secondary"><?= $anzahlArchiv ?></span>
                        <small>(<?= number_format($summeArchiv, 2, ',', '.') ?> €)</small>
                    </button>
                </li>
            </ul>
            <div class="tab-content mt-3" id="kassenTabsContent">
                <div class="tab-pane fade show active" id="aktive" role="tabpanel">
                    <div class="row">
                        <?php
                        // Aktive Kassen laden
                        $stmt = $pdo->prepare("SELECT * FROM kasse WHERE userid = :userid AND archiviert = 0 ORDER BY datumab ASC");
                        $stmt->execute([':userid' => $userid]);
                        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                            $jahr = date('Y');
                            $result = berechneBestaende($pdo, $userid, $row['id'], $jahr);
                            include 'KassenCard.php';
                        }
                        ?>
                    </div>
                </div>
                <div class="tab-pane fade" id="archivierte" role="tabpanel">
                    <div class="row">
                        <?php
                        // Archivierte Kassen laden
                        $stmt = $pdo->prepare("SELECT * FROM kasse WHERE userid = :userid AND archiviert = 1 ORDER BY datumab ASC");
                        $stmt->execute([':userid' => $userid]);
                        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                            include 'KassenCard.php';
                        }
                        ?>
                    </div>
                </div>
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