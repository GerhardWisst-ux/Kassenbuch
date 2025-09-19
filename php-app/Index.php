<?php
ob_start();
session_start();

require 'db.php';
require_once 'includes/bestaende_berechnen.php';

// Session prüfen
if (empty($_SESSION['userid'])) {
    header('Location: Login.php');
    exit;
}

$userid = $_SESSION['userid'];
$mandantennummer = $_SESSION['mandantennummer'] ?? null;

// Kassennummer für Startseite explizit zurücksetzen
unset($_SESSION['kassennummer']);

// CSRF-Token erzeugen
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$csrf_token = $_SESSION['csrf_token'];

// Erfolg / Fehler aus Session holen
$success = $_SESSION['success_message'] ?? null;
$error = $_SESSION['error_message'] ?? null;
unset($_SESSION['success_message'], $_SESSION['error_message']);

// Header zuletzt einbinden, nachdem die Session-Variablen angepasst wurden
require_once 'includes/header.php';
?>
<!DOCTYPE html>
<html lang="de">

<head>
    <meta charset="UTF-8">
    <title>CashControl - Kassenübersicht</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="css/jquery.dataTables.min.css" rel="stylesheet">
    <link href="css/responsive.dataTables.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">

    <style>
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
    <header class="custom-header py-2 text-white">
        <div class="container-fluid">
            <div class="row align-items-center">
                <!-- Titel zentriert -->
                <div class="col-12 text-center mb-2 mb-md-0">
                    <h2 class="h4 mb-0">CashControl - Kassenübersicht</h2>
                </div>

                <?php
                require_once 'includes/benutzerversion.php';
                ?>
            </div>
        </div>
    </header>

    <div class="container-fluid mt-3">
        <!-- Toast -->
        <?php if ($success): ?>
            <div class="toast-container position-fixed top-0 end-0 p-3">
                <div id="deleteToast" class="toast align-items-center text-bg-success border-0" role="alert"
                    aria-live="assertive" aria-atomic="true">
                    <div class="d-flex">
                        <div class="toast-body"><?= htmlspecialchars($success, ENT_QUOTES, 'UTF-8') ?></div>
                        <button type="button" class="btn-close btn-close-white me-2 m-auto"
                            data-bs-dismiss="toast"></button>
                    </div>
                </div>
            </div>
            <script>
                var toastEl = document.getElementById('deleteToast');
                if (toastEl) { new bootstrap.Toast(toastEl).show(); }
            </script>
        <?php endif; ?>


        <form id="kasseform" method="get">
            <input type="hidden" id="csrf_token" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
            <!-- Toolbar -->

            <div class="d-flex justify-content-between align-items-center mb-3">
                <!-- Linke Buttons -->
                <div>
                    <a href="Addkasse.php" class="btn btn-primary btn-sm rounded-circle me-2 circle-btn"
                        title="Kasse hinzufügen"><i class="fa fa-plus"></i></a>
                </div>

                <!-- Hilfe rechts -->
                <div>
                    <div class="ms-auto">
                        <a href="help/Kasse.php" class="btn btn-primary btn-sm rounded-circle me-2 circle-btn"
                            title="Hilfe"><i class="fa fa-question-circle"></i></a>
                    </div>
                    </a>
                </div>
            </div>

            <?php
            // Anzahl aktive / archivierte Kassen
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM kasse WHERE userid=:uid AND mandantennummer=:mn AND archiviert=0");
            $stmt->execute(['uid' => $userid, 'mn' => $mandantennummer]);
            $anzahlAktiv = (int) $stmt->fetchColumn();

            $stmt = $pdo->prepare("SELECT COUNT(*) FROM kasse WHERE userid=:uid AND mandantennummer=:mn AND archiviert=1");
            $stmt->execute(['uid' => $userid, 'mn' => $mandantennummer]);
            $anzahlArchiv = (int) $stmt->fetchColumn();

            // Summen der aktuellen Bestände
            function getSummeBestand(PDO $pdo, int $userid, int $mandantennummer, int $archiviert): float
            {
                $stmt = $pdo->prepare("
                                SELECT SUM(b.bestand) 
                                FROM bestaende b
                                JOIN kasse k ON b.kassennummer = k.id
                                WHERE k.userid = :uid AND k.mandantennummer = :mn AND k.archiviert = :archiviert
                                AND b.datum = (
                                    SELECT MAX(b2.datum) FROM bestaende b2 WHERE b2.kassennummer = b.kassennummer
                                )
                            ");
                $stmt->execute(['uid' => $userid, 'mn' => $mandantennummer, 'archiviert' => $archiviert]);
                return (float) $stmt->fetchColumn() ?: 0;
            }

            $summeAktiv = getSummeBestand($pdo, $userid, $mandantennummer, 0);
            $summeArchiv = getSummeBestand($pdo, $userid, $mandantennummer, 1);
            ?>

            <ul class="nav nav-tabs" id="kassenTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#aktive" type="button">
                        Aktive Kassen <span class="badge bg-primary"><?= $anzahlAktiv ?></span>
                        <small>(<?= number_format($summeAktiv, 2, ',', '.') ?> €)</small>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#archivierte" type="button">
                        Archivierte Kassen <span class="badge bg-secondary"><?= $anzahlArchiv ?></span>
                        <small>(<?= number_format($summeArchiv, 2, ',', '.') ?> €)</small>
                    </button>
                </li>
            </ul>

            <div class="tab-content mt-3">
                <div class="tab-pane fade show active" id="aktive">
                    <div class="row">
                        <?php
                        $stmt = $pdo->prepare("
                                    SELECT k.*, ku.nachname 
                                    FROM kasse k 
                                    LEFT JOIN mandanten ku ON k.mandantennummer = ku.kundennummer 
                                    WHERE k.userid = :uid AND k.mandantennummer = :mn AND k.archiviert = 0 
                                    ORDER BY k.datumab ASC
                                ");
                        $stmt->execute(['uid' => $userid, 'mn' => $mandantennummer]);

                        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                            // Anfangsbestand prüfen: falls keine Einträge in 'bestaende', initialisieren
                            $stmtCheck = $pdo->prepare("SELECT COUNT(*) FROM bestaende WHERE kassennummer = :kid");
                            $stmtCheck->execute(['kid' => $row['id']]);
                            if ((int) $stmtCheck->fetchColumn() === 0) {
                                $stmtInit = $pdo->prepare("INSERT INTO bestaende (kassennummer, datum, bestand) VALUES (:kid, NOW(), :bestand)");
                                $stmtInit->execute([
                                    'kid' => $row['id'],
                                    'bestand' => $row['anfangsbestand']
                                ]);
                            }

                            // Aktuellen Bestand abrufen
                            $stmtBestand = $pdo->prepare("
                                    SELECT bestand FROM bestaende 
                                    WHERE kassennummer = :kid 
                                    ORDER BY datum DESC LIMIT 1
                                ");
                            $stmtBestand->execute(['kid' => $row['id']]);
                            $row['aktueller_bestand'] = (float) $stmtBestand->fetchColumn();

                            include 'KassenCard.php';
                        }
                        ?>
                    </div>
                </div>
                <div class="tab-pane fade" id="archivierte">
                    <div class="row">
                        <?php
                        $stmt = $pdo->prepare("
                                SELECT k.*, ku.nachname 
                                FROM kasse k 
                                LEFT JOIN mandanten ku ON k.mandantennummer = ku.kundennummer 
                                WHERE k.userid = :uid AND k.mandantennummer = :mn AND k.archiviert = 1 
                                ORDER BY k.datumab ASC
                            ");
                        $stmt->execute(['uid' => $userid, 'mn' => $mandantennummer]);

                        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                            // Aktuellen Bestand abrufen
                            $stmtBestand = $pdo->prepare("
                                    SELECT bestand FROM bestaende 
                                    WHERE kassennummer = :kid 
                                    ORDER BY datum DESC LIMIT 1
                                ");
                            $stmtBestand->execute(['kid' => $row['id']]);
                            $row['aktueller_bestand'] = (float) $stmtBestand->fetchColumn();

                            include 'KassenCard.php';
                        }
                        ?>
                    </div>
                </div>
            </div>

        </form>

        <!-- Delete Modal -->
        <div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Löschbestätigung</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        Möchten Sie diese Kasse mitsamt den Buchungen und Beständen wirklich löschen?
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Abbrechen</button>
                        <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Löschen</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="js/jquery.min.js"></script>
    <script src="js/bootstrap.bundle.min.js"></script>
    <script src="js/jquery.dataTables.min.js"></script>
    <script src="js/dataTables.responsive.min.js"></script>

    <script>
        $(document).ready(function () {
            $('#TableKassen').DataTable({
                language: { url: "https://cdn.datatables.net/plug-ins/1.13.4/i18n/de-DE.json" },
                responsive: true,
                scrollX: false,
                pageLength: 50,
                autoWidth: false
            });

            let deleteId = null;

            $('.delete-button').on('click', function (e) {
                e.preventDefault();
                deleteId = $(this).data('id');
                $('#confirmDeleteModal').modal('show');
            });

            $('#confirmDeleteBtn').on('click', function () {
                if (!deleteId) return;

                const csrfToken = $('#csrf_token').val();
                const form = $('<form>', {
                    method: 'POST',
                    action: 'Deletekasse.php'
                }).append($('<input>', {
                    type: 'hidden', name: 'id', value: deleteId
                })).append($('<input>', {
                    type: 'hidden', name: 'csrf_token', value: csrfToken
                }));

                $(document.body).append(form);
                form.submit();
                $('#confirmDeleteModal').modal('hide');
            });            
        });
    </script>

</body>

</html>