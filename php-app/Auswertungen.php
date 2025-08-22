<?php
ob_start();
session_start();
if (empty($_SESSION['userid'])) {
    http_response_code(403);
    echo json_encode(['error' => 'not authorized']);
    header('Location: Login.php'); // zum Loginformular
    exit;
}
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require 'db.php';
$userid = $_SESSION['userid'];
?>

<!DOCTYPE html>
<html lang="de">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Kassenbuch Auswertungen</title>

    <!-- CSS -->
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="css/jquery.dataTables.min.css" rel="stylesheet">
    <link href="css/responsive.dataTables.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">

    <style>
       
        #TableAuswertungen {
            width: 100%;
            font-size: 0.9rem;
        }

        #TableAuswertungen tbody tr:hover {
            background-color: #f1f5ff;
        }      
    </style>
</head>

<body>

    <?php

    require 'db.php';
    $email = $_SESSION['email'];
    $userid = $_SESSION['userid'];

    require_once 'includes/header.php';

    ?>

    <div class="custom-container mx-2">
        <header class="custom-header py-2 text-white">
            <div class="container-fluid">
                <div class="row align-items-center">
                    <!-- Titel zentriert -->
                    <div class="col-12 text-center mb-2 mb-md-0">
                        <h2 class="h4 mb-0">Kassenbuch - Auswertungen</h2>
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
        <a href="Index.php" class="btn btn-primary btn-sm mb-3"><i class="fa fa-arrow-left"></i></a>
        <table id="TableAuswertungen" class="display nowrap">
            <thead>
                <tr>
                    <th>Buchungstyp</th>
                    <th>Buchungsart</th>
                    <th style="text-align:right;">Anzahl</th>
                    <th style="text-align:right;">Gesamtbetrag</th>
                    <th style="text-align:right;">Anteil</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $sql = "WITH Gesamtausgaben AS (
                        SELECT SUM(betrag) AS gesamt_ausgaben
                        FROM buchungen
                        WHERE barkasse = 1
                          AND typ = 'Ausgabe'
                          AND userid = :userid
                    )
                    SELECT 
                        b.typ AS buchungstyp,
                        b.buchungsart AS buchungsart,
                        COUNT(*) AS anzahl,
                        SUM(b.betrag) AS gesamt_betrag,
                        CASE 
                            WHEN b.typ = 'Ausgabe' THEN 
                                ROUND(SUM(b.betrag) / (SELECT gesamt_ausgaben FROM Gesamtausgaben) * 100, 2)
                            ELSE 
                                0
                        END AS anteil
                    FROM buchungen b
                    LEFT JOIN buchungsarten ba ON b.buchungsart = ba.id
                    WHERE b.barkasse = 1
                      AND b.typ = 'Ausgabe'
                      AND b.userid = :userid
                    GROUP BY b.typ, b.buchungsart
                    ORDER BY anteil DESC, b.betrag DESC";

                $stmt = $pdo->prepare($sql);
                $stmt->execute(['userid' => $userid]);

                $gesamtBetrag = 0;
                $gesamtAnteil = 0;

                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $gesamtBetrag += $row['gesamt_betrag'];
                    if ($row['buchungstyp'] === 'Ausgabe') {
                        $gesamtAnteil += $row['anteil'];
                    }

                    echo "<tr>
                    <td>" . htmlspecialchars($row['buchungstyp']) . "</td>
                    <td>" . htmlspecialchars($row['buchungsart'] ?? 'Unbekannt') . "</td>
                    <td style='text-align:right'>" . number_format($row['anzahl'], 0, ',', '.') . "</td>
                    <td style='text-align:right'>" . number_format($row['gesamt_betrag'], 2, ',', '.') . " €</td>
                    <td style='text-align:right'>" . number_format($row['anteil'], 2, ',', '.') . " %</td>
                </tr>";
                }
                ?>
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="2" style="text-align:right;">Gesamt:</th>
                    <th></th>
                    <th style="text-align:right;"><?= number_format($gesamtBetrag, 2, ',', '.') ?> €</th>
                    <th style="text-align:right;"><?= number_format($gesamtAnteil, 2, ',', '.') ?> %</th>
                </tr>
            </tfoot>
        </table>
    </div>

    <!-- JS -->
    <script src="js/jquery.min.js"></script>
    <script src="js/bootstrap.bundle.min.js"></script>
    <script src="js/jquery.dataTables.min.js"></script>
    <script src="js/dataTables.min.js"></script>
    <script src="js/dataTables.responsive.min.js"></script>

    <script>
        $('#TableAuswertungen').DataTable({
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
    </script>
</body>

</html>
<?php ob_end_flush(); ?>