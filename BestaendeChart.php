<?php
ob_start();
session_start();

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

    <!-- JS -->
    <script src="js/jquery.min.js"></script>
    <script src="js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.5.0/Chart.min.js"></script>

</head>
<style>
    .topnav {
        overflow: hidden;
        background-color: #333;
    }

    .topnav a {
        float: left;
        display: block;
        color: white;
        text-align: center;
        padding: 14px 16px;
        text-decoration: none;
    }

    .topnav .icon {
        display: none;
    }

    @media screen and (max-width: 600px) {
        .topnav a:not(:first-child) {
            display: none;
        }

        .topnav a.icon {
            float: right;
            display: block;
        }
    }

    @media screen and (max-width: 600px) {
        .topnav.responsive {
            position: relative;
        }

        .topnav.responsive a.icon {
            position: absolute;
            right: 0;
            top: 0;
        }

        .topnav.responsive a {
            float: none;
            display: block;
            text-align: left;
        }
    }
</style>

<body>
    <div class="topnav" id="myTopnav">
        <a href="Index.php">Haupseite</a>
        <a href="Buchungsarten.php">Buchungsarten</a>
        <a class="active" href="Bestaende.php">Bestände</a>
        <a href="About.php">Über</a>
        <a href="javascript:void(0);" class="icon" onclick="NavBarClick()">
            <i class="fa fa-bars"></i>
        </a>
    </div>

    <div id="chart">
        <form id="chartform" method="post">
            <div class="custom-container">
                <div class="mt-0 p-5 bg-secondary text-white text-center rounded-bottom">
                    <h1>Kassenbuch</h1>
                    <p>Chart</p>
                </div>

                <?php
                require 'db.php';

                $email = $_SESSION['email'];
                $userid = $_SESSION['userid'];

                $monatMapping = [
                    "1" => "Januar",
                    "2" => "Februar",
                    "3" => "März",
                    "4" => "April",
                    "5" => "Mai",
                    "6" => "Juni",
                    "7" => "Juli",
                    "8" => "August",
                    "9" => "September",
                    "10" => "Oktober",
                    "11" => "November",
                    "12" => "Dezember"
                ];

                $einnahmen = [];
                $ausgaben = [];

                // Einnahmen
                $sqlEinnahmen = "SELECT monat, einnahmen FROM bestaende ORDER BY monat ASC";
                $stmtEinnahmen = $pdo->prepare($sqlEinnahmen);
                $stmtEinnahmen->execute();

                while ($row = $stmtEinnahmen->fetch(PDO::FETCH_ASSOC)) {
                    $monatName = $monatMapping[trim($row['monat'])] ?? null;
                    if ($monatName) {
                        $einnahmen[$monatName] = (float) $row['einnahmen'];
                    }
                }

                // Ausgaben
                $sqlAusgaben = "SELECT monat, ausgaben FROM bestaende ORDER BY monat ASC";
                $stmtAusgaben = $pdo->prepare($sqlAusgaben);
                $stmtAusgaben->execute();

                while ($row = $stmtAusgaben->fetch(PDO::FETCH_ASSOC)) {
                    $monatName = $monatMapping[trim($row['monat'])] ?? null;
                    if ($monatName) {
                        $ausgaben[$monatName] = (float) $row['ausgaben'];
                    }
                }
                $monate = array_values($monatMapping); // Monatsnamen
                $einnahmenWerte = array_map(function ($monat) use ($einnahmen) {
                    return $einnahmen[$monat] ?? 0;
                }, $monate);

                $ausgabenWerte = array_map(function ($monat) use ($ausgaben) {
                    return $ausgaben[$monat] ?? 0;
                }, $monate);

                ?>

                <div class="container-fluid mt-3">
                    <div class="row">
                        <div class="col-12 text-end">
                            <?php echo "<span>Angemeldet als: " . htmlspecialchars($email) . "</span>"; ?>
                            <a class="btn btn-primary" title="Abmelden vom Kassenbuch" href="logout.php">
                                <i class="fa fa-sign-out" aria-hidden="true"></i>
                            </a>
                        </div>
                    </div>                   
                    <div class="form-group row me-4">
                        <div class="col-sm-offset-2 col-sm-10">
                            <a href="Bestaende.php" title="Zurück zur Bestandsübersicht" class="btn btn-primary"><i
                                    class="fa fa-arrow-left" aria-hidden="true"></i></a>
                        </div>
                    </div>
                    <div class="form-group row me-4">
                        <canvas id="myChart" style="width:100%;"></canvas>
                    </div>
                </div>
            </div>
        </form>

        <script>
            var xValues = <?php echo json_encode($monate); ?>;
            var yEinnahmen = <?php echo json_encode($einnahmenWerte); ?>;
            var yAusgaben = <?php echo json_encode($ausgabenWerte); ?>;

            new Chart("myChart", {
                type: "bar",
                data: {
                    labels: xValues,
                    datasets: [
                        {
                            label: "Einnahmen",
                            backgroundColor: "green",
                            data: yEinnahmen,
                        },
                        {
                            label: "Ausgaben",
                            backgroundColor: "red",
                            data: yAusgaben,
                        },
                    ],
                },
                options: {
                    title: {
                        display: true,
                        text: "Einnahmen und Ausgaben Jahresverlauf 2025",
                        fontSize: 18 // Titelgröße
                    },
                    legend: {
                        labels: {
                            fontSize: 12 // Legendentext
                        }
                    },
                    scales: {
                        xAxes: [{
                            barPercentage: 0.9,
                            categoryPercentage: 0.9,
                            ticks: {
                                fontSize: 12 // Achsentext
                            }
                        }],
                        yAxes: [{
                            ticks: {
                                beginAtZero: true,
                                fontSize: 12
                            }
                        }]
                    }
                }
            });
        </script>
    </div>
</body>

</html>
<script>
    function NavBarClick() {
        var x = document.getElementById("myTopnav");
        if (x.className === "topnav") {
            x.className += " responsive";
        } else {
            x.className = "topnav";
        }
    }
</script>