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
    <title>CashControl Bestände Chart</title>

    <!-- CSS -->
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="css/jquery.dataTables.min.css" rel="stylesheet">
    <link href="css/responsive.dataTables.min" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
  
</head>

<?php

require 'db.php';
$userid = (int) $_SESSION['userid'];
$email = $_SESSION['email'] ?? '';

// Monatsnamen
$monatMapping = [
    1 => "Januar",
    2 => "Februar",
    3 => "März",
    4 => "April",
    5 => "Mai",
    6 => "Juni",
    7 => "Juli",
    8 => "August",
    9 => "September",
    10 => "Oktober",
    11 => "November",
    12 => "Dezember"
];

// Jahre für Dropdown
$stmtYears = $pdo->prepare("SELECT DISTINCT YEAR(datum) AS jahr 
                            FROM bestaende 
                            WHERE userid = :uid 
                            ORDER BY jahr DESC");
$stmtYears->execute(['uid' => $userid]);
$jahre = $stmtYears->fetchAll(PDO::FETCH_COLUMN);

// Aktuelles Jahr auswählen
$jahrSelected = date('Y');
if (!empty($jahre))
    $jahrSelected = (int) $jahre[0];

// Anfangsdaten
$einnahmen = array_fill(1, 12, 0.0);
$ausgaben = array_fill(1, 12, 0.0);

$sql = "SELECT MONTH(datum) AS m, SUM(einlagen) AS sum_einlagen, SUM(ausgaben) AS sum_ausgaben
        FROM bestaende
        WHERE userid = :uid AND YEAR(datum) = :jahr
        GROUP BY MONTH(datum)
        ORDER BY MONTH(datum)";
$stmt = $pdo->prepare($sql);
$stmt->execute(['uid' => $userid, 'jahr' => $jahrSelected]);
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $m = (int) $row['m'];
    $einnahmen[$m] = (float) $row['sum_einlagen'];
    $ausgaben[$m] = (float) $row['sum_ausgaben'];
}

$labels = array_values($monatMapping);
$einlagenWerte = array_values($einnahmen);
$ausgabenWerte = array_values($ausgaben);
?>

<body>
    <?php require_once 'includes/header.php'; ?>

    <header class="custom-header py-2 text-white">
        <div class="container-fluid">
            <div class="row align-items-center">

                <!-- Titel zentriert -->
                <div class="col-12 text-center mb-2 mb-md-0">
                    <h2 class="h4 mb-0">CashControl - Bestände - Chart</h2>
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

    <div class="container-fluid mt-3">
        <!-- Erste Zeile: Zurück-Button -->
        <div class="row mb-2">
            <div class="col-auto">
                <a href="Bestaende.php" class="btn btn-primary">
                    <i class="fa fa-arrow-left"></i>
                </a>
            </div>
        </div>

        <!-- Zweite Zeile: Jahr + Dropdown in einer Linie -->
        <div class="row align-items-center mb-3">
            <div class="col-auto">
                <label for="jahr" class="form-label mb-0">Jahr:</label>
            </div>
            <div class="col-auto">
                <select id="jahr" class="form-select">
                    <?php foreach ($jahre as $j) {
                        $sel = ((int) $j === $jahrSelected) ? 'selected' : '';
                        echo "<option value='$j' $sel>$j</option>";
                    } ?>
                </select>
            </div>
        </div>

        <!-- Chart -->
        <div class="row">
            <div class="col-12 chart-wrap">
                <canvas id="myChart"></canvas>
            </div>
        </div>
    </div>


    <!-- JS -->
    <script src="js/jquery.min.js"></script>
    <script src="js/bootstrap.bundle.min.js"></script>
    <script src="js/Chart.min.js"></script>

    <script>
        var ctx = document.getElementById("myChart").getContext("2d");
        var chart = new Chart(ctx, {
            type: "bar",
            data: {
                labels: <?php echo json_encode($labels); ?>,
                datasets: [
                    { label: "Einlagen", backgroundColor: "rgba(25,135,84,0.7)", borderColor: "rgba(25,135,84,1)", data: <?php echo json_encode($einlagenWerte); ?> },
                    { label: "Ausgaben", backgroundColor: "rgba(220,53,69,0.7)", borderColor: "rgba(220,53,69,1)", data: <?php echo json_encode($ausgabenWerte); ?> }
                ]
            },
            options: {
                title: { display: true, text: "Einlagen und Ausgaben – Jahresverlauf <?php echo $jahrSelected; ?>", fontSize: 18 },
                scales: { xAxes: [{ barPercentage: 0.9, categoryPercentage: 0.9 }], yAxes: [{ ticks: { beginAtZero: true } }] }
            }
        });

        $("#jahr").change(function () {
            var jahr = $(this).val();
            $.post("getChartData.php", { jahr: jahr }, function (data) {
                chart.data.labels = data.monate;
                chart.data.datasets[0].data = data.einlagen;
                chart.data.datasets[1].data = data.ausgaben;
                chart.options.title.text = "Einlagen und Ausgaben – Jahresverlauf " + jahr;
                chart.update();
            }, "json");
        });
    </script>
</body>

</html>
<?php ob_end_flush(); ?>