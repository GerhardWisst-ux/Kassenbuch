<?php
ob_start();
session_start();
if ($_SESSION['userid'] == "") {
    header('Location: Login.php'); // zum Loginformular
}
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Kassenbuch Bestände Chart</title>

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
                    <h2 class="h4 mb-0">Kassenbuch - Bestände - Chart</h2>
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


    <script src="js/jquery.min.js"></script>
    <script src="js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.min.js"></script>
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