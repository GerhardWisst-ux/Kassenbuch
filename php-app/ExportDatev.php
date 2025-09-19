<?php
require 'db.php';
session_start();

if (empty($_SESSION['userid'])) {
    header("Location: Login.php");
    exit;
}

$buchungen = [];
$export_file = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $kontenrahmen = $_POST['kontenrahmen'] ?? 'SKR03';
    $datum_von = $_POST['datum_von'] ?? '';
    $datum_bis = $_POST['datum_bis'] ?? '';

    // Mapping laden
    $mappingStmt = $pdo->prepare("
        SELECT buchungsart, konto, gegenkonto, bu_schluessel
        FROM buchungsart_mapping
        WHERE kontenrahmen = :rahmen
    ");
    $mappingStmt->execute(['rahmen' => $kontenrahmen]);
    $mapping = [];
    while ($m = $mappingStmt->fetch(PDO::FETCH_ASSOC)) {
        $mapping[$m['buchungsart']] = $m;
    }

    // SQL vorbereiten
    $sql = "SELECT id, kassennummer, belegnr, datum, beschreibung, betrag, typ, buchungsart 
            FROM buchungen WHERE userid=:userid AND kassennummer = :kassennummer ";
    $params = [];
    if (!empty($datum_von)) {
        $sql .= " AND datum >= :datum_von";
        $params['datum_von'] = $datum_von;
    }
    if (!empty($datum_bis)) {
        $sql .= " AND datum <= :datum_bis";
        $params['datum_bis'] = $datum_bis;
    }
    $params['userid'] = $_SESSION['userid'];
    $params['kassennummer'] = 1;
    $sql .= " ORDER BY datum ASC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $buchungen = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Export starten
    if (isset($_POST['export'])) {
        $export_file = "exports/DATEV_Export_" . $kontenrahmen . "_" . date("Ymd_His") . ".csv";
        if (!is_dir('exports'))
            mkdir('exports', 0777, true);

        $fp = fopen($export_file, "w");
        $header = ["EXTF", "700", "21", "Kassenexport", "Buchungen", date("Ymd"), "", "", "", "", "", "", ""];
        $columns = [
            "Umsatz (ohne Soll/Haben-Kz)",
            "Soll/Haben-Kennzeichen",
            "WKZ Umsatz",
            "Kurs",
            "Basisumsatz",
            "WKZ Basisumsatz",
            "Konto",
            "Gegenkonto",
            "BU-Schlüssel",
            "Belegfeld 1",
            "Belegfeld 2",
            "Buchungstext",
            "Belegdatum",
            "Buchungstyp",
            "Kassen-Nr"
        ];
        fputcsv($fp, $header, ";");
        fputcsv($fp, $columns, ";");

        foreach ($buchungen as $row) {
            $sh = ($row['typ'] === "Ausgabe") ? "H" : "S";
            $buchungsart = $row['buchungsart'];
            $konto = $mapping[$buchungsart]['konto'] ?? '9999';
            $gegenkonto = $mapping[$buchungsart]['gegenkonto'] ?? '9999';
            $bu = $mapping[$buchungsart]['bu_schluessel'] ?? '';

            $data = [
                number_format($row['betrag'], 2, ",", ""),
                $sh,
                "EUR",
                "",
                "",
                "",
                $konto,
                $gegenkonto,
                $bu,
                $row['belegnr'],
                $row['id'],
                $row['beschreibung'],
                date("Ymd", strtotime($row['datum'])),
                $row['typ'],
                $row['kassennummer']
            ];
            fputcsv($fp, $data, ";");
        }
        fclose($fp);
    }
}
?>


<!DOCTYPE html>
<html lang="de">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Datensicherung für das Kassenbuch – einfache Verwaltung und sichere Backups.">
    <meta name="author" content="Gerhard Wißt">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>CashControl - DATEV-Export</title>
    <link rel="icon" type="image/png" href="images/favicon.png" />
    <!-- CSS -->
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="css/jquery.dataTables.min.css" rel="stylesheet">
    <link href="css/responsive.dataTables.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">

    <style>
        /* grün */
        .income {
            background-color: #d4edda;
        }

        /* rot */
        .expense {
            background-color: #f8d7da;
        }

        .circle-btn {
            width: 36px;
            height: 36px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0;
            font-size: 14px;
            /* Icon-Größe */
        }
    </style>
</head>

