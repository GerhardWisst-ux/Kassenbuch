<?php
ob_start();
session_start();
if (empty($_SESSION['userid'])) {
    http_response_code(403);
    header('Location: Login.php');
    exit;
}
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>CashControl - Bestände</title>

    <!-- CSS -->
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="css/jquery.dataTables.min.css" rel="stylesheet">
    <link href="css/responsive.dataTables.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
    <style>
        #TableBestaende {
            width: 100%;
            font-size: 0.9rem;
        }

        #TableBestaende tbody tr:hover {
            background-color: #f1f5ff;
        }
    </style>
</head>

<body>
    <?php
    require 'db.php';
    require_once 'includes/header.php';
    require_once 'includes/bestaende_berechnen.php';

    $email = $_SESSION['email'];
    $userid = $_SESSION['userid'];
    $kassennummer = $_SESSION['kassennummer'] ?? null;
    //echo $kassennummer;
    
    // Verfügbare Jahre aus der Datenbank holen
    $sqlYears = "SELECT DISTINCT YEAR(datum) AS Jahr FROM bestaende WHERE userid = :userid AND kassennummer = :kassennummer ORDER BY Jahr DESC";
    $stmtYears = $pdo->prepare($sqlYears);
    $stmtYears->execute([
        'userid' => $userid,
        'kassennummer' => $kassennummer
    ]);
    $jahre = $stmtYears->fetchAll(PDO::FETCH_COLUMN);

    // Aktuelles Jahr
    if (isset($_POST['jahr'])) {
        $jahrFilter = (int) $_POST['jahr'];
    } elseif (isset($_GET['jahr'])) {
        $jahrFilter = (int) $_GET['jahr'];
    } else {
        $jahrFilter = date("Y"); // Alle Jahre
    }

    // Berechnung der Bestände
    $bestaendeBerechnet = false;
    $berechnungsMeldung = '';
    $gesamtSaldo = 0;

    if (isset($_POST['berechne_bestaende'])) {
        $jahr = $jahrFilter ?: date('Y');

        $result = berechneBestaende($pdo, $userid, $kassennummer, $jahr);

        header("Location: Bestaende.php?jahr=$jahr&berechnet=1&eingefuegt={$result['eingefuegt']}&aktualisiert={$result['aktualisiert']}&saldo={$result['saldo']}");
        exit;
    }
    ?>

    <header class="custom-header py-2 text-white">
        <div class="container-fluid">
            <div class="row align-items-center">
                <?php
                $sql = "SELECT * FROM kasse WHERE userid = :userid AND id = :kassennummer";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    'userid' => $userid,
                    'kassennummer' => $kassennummer
                ]);

                $kasse = "Unbekannte Kasse";
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $kasse = $row['kasse'];
                }
                ?>
                <!-- Titel zentriert -->
                <div class="col-12 text-center mb-2 mb-md-0">
                    <h2 class="h2 mb-0"><?php echo htmlspecialchars($kasse); ?> - Bestände</h2>
                </div>
                <!-- Benutzerinfo + Logout -->
                <div class="col-12 col-md-auto ms-md-auto text-center text-md-end">
                    <div class="d-block d-md-inline mb-1 mb-md-0">
                        <span class="me-2">Angemeldet als:
                            <?= htmlspecialchars($_SESSION['email']) ?></span>
                    </div>
                    <a class="btn btn-darkgreen btn-sm" title="Abmelden vom Webshop" href="logout.php">
                        <i class="fa fa-sign-out" aria-hidden="true"></i> Ausloggen
                    </a>
                </div>
            </div>
        </div>
    </header>

    <div class="custom-container mt-3 mx-2">
        <!-- Formular: Jahr + Berechnen -->
        <form method="POST" id="bestaendeForm" class="mb-3 d-flex align-items-center">
            <button type="submit" name="berechne_bestaende" class="btn btn-primary btn-sm me-2">
                <i class="fas fa-calculator"></i>
            </button>
            <a href="BestaendeChart.php" class="btn btn-primary btn-sm me-2"><i class="fas fa-chart-bar"></i></a>
            <a href="Index.php" class="btn btn-primary btn-sm"><i class="fa fa-arrow-left"></i></a>

            <div class="ms-3">
                <label for="jahr" class="form-label fw-bold me-2">Jahr auswählen:</label>
                <select name="jahr" id="jahr" class="form-select w-auto me-2">
                    <?php foreach ($jahre as $jahrOption): ?>
                        <option value="<?= $jahrOption ?>" <?= ($jahrOption == $jahrFilter) ? 'selected' : '' ?>>
                            <?= $jahrOption ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </form>

        <!-- Berechnungs-Toast -->
        <?php if (isset($_GET['berechnet']) && $_GET['berechnet'] == 1):
            $eingefuegt = (int) ($_GET['eingefuegt'] ?? 0);
            $gesamtSaldo = (float) ($_GET['saldo'] ?? 0);
            $berechnungsMeldung = $eingefuegt > 0
                ? "Bestände für $eingefuegt Monate erfolgreich berechnet."
                : "Bestände erfolgreich berechnet.";
            ?>
            <div class="toast-container position-fixed top-0 end-0 p-3">
                <div id="calcToast" class="toast toast-green" role="alert" aria-live="assertive" aria-atomic="true">
                    <div class="toast-header">
                        <strong class="me-auto">Benachrichtigung</strong>
                        <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
                    </div>
                    <div class="toast-body">
                        <?= htmlspecialchars($berechnungsMeldung) ?><br>
                        <?= $eingefuegt > 0 ? "Gesamtsaldo der neuen Monate: " . number_format($gesamtSaldo, 2, ',', '.') . ' €' : "" ?>
                    </div>
                </div>
            </div>
            <script>
                document.addEventListener("DOMContentLoaded", function () {
                    var toast = new bootstrap.Toast(document.getElementById('calcToast'));
                    toast.show();
                });
            </script>
        <?php endif; ?>

        <!-- Tabelle -->
        <div class="table-responsive">
            <table id="TableBestaende" class="display nowrap table table-striped w-100">
                <thead>
                    <tr>
                        <th>Datum</th>
                        <th style="text-align:right;">Einlagen</th>
                        <th style="text-align:right;">Ausgaben</th>
                        <th style="text-align:right;">Bestand</th>
                        <th style="text-align:center;">Aktionen</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $sql = ($jahrFilter == 0)
                        ? "SELECT * FROM bestaende WHERE userid=:userid  AND kassennummer = :kassennummer ORDER BY datum DESC"
                        : "SELECT * FROM bestaende WHERE userid=:userid AND kassennummer = :kassennummer AND YEAR(datum)=:jahr ORDER BY datum DESC";
                    $stmt = $pdo->prepare($sql);
                    $params = [
                        'userid' => $userid,
                        'kassennummer' => $kassennummer
                    ];
                    if ($jahrFilter != 0)
                        $params['jahr'] = $jahrFilter;
                    $stmt->execute($params);

                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        $formattedDate = (new DateTime($row['datum']))->format('d.m.Y');
                        $einlagen = number_format((float) $row['einlagen'], 2, ',', '.') . ' €';
                        $ausgaben = number_format((float) $row['ausgaben'], 2, ',', '.') . ' €';
                        $bestand = number_format((float) $row['bestand'], 2, ',', '.') . ' €';

                        echo "<tr>
                        <td>$formattedDate</td>
                        <td style='text-align:right;'>$einlagen</td>
                        <td style='text-align:right;'>$ausgaben</td>
                        <td style='text-align:right;'>$bestand</td>
                        <td style='text-align:center; white-space: nowrap;'>
                            <a href='EditBestand.php?id={$row['id']}' class='btn btn-primary btn-sm me-1'>
                                <i class='fa-solid fa-pen-to-square'></i>
                            </a>
                            <a href='#' data-id='{$row['id']}' class='btn btn-danger btn-sm delete-button'>
                                <i class='fa-solid fa-trash'></i>
                            </a>
                        </td>
                    </tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- JS -->
    <script src="js/jquery.min.js"></script>
    <script src="js/bootstrap.bundle.min.js"></script>
    <script src="js/jquery.dataTables.min.js"></script>
    <script src="js/dataTables.min.js"></script>
    <script src="js/dataTables.responsive.min.js"></script>

    <script>
        $(document).ready(function () {
            $('#TableBestaende').DataTable({
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

            let deleteId = null;
            $('.delete-button').on('click', function (e) {
                e.preventDefault();
                deleteId = $(this).data('id');
                $('#confirmDeleteModal').modal('show');
            });

            $('#confirmDeleteBtn').on('click', function () {
                if (deleteId) {
                    const form = $('<form>', { action: 'DeleteBestand.php', method: 'POST' })
                        .append($('<input>', { type: 'hidden', name: 'id', value: deleteId }));
                    $('body').append(form);
                    form.submit();
                }
                $('#confirmDeleteModal').modal('hide');
                var toast = new bootstrap.Toast($('#deleteToast')[0]);
                toast.show();
            });
        });

        // Automatisch Berechnung auslösen, wenn das Jahr geändert wird
        document.getElementById('jahr').addEventListener('change', function () {
            const form = document.getElementById('bestaendeForm');
            // Simuliere Klick auf den Berechnen-Button
            const hiddenButton = document.createElement('input');
            hiddenButton.type = 'hidden';
            hiddenButton.name = 'berechne_bestaende';
            hiddenButton.value = '1';
            form.appendChild(hiddenButton);
            form.submit();
        });
    </script>

    <?php ob_end_flush(); ?>