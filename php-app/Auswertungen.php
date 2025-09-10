<?php
ob_start();
session_start();
if (empty($_SESSION['userid'])) {
    header('Location: Login.php');
    exit;
}

require 'db.php';
$userid = $_SESSION['userid'];
$email = $_SESSION['email'];
$kassennummer = $_SESSION['kassennummer'] ?? null;
//echo $kassennummer;

// Monatsnamen auf Deutsch
$monate_de = [
    '01' => 'Januar',
    '02' => 'Februar',
    '03' => 'März',
    '04' => 'April',
    '05' => 'Mai',
    '06' => 'Juni',
    '07' => 'Juli',
    '08' => 'August',
    '09' => 'September',
    '10' => 'Oktober',
    '11' => 'November',
    '12' => 'Dezember'
];

// ausgewählter Monat
$selectedMonat = $_GET['monat'] ?? '';

// Alle Monate für Dropdown
$stmtMonate = $pdo->prepare("
    SELECT DISTINCT DATE_FORMAT(datum,'%Y-%m') AS monat
    FROM buchungen
    WHERE userid=:userid
    AND kassennummer = :kassennummer
    ORDER BY datum DESC
");
$stmtMonate->execute([
    'userid' => $userid,
    'kassennummer' => $kassennummer
]);
$monate = $stmtMonate->fetchAll(PDO::FETCH_COLUMN);

// Bedingungen für SQL (jetzt eindeutig mit Tabellennamen)
$where = "b.barkasse=1 AND b.typ='Ausgabe' AND b.kassennummer = :kassennummer AND b.userid=:userid";
$params = [
    'userid' => $userid,
    'kassennummer' => $kassennummer
];

if ($selectedMonat !== '') {
    $where .= " AND DATE_FORMAT(b.datum,'%Y-%m')=:monat";
    $params['monat'] = $selectedMonat;
}

// Gesamtausgaben berechnen (nur für selektierte Buchungen)
$sqlGesamt = "SELECT SUM(b.betrag) AS gesamt FROM buchungen b WHERE $where";
$stmt = $pdo->prepare($sqlGesamt);
$stmt->execute($params);
$gesamt = $stmt->fetchColumn();
$gesamt = $gesamt ?? 0;

// Buchungen nach Buchungsart gruppiert
$sql = "
SELECT 
    COALESCE(ba.buchungsart,b.buchungsart) AS buchungsart,
    COUNT(*) AS anzahl,
    SUM(b.betrag) AS gesamt_betrag,
    CASE WHEN :gesamt>0 THEN ROUND(SUM(b.betrag)/:gesamt*100,2) ELSE 0 END AS anteil
FROM buchungen b
LEFT JOIN buchungsarten ba ON b.buchungsart=ba.id
WHERE $where
GROUP BY buchungsart
ORDER BY anteil DESC
";
$params['gesamt'] = $gesamt;

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Summen für Fußzeile
$sumBetrag = array_sum(array_column($rows, 'gesamt_betrag'));
$sumAnteil = array_sum(array_column($rows, 'anteil'));
?>
<!DOCTYPE html>
<html lang="de">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>CashControl Auswertungen</title>
    <link rel="icon" type="image/png" href="images/favicon.png" />
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

        .details-control {
            cursor: pointer;
        }

        /* Dropdown-Breite begrenzen */
        #monat {
            width: 200px;
            /* feste Breite */
            max-width: 100%;
            /* responsive, nicht größer als Container */
        }

        /* Scrollbar für lange Listen */
        #monat option {
            max-height: 150px;
            /* maximale Höhe jeder Option-Liste (in manchen Browsern nötig) */
        }

        /* Modernere Darstellung mit leichtem Schatten */
        #monat {
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.15);
        }
    </style>
</head>

<body>
    <?php require_once 'includes/header.php'; ?>

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
                    <h2 class="h4 mb-0"><?php echo htmlspecialchars($kasse); ?> - Auswertungen</h2>
                </div>
                <?php
                require_once 'includes/benutzerversion.php';
                ?>
            </div>
        </div>
    </header>
    <div class="custom-container mt-3 mx-2">
        <a href="Index.php" class="btn btn-primary btn-sm mb-3"><i class="fa fa-arrow-left"></i></a>
        <button id="btnPieChart" class="btn btn-primary btn-sm mb-3">
            <i class="fa fa-chart-pie"></i> Chart
        </button>

        <form method="get" class="mb-3">
            <label for="monat" class="form-label">Filter nach Monat:</label>
            <select name="monat" id="monat" class="form-select" onchange="this.form.submit()">
                <option value="">Alle Monate</option>
                <?php foreach ($monate as $monat):
                    $parts = explode('-', $monat);
                    $monatName = $monate_de[$parts[1]] . ' ' . $parts[0];
                    $selected = ($selectedMonat === $monat) ? 'selected' : ''; ?>
                    <option value="<?= htmlspecialchars($monat) ?>" <?= $selected ?>><?= $monatName ?></option>
                <?php endforeach; ?>
            </select>
        </form>

        <div id="chartContainer" style="width:100%; max-width:600px; margin-bottom:20px; display:none;">
            <canvas id="pieChart"></canvas>
        </div>
        <table id="TableAuswertungen" class="display nowrap">
            <thead>
                <tr>
                    <th></th>
                    <th>Buchungsart</th>
                    <th style="text-align:right;">Anzahl</th>
                    <th style="text-align:right;">Gesamtbetrag</th>
                    <th style="text-align:right;">Anteil</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($rows as $row): ?>
                    <tr>
                        <td class="details-control"><i class="fa fa-plus-circle"></i></td>
                        <td><?= htmlspecialchars($row['buchungsart'] ?? 'Unbekannt') ?></td>
                        <td style="text-align:right"><?= number_format($row['anzahl'], 0, ',', '.') ?></td>
                        <td style="text-align:right"><?= number_format($row['gesamt_betrag'], 2, ',', '.') ?> €</td>
                        <td style="text-align:right"><?= number_format($row['anteil'], 2, ',', '.') ?> %</td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr>
                    <th>Gesamt:</th>
                    <th></th>
                    <th style="text-align:right"><?= array_sum(array_column($rows, 'anzahl')) ?></th>
                    <th style="text-align:right"><?= number_format($sumBetrag, 2, ',', '.') ?> €</th>
                    <th style="text-align:right"><?= number_format($sumAnteil, 2, ',', '.') ?> %</th>
                </tr>
            </tfoot>
        </table>
    </div>

    <script src="js/jquery.min.js"></script>
    <script src="js/bootstrap.bundle.min.js"></script>
    <script src="js/jquery.dataTables.min.js"></script>
    <script src="js/dataTables.responsive.min.js"></script>
    <script src="js/Chart.min.js"></script>
    <script>
        var table = $('#TableAuswertungen').DataTable({
            language: { url: "https://cdn.datatables.net/plug-ins/1.13.4/i18n/de-DE.json" },
            responsive: false,
            scrollX: false,
            pageLength: 50,
            autoWidth: false
        });

        function format(buchungsartId) {
            return $.ajax({
                url: 'GetBuchungenDetails.php',
                type: 'POST',
                data: { id: buchungsartId, monat: '<?= $selectedMonat ?>' },
                dataType: 'html'
            });
        }

        $('#TableAuswertungen tbody').on('click', 'td.details-control', function () {
            var tr = $(this).closest('tr');
            var row = table.row(tr);
            var buchungsartId = tr.find('td:nth-child(2)').text();
            if (row.child.isShown()) {
                row.child.hide();
                tr.removeClass('shown');
                $(this).html('<i class="fa fa-plus-circle"></i>');
            } else {
                $(this).html('<i class="fa fa-minus-circle"></i>');
                format(buchungsartId).done(function (html) {
                    row.child(html).show();
                    tr.addClass('shown');
                });
            }
        });


    </script>
    <script>
        document.getElementById('btnPieChart').addEventListener('click', function () {
            // Chart-Container anzeigen
            document.getElementById('chartContainer').style.display = 'block';

            // Daten aus PHP
            const labels = <?= json_encode(array_column($rows, 'buchungsart')) ?>;
            const data = <?= json_encode(array_map(function ($r) {
                return (float) $r['anteil'];
            }, $rows)) ?>;

            const ctx = document.getElementById('pieChart').getContext('2d');

            // Prüfen, ob schon ein Chart existiert und zerstören
            if (window.pieChartInstance) {
                window.pieChartInstance.destroy();
            }

            window.pieChartInstance = new Chart(ctx, {
                type: 'pie',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Anteil in %',
                        data: data,
                        backgroundColor: [
                            '#007bff', '#28a745', '#dc3545', '#ffc107', '#17a2b8', '#6f42c1',
                            '#fd7e14', '#20c997', '#6610f2', '#e83e8c', '#343a40', '#fd6f6f'
                        ],
                        borderColor: '#fff',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'right',
                        },
                        tooltip: {
                            callbacks: {
                                label: function (context) {
                                    return context.label + ': ' + context.raw + ' %';
                                }
                            }
                        }
                    }
                }
            });
        });
    </script>

</body>

</html>
<?php ob_end_flush(); ?>