<body>

    <?php
    require_once 'includes/header.php';
    ?>
    <header class="custom-header py-2 text-white">
        <div class="container-fluid">
            <div class="row align-items-center">
                <!-- Titel zentriert -->
                <div class="col-12 text-center mb-2 mb-md-0">
                    <h2 class="h4 mb-0">CashControl - DATEV-Export</h2>
                </div>
                <?php
                require_once 'includes/benutzerversion.php';
                ?>
            </div>
        </div>
    </header>
    <div class="container-fluid">

        <form method="post" class="card p-4 shadow-sm">
            <div class="mb-3">

                <!-- Toolbar -->
                <div class="btn-toolbar mb-3" role="toolbar" aria-label="Toolbar">
                    <a href="Index.php" class="btn btn-primary rounded-circle me-2 circle-btn"><i
                            class="fa fa-arrow-left"></i></a>

                    <div class="ms-auto">
                        <a href="help/ExportDatev.php" class="btn btn-primary btn-sm" title="Hilfe"><i
                                class="fa fa-question-circle"></i></a>
                    </div>
                </div>

                <label for="kontenrahmen" class="form-label">Kontenrahmen wählen</label>
                <select id="kontenrahmen" name="kontenrahmen" class="form-select">
                    <option value="SKR03">SKR03 (Deutschland)</option>
                    <option value="SKR04">SKR04 (Deutschland)</option>
                </select>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="datum_von" class="form-label">Von (Datum)</label>
                    <?php
                    $default_von = date('Y-m-01'); // erster Tag des Monats
                    $datum_von_value = $_POST['datum_von'] ?? $default_von;
                    ?>
                    <input type="date" id="datum_von" name="datum_von" class="form-control"
                        value="<?= htmlspecialchars($datum_von_value) ?>">
                </div>
                <div class="col-md-6 mb-3">
                    <label for="datum_bis" class="form-label">Bis (Datum)</label>
                    <?php
                    $default_bis = date('Y-m-d'); // erster Tag des Monats
                    $datum_bis_value = $_POST['datum_bis'] ?? $default_bis;
                    ?>
                    <input type="date" id="datum_bis" name="datum_bis" class="form-control"
                        value="<?= htmlspecialchars($datum_bis_value) ?>">
                </div>
            </div>

            <button type="submit" name="preview" class="btn btn-primary">🔍 Vorschau anzeigen</button>
            <?php if (!empty($buchungen ?? [])): ?>
                <button type="submit" name="export" class="btn btn-success">📥 Export starten</button>
            <?php endif; ?>
        </form>

        <?php if (!empty($export_file) && file_exists($export_file)): ?>
            <div class="alert alert-success">
                DATEV-Datei wurde erzeugt: <a href="<?= htmlspecialchars($export_file) ?>"
                    target="_blank"><?= basename($export_file) ?></a>
            </div>
        <?php endif; ?>

        <?php
        // Summen berechnen
        $summe_einnahmen = 0;
        $summe_ausgaben = 0;
        foreach ($buchungen as $row) {
            if ($row['typ'] === "Ausgabe") {
                $summe_ausgaben += $row['betrag'];
            } else {
                $summe_einnahmen += $row['betrag'];
            }
        }
        $saldo = $summe_einnahmen - $summe_ausgaben;

        // CSS für Summenfarbgebung
        $saldo_class = ($saldo >= 0) ? "income" : "expense";
        ?>

        <?php if (!empty($buchungen ?? [])): ?>
            <h4>Vorschau der Buchungen</h4>
            <table class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th>Datum</th>
                        <th>Belegnr</th>
                        <th>Buchungsart</th>
                        <th>Beschreibung</th>
                        <th>Betrag</th>
                        <th>Typ</th>
                        <th>Konto</th>
                        <th>Gegenkonto</th>
                        <th>BU-Schlüssel</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($buchungen as $row):
                        $buchungsart = $row['buchungsart'];
                        $konto = $mapping[$buchungsart]['konto'] ?? '9999';
                        $gegenkonto = $mapping[$buchungsart]['gegenkonto'] ?? '9999';
                        $bu = $mapping[$buchungsart]['bu_schluessel'] ?? '';

                        // Zeilenklasse
                        $class = ($row['typ'] === "Ausgabe") ? "expense" : "income";

                        // Betrag formatieren und farblich hervorheben
                        $farbe = ($row['typ'] === 'Ausgabe') ? 'red' : 'green';
                        $betragFormatted = "<span style='color: {$farbe}; font-weight: bold;'>"
                            . number_format($row['betrag'], 2, ",", "")
                            . "</span>";
                        ?>
                        <tr class="<?= $class ?>">
                            <td><?= htmlspecialchars($row['datum']) ?></td>
                            <td><?= htmlspecialchars($row['belegnr']) ?></td>
                            <td><?= htmlspecialchars($row['buchungsart']) ?></td>
                            <td><?= htmlspecialchars($row['beschreibung']) ?></td>
                            <td style="text-align: right;"><?= $betragFormatted ?></td>
                            <td><?= htmlspecialchars($row['typ']) ?></td>
                            <td><?= htmlspecialchars($konto) ?></td>
                            <td><?= htmlspecialchars($gegenkonto) ?></td>
                            <td><?= htmlspecialchars($bu) ?></td>
                        </tr>
                    <?php endforeach; ?>

                </tbody>
                <tfoot class="table-secondary fw-bold">
                    <tr>
                        <td style="vertical-align: top;" colspan="1">Summen</td>
                        <td>
                            <span style="color: green;">Einlagen:
                                <?= number_format($summe_einnahmen, 2, ",", "") ?></span><br>
                            <span style="color: red;">Ausgaben: <?= number_format($summe_ausgaben, 2, ",", "") ?></span><br>
                            <span style="font-weight: bold;">Saldo: <?= number_format($saldo, 2, ",", "") ?></span>
                        </td>
                        <td colspan="4"></td>
                    </tr>
                </tfoot>
            </table>
        </div>

    <?php endif; ?>

    </div>
    <script src="js/bootstrap.bundle.min.js"></script>
</body>

</html